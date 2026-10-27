@extends('layouts.app')

@section('title', 'Panel de Control')
@section('topbar', 'Panel de Control')

@section('content')
    <h1 class="page-title">Panel de Control</h1>
    <p class="page-subtitle">Resumen general de la operación · {{ now()->translatedFormat('d \d\e F, Y') }}</p>

    {{-- ===== TARJETAS ===== --}}
    <div class="stats-grid">
        <div class="stat-card bg-blue">
            <i class="bi bi-people-fill stat-icon"></i>
            <div class="stat-label">CLIENTES</div>
            <div class="stat-value">{{ number_format($totalClientes) }}</div>
            <div class="stat-foot">Registrados en el sistema</div>
        </div>
        <div class="stat-card bg-teal">
            <i class="bi bi-cash-stack stat-icon"></i>
            <div class="stat-label">CAPITAL PRESTADO</div>
            <div class="stat-value">S/ {{ number_format($capitalPrestado, 2) }}</div>
            <div class="stat-foot">Activo en la calle</div>
        </div>
        <div class="stat-card bg-purple">
            <i class="bi bi-graph-up-arrow stat-icon"></i>
            <div class="stat-label">GANANCIA / INTERESES</div>
            <div class="stat-value">S/ {{ number_format($gananciaInteres, 2) }}</div>
            <div class="stat-foot">Interés proyectado</div>
        </div>
        <div class="stat-card bg-orange">
            <i class="bi bi-gem stat-icon"></i>
            <div class="stat-label">EMPEÑOS</div>
            <div class="stat-value">{{ number_format($totalEmpenos) }}</div>
            <div class="stat-foot">Artículos en garantía</div>
        </div>
        <div class="stat-card bg-cyan">
            <i class="bi bi-wallet2 stat-icon"></i>
            <div class="stat-label">TOTAL COBRADO</div>
            <div class="stat-value">S/ {{ number_format($totalCobrado, 2) }}</div>
            <div class="stat-foot">Recuperado a la fecha</div>
        </div>
    </div>

    {{-- ===== GRÁFICOS ===== --}}
    <div class="grid-2">
        <div class="card">
            <div class="card__header">Estado de la Cartera</div>
            <div class="card__body">
                <div class="chart-wrap"><canvas id="carteraChart"></canvas></div>
            </div>
        </div>
        <div class="card">
            <div class="card__header">Balance: Prestado vs Recuperado</div>
            <div class="card__body">
                <div class="chart-wrap"><canvas id="balanceChart"></canvas></div>
            </div>
        </div>
    </div>

    {{-- ===== ACCESOS RÁPIDOS ===== --}}
    <h2 class="section-title">Accesos Rápidos</h2>
    <div class="quick-grid">
        <a href="{{ route('prestamos.create') }}" class="quick-card">
            <div class="q-icon bg-blue"><i class="bi bi-plus-circle"></i></div>
            <div><div class="q-title">Nuevo Préstamo</div><div class="q-desc">Registrar entrega de dinero</div></div>
        </a>
        <a href="{{ route('empenos.create') }}" class="quick-card">
            <div class="q-icon bg-purple"><i class="bi bi-gem"></i></div>
            <div><div class="q-title">Nuevo Empeño</div><div class="q-desc">Registrar artículo en garantía</div></div>
        </a>
        <a href="{{ route('clientes.create') }}" class="quick-card">
            <div class="q-icon bg-teal"><i class="bi bi-person-plus"></i></div>
            <div><div class="q-title">Nuevo Cliente</div><div class="q-desc">Registrar nueva persona</div></div>
        </a>
    </div>

    {{-- ===== PRÉSTAMOS RECIENTES ===== --}}
    <div class="card" style="margin-top:24px">
        <div class="card__header">
            Préstamos Recientes
            <a href="{{ route('prestamos.index') }}" class="btn btn-light btn-sm">Ver todos</a>
        </div>
        <div class="table-wrap">
            <table class="data">
                <thead>
                    <tr><th>Código</th><th>Cliente</th><th>Monto</th><th>Cuotas</th><th>Saldo</th><th>Estado</th></tr>
                </thead>
                <tbody>
                    @forelse ($prestamosRecientes as $p)
                        <tr>
                            <td><strong>{{ $p->codigo }}</strong></td>
                            <td>{{ $p->cliente->nombre_completo ?? '—' }}</td>
                            <td>S/ {{ number_format($p->monto, 2) }}</td>
                            <td>{{ $p->numero_cuotas }}</td>
                            <td>S/ {{ number_format($p->saldo, 2) }}</td>
                            <td>
                                @php
                                    $map = ['activo'=>'b-blue','pagado'=>'b-green','mora'=>'b-red','cancelado'=>'b-gray'];
                                @endphp
                                <span class="badge-pill {{ $map[$p->estado] ?? 'b-gray' }}">{{ ucfirst($p->estado) }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" style="text-align:center;color:var(--text-muted);padding:30px">Sin préstamos registrados aún.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Donut - Estado de la cartera
    new Chart(document.getElementById('carteraChart'), {
        type: 'doughnut',
        data: {
            labels: ['Al día', 'Pagadas', 'Vencidas'],
            datasets: [{
                data: [{{ $cuotasAlDia }}, {{ $cuotasPagadas }}, {{ $cuotasVencidas }}],
                backgroundColor: ['#3b82f6', '#22c55e', '#ef4444'],
                borderWidth: 0,
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false, cutout: '62%',
            plugins: { legend: { position: 'bottom', labels: { padding: 18, font: { size: 12 } } } }
        }
    });

    // Barras - Prestado vs Recuperado
    new Chart(document.getElementById('balanceChart'), {
        type: 'bar',
        data: {
            labels: ['Total Prestado', 'Total Recuperado'],
            datasets: [{
                label: 'Soles (S/)',
                data: [{{ $totalPrestado }}, {{ $totalRecuperado }}],
                backgroundColor: ['#2563eb', '#06b6d4'],
                borderRadius: 8, barThickness: 90,
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { callback: v => 'S/ ' + v.toLocaleString() } } }
        }
    });
</script>
@endpush