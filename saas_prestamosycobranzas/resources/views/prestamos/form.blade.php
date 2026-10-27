@extends('layouts.app')

@php $editando = $prestamo->exists; @endphp

@section('title', $editando ? 'Editar Préstamo' : 'Nuevo Préstamo')
@section('topbar', $editando ? 'Editar Préstamo' : 'Nuevo Préstamo')

@section('content')
    <div style="margin-bottom:22px">
        <a href="{{ route('prestamos.index') }}" class="btn btn-light btn-sm"><i class="bi bi-arrow-left"></i> Volver</a>
        <h1 class="page-title" style="margin-top:12px">{{ $editando ? 'Editar Préstamo' : 'Nuevo Préstamo' }}</h1>
        <p class="page-subtitle">El sistema calcula automáticamente las cuotas e intereses.</p>
    </div>

    @if ($errors->any())
        <div class="alert alert-error">
            <i class="bi bi-exclamation-circle"></i> Revisa los campos:
            <ul style="margin:6px 0 0 18px">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="grid-2" style="align-items:start">
        {{-- ===== Formulario ===== --}}
        <div class="card">
            <div class="card__header">Datos del préstamo</div>
            <div class="card__body">
                <form action="{{ $editando ? route('prestamos.update', $prestamo) : route('prestamos.store') }}" method="POST" id="formPrestamo">
                    @csrf
                    @if ($editando) @method('PUT') @endif

                    <div class="form-group" style="margin-bottom:18px">
                        <label>Cliente *</label>
                        <select name="cliente_id" class="form-control" required>
                            <option value="">— Selecciona un cliente —</option>
                            @foreach ($clientes as $c)
                                <option value="{{ $c->id }}" @selected(old('cliente_id', $prestamo->cliente_id) == $c->id)>
                                    {{ $c->codigo }} · {{ $c->nombre_completo }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label>Monto del préstamo (S/) *</label>
                            <input type="number" step="0.01" min="1" name="monto" id="f_monto" class="form-control" value="{{ old('monto', $prestamo->monto) }}" required>
                        </div>
                        <div class="form-group">
                            <label>Tasa de interés (%) *</label>
                            <input type="number" step="0.01" min="0" max="100" name="tasa_interes" id="f_tasa" class="form-control" value="{{ old('tasa_interes', $prestamo->tasa_interes) }}" required>
                        </div>
                        <div class="form-group">
                            <label>Número de cuotas *</label>
                            <input type="number" min="1" max="360" name="numero_cuotas" id="f_cuotas" class="form-control" value="{{ old('numero_cuotas', $prestamo->numero_cuotas) }}" required>
                        </div>
                        <div class="form-group">
                            <label>Frecuencia de pago *</label>
                            <select name="frecuencia" id="f_frecuencia" class="form-control" required>
                                @foreach ($frecuencias as $v=>$l)
                                    <option value="{{ $v }}" @selected(old('frecuencia', $prestamo->frecuencia) === $v)>{{ $l }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Fecha de inicio *</label>
                            <input type="date" name="fecha_inicio" id="f_fecha" class="form-control" value="{{ old('fecha_inicio', \Illuminate\Support\Carbon::parse($prestamo->fecha_inicio ?? now())->format('Y-m-d')) }}" required>
                        </div>
                        <div class="form-group full">
                            <label>Observaciones</label>
                            <textarea name="observaciones" class="form-control" rows="2">{{ old('observaciones', $prestamo->observaciones) }}</textarea>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> {{ $editando ? 'Guardar y regenerar' : 'Registrar préstamo' }}</button>
                        <a href="{{ route('prestamos.index') }}" class="btn btn-light">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>

        {{-- ===== Resumen / Preview ===== --}}
        <div class="card">
            <div class="card__header">Resumen del cálculo</div>
            <div class="card__body">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:18px">
                    <div class="stat-card bg-blue" style="box-shadow:none">
                        <div class="stat-label">CAPITAL</div>
                        <div class="stat-value" id="r_capital" style="font-size:22px">S/ 0.00</div>
                    </div>
                    <div class="stat-card bg-purple" style="box-shadow:none">
                        <div class="stat-label">INTERÉS TOTAL</div>
                        <div class="stat-value" id="r_interes" style="font-size:22px">S/ 0.00</div>
                    </div>
                    <div class="stat-card bg-teal" style="box-shadow:none">
                        <div class="stat-label">TOTAL A PAGAR</div>
                        <div class="stat-value" id="r_total" style="font-size:22px">S/ 0.00</div>
                    </div>
                    <div class="stat-card bg-orange" style="box-shadow:none">
                        <div class="stat-label">VALOR CUOTA</div>
                        <div class="stat-value" id="r_cuota" style="font-size:22px">S/ 0.00</div>
                    </div>
                </div>

                <h3 style="font-size:13px;color:var(--text-muted);margin-bottom:8px">Cronograma estimado</h3>
                <div class="table-wrap" style="max-height:300px;overflow-y:auto">
                    <table class="data">
                        <thead><tr><th>#</th><th>Vencimiento</th><th>Capital</th><th>Interés</th><th>Cuota</th></tr></thead>
                        <tbody id="r_cronograma">
                            <tr><td colspan="5" style="text-align:center;color:var(--text-muted);padding:24px">Completa los datos para ver el cronograma.</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const $ = id => document.getElementById(id);
    const money = n => 'S/ ' + (isFinite(n) ? n : 0).toLocaleString('es-PE', {minimumFractionDigits: 2, maximumFractionDigits: 2});

    function addPeriodo(fecha, frecuencia) {
        const d = new Date(fecha.getTime());
        if (frecuencia === 'diario') d.setDate(d.getDate() + 1);
        else if (frecuencia === 'semanal') d.setDate(d.getDate() + 7);
        else if (frecuencia === 'quincenal') d.setDate(d.getDate() + 15);
        else d.setMonth(d.getMonth() + 1);
        return d;
    }

    function calcular() {
        const monto = parseFloat($('f_monto').value) || 0;
        const tasa = parseFloat($('f_tasa').value) || 0;
        const n = parseInt($('f_cuotas').value) || 0;
        const frecuencia = $('f_frecuencia').value;
        const fechaStr = $('f_fecha').value;

        const interesTotal = Math.round(monto * tasa) / 100;
        const total = Math.round((monto + interesTotal) * 100) / 100;
        const cuota = n > 0 ? Math.round(total / n * 100) / 100 : 0;

        $('r_capital').textContent = money(monto);
        $('r_interes').textContent = money(interesTotal);
        $('r_total').textContent = money(total);
        $('r_cuota').textContent = money(cuota);

        const tbody = $('r_cronograma');
        if (n < 1 || monto < 1 || !fechaStr) {
            tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;color:var(--text-muted);padding:24px">Completa los datos para ver el cronograma.</td></tr>';
            return;
        }

        const capCuota = Math.round(monto / n * 100) / 100;
        const intCuota = Math.round(interesTotal / n * 100) / 100;
        let fecha = new Date(fechaStr + 'T00:00:00');
        let accCap = 0, accInt = 0, accTot = 0, rows = '';

        for (let i = 1; i <= n; i++) {
            fecha = addPeriodo(fecha, frecuencia);
            let cap, int, mon;
            if (i < n) { cap = capCuota; int = intCuota; mon = cuota; }
            else {
                cap = Math.round((monto - accCap) * 100) / 100;
                int = Math.round((interesTotal - accInt) * 100) / 100;
                mon = Math.round((total - accTot) * 100) / 100;
            }
            accCap += cap; accInt += int; accTot += mon;
            const f = fecha.toLocaleDateString('es-PE');
            rows += `<tr><td>${i}</td><td>${f}</td><td>${money(cap)}</td><td>${money(int)}</td><td><strong>${money(mon)}</strong></td></tr>`;
        }
        tbody.innerHTML = rows;
    }

    ['f_monto','f_tasa','f_cuotas','f_frecuencia','f_fecha'].forEach(id => {
        $(id).addEventListener('input', calcular);
        $(id).addEventListener('change', calcular);
    });
    calcular();
</script>
@endpush
