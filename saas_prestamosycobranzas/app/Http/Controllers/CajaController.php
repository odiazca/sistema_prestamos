<?php

namespace App\Http\Controllers;

use App\Models\MovimientoCaja;
use App\Models\Pago;
use Illuminate\Http\Request;

class CajaController extends Controller
{
    public const CATEGORIAS = [
        'ingreso' => [
            'aporte_capital' => 'Aporte de capital',
            'cobro_externo' => 'Cobro externo',
            'otro_ingreso' => 'Otro ingreso',
        ],
        'egreso' => [
            'desembolso' => 'Desembolso de prestamo',
            'gasto_operativo' => 'Gasto operativo',
            'pago_proveedor' => 'Pago a proveedor',
            'retiro' => 'Retiro / utilidad',
            'otro_egreso' => 'Otro egreso',
        ],
    ];

    public const METODOS = ['efectivo' => 'Efectivo', 'transferencia' => 'Transferencia', 'yape' => 'Yape', 'plin' => 'Plin'];

    public function index(Request $request)
    {
        $fecha = $request->query('fecha', now()->toDateString());

        $movimientos = MovimientoCaja::with('user')
            ->whereDate('fecha', $fecha)
            ->latest('id')
            ->get();

        $cobros = (float) Pago::whereDate('fecha_pago', $fecha)->sum('monto');
        $ingresos = (float) $movimientos->where('tipo', 'ingreso')->sum('monto');
        $egresos = (float) $movimientos->where('tipo', 'egreso')->sum('monto');
        $saldo = $cobros + $ingresos - $egresos;

        return view('caja.index', [
            'movimientos' => $movimientos,
            'fecha' => $fecha,
            'cobros' => $cobros,
            'ingresos' => $ingresos,
            'egresos' => $egresos,
            'saldo' => $saldo,
            'categorias' => self::CATEGORIAS,
            'metodos' => self::METODOS,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'fecha' => ['required', 'date'],
            'tipo' => ['required', 'in:ingreso,egreso'],
            'categoria' => ['required', 'string', 'max:40'],
            'concepto' => ['required', 'string', 'max:150'],
            'monto' => ['required', 'numeric', 'min:0.01'],
            'metodo' => ['required', 'in:'.implode(',', array_keys(self::METODOS))],
        ]);

        $data['codigo'] = $this->generarCodigo();
        $data['user_id'] = auth()->id();
        MovimientoCaja::create($data);

        return redirect()->route('caja.index', ['fecha' => $data['fecha']])
            ->with('ok', 'Movimiento registrado.');
    }

    public function destroy(MovimientoCaja $movimiento)
    {
        $fecha = $movimiento->fecha->toDateString();
        $movimiento->delete();

        return redirect()->route('caja.index', ['fecha' => $fecha])->with('ok', 'Movimiento eliminado.');
    }

    private function generarCodigo(): string
    {
        $next = (int) MovimientoCaja::max('id') + 1;

        return 'MOV-'.str_pad((string) $next, 6, '0', STR_PAD_LEFT);
    }
}
