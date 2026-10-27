<?php

namespace App\Services\Facturacion;

use App\Models\FacturacionConfig;
use App\Services\Facturacion\Contracts\EmisorDriver;
use App\Services\Facturacion\Drivers\NingunoDriver;
use InvalidArgumentException;

/**
 * Punto de entrada de la facturación electrónica.
 *
 * Resuelve el driver de emisión adecuado a partir de la configuración vigente
 * y delega en él las operaciones de prueba de conexión y emisión.
 */
class FacturacionManager
{
    public function __construct(protected ?FacturacionConfig $config = null)
    {
        $this->config = $config ?: FacturacionConfig::actual();
    }

    public function config(): FacturacionConfig
    {
        return $this->config;
    }

    /** Resuelve la instancia del driver configurado. */
    public function driver(): EmisorDriver
    {
        $mapa = config('facturacion.drivers', []);
        $clave = $this->config->driver ?: 'ninguno';

        $class = $mapa[$clave] ?? null;

        if (! $class || ! class_exists($class)) {
            return new NingunoDriver($this->config);
        }

        $instancia = new $class($this->config);

        if (! $instancia instanceof EmisorDriver) {
            throw new InvalidArgumentException("El driver '{$clave}' no implementa EmisorDriver.");
        }

        return $instancia;
    }

    /** Prueba la conexión con el servicio de emisión. */
    public function probarConexion(): array
    {
        return $this->driver()->probarConexion();
    }

    /** Emite un comprobante usando el driver configurado. */
    public function emitir(array $comprobante): array
    {
        if (! $this->config->habilitado) {
            return [
                'estado'  => 'pendiente',
                'mensaje' => 'La facturación electrónica está deshabilitada.',
            ];
        }

        return $this->driver()->emitir($comprobante);
    }

    /**
     * Construye un comprobante de prueba (boleta a CLIENTE VARIOS) para
     * validar la emisión real contra el entorno Beta de SUNAT.
     */
    public function comprobanteDemo(): array
    {
        return [
            'tipo'          => '03', // Boleta
            'serie'         => $this->config->serie_boleta ?: 'B001',
            'correlativo'   => (string) random_int(1, 99999),
            'fecha_emision' => now()->toDateString(),
            'moneda'        => 'PEN',
            'cliente'       => [
                'tipo_doc'     => '1',
                'num_doc'      => '00000000',
                'razon_social' => 'CLIENTE DE PRUEBA',
            ],
            'items' => [
                [
                    'codigo'         => 'DEMO01',
                    'descripcion'    => 'Servicio de prueba - homologación',
                    'cantidad'       => 1,
                    'unidad'         => 'NIU',
                    'valor_unitario' => 100.00,
                ],
            ],
        ];
    }
}
