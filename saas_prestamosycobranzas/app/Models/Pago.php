<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    protected $table = 'pagos';

    protected $fillable = [
        'codigo', 'prestamo_id', 'cuota_id', 'monto', 'fecha_pago',
        'metodo', 'referencia', 'user_id',
    ];

    protected $casts = [
        'fecha_pago' => 'date',
    ];

    public function prestamo()
    {
        return $this->belongsTo(Prestamo::class);
    }
}
