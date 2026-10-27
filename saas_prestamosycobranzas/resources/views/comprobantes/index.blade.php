@extends('layouts.app')

@section('title', 'Comprobantes')
@section('topbar', 'Comprobantes Electrónicos')

@section('content')
    <h1 class="page-title">Comprobantes Electrónicos</h1>
    <p class="page-subtitle">Boletas y facturas emitidas ante SUNAT a partir de los pagos.</p>

    @if (session('error'))
        <div class="alert alert-error"><i class="bi bi-x-circle"></i> {{ session('error') }}</div>
    @endif

    <div class="stats-grid" style="grid-template-columns:repeat(4,1fr);max-width:900px">
        <div class="stat-card bg-blue">
            <i class="bi bi-receipt stat-icon"></i>
            <div class="stat-label">EMITIDOS</div>
            <div class="stat-value">{{ $resumen['total'] }}</div>
        </div>
        <div class="stat-card bg-teal">
            <i class="bi bi-check2-circle stat-icon"></i>
            <div class="stat-label">ACEPTADOS</div>
            <div class="stat-value">{{ $resumen['aceptados'] }}</div>
        </div>
        <div class="stat-card bg-orange">
            <i class="bi bi-hourglass-split stat-icon"></i>
            <div class="stat-label">PENDIENTES</div>
            <div class="stat-value">{{ $resumen['pendientes'] }}</div>
        </div>
        <div class="stat-card bg-purple">
            <i class="bi bi-cash-coin stat-icon"></i>
            <div class="stat-label">FACTURADO (ACEPTADO)</div>
            <div class="stat-value">S/ {{ number_format($resumen['monto'], 2) }}</div>
        </div>
    </div>

    <div class="card">
        <div class="card__header" style="gap:12px;flex-wrap:wrap">
            <span>Comprobantes ({{ $comprobantes->total() }})</span>
            <form method="GET" style="display:flex;gap:10px;align-items:center;flex-wrap:wrap">
                <select name="estado" class="form-control" style="width:auto">
                    <option value="">Todos los estados</option>
                    @foreach (['aceptado' => 'Aceptado', 'pendiente' => 'Pendiente', 'rechazado' => 'Rechazado', 'error' => 'Error'] as $val => $lbl)
                        <option value="{{ $val }}" {{ $estado === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                    @endforeach
                </select>
                <div class="topbar__search" style="margin:0">
                    <i class="bi bi-search" style="color:#94a3b8"></i>
                    <input type="text" name="q" value="{{ $buscar }}" placeholder="Serie, cliente o documento...">
                </div>
                <button class="btn btn-light btn-sm"><i class="bi bi-funnel"></i> Filtrar</button>
            </form>
        </div>
        <div class="table-wrap">
            <table class="data">
                <thead>
                    <tr><th>Comprobante</th><th>Fecha</th><th>Tipo</th><th>Cliente</th><th>Préstamo</th><th>Total</th><th>Estado</th><th>Acciones</th></tr>
                </thead>
                <tbody>
                    @forelse ($comprobantes as $c)
                        <tr>
                            <td><strong>{{ $c->numero }}</strong></td>
                            <td>{{ $c->created_at->format('d/m/Y') }}</td>
                            <td><span class="badge-pill {{ $c->tipo === '01' ? 'b-indigo' : 'b-blue' }}">{{ $c->tipoLabel() }}</span></td>
                            <td>
                                {{ $c->cliente_nombre ?? '—' }}
                                @if ($c->cliente_num_doc)
                                    <div style="font-size:11px;color:var(--text-muted)">{{ $c->cliente_num_doc }}</div>
                                @endif
                            </td>
                            <td>
                                @if ($c->prestamo)
                                    <a href="{{ route('prestamos.show', $c->prestamo) }}" style="color:var(--primary);font-weight:600">{{ $c->prestamo->codigo }}</a>
                                @else — @endif
                            </td>
                            <td><strong>S/ {{ number_format($c->total, 2) }}</strong></td>
                            <td>
                                <span class="badge-pill {{ $c->estadoBadge() }}">{{ ucfirst($c->estado) }}</span>
                                @if ($c->estado === 'rechazado' && $c->mensaje)
                                    <div style="font-size:11px;color:#b91c1c;max-width:220px">{{ \Illuminate\Support\Str::limit($c->mensaje, 80) }}</div>
                                @endif
                            </td>
                            <td style="white-space:nowrap">
                                @if ($c->xml_path)
                                    <a href="{{ route('comprobantes.xml', $c) }}" class="btn btn-light btn-sm" title="Descargar XML"><i class="bi bi-filetype-xml"></i></a>
                                @endif
                                @if ($c->estado !== 'aceptado')
                                    <form action="{{ route('comprobantes.reemitir', $c) }}" method="POST" style="display:inline" onsubmit="return confirm('¿Reintentar el envío a SUNAT?')">
                                        @csrf
                                        <button class="btn btn-primary btn-sm" title="Reintentar envío"><i class="bi bi-arrow-repeat"></i></button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" style="text-align:center;color:var(--text-muted);padding:36px">Aún no se han emitido comprobantes.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="padding:0 20px 18px">{{ $comprobantes->links() }}</div>
    </div>
@endsection
