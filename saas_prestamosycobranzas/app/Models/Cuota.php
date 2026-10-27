<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class Cuota extends Model
{
    protected $table = 'cuotas';

    protected $fillable = [
        'prestamo_id', 'numero', 'fecha_vencimiento', 'monto', 'capital',
        'interes', 'mora', 'monto_pagado', 'fecha_pago', 'estado',
    ];

    protected $casts = [
        'fecha_vencimiento' => 'date',
        'fecha_pago' => 'date',
    ];

    public function prestamo()
    {
        return $this->belongsTo(Prestamo::class);
    }

    /** Saldo pendiente de la cuota */
    public function getPendienteAttribute(): float
    {
        return round((float) $this->monto + (float) $this->mora - (float) $this->monto_pagado, 2);
    }

    /** Dias de atraso (0 si no esta vencida) */
    public function getDiasAtrasoAttribute(): int
    {
        if ($this->estado === 'pagado') {
            return 0;
        }
        $venc = Carbon::parse($this->fecha_vencimiento)->startOfDay();

        // Valor absoluto explícito (portable en Carbon 2 y 3).
        return $venc->isPast() ? (int) $venc->diffInDays(now()->startOfDay(), true) : 0;
    }

    /**
     * Marca como 'vencido' las cuotas pendientes/parciales cuya fecha ya paso.
     * Se ejecuta de forma perezosa al abrir las bandejas.
     */
    public static function actualizarVencidas(): void
    {
        DB::table('cuotas')
            ->whereIn('estado', ['pendiente', 'parcial'])
            ->whereDate('fecha_vencimiento', '<', now()->toDateString())
            ->update(['estado' => 'vencido', 'updated_at' => now()]);

        // Prestamos con cuotas vencidas pasan a estado 'mora'
        DB::table('prestamos')
            ->where('estado', 'activo')
            ->whereExists(function ($q) {
                $q->select(DB::raw(1))
                    ->from('cuotas')
                    ->whereColumn('cuotas.prestamo_id', 'prestamos.id')
                    ->where('cuotas.estado', 'vencido');
            })
            ->update(['estado' => 'mora', 'updated_at' => now()]);
    }
}
