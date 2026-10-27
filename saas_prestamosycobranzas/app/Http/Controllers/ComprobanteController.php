<?php

namespace App\Http\Controllers;

use App\Models\Comprobante;
use App\Services\Facturacion\ComprobanteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Historial de comprobantes electrónicos emitidos.
 */
class ComprobanteController extends Controller
{
    /** Listado con filtros. */
    public function index(Request $request)
    {
        $estado = $request->query('estado');
        $buscar = $request->query('q');

        $comprobantes = Comprobante::query()
            ->with(['prestamo', 'cliente'])
            ->when($estado, fn ($q) => $q->where('estado', $estado))
            ->when($buscar, function ($q) use ($buscar) {
                $q->where('serie', 'like', "%{$buscar}%")
                    ->orWhere('cliente_nombre', 'like', "%{$buscar}%")
                    ->orWhere('cliente_num_doc', 'like', "%{$buscar}%");
            })
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $resumen = [
            'total'     => Comprobante::count(),
            'aceptados' => Comprobante::where('estado', 'aceptado')->count(),
            'pendientes' => Comprobante::whereIn('estado', ['pendiente', 'rechazado', 'error'])->count(),
            'monto'     => (float) Comprobante::where('estado', 'aceptado')->sum('total'),
        ];

        return view('comprobantes.index', compact('comprobantes', 'estado', 'buscar', 'resumen'));
    }

    /** Reintenta el envío de un comprobante pendiente / rechazado / con error. */
    public function reemitir(Comprobante $comprobante, ComprobanteService $service)
    {
        if ($comprobante->estado === 'aceptado') {
            return back()->with('error', 'Este comprobante ya fue aceptado por SUNAT.');
        }

        $service->emitir($comprobante);

        return back()->with(
            $comprobante->estado === 'aceptado' ? 'ok' : 'error',
            'Reintento '.$comprobante->numero.': '.$comprobante->estado.' — '.$comprobante->mensaje
        );
    }

    /** Descarga el XML firmado del comprobante. */
    public function xml(Comprobante $comprobante)
    {
        abort_unless($comprobante->xml_path && Storage::disk('local')->exists($comprobante->xml_path), 404, 'XML no disponible.');

        return Storage::disk('local')->download($comprobante->xml_path, $comprobante->numero.'.xml');
    }
}
