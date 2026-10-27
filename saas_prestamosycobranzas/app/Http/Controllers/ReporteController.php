<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Cuota;
use App\Models\Empeno;
use App\Models\MovimientoCaja;
use App\Models\Pago;
use App\Models\Prestamo;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReporteController extends Controller
{
    public const TIPOS = [
        'prestamos' => ['titulo' => 'Cartera de Prestamos', 'icono' => 'bi-cash-stack', 'fecha' => true],
        'pagos' => ['titulo' => 'Pagos / Cobros', 'icono' => 'bi-credit-card-2-front', 'fecha' => true],
        'mora' => ['titulo' => 'Cartera en Mora', 'icono' => 'bi-exclamation-triangle', 'fecha' => false],
        'clientes' => ['titulo' => 'Clientes', 'icono' => 'bi-people', 'fecha' => false],
        'empenos' => ['titulo' => 'Empenos', 'icono' => 'bi-gem', 'fecha' => false],
        'caja' => ['titulo' => 'Movimientos de Caja', 'icono' => 'bi-cash', 'fecha' => true],
    ];

    public function index()
    {
        return view('reportes.index', ['tipos' => self::TIPOS]);
    }

    public function ver(Request $request, string $tipo)
    {
        abort_unless(isset(self::TIPOS[$tipo]), 404);

        [$desde, $hasta] = $this->rango($request);
        $data = $this->dataset($tipo, $desde, $hasta);

        return view('reportes.ver', [
            'tipo' => $tipo,
            'meta' => self::TIPOS[$tipo],
            'headers' => $data['headers'],
            'rows' => $data['rows'],
            'totales' => $data['totales'] ?? [],
            'desde' => $desde,
            'hasta' => $hasta,
        ]);
    }

    public function excel(Request $request, string $tipo): StreamedResponse
    {
        abort_unless(isset(self::TIPOS[$tipo]), 404);

        [$desde, $hasta] = $this->rango($request);
        $data = $this->dataset($tipo, $desde, $hasta);
        $filename = 'reporte_'.$tipo.'_'.now()->format('Ymd_His').'.csv';

        return response()->streamDownload(function () use ($data) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // BOM UTF-8 para Excel
            fputcsv($out, $data['headers'], ';');
            foreach ($data['rows'] as $row) {
                fputcsv($out, $row, ';');
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function rango(Request $request): array
    {
        $desde = $request->query('desde', now()->startOfMonth()->toDateString());
        $hasta = $request->query('hasta', now()->toDateString());

        return [$desde, $hasta];
    }

    private function dataset(string $tipo, string $desde, string $hasta): array
    {
        return match ($tipo) {
            'prestamos' => $this->repPrestamos($desde, $hasta),
            'pagos' => $this->repPagos($desde, $hasta),
            'mora' => $this->repMora(),
            'clientes' => $this->repClientes(),
            'empenos' => $this->repEmpenos(),
            'caja' => $this->repCaja($desde, $hasta),
        };
    }

    private function repPrestamos(string $desde, string $hasta): array
    {
        $items = Prestamo::with('cliente')
            ->whereBetween('fecha_inicio', [$desde, $hasta])
            ->orderBy('fecha_inicio')
            ->get();

        $rows = $items->map(fn ($p) => [
            $p->codigo,
            $p->cliente->nombre_completo ?? '',
            number_format($p->monto, 2, '.', ''),
            number_format($p->tasa_interes, 2, '.', ''),
            $p->numero_cuotas,
            $p->frecuencia,
            number_format($p->total_pagar, 2, '.', ''),
            number_format($p->saldo, 2, '.', ''),
            $p->fecha_inicio->format('d/m/Y'),
            ucfirst($p->estado),
        ])->toArray();

        return [
            'headers' => ['Codigo', 'Cliente', 'Monto', 'Tasa %', 'Cuotas', 'Frecuencia', 'Total a pagar', 'Saldo', 'Inicio', 'Estado'],
            'rows' => $rows,
            'totales' => ['Monto' => $items->sum('monto'), 'Total a pagar' => $items->sum('total_pagar'), 'Saldo' => $items->sum('saldo')],
        ];
    }

    private function repPagos(string $desde, string $hasta): array
    {
        $items = Pago::with('prestamo.cliente')
            ->whereBetween('fecha_pago', [$desde, $hasta])
            ->orderBy('fecha_pago')
            ->get();

        $rows = $items->map(fn ($p) => [
            $p->codigo,
            $p->fecha_pago->format('d/m/Y'),
            $p->prestamo->codigo ?? '',
            $p->prestamo->cliente->nombre_completo ?? '',
            number_format($p->monto, 2, '.', ''),
            ucfirst($p->metodo),
        ])->toArray();

        return [
            'headers' => ['Codigo', 'Fecha', 'Prestamo', 'Cliente', 'Monto', 'Metodo'],
            'rows' => $rows,
            'totales' => ['Monto' => $items->sum('monto')],
        ];
    }

    private function repMora(): array
    {
        Cuota::actualizarVencidas();
        $items = Cuota::with('prestamo.cliente')->where('estado', 'vencido')->orderBy('fecha_vencimiento')->get();

        $rows = $items->map(fn ($c) => [
            $c->prestamo->codigo ?? '',
            $c->prestamo->cliente->nombre_completo ?? '',
            $c->prestamo->cliente->telefono ?? '',
            $c->numero,
            $c->fecha_vencimiento->format('d/m/Y'),
            $c->dias_atraso,
            number_format($c->monto - $c->monto_pagado, 2, '.', ''),
        ])->toArray();

        return [
            'headers' => ['Prestamo', 'Cliente', 'Telefono', 'Cuota', 'Vencimiento', 'Dias atraso', 'Deuda'],
            'rows' => $rows,
            'totales' => ['Deuda' => $items->sum(fn ($c) => $c->monto - $c->monto_pagado)],
        ];
    }

    private function repClientes(): array
    {
        $items = Cliente::orderBy('nombres')->get();

        $rows = $items->map(fn ($c) => [
            $c->codigo,
            $c->nombre_completo,
            $c->tipo_documento.' '.$c->documento,
            $c->telefono,
            $c->ocupacion,
            ucfirst($c->estado),
        ])->toArray();

        return [
            'headers' => ['Codigo', 'Nombre', 'Documento', 'Telefono', 'Ocupacion', 'Estado'],
            'rows' => $rows,
        ];
    }

    private function repEmpenos(): array
    {
        Empeno::actualizarVencidos();
        $items = Empeno::with('cliente')->orderBy('fecha_vencimiento')->get();

        $rows = $items->map(fn ($e) => [
            $e->codigo,
            $e->articulo,
            $e->cliente->nombre_completo ?? '',
            number_format($e->valor_tasacion, 2, '.', ''),
            number_format($e->monto_prestado, 2, '.', ''),
            number_format($e->total_recuperar, 2, '.', ''),
            $e->fecha_vencimiento->format('d/m/Y'),
            ucfirst($e->estado),
        ])->toArray();

        return [
            'headers' => ['Codigo', 'Articulo', 'Cliente', 'Tasacion', 'Prestado', 'A recuperar', 'Vence', 'Estado'],
            'rows' => $rows,
            'totales' => ['Prestado' => $items->sum('monto_prestado'), 'A recuperar' => $items->sum(fn ($e) => $e->total_recuperar)],
        ];
    }

    private function repCaja(string $desde, string $hasta): array
    {
        $items = MovimientoCaja::with('user')->whereBetween('fecha', [$desde, $hasta])->orderBy('fecha')->get();

        $rows = $items->map(fn ($m) => [
            $m->codigo,
            $m->fecha->format('d/m/Y'),
            ucfirst($m->tipo),
            ucfirst(str_replace('_', ' ', $m->categoria)),
            $m->concepto,
            number_format($m->monto, 2, '.', ''),
            ucfirst($m->metodo),
        ])->toArray();

        return [
            'headers' => ['Codigo', 'Fecha', 'Tipo', 'Categoria', 'Concepto', 'Monto', 'Metodo'],
            'rows' => $rows,
            'totales' => [
                'Ingresos' => $items->where('tipo', 'ingreso')->sum('monto'),
                'Egresos' => $items->where('tipo', 'egreso')->sum('monto'),
            ],
        ];
    }
}
