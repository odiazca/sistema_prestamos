<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovimientoCaja extends Model
{
    protected $table = 'movimientos_caja';

    protected $fillable = [
        'codigo', 'fecha', 'tipo', 'categoria', 'concepto', 'monto', 'metodo', 'user_id',
    ];

    protected $casts = ['fecha' => 'date'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
