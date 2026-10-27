<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Cuota;
use App\Models\Prestamo;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PrestamoController extends Controller
{
    /** Frecuencias soportadas => label */
    public const FRECUENCIAS = [
        'diario' => 'Diario',
        'semanal' => 'Semanal',
        'quincenal' => 'Quincenal',
        'mensual' => 'Mensual',
    ];

    public function index(Request $request)
    {
        $estado = $request->query('estado');
        $buscar = $request->query('q');

        $prestamos = Prestamo::query()
            ->with('cliente')
            ->when($estado, fn ($q) => $q->where('estado', $estado))
            ->when($buscar, function ($q) use ($buscar) {
                $q->where(function ($sub) use ($buscar) {
                    $sub->where('codigo', 'like', "%{$buscar}%")
                        ->orWhereHas('cliente', function ($c) use ($buscar) {
                            $c->where('nombres', 'like', "%{$buscar}%")
                                ->orWhere('apellidos', 'like', "%{$buscar}%")
                                ->orWhere('documento', 'like', "%{$buscar}%");
                        });
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $resumen = [
            'total' => Prestamo::count(),
            'activos' => Prestamo::where('estado', 'activo')->count(),
            'mora' => Prestamo::where('estado', 'mora')->count(),
            'capital' => (float) Prestamo::whereIn('estado', ['activo', 'mora'])->sum('saldo'),
        ];

        return view('prestamos.index', compact('prestamos', 'estado', 'buscar', 'resumen'));
    }

    public function create()
    {
        return view('prestamos.form', [
            'prestamo' => new Prestamo(['frecuencia' => 'mensual', 'fecha_inicio' => now()->toDateString()]),
            'clientes' => Cliente::orderBy('nombres')->get(),
            'frecuencias' => self::FRECUENCIAS,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validar($request);

        $calc = $this->calcular(
            (float) $data['monto'],
            (float) $data['tasa_interes'],
            (int) $data['numero_cuotas']
        );

        $prestamo = DB::transaction(function () use ($data, $calc) {
            $prestamo = Prestamo::create([
                'codigo' => $this->generarCodigo(),
                'cliente_id' => $data['cliente_id'],
                'monto' => $data['monto'],
                'tasa_interes' => $data['tasa_interes'],
                'numero_cuotas' => $data['numero_cuotas'],
                'frecuencia' => $data['frecuencia'],
                'monto_cuota' => $calc['monto_cuota'],
                'total_pagar' => $calc['total_pagar'],
                'interes_total' => $calc['interes_total'],
                'saldo' => $calc['total_pagar'],
                'fecha_inicio' => $data['fecha_inicio'],
                'estado' => 'activo',
                'observaciones' => $data['observaciones'] ?? null,
                'user_id' => auth()->id(),
            ]);

            $this->generarCronograma($prestamo, $calc);

            return $prestamo;
        });

        return redirect()->route('prestamos.show', $prestamo)
            ->with('ok', "Prestamo {$prestamo->codigo} registrado. Se generaron {$prestamo->numero_cuotas} cuotas.");
    }

    public function show(Prestamo $prestamo)
    {
        $prestamo->load(['cliente', 'cuotas' => fn ($q) => $q->orderBy('numero')]);

        $pagado = (float) $prestamo->cuotas->sum('monto_pagado');
        $progreso = $prestamo->total_pagar > 0
            ? round($pagado / $prestamo->total_pagar * 100)
            : 0;

        return view('prestamos.show', compact('prestamo', 'pagado', 'progreso'));
    }

    public function edit(Prestamo $prestamo)
    {
        if ($prestamo->pagos()->exists()) {
            return redirect()->route('prestamos.show', $prestamo)
                ->with('ok', 'No se puede editar un prestamo que ya tiene pagos registrados.');
        }

        return view('prestamos.form', [
            'prestamo' => $prestamo,
            'clientes' => Cliente::orderBy('nombres')->get(),
            'frecuencias' => self::FRECUENCIAS,
        ]);
    }

    public function update(Request $request, Prestamo $prestamo)
    {
        if ($prestamo->pagos()->exists()) {
            return redirect()->route('prestamos.show', $prestamo)
                ->with('ok', 'No se puede editar un prestamo con pagos registrados.');
        }

        $data = $this->validar($request);
        $calc = $this->calcular((float) $data['monto'], (float) $data['tasa_interes'], (int) $data['numero_cuotas']);

        DB::transaction(function () use ($prestamo, $data, $calc) {
            $prestamo->update([
                'cliente_id' => $data['cliente_id'],
                'monto' => $data['monto'],
                'tasa_interes' => $data['tasa_interes'],
                'numero_cuotas' => $data['numero_cuotas'],
                'frecuencia' => $data['frecuencia'],
                'monto_cuota' => $calc['monto_cuota'],
                'total_pagar' => $calc['total_pagar'],
                'interes_total' => $calc['interes_total'],
                'saldo' => $calc['total_pagar'],
                'fecha_inicio' => $data['fecha_inicio'],
                'observaciones' => $data['observaciones'] ?? null,
            ]);

            $prestamo->cuotas()->delete();
            $this->generarCronograma($prestamo, $calc);
        });

        return redirect()->route('prestamos.show', $prestamo)
            ->with('ok', 'Prestamo actualizado y cronograma regenerado.');
    }

    public function destroy(Prestamo $prestamo)
    {
        $prestamo->delete();

        return redirect()->route('prestamos.index')->with('ok', 'Prestamo eliminado.');
    }

    /* =================== LOGICA DE NEGOCIO =================== */

    private function calcular(float $monto, float $tasa, int $cuotas): array
    {
        $interesTotal = round($monto * $tasa / 100, 2);
        $totalPagar = round($monto + $interesTotal, 2);
        $montoCuota = round($totalPagar / max($cuotas, 1), 2);

        return [
            'interes_total' => $interesTotal,
            'total_pagar' => $totalPagar,
            'monto_cuota' => $montoCuota,
        ];
    }

    private function generarCronograma(Prestamo $prestamo, array $calc): void
    {
        $n = $prestamo->numero_cuotas;
        $capitalPorCuota = round($prestamo->monto / $n, 2);
        $interesPorCuota = round($calc['interes_total'] / $n, 2);
        $fecha = Carbon::parse($prestamo->fecha_inicio);

        $acumCapital = 0;
        $acumInteres = 0;
        $acumMonto = 0;

        for ($i = 1; $i <= $n; $i++) {
            $fecha = $this->siguienteFecha($fecha, $prestamo->frecuencia);

            if ($i < $n) {
                $capital = $capitalPorCuota;
                $interes = $interesPorCuota;
                $monto = $calc['monto_cuota'];
            } else {
                $capital = round($prestamo->monto - $acumCapital, 2);
                $interes = round($calc['interes_total'] - $acumInteres, 2);
                $monto = round($calc['total_pagar'] - $acumMonto, 2);
            }

            $acumCapital += $capital;
            $acumInteres += $interes;
            $acumMonto += $monto;

            Cuota::create([
                'prestamo_id' => $prestamo->id,
                'numero' => $i,
                'fecha_vencimiento' => $fecha->copy(),
                'monto' => $monto,
                'capital' => $capital,
                'interes' => $interes,
                'mora' => 0,
                'monto_pagado' => 0,
                'estado' => 'pendiente',
            ]);
        }
    }

    private function siguienteFecha(Carbon $fecha, string $frecuencia): Carbon
    {
        return match ($frecuencia) {
            'diario' => $fecha->copy()->addDay(),
            'semanal' => $fecha->copy()->addWeek(),
            'quincenal' => $fecha->copy()->addDays(15),
            default => $fecha->copy()->addMonth(),
        };
    }

    private function validar(Request $request): array
    {
        return $request->validate([
            'cliente_id' => ['required', 'exists:clientes,id'],
            'monto' => ['required', 'numeric', 'min:1'],
            'tasa_interes' => ['required', 'numeric', 'min:0', 'max:100'],
            'numero_cuotas' => ['required', 'integer', 'min:1', 'max:360'],
            'frecuencia' => ['required', 'in:diario,semanal,quincenal,mensual'],
            'fecha_inicio' => ['required', 'date'],
            'observaciones' => ['nullable', 'string'],
        ], [], [
            'cliente_id' => 'cliente',
            'numero_cuotas' => 'numero de cuotas',
            'tasa_interes' => 'tasa de interes',
        ]);
    }

    private function generarCodigo(): string
    {
        $next = (int) Prestamo::max('id') + 1;

        return 'PRE-'.str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }
}
