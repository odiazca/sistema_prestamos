@extends('layouts.app')

@section('title', 'Clientes')
@section('topbar', 'Clientes')

@section('content')
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:22px;gap:12px;flex-wrap:wrap">
        <div>
            <h1 class="page-title">Clientes</h1>
            <p class="page-subtitle" style="margin:0">Administra la cartera de clientes del negocio.</p>
        </div>
        <a href="{{ route('clientes.create') }}" class="btn btn-primary"><i class="bi bi-person-plus"></i> Nuevo Cliente</a>
    </div>

    <div class="card">
        <div class="card__header">
            <span>Listado ({{ $clientes->total() }})</span>
            <form method="GET" class="topbar__search" style="margin:0">
                <i class="bi bi-search" style="color:#94a3b8"></i>
                <input type="text" name="q" value="{{ $buscar }}" placeholder="Buscar por nombre, documento o código...">
            </form>
        </div>
        <div class="table-wrap">
            <table class="data">
                <thead>
                    <tr><th>Código</th><th>Nombre</th><th>Documento</th><th>Teléfono</th><th>Ocupación</th><th>Estado</th><th>Acciones</th></tr>
                </thead>
                <tbody>
                    @forelse ($clientes as $c)
                        <tr>
                            <td><strong>{{ $c->codigo }}</strong></td>
                            <td>{{ $c->nombre_completo }}</td>
                            <td>{{ $c->tipo_documento }} {{ $c->documento ?: '—' }}</td>
                            <td>{{ $c->telefono ?: '—' }}</td>
                            <td>{{ $c->ocupacion ?: '—' }}</td>
                            <td>
                                @php $m = ['activo'=>'b-green','inactivo'=>'b-gray','moroso'=>'b-red']; @endphp
                                <span class="badge-pill {{ $m[$c->estado] ?? 'b-gray' }}">{{ ucfirst($c->estado) }}</span>
                            </td>
                            <td style="white-space:nowrap">
                                <a href="{{ route('clientes.edit', $c) }}" class="btn btn-light btn-sm"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('clientes.destroy', $c) }}" method="POST" style="display:inline" onsubmit="return confirm('¿Eliminar este cliente?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" style="text-align:center;color:var(--text-muted);padding:36px">No se encontraron clientes.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="padding:0 20px 18px">
            {{ $clientes->links() }}
        </div>
    </div>
@endsection
