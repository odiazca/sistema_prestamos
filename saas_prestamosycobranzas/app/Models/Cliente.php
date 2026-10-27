<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $table = 'clientes';

    protected $fillable = [
        'codigo', 'nombres', 'apellidos', 'documento', 'tipo_documento',
        'telefono', 'email', 'direccion', 'ocupacion', 'ingreso_mensual',
        'estado', 'observaciones',
    ];

    public function prestamos()
    {
        return $this->hasMany(Prestamo::class);
    }

    public function empenos()
    {
        return $this->hasMany(Empeno::class);
    }

    public function getNombreCompletoAttribute(): string
    {
        return trim("{$this->nombres} {$this->apellidos}");
    }
}
