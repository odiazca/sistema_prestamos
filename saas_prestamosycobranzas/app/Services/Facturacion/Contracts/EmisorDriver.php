<?php

namespace App\Services\Facturacion\Contracts;

use App\Models\FacturacionConfig;

/**
 * Contrato para los drivers de emisión de comprobantes electrónicos.
 *
 * Un driver recibe la configuración vigente y sabe cómo (o si) emitir
 * comprobantes ante la autoridad tributaria correspondiente.
 */
interface EmisorDriver
{
    public function __construct(FacturacionConfig $config);

    /** Nombre legible del driver (para UI / logs). */
    public function nombre(): string;

    /**
     * Comprueba la conectividad y credenciales con el servicio de emisión.
     *
     * @return array{ok: bool, mensaje: string, detalle?: array}
     */
    public function probarConexion(): array;

    /**
     * Emite un comprobante electrónico.
     *
     * @param  array  $comprobante  Datos normalizados del comprobante.
     * @return array{estado: string, mensaje: string, cdr?: mixed, xml?: string, hash?: string}
     *         estado ∈ { 'aceptado', 'rechazado', 'pendiente', 'error' }
     */
    public function emitir(array $comprobante): array;
}
