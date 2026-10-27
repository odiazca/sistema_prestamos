@extends('layouts.app')

@section('title', 'Super Admin')
@section('topbar', 'Panel Super Administrador')

@php
    $rmap = ['superadmin'=>'b-indigo','admin'=>'b-red','gerente'=>'b-purple','operador'=>'b-blue','cobrador'=>'b-yellow'];
    $roles = ['superadmin'=>'Super Administrador','admin'=>'Administrador','gerente'=>'Gerente','operador'=>'Operador','cobrador'=>'Cobrador'];
    $amap = ['creo'=>'#22c55e','actualizo'=>'#3b82f6','elimino'=>'#ef4444','inicio sesion'=>'#8b5cf6','cierre sesion'=>'#94a3b8'];
@endphp

@section('content')
    <div style="margin-bottom:22px">
        <h1 class="page-title"><i class="bi bi-shield-lock-fill" style="color:var(--primary)"></i> Centro de Control</h1>
        <p class="page-subtitle">Visión global de la plataforma y administración central. Acceso exclusivo del super administrador.</p>
    </div>

    {{-- ===== KPIs globales ===== --}}
    <div class="stats-grid" style="grid-template-columns:repeat(4,1fr)">
        <div class="stat-card bg-blue">
            <i class="bi bi-people-fill stat-icon"></i>
            <div class="stat-label">USUARIOS</div>
            <div class="stat-value">{{ number_format($metricas['usuarios']) }}</div>
            <div class="stat-foot">{{ number_format($metricas['usuarios_activos']) }} activos</div>
        </div>
        <div class="stat-card bg-teal">
            <i class="bi bi-person-vcard stat-icon"></i>
            <div class="stat-label">CLIENTES</div>
            <div class="stat-value">{{ number_format($metricas['clientes']) }}</div>
        </div>
        <div class="stat-card bg-purple">
            <i class="bi bi-cash-stack stat-icon"></i>
            <div class="stat-label">PRÉSTAMOS ACTIVOS</div>
            <div class="stat-value">{{ number_format($metricas['prestamos_activos']) }}</div>
        </div>
        <div class="stat-card bg-orange">
            <i class="bi bi-wallet2 stat-icon"></i>
            <div class="stat-label">CARTERA POR COBRAR</div>
            <div class="stat-value" style="font-size:20px">S/ {{ number_format($metricas['cartera'], 2) }}</div>
        </div>
        <div class="stat-card bg-cyan">
            <i class="bi bi-graph-up-arrow stat-icon"></i>
            <div class="stat-label">RECAUDADO (MES)</div>
            <div class="stat-value" style="font-size:20px">S/ {{ number_format($metricas['recaudado_mes'], 2) }}</div>
        </div>
        <div class="stat-card bg-red">
            <i class="bi bi-gem stat-icon"></i>
            <div class="stat-label">EMPEÑOS VIGENTES</div>
            <div class="stat-value">{{ number_format($metricas['empenos_vigentes']) }}</div>
        </div>
        <div class="stat-card bg-blue">
            <i class="bi bi-activity stat-icon"></i>
            <div class="stat-label">ACCIONES HOY</div>
            <div class="stat-value">{{ number_format($metricas['acciones_hoy']) }}</div>
        </div>
        <a href="{{ route('usuarios.create') }}" class="stat-card bg-teal" style="text-decoration:none">
            <i class="bi bi-person-plus stat-icon"></i>
            <div class="stat-label">ACCIÓN RÁPIDA</div>
            <div class="stat-value" style="font-size:18px">Nuevo usuario</div>
            <div class="stat-foot">Crear acceso</div>
        </a>
    </div>

    <div class="grid-2" style="align-items:start">
        {{-- ===== Usuarios por rol ===== --}}
        <div class="card">
            <div class="card__header">Usuarios por rol</div>
            <div class="card__body">
                <table class="info-list">
                    @forelse ($porRol as $rol => $total)
                        <tr>
                            <th><span class="badge-pill {{ $rmap[$rol] ?? 'b-gray' }}">{{ $roles[$rol] ?? ucfirst($rol) }}</span></th>
                            <td style="text-align:right;font-weight:700">{{ number_format($total) }}</td>
                        </tr>
                    @empty
                        <tr><td>Sin usuarios registrados.</td></tr>
                    @endforelse
                </table>
                <div style="margin-top:16px">
                    <a href="{{ route('usuarios.index') }}" class="btn btn-light btn-sm"><i class="bi bi-people"></i> Gestionar usuarios</a>
                </div>
            </div>
        </div>

        {{-- ===== Información del sistema ===== --}}
        <div class="card">
            <div class="card__header">Información del sistema</div>
            <div class="card__body">
                <table class="info-list">
                    <tr><th>Aplicación</th><td>{{ $sistema['app'] }}</td></tr>
                    <tr><th>Entorno</th><td><span class="badge-pill {{ $sistema['entorno'] === 'production' ? 'b-green' : 'b-yellow' }}">{{ ucfirst($sistema['entorno']) }}</span></td></tr>
                    <tr><th>Versión Laravel</th><td>{{ $sistema['laravel'] }}</td></tr>
                    <tr><th>Versión PHP</th><td>{{ $sistema['php'] }}</td></tr>
                    <tr><th>Fecha del servidor</th><td>{{ $sistema['fecha'] }}</td></tr>
                </table>
            </div>
        </div>
    </div>

    {{-- ===== Accesos de administración ===== --}}
    <h2 class="section-title"><i class="bi bi-grid-3x3-gap"></i> Administración central</h2>
    <div class="quick-grid" style="grid-template-columns:repeat(4,1fr);margin-bottom:24px">
        <a href="{{ route('usuarios.index') }}" class="quick-card"><div class="q-icon bg-blue"><i class="bi bi-person-badge"></i></div><div><div class="q-title">Usuarios y accesos</div><div class="q-desc">Roles y permisos</div></div></a>
        <a href="{{ route('config.index') }}" class="quick-card"><div class="q-icon bg-purple"><i class="bi bi-sliders"></i></div><div><div class="q-title">Configuración</div><div class="q-desc">Parámetros del sistema</div></div></a>
        <a href="{{ route('auditoria.index') }}" class="quick-card"><div class="q-icon bg-teal"><i class="bi bi-shield-check"></i></div><div><div class="q-title">Auditoría</div><div class="q-desc">Bitácora completa</div></div></a>
        <a href="{{ route('reportes.index') }}" class="quick-card"><div class="q-icon bg-orange"><i class="bi bi-file-earmark-spreadsheet"></i></div><div><div class="q-title">Reportes</div><div class="q-desc">Exportar datos</div></div></a>
    </div>

    {{-- ===== Actividad global ===== --}}
    <div class="card">
        <div class="card__header">
            Actividad reciente de la plataforma
            <a href="{{ route('auditoria.index') }}" class="btn btn-light btn-sm"><i class="bi bi-arrow-right"></i> Ver todo</a>
        </div>
        <div class="card__body">
            @if ($actividad->isEmpty())
                <p style="color:var(--text-muted);text-align:center;padding:24px">Aún no hay actividad registrada.</p>
            @else
                <div class="timeline">
                    @foreach ($actividad as $a)
                        <div class="timeline-item">
                            <span class="timeline-dot" style="background:{{ $amap[$a->accion] ?? '#94a3b8' }}"></span>
                            <div class="t-title">
                                <strong>{{ $a->usuario_nombre }}</strong> · {{ ucfirst($a->accion) }}
                                @if($a->modulo) <span style="color:var(--text-muted)">en {{ $a->modulo }}</span> @endif
                                @if($a->referencia) ({{ $a->referencia }}) @endif
                            </div>
                            <div class="t-time"><i class="bi bi-clock"></i> {{ $a->created_at->format('d/m/Y H:i:s') }} · IP {{ $a->ip ?? '—' }}</div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection
