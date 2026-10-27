<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * Configuración singleton de Facturación Electrónica (SUNAT - Perú).
 *
 * Usar FacturacionConfig::actual() para obtener (o crear) la única fila.
 */
class FacturacionConfig extends Model
{
    protected $table = 'facturacion_configs';

    protected $fillable = [
        'habilitado', 'emitir_automatico', 'driver', 'entorno',
        'ruc', 'razon_social', 'nombre_comercial', 'direccion',
        'ubigeo', 'departamento', 'provincia', 'distrito',
        'serie_factura', 'serie_boleta', 'afectacion_igv',
        'sol_usuario', 'sol_clave', 'certificado_path', 'certificado_clave',
    ];

    protected $casts = [
        'habilitado'        => 'boolean',
        'emitir_automatico' => 'boolean',
        // Los campos sensibles se guardan cifrados en la base de datos.
        'sol_clave'         => 'encrypted',
        'certificado_clave' => 'encrypted',
    ];

    /** Ocultar credenciales al serializar. */
    protected $hidden = ['sol_clave', 'certificado_clave'];

    /** Obtiene la única fila de configuración, creándola si no existe. */
    public static function actual(): self
    {
        return static::query()->firstOrCreate([], [
            'driver'  => 'ninguno',
            'entorno' => 'beta',
        ]);
    }

    /** ¿La facturación electrónica está activa y con un driver que emite? */
    public function estaOperativa(): bool
    {
        return $this->habilitado && $this->driver !== 'ninguno';
    }

    /** ¿Existe físicamente el archivo del certificado en la ruta indicada? */
    public function certificadoExiste(): bool
    {
        if (blank($this->certificado_path)) {
            return false;
        }

        // Se admite ruta absoluta del sistema o ruta relativa al disco 'local'.
        if (is_file($this->certificado_path)) {
            return true;
        }

        return Storage::disk('local')->exists($this->certificado_path);
    }

    /** Ruta absoluta del certificado (para leerlo desde el sistema de archivos). */
    public function certificadoRutaAbsoluta(): ?string
    {
        if (blank($this->certificado_path)) {
            return null;
        }

        if (is_file($this->certificado_path)) {
            return $this->certificado_path;
        }

        if (Storage::disk('local')->exists($this->certificado_path)) {
            return Storage::disk('local')->path($this->certificado_path);
        }

        return null;
    }

    /** Etiqueta legible del entorno. */
    public function entornoLabel(): string
    {
        return $this->entorno === 'produccion'
            ? 'Producción'
            : 'Beta (homologación / pruebas)';
    }
}
