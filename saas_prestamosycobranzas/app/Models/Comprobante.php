<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Comprobante electrónico (boleta / factura) emitido ante SUNAT.
 */
class Comprobante extends Model
{
    protected $table = 'comprobantes';

    protected $fillable = [
        'tipo', 'serie', 'correlativo',
        'prestamo_id', 'cliente_id',
        'cliente_tipo_doc', 'cliente_num_doc', 'cliente_nombre',
        'moneda', 'afectacion', 'gravado', 'exonerado', 'inafecto', 'igv', 'total', 'concepto',
        'estado', 'sunat_codigo', 'mensaje', 'hash', 'xml_path', 'cdr_path', 'user_id',
    ];

    protected $casts = [
        'gravado'   => 'decimal:2',
        'exonerado' => 'decimal:2',
        'inafecto'  => 'decimal:2',
        'igv'       => 'decimal:2',
        'total'     => 'decimal:2',
    ];

    public function prestamo()
    {
        return $this->belongsTo(Prestamo::class);
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** Número completo del comprobante, ej. B001-00000123. */
    public function getNumeroAttribute(): string
    {
        return $this->serie.'-'.str_pad((string) $this->correlativo, 8, '0', STR_PAD_LEFT);
    }

    /** Etiqueta legible del tipo de comprobante. */
    public function tipoLabel(): string
    {
        return $this->tipo === '01' ? 'Factura' : 'Boleta';
    }

    /** Clase de badge según el estado. */
    public function estadoBadge(): string
    {
        return [
            'aceptado'  => 'b-green',
            'pendiente' => 'b-yellow',
            'rechazado' => 'b-red',
            'error'     => 'b-red',
            'anulado'   => 'b-gray',
        ][$this->estado] ?? 'b-gray';
    }
}
