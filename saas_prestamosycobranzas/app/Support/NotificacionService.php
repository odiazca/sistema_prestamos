<?php

namespace App\Support;

use App\Models\Cuota;
use App\Models\Empeno;
use Illuminate\Support\Carbon;

/**
 * Genera las notificaciones operativas que se muestran en la campana del topbar.
 */
class NotificacionService
{
    /** @return array{items: array, total: int} */
    public static function obtener(): array
    {
        $hoy = Carbon::today();
        $items = [];

        $user = auth()->user();
        $puedeEmpenos = $user && ($user->esSuperAdmin() || in_array($user->rol, ['admin', 'gerente', 'operador'], true));

        // Cuotas vencidas (en mora)
        $vencidas = Cuota::where('estado', 'vencido')->count();
        if ($vencidas > 0) {
            $items[] = [
                'icono' => 'bi-exclamation-octagon-fill',
                'color' => '#ef4444',
                'titulo' => $vencidas.' '.($vencidas === 1 ? 'cuota vencida' : 'cuotas vencidas'),
                'detalle' => 'Requieren gestión de cobranza',
                'url' => route('mora.index'),
            ];
        }

        // Cuotas por vencer en los próximos 3 días
        $porVencer = Cuota::where('estado', 'pendiente')
            ->whereBetween('fecha_vencimiento', [$hoy, $hoy->copy()->addDays(3)])
            ->count();
        if ($porVencer > 0) {
            $items[] = [
                'icono' => 'bi-clock-history',
                'color' => '#f59e0b',
                'titulo' => $porVencer.' '.($porVencer === 1 ? 'cuota por vencer' : 'cuotas por vencer'),
                'detalle' => 'Vencen en los próximos 3 días',
                'url' => route('cobranzas.index'),
            ];
        }

        // Empeños próximos a vencer (5 días) o vencidos sin renovar
        $empenos = $puedeEmpenos ? Empeno::where('estado', 'vigente')
            ->where('fecha_vencimiento', '<=', $hoy->copy()->addDays(5))
            ->count() : 0;
        if ($empenos > 0) {
            $items[] = [
                'icono' => 'bi-gem',
                'color' => '#8b5cf6',
                'titulo' => $empenos.' '.($empenos === 1 ? 'empeño por vencer' : 'empeños por vencer'),
                'detalle' => 'Próximos a su fecha límite',
                'url' => route('empenos.index'),
            ];
        }

        return ['items' => $items, 'total' => count($items)];
    }
}
