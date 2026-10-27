<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Cuota;
use App\Models\Empeno;
use App\Models\Pago;
use App\Models\Prestamo;

class DashboardController extends Controller
{
    public function index()
    {
        $totalClientes   = Cliente::count();
        $capitalPrestado = (float) Prestamo::whereIn('estado', ['activo', 'mora'])->sum('monto');
        $gananciaInteres = (float) Prestamo::sum('interes_total');
        $totalEmpenos    = Empeno::count();
        $totalCobrado    = (float) Pago::sum('monto');
        $totalPorCobrar  = (float) Prestamo::whereIn('estado', ['activo', 'mora'])->sum('saldo');

        // Estado de la cartera (para grafico donut)
        $cuotasAlDia    = Cuota::where('estado', 'pendiente')->count();
        $cuotasPagadas  = Cuota::where('estado', 'pagado')->count();
        $cuotasVencidas = Cuota::where('estado', 'vencido')->count();

        // Balance prestado vs recuperado (barra)
        $totalPrestado   = (float) Prestamo::sum('total_pagar');
        $totalRecuperado = $totalCobrado;

        // Prestamos recientes
        $prestamosRecientes = Prestamo::with('cliente')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.index', compact(
            'totalClientes', 'capitalPrestado', 'gananciaInteres', 'totalEmpenos',
            'totalCobrado', 'totalPorCobrar', 'cuotasAlDia', 'cuotasPagadas',
            'cuotasVencidas', 'totalPrestado', 'totalRecuperado', 'prestamosRecientes'
        ));
    }
}
