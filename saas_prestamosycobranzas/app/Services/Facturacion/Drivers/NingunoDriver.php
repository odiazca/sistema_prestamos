<?php

namespace App\Services\Facturacion\Drivers;

use App\Models\FacturacionConfig;
use App\Services\Facturacion\Contracts\EmisorDriver;

/**
 * Driver nulo: no realiza ninguna emisión.
 *
 * Los comprobantes quedan en estado "pendiente" para ser emitidos más tarde,
 * o simplemente no se generan. Útil mientras se termina de configurar SUNAT.
 */
class NingunoDriver implements EmisorDriver
{
    public function __construct(protected FacturacionConfig $config)
    {
    }

    public function nombre(): string
    {
        return 'Ninguno';
    }

    public function probarConexion(): array
    {
        return [
            'ok'      => false,
            'mensaje' => 'No hay un driver de emisión seleccionado. Elige "SUNAT" para emitir comprobantes reales.',
        ];
    }

    public function emitir(array $comprobante): array
    {
        return [
            'estado'  => 'pendiente',
            'mensaje' => 'Facturación sin driver activo: el comprobante queda pendiente de emisión.',
        ];
    }
}
