<?php

namespace App\Services\Facturacion;

use App\Models\Comprobante;
use App\Models\Prestamo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

/**
 * Genera y emite comprobantes electrónicos a partir de los pagos de préstamos.
 */
class ComprobanteService
{
    public function __construct(protected FacturacionManager $manager)
    {
    }

    /**
     * Crea el comprobante para un pago de préstamo y lo emite (si procede).
     *
     * @param  float  $monto  Importe total pagado (incluye IGV si aplica).
     */
    public function emitirDesdePrestamo(Prestamo $prestamo, float $monto, array $opts = []): Comprobante
    {
        $config = $this->manager->config();
        $prestamo->loadMissing('cliente');
        $cliente = $prestamo->cliente;

        [$tipoDocCliente, $tipoComprobante] = $this->resolverTipos($cliente);

        $serie = $tipoComprobante === '01' ? ($config->serie_factura ?: 'F001') : ($config->serie_boleta ?: 'B001');
        $afectacion = $config->afectacion_igv ?: '20';

        // Desglose de importes de modo que el total sea exactamente lo pagado.
        [$gravado, $exonerado, $inafecto, $igv] = $this->desglosar($monto, $afectacion);

        // Reserva atómica del correlativo y creación del registro.
        $comprobante = DB::transaction(function () use (
            $prestamo, $cliente, $tipoComprobante, $serie, $afectacion,
            $gravado, $exonerado, $inafecto, $igv, $monto, $tipoDocCliente, $opts
        ) {
            $correlativo = (int) Comprobante::where('tipo', $tipoComprobante)
                ->where('serie', $serie)
                ->lockForUpdate()
                ->max('correlativo') + 1;

            return Comprobante::create([
                'tipo'             => $tipoComprobante,
                'serie'            => $serie,
                'correlativo'      => $correlativo,
                'prestamo_id'      => $prestamo->id,
                'cliente_id'       => $cliente?->id,
                'cliente_tipo_doc' => $tipoDocCliente,
                'cliente_num_doc'  => $cliente?->documento,
                'cliente_nombre'   => $cliente?->nombre_completo ?: 'CLIENTE VARIOS',
                'moneda'           => 'PEN',
                'afectacion'       => $afectacion,
                'gravado'          => $gravado,
                'exonerado'        => $exonerado,
                'inafecto'         => $inafecto,
                'igv'              => $igv,
                'total'            => round($monto, 2),
                'concepto'         => $opts['concepto'] ?? ('Pago de préstamo '.$prestamo->codigo),
                'estado'           => 'pendiente',
                'user_id'          => auth()->id(),
            ]);
        });

        return $this->emitir($comprobante);
    }

    /**
     * Envía (o reenvía) un comprobante existente a SUNAT.
     */
    public function emitir(Comprobante $comprobante): Comprobante
    {
        $config = $this->manager->config();

        if (! $config->habilitado || $config->driver !== 'sunat') {
            $comprobante->update([
                'estado'  => 'pendiente',
                'mensaje' => $config->habilitado
                    ? 'Sin driver SUNAT activo: comprobante pendiente de emisión.'
                    : 'Facturación electrónica deshabilitada: comprobante pendiente.',
            ]);

            return $comprobante;
        }

        try {
            $resultado = $this->manager->driver()->emitir($this->armarArray($comprobante));

            $xmlPath = null;
            if (! empty($resultado['xml'])) {
                $xmlPath = 'facturacion/pe/xml/'.$comprobante->numero.'.xml';
                Storage::disk('local')->put($xmlPath, $resultado['xml']);
            }

            $comprobante->update([
                'estado'       => $resultado['estado'] ?? 'error',
                'mensaje'      => $resultado['mensaje'] ?? null,
                'sunat_codigo' => isset($resultado['cdr']) && method_exists($resultado['cdr'], 'getCode')
                    ? $resultado['cdr']->getCode()
                    : null,
                'xml_path'     => $xmlPath ?? $comprobante->xml_path,
            ]);
        } catch (Throwable $e) {
            $comprobante->update([
                'estado'  => 'error',
                'mensaje' => 'Error al emitir: '.$e->getMessage(),
            ]);
        }

        return $comprobante;
    }

    /** Construye el array normalizado que consume el driver de emisión. */
    protected function armarArray(Comprobante $c): array
    {
        return [
            'tipo'          => $c->tipo,
            'serie'         => $c->serie,
            'correlativo'   => (string) $c->correlativo,
            'fecha_emision' => $c->created_at?->toDateString() ?? now()->toDateString(),
            'moneda'        => $c->moneda,
            'cliente'       => [
                'tipo_doc'     => $c->cliente_tipo_doc ?: '0',
                'num_doc'      => $c->cliente_num_doc ?: '00000000',
                'razon_social' => $c->cliente_nombre ?: 'CLIENTE VARIOS',
            ],
            'items' => [[
                'codigo'          => 'PAGO',
                'descripcion'     => $c->concepto ?: 'Pago de préstamo',
                'cantidad'        => 1,
                'unidad'          => 'NIU',
                'valor_unitario'  => (float) ($c->gravado + $c->exonerado + $c->inafecto),
                'igv'             => (float) $c->igv,
                'tipo_afectacion' => $c->afectacion,
            ]],
        ];
    }

    /**
     * Mapea el cliente al catálogo 06 de SUNAT y decide boleta/factura.
     *
     * @return array{0:string,1:string} [tipoDocCliente, tipoComprobante]
     */
    protected function resolverTipos($cliente): array
    {
        $doc = strtoupper((string) ($cliente->tipo_documento ?? 'DNI'));
        $num = (string) ($cliente->documento ?? '');

        // Factura solo si el cliente tiene RUC válido (11 dígitos).
        if ($doc === 'RUC' && strlen($num) === 11) {
            return ['6', '01'];
        }

        $tipoDoc = match ($doc) {
            'RUC'                              => '6',
            'CE', 'CARNET DE EXTRANJERIA'      => '4',
            'PASAPORTE'                        => '7',
            'DNI'                              => '1',
            default                            => $num === '' ? '0' : '1',
        };

        return [$tipoDoc, '03']; // Boleta
    }

    /**
     * Desglosa el importe total según la afectación de IGV, de modo que
     * gravado/exonerado/inafecto + igv = total.
     *
     * @return array{0:float,1:float,2:float,3:float} [gravado, exonerado, inafecto, igv]
     */
    protected function desglosar(float $total, string $afectacion): array
    {
        $total = round($total, 2);

        if ($afectacion === '10') { // Gravado: el total incluye IGV
            $base = round($total / 1.18, 2);
            $igv = round($total - $base, 2);

            return [$base, 0.0, 0.0, $igv];
        }

        if ($afectacion === '20') { // Exonerado
            return [0.0, $total, 0.0, 0.0];
        }

        return [0.0, 0.0, $total, 0.0]; // Inafecto (30)
    }
}
