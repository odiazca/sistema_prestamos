<?php

namespace App\Http\Controllers;

use App\Models\FacturacionConfig;
use App\Services\Facturacion\FacturacionManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Módulo de configuración de Facturación Electrónica (Perú - SUNAT).
 */
class FacturacionController extends Controller
{
    private function soloAdmin(): void
    {
        abort_unless(
            auth()->user() && auth()->user()->esAdmin(),
            403,
            'Acceso restringido a administradores.'
        );
    }

    /** Muestra el módulo de configuración. */
    public function index()
    {
        $this->soloAdmin();

        $config = FacturacionConfig::actual();

        $drivers  = config('facturacion.driver_labels');
        $entornos = config('facturacion.entornos');

        return view('facturacion.configuracion', compact('config', 'drivers', 'entornos'));
    }

    /** Guarda la configuración (incluye la subida opcional del certificado). */
    public function update(Request $request)
    {
        $this->soloAdmin();

        $data = $request->validate([
            'habilitado'        => ['nullable', 'boolean'],
            'emitir_automatico' => ['nullable', 'boolean'],
            'driver'            => ['required', 'in:'.implode(',', array_keys(config('facturacion.drivers')))],
            'entorno'           => ['required', 'in:'.implode(',', array_keys(config('facturacion.entornos')))],

            'ruc'               => ['nullable', 'string', 'size:11'],
            'razon_social'      => ['nullable', 'string', 'max:150'],
            'nombre_comercial'  => ['nullable', 'string', 'max:150'],
            'direccion'         => ['nullable', 'string', 'max:200'],
            'ubigeo'            => ['nullable', 'string', 'max:6'],
            'departamento'      => ['nullable', 'string', 'max:60'],
            'provincia'         => ['nullable', 'string', 'max:60'],
            'distrito'          => ['nullable', 'string', 'max:60'],
            'serie_factura'     => ['nullable', 'string', 'max:4'],
            'serie_boleta'      => ['nullable', 'string', 'max:4'],
            'afectacion_igv'    => ['nullable', 'in:10,20,30'],

            'sol_usuario'       => ['nullable', 'string', 'max:60'],
            'sol_clave'         => ['nullable', 'string', 'max:100'],
            'certificado'       => ['nullable', 'file', 'max:2048'],
            'certificado_clave' => ['nullable', 'string', 'max:100'],
        ], [
            'ruc.size'          => 'El RUC debe tener 11 dígitos.',
            'certificado.max'   => 'El certificado no debe superar los 2 MB.',
        ]);

        // El certificado debe ser un archivo .pem / .txt
        if ($request->hasFile('certificado')
            && ! in_array(strtolower($request->file('certificado')->getClientOriginalExtension()), ['pem', 'txt'], true)) {
            return back()->withInput()->with('error', 'El certificado debe tener extensión .pem');
        }

        $config = FacturacionConfig::actual();

        // Checkboxes
        $config->habilitado        = $request->boolean('habilitado');
        $config->emitir_automatico = $request->boolean('emitir_automatico');

        // Campos de texto
        foreach ([
            'driver', 'entorno', 'ruc', 'razon_social', 'nombre_comercial', 'direccion',
            'ubigeo', 'departamento', 'provincia', 'distrito', 'serie_factura', 'serie_boleta',
            'afectacion_igv', 'sol_usuario',
        ] as $campo) {
            if (array_key_exists($campo, $data)) {
                $config->{$campo} = $data[$campo];
            }
        }

        // Las claves solo se actualizan si el usuario escribió algo (no borran las guardadas).
        if (! empty($data['sol_clave'])) {
            $config->sol_clave = $data['sol_clave'];
        }
        if (! empty($data['certificado_clave'])) {
            $config->certificado_clave = $data['certificado_clave'];
        }

        // Subida del certificado .pem
        if ($request->hasFile('certificado')) {
            $dir  = config('facturacion.certificado_dir', 'facturacion/pe');
            $path = $request->file('certificado')->storeAs($dir, 'certificate.pem', 'local');
            $config->certificado_path = $path;
        }

        $config->save();

        return redirect()
            ->route('facturacion.config')
            ->with('ok', 'Configuración de facturación electrónica guardada correctamente.');
    }

    /** Prueba la conexión con SUNAT usando el driver configurado. */
    public function probarConexion(FacturacionManager $manager)
    {
        $this->soloAdmin();

        $r = $manager->probarConexion();

        return redirect()
            ->route('facturacion.config')
            ->with($r['ok'] ? 'ok' : 'error', $r['mensaje']);
    }

    /** Emite un comprobante de prueba contra el entorno Beta de SUNAT. */
    public function emitirPrueba(FacturacionManager $manager)
    {
        $this->soloAdmin();

        $config = $manager->config();

        if ($config->driver !== 'sunat') {
            return redirect()->route('facturacion.config')
                ->with('error', 'Selecciona el driver "SUNAT" para emitir un comprobante de prueba.');
        }

        $r = $manager->driver()->emitir($manager->comprobanteDemo());

        $ok = in_array($r['estado'], ['aceptado', 'pendiente'], true);

        return redirect()->route('facturacion.config')
            ->with($ok ? 'ok' : 'error', 'Emisión de prueba ['.$r['estado'].']: '.$r['mensaje']);
    }
}
