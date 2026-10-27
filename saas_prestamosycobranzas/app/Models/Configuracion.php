<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Configuracion extends Model
{
    protected $table = 'configuraciones';

    protected $fillable = ['clave', 'valor', 'grupo'];

    public $timestamps = true;

    /** Obtiene un valor de configuracion (con cache) */
    public static function get(string $clave, $default = null)
    {
        $all = Cache::rememberForever('config_all', function () {
            return static::pluck('valor', 'clave')->toArray();
        });

        return $all[$clave] ?? $default;
    }

    /** Guarda un valor de configuracion */
    public static function set(string $clave, $valor, string $grupo = 'general'): void
    {
        static::updateOrCreate(['clave' => $clave], ['valor' => $valor, 'grupo' => $grupo]);
        Cache::forget('config_all');
    }

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('config_all'));
        static::deleted(fn () => Cache::forget('config_all'));
    }
}
