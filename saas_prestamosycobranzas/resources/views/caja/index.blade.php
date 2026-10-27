@extends('layouts.app')

@section('title', 'Caja')
@section('topbar', 'Caja')

@section('content')
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:22px;gap:12px;flex-wrap:wrap">
        <div>
            <h1 class="page-title">Caja</h1>
            <p class="page-subtitle" style="margin:0">Movimientos de ingresos y egresos del día.</p>
        </div>
        <form method="GET" style="display:flex;gap:8px;align-items:center">
            <label style="font-size:13px;color:var(--text-muted)">Fecha:</label>
            <input type="date" name="fecha" value="{{ $fecha }}" class="form-control" style="width:auto" onchange="this.form.submit()">
        </form>
    </div>

    <div class="stats-grid" style="grid-template-columns:repeat(4,1fr)">
        <div class="stat-card bg-teal">
            <i class="bi bi-wallet2 stat-icon"></i>
            <div class="stat-label">COBROS DEL DÍA</div>
            <div class="stat-value">S/ {{ number_format($cobros, 2) }}</div>
            <div class="stat-foot">Pagos de cuotas</div>
        </div>
        <div class="stat-card bg-blue">
            <i class="bi bi-arrow-down-circle stat-icon"></i>
            <div class="stat-label">OTROS INGRESOS</div>
            <div class="stat-value">S/ {{ number_format($ingresos, 2) }}</div>
        </div>
        <div class="stat-card bg-orange">
            <i class="bi bi-arrow-up-circle stat-icon"></i>
            <div class="stat-label">EGRESOS</div>
            <div class="stat-value">S/ {{ number_format($egresos, 2) }}</div>
        </div>
        <div class="stat-card bg-purple">
            <i class="bi bi-cash-stack stat-icon"></i>
            <div class="stat-label">SALDO DEL DÍA</div>
            <div class="stat-value">S/ {{ number_format($saldo, 2) }}</div>
        </div>
    </div>

    <div class="grid-2" style="align-items:start">
        <div class="card">
            <div class="card__header">Registrar movimiento</div>
            <div class="card__body">
                @if ($errors->any())
                    <div class="alert alert-error"><i class="bi bi-exclamation-circle"></i> {{ $errors->first() }}</div>
                @endif
                <form action="{{ route('caja.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="fecha" value="{{ $fecha }}">
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Tipo *</label>
                            <select name="tipo" id="c_tipo" class="form-control" required onchange="updateCategorias()">
                                <option value="ingreso">Ingreso</option>
                                <option value="egreso">Egreso</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Categoría *</label>
                            <select name="categoria" id="c_categoria" class="form-control" required></select>
                        </div>
                        <div class="form-group full">
                            <label>Concepto *</label>
                            <input type="text" name="concepto" class="form-control" placeholder="Descripción del movimiento" required>
                        </div>
                        <div class="form-group">
                            <label>Monto (S/) *</label>
                            <input type="number" step="0.01" min="0.01" name="monto" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Método *</label>
                            <select name="metodo" class="form-control" required>
                                @foreach ($metodos as $v => $l)<option value="{{ $v }}">{{ $l }}</option>@endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Registrar</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card__header">Movimientos del {{ \Illuminate\Support\Carbon::parse($fecha)->format('d/m/Y') }} ({{ $movimientos->count() }})</div>
            <div class="table-wrap" style="max-height:420px;overflow-y:auto">
                <table class="data">
                    <thead><tr><th>Código</th><th>Concepto</th><th>Tipo</th><th>Monto</th><th></th></tr></thead>
                    <tbody>
                        @forelse ($movimientos as $m)
                            <tr>
                                <td><strong>{{ $m->codigo }}</strong><br><span style="font-size:11px;color:var(--text-muted)">{{ ucfirst(str_replace('_',' ',$m->categoria)) }}</span></td>
                                <td>{{ $m->concepto }}</td>
                                <td>
                                    @if ($m->tipo === 'ingreso')
                                        <span class="badge-pill b-green">Ingreso</span>
                                    @else
                                        <span class="badge-pill b-red">Egreso</span>
                                    @endif
                                </td>
                                <td><strong style="color:{{ $m->tipo==='ingreso' ? '#166534' : '#991b1b' }}">{{ $m->tipo==='ingreso'?'+':'-' }} S/ {{ number_format($m->monto, 2) }}</strong></td>
                                <td>
                                    <form action="{{ route('caja.destroy', $m) }}" method="POST" onsubmit="return confirm('¿Eliminar movimiento?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" style="text-align:center;color:var(--text-muted);padding:24px">Sin movimientos manuales este día.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const CATS = @json($categorias);
    function updateCategorias() {
        const tipo = document.getElementById('c_tipo').value;
        const sel = document.getElementById('c_categoria');
        sel.innerHTML = '';
        Object.entries(CATS[tipo]).forEach(([v, l]) => {
            const o = document.createElement('option'); o.value = v; o.textContent = l; sel.appendChild(o);
        });
    }
    updateCategorias();
</script>
@endpush
