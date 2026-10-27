<?php

namespace App\Http\Controllers;

use App\Models\Cuota;
use Illuminate\Http\Request;

class CobranzaController extends Controller
{
    /** Bandeja de cobranza: cuotas por vencer y vencidas */
    public function index(Request $request)
    {
        Cuota::actualizarVencidas();

        $filtro = $request->query('filtro', 'todas'); // todas / hoy / vencidas / proximas
        $buscar = $request->query('q');

        $query = Cuota::query()
            ->with('prestamo.cliente')
            ->whereIn('estado', ['pendiente', 'parcial', 'vencido'])
            ->when($buscar, fn ($q) => $q->whereHas('prestamo.cliente', function ($c) use ($buscar) {
                $c->where('nombres', 'like', "%{$buscar}%")
                    ->orWhere('apellidos', 'like', "%{$buscar}%")
                    ->orWhere('documento', 'like', "%{$buscar}%");
            }));

        match ($filtro) {
            'hoy' => $query->whereDate('fecha_vencimiento', now()->toDateString()),
            'vencidas' => $query->where('estado', 'vencido'),
            'proximas' => $query->whereDate('fecha_vencimiento', '>=', now()->toDateString())
                                ->whereDate('fecha_vencimiento', '<=', now()->addDays(7)->toDateString()),
            default => null,
        };

        $cuotas = $query->orderBy('fecha_vencimiento')->paginate(15)->withQueryString();

        $resumen = [
            'vencidas' => Cuota::where('estado', 'vencido')->count(),
            'hoy' => Cuota::whereIn('estado', ['pendiente', 'parcial'])->whereDate('fecha_vencimiento', now()->toDateString())->count(),
            'porCobrar' => (float) Cuota::whereIn('estado', ['pendiente', 'parcial', 'vencido'])
                ->sum(\Illuminate\Support\Facades\DB::raw('monto - monto_pagado')),
            'montoVencido' => (float) Cuota::where('estado', 'vencido')
                ->sum(\Illuminate\Support\Facades\DB::raw('monto - monto_pagado')),
        ];

        return view('cobranzas.index', compact('cuotas', 'filtro', 'buscar', 'resumen'));
    }

    /** Bandeja de Mora: solo cuotas vencidas */
    public function mora(Request $request)
    {
        Cuota::actualizarVencidas();
        $buscar = $request->query('q');

        $cuotas = Cuota::query()
            ->with('prestamo.cliente')
            ->where('estado', 'vencido')
            ->when($buscar, fn ($q) => $q->whereHas('prestamo.cliente', function ($c) use ($buscar) {
                $c->where('nombres', 'like', "%{$buscar}%")
                    ->orWhere('apellidos', 'like', "%{$buscar}%")
                    ->orWhere('documento', 'like', "%{$buscar}%");
            }))
            ->orderBy('fecha_vencimiento')
            ->paginate(15)
            ->withQueryString();

        $resumen = [
            'cuotas' => Cuota::where('estado', 'vencido')->count(),
            'monto' => (float) Cuota::where('estado', 'vencido')->sum(\Illuminate\Support\Facades\DB::raw('monto - monto_pagado')),
            'prestamos' => \App\Models\Prestamo::where('estado', 'mora')->count(),
        ];

        return view('cobranzas.mora', compact('cuotas', 'buscar', 'resumen'));
    }
}
