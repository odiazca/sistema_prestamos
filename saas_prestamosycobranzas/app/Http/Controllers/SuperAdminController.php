<?php

namespace App\Http\Controllers;

use App\Models\Auditoria;
use App\Models\Cliente;
use App\Models\Empeno;
use App\Models\Pago;
use App\Models\Prestamo;
use App\Models\User;

class SuperAdminController extends Controller
{
    private function soloSuperAdmin(): void
    {
        abort_unless(auth()->user() && auth()->user()->esSuperAdmin(), 403, 'Acceso exclusivo del super administrador.');
    }

    public function index()
    {
        $this->soloSuperAdmin();

        $metricas = [
            'usuarios' => User::count(),
            'usuarios_activos' => User::where('activo', true)->count(),
            'clientes' => Cliente::count(),
            'prestamos_activos' => Prestamo::whereIn('estado', ['activo', 'mora'])->count(),
            'cartera' => (float) Prestamo::whereIn('estado', ['activo', 'mora'])->sum('saldo'),
            'recaudado_mes' => (float) Pago::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)->sum('monto'),
            'empenos_vigentes' => Empeno::where('estado', 'vigente')->count(),
            'acciones_hoy' => Auditoria::whereDate('created_at', now()->toDateString())->count(),
        ];

        // Usuarios por rol
        $porRol = User::selectRaw('rol, COUNT(*) as total')
            ->groupBy('rol')
            ->pluck('total', 'rol')
            ->all();

        // Actividad reciente de toda la plataforma
        $actividad = Auditoria::latest('id')->take(12)->get();

        // Información del sistema
        $sistema = [
            'app' => config('app.name'),
            'entorno' => app()->environment(),
            'php' => PHP_VERSION,
            'laravel' => app()->version(),
            'fecha' => now()->format('d/m/Y H:i'),
        ];

        return view('superadmin.index', compact('metricas', 'porRol', 'actividad', 'sistema'));
    }
}
