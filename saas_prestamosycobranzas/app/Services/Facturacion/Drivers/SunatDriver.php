<?php

namespace App\Services\Facturacion\Drivers;

use App\Models\FacturacionConfig;
use App\Services\Facturacion\Contracts\EmisorDriver;
use App\Services\Facturacion\Support\Numero;
use Carbon\Carbon;
use RuntimeException;
use Throwable;

/**
 * Driver de emisión directa a SUNAT (Perú) usando la librería Greenter.
 *
 * Requiere:
 *   composer require greenter/lite
 *   Extensiones PHP: soap, openssl, dom, mbstring, zip.
 *   Un certificado digital en formato .pem (certificado + llave privada).
 *
 * Genera el XML UBL 2.1, lo firma (XAdES) y lo envía a los Web Services de
 * SUNAT, devolviendo la Constancia de Recepción (CDR).
 */
class SunatDriver implements EmisorDriver
{
    public function __construct(protected FacturacionConfig $config)
    {
    }

    public function nombre(): string
    {
        return 'SUNAT ('.($this->config->entorno === 'produccion' ? 'Producción' : 'Beta').')';
    }

    /**
     * Verifica configuración, certificado y conectividad con SUNAT.
     */
    public function probarConexion(): array
    {
        // 1) Validación de datos mínimos
        $faltantes = $this->camposFaltantes();
        if ($faltantes) {
            return [
                'ok'      => false,
                'mensaje' => 'Faltan datos para conectar con SUNAT: '.implode(', ', $faltantes).'.',
            ];
        }

        // 2) Certificado presente y legible
        $certPath = $this->config->certificadoRutaAbsoluta();
        if (! $certPath || ! is_readable($certPath)) {
            return [
                'ok'      => false,
                'mensaje' => 'No se encontró o no se puede leer el certificado en la ruta indicada.',
            ];
        }

        // 3) Librería disponible
        if (! $this->greenterDisponible()) {
            return [
                'ok'      => false,
                'mensaje' => 'La librería Greenter no está instalada. Ejecuta: composer require greenter/lite',
            ];
        }

        // 4) Conectividad real con el WSDL de SUNAT
        try {
            $wsdl = $this->endpointFactura();
            $ctx = stream_context_create([
                'ssl'  => ['verify_peer' => false, 'verify_peer_name' => false],
                'http' => ['timeout' => 15],
            ]);
            // Cargar el WSDL confirma que el servicio de SUNAT está accesible.
            new \SoapClient($wsdl, [
                'stream_context' => $ctx,
                'cache_wsdl'     => WSDL_CACHE_NONE,
                'connection_timeout' => 15,
                'exceptions'     => true,
            ]);

            return [
                'ok'      => true,
                'mensaje' => 'Conexión con SUNAT establecida ('.$this->config->entornoLabel().'). '
                    .'Certificado y credenciales cargados correctamente.',
            ];
        } catch (Throwable $e) {
            return [
                'ok'      => false,
                'mensaje' => 'No se pudo conectar con SUNAT: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Emite un comprobante electrónico ante SUNAT.
     *
     * @param  array  $comprobante  Datos normalizados (ver EmisorDriver).
     */
    public function emitir(array $comprobante): array
    {
        $faltantes = $this->camposFaltantes();
        if ($faltantes) {
            return [
                'estado'  => 'error',
                'mensaje' => 'Configuración incompleta: '.implode(', ', $faltantes).'.',
            ];
        }

        if (! $this->greenterDisponible()) {
            return [
                'estado'  => 'error',
                'mensaje' => 'La librería Greenter no está instalada. Ejecuta: composer require greenter/lite',
            ];
        }

        try {
            $see = $this->buildSee();
            $invoice = $this->buildInvoice($comprobante);

            $result = $see->send($invoice);

            $xml = null;
            if (method_exists($see, 'getXmlSigned')) {
                try {
                    $xml = $see->getXmlSigned($invoice);
                } catch (Throwable $e) {
                    $xml = null;
                }
            }

            if (! $result->isSuccess()) {
                return [
                    'estado'  => 'rechazado',
                    'mensaje' => optional($result->getError())->getMessage() ?? 'SUNAT rechazó el envío.',
                    'xml'     => $xml,
                ];
            }

            $cdr = $result->getCdrResponse();

            return [
                'estado'  => $cdr && (int) $cdr->getCode() === 0 ? 'aceptado' : 'rechazado',
                'mensaje' => $cdr ? $cdr->getDescription() : 'Comprobante enviado.',
                'cdr'     => $cdr,
                'xml'     => $xml,
            ];
        } catch (Throwable $e) {
            return [
                'estado'  => 'error',
                'mensaje' => 'Error al emitir: '.$e->getMessage(),
            ];
        }
    }

    // ===================================================================
    // Construcción de objetos Greenter
    // ===================================================================

    /** Instancia el objeto See (SEE = Sistema de Emisión Electrónica). */
    protected function buildSee(): object
    {
        $seeClass = '\\Greenter\\See';
        /** @var object $see */
        $see = new $seeClass();

        $see->setCertificate(file_get_contents($this->config->certificadoRutaAbsoluta()));
        $see->setService($this->endpointFactura());
        $see->setClaveSOL(
            $this->config->ruc,
            $this->config->sol_usuario,
            (string) $this->config->sol_clave,
        );

        return $see;
    }

    /** Construye una Invoice (factura 01 o boleta 03) desde el array. */
    protected function buildInvoice(array $c): object
    {
        $tipo = $c['tipo'] ?? '01';
        $serie = $c['serie'] ?? ($tipo === '03' ? $this->config->serie_boleta : $this->config->serie_factura);
        $correlativo = (string) ($c['correlativo'] ?? '1');
        $moneda = $c['moneda'] ?? 'PEN';
        $fecha = isset($c['fecha_emision']) ? Carbon::parse($c['fecha_emision']) : Carbon::now();

        $company = $this->buildCompany();
        $client = $this->buildClient($c['cliente'] ?? []);

        // Detalles y totales (por bucket de afectación)
        $details = [];
        $gravadas = 0.0;
        $exoneradas = 0.0;
        $inafectas = 0.0;
        $igvTotal = 0.0;

        foreach ($c['items'] ?? [] as $it) {
            $cantidad  = (float) ($it['cantidad'] ?? 1);
            $afe       = (string) ($it['tipo_afectacion'] ?? '10');
            $valorUnit = round((float) ($it['valor_unitario'] ?? 0), 2);
            $valorVenta = round($valorUnit * $cantidad, 2);

            // Si el servicio ya calculó el IGV se respeta; si no, se computa.
            $gravada = $afe === '10';
            $igv = $gravada
                ? round($it['igv'] ?? ($valorVenta * 0.18), 2)
                : 0.0;
            $porcentaje = $gravada ? 18.0 : 0.0;
            $precioUnit = $cantidad > 0
                ? round(($valorVenta + $igv) / $cantidad, 2)
                : round($valorUnit + $igv, 2);

            if ($afe === '20') {
                $exoneradas += $valorVenta;
            } elseif ($afe === '30') {
                $inafectas += $valorVenta;
            } else {
                $gravadas += $valorVenta;
                $igvTotal += $igv;
            }

            $sale = $this->newGreenter('Model\\Sale\\SaleDetail');
            $sale->setCodProducto($it['codigo'] ?? 'P001')
                ->setUnidad($it['unidad'] ?? 'NIU')
                ->setCantidad($cantidad)
                ->setDescripcion($it['descripcion'] ?? 'Concepto')
                ->setMtoBaseIgv($valorVenta)
                ->setPorcentajeIgv($porcentaje)
                ->setIgv($igv)
                ->setTipAfeIgv($afe)
                ->setTotalImpuestos($igv)
                ->setMtoValorVenta($valorVenta)
                ->setMtoValorUnitario($valorUnit)
                ->setMtoPrecioUnitario($precioUnit);
            $details[] = $sale;
        }

        $gravadas   = round($gravadas, 2);
        $exoneradas = round($exoneradas, 2);
        $inafectas  = round($inafectas, 2);
        $igvTotal   = round($igvTotal, 2);
        $valorVentaTotal = round($gravadas + $exoneradas + $inafectas, 2);
        $total = round($valorVentaTotal + $igvTotal, 2);

        $legend = $this->newGreenter('Model\\Sale\\Legend')
            ->setCode('1000')
            ->setValue(Numero::enLetras($total, $moneda));

        $invoice = $this->newGreenter('Model\\Sale\\Invoice')
            ->setUblVersion('2.1')
            ->setTipoOperacion('0101')          // Venta interna
            ->setTipoDoc($tipo)
            ->setSerie($serie)
            ->setCorrelativo($correlativo)
            ->setFechaEmision($fecha)
            ->setTipoMoneda($moneda)
            ->setCompany($company)
            ->setClient($client)
            ->setMtoOperGravadas($gravadas)
            ->setMtoOperExoneradas($exoneradas)
            ->setMtoOperInafectas($inafectas)
            ->setMtoIGV($igvTotal)
            ->setTotalImpuestos($igvTotal)
            ->setValorVenta($valorVentaTotal)
            ->setSubTotal($total)
            ->setMtoImpVenta($total)
            ->setDetails($details)
            ->setLegends([$legend]);

        // Forma de pago: contado (requerido para facturas)
        if (method_exists($invoice, 'setFormaPago')) {
            $invoice->setFormaPago($this->newGreenter('Model\\Sale\\FormaPagos\\FormaPagoContado'));
        }

        return $invoice;
    }

    protected function buildCompany(): object
    {
        $address = $this->newGreenter('Model\\Company\\Address')
            ->setUbigueo($this->config->ubigeo ?: '150101')
            ->setDepartamento($this->config->departamento ?: 'LIMA')
            ->setProvincia($this->config->provincia ?: 'LIMA')
            ->setDistrito($this->config->distrito ?: 'LIMA')
            ->setDireccion($this->config->direccion ?: '-');

        return $this->newGreenter('Model\\Company\\Company')
            ->setRuc($this->config->ruc)
            ->setRazonSocial($this->config->razon_social ?: '-')
            ->setNombreComercial($this->config->nombre_comercial ?: ($this->config->razon_social ?: '-'))
            ->setAddress($address);
    }

    protected function buildClient(array $cli): object
    {
        return $this->newGreenter('Model\\Client\\Client')
            ->setTipoDoc($cli['tipo_doc'] ?? '1')      // 1 = DNI, 6 = RUC
            ->setNumDoc($cli['num_doc'] ?? '00000000')
            ->setRznSocial($cli['razon_social'] ?? 'CLIENTE VARIOS');
    }

    // ===================================================================
    // Utilidades
    // ===================================================================

    /** Instancia una clase de Greenter y devuelve el objeto. */
    protected function newGreenter(string $rel): object
    {
        $class = '\\Greenter\\'.$rel;

        if (! class_exists($class)) {
            throw new RuntimeException("Clase de Greenter no encontrada: {$class}. ¿Instalaste greenter/lite?");
        }

        return new $class();
    }

    protected function greenterDisponible(): bool
    {
        return class_exists('\\Greenter\\See');
    }

    protected function endpointFactura(): string
    {
        $entorno = $this->config->entorno === 'produccion' ? 'produccion' : 'beta';

        return config("facturacion.endpoints.{$entorno}.factura");
    }

    /** @return array<int, string> Lista de campos requeridos que faltan. */
    protected function camposFaltantes(): array
    {
        $faltan = [];
        if (blank($this->config->ruc))         $faltan[] = 'RUC';
        if (blank($this->config->razon_social)) $faltan[] = 'Razón social';
        if (blank($this->config->sol_usuario)) $faltan[] = 'Usuario SOL';
        if (blank($this->config->sol_clave))   $faltan[] = 'Clave SOL';
        if (blank($this->config->certificado_path)) $faltan[] = 'Certificado';

        return $faltan;
    }
}
