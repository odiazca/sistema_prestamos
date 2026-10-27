<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Configuración del proveedor de IA (una sola fila: el sistema es de un solo negocio). */
class IaConfig extends Model
{
    protected $table = 'ia_configs';
    protected $guarded = [];
    protected $casts = ['resumen' => 'boolean'];
    protected $hidden = ['clave'];
}
