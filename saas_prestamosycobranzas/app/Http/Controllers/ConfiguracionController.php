<?php

namespace App\Http\Controllers;

use App\Models\Configuracion;
use Illuminate\Http\Request;

class ConfiguracionController extends Controller
{
    /** Valores por defecto del sistema */
    public const DEFAULTS = [
        'empresa_nombre' => 'Sistema de Prestamos Pro',
        'empresa_ruc' => '',
        'empresa_direccion' => '',
        'empresa_telefono' => '',
        'empresa_email' => '',
        'moneda' => 'S/',
        'tasa_default' => '15',
        'mora_diaria' => '1',
        'dias_gracia' => '0',
    ];

    private function soloAdmin(): void
    {
        abort_unless(auth()->user() && auth()->user()->esAdmin(), 403, 'Acceso restringido a administradores.');
    }

    public function index()
    {
        $this->soloAdmin();

        $config = [];
        foreach (self::DEFAULTS as $clave => $default) {
            $config[$clave] = Configuracion::get($clave, $default);
        }

        return view('configuracion.index', compact('config'));
    }

    public function update(Request $request)
    {
        $this->soloAdmin();

        $data = $request->validate([
            'empresa_nombre' => ['required', 'string', 'max:150'],
            'empresa_ruc' => ['nullable', 'string', 'max:30'],
            'empresa_direccion' => ['nullable', 'string', 'max:200'],
            'empresa_telefono' => ['nullable', 'string', 'max:30'],
            'empresa_email' => ['nullable', 'email', 'max:120'],
            'moneda' => ['required', 'string', 'max:5'],
            'tasa_default' => ['required', 'numeric', 'min:0', 'max:100'],
            'mora_diaria' => ['required', 'numeric', 'min:0', 'max:100'],
            'dias_gracia' => ['required', 'integer', 'min:0', 'max:60'],
        ]);

        foreach ($data as $clave => $valor) {
            Configuracion::set($clave, $valor, 'general');
        }

        return back()->with('ok', 'Configuración guardada correctamente.');
    }
}
