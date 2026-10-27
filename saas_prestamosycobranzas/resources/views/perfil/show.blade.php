@extends('layouts.app')

@section('title', 'Mi Perfil')
@section('topbar', 'Mi Perfil')

@php
    $rmap = ['superadmin'=>'b-indigo','admin'=>'b-red','gerente'=>'b-purple','operador'=>'b-blue','cobrador'=>'b-yellow'];
    $roles = ['superadmin'=>'Super Administrador','admin'=>'Administrador','gerente'=>'Gerente','operador'=>'Operador','cobrador'=>'Cobrador'];
@endphp

@section('content')
    {{-- ===== Mensajes ===== --}}
    @error('avatar')
        <div class="alert alert-error" style="margin-bottom:16px"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
    @enderror

    {{-- ===== Cabecera ===== --}}
    <div class="profile-header">
        <div class="profile-cover"></div>
        <div class="profile-body">
            <div class="profile-avatar">
                @if ($user->avatar_url)
                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}">
                @else
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                @endif
                <label class="avatar-cam" for="avatarInput" title="Cambiar foto de perfil">
                    <i class="bi bi-camera"></i>
                </label>
            </div>
            <form id="avatarForm" action="{{ route('perfil.foto') }}" method="POST" enctype="multipart/form-data" style="display:none">
                @csrf
                <input type="file" id="avatarInput" name="avatar" accept="image/jpeg,image/png,image/webp"
                       onchange="if(this.files.length) document.getElementById('avatarForm').submit()">
            </form>
            <div class="profile-meta">
                <h1>
                    {{ $user->name }}
                    <span class="badge-pill {{ $rmap[$user->rol] ?? 'b-gray' }}">{{ $roles[$user->rol] ?? ucfirst($user->rol) }}</span>
                    @if ($user->activo)<span class="badge-pill b-green">Activo</span>@else<span class="badge-pill b-gray">Inactivo</span>@endif
                </h1>
                <div class="email"><i class="bi bi-envelope"></i> {{ $user->email }}</div>
                <div class="chips">
                    <span class="chip"><i class="bi bi-telephone"></i> {{ $user->telefono ?: 'Sin teléfono' }}</span>
                    <span class="chip"><i class="bi bi-calendar-check"></i> Miembro desde {{ $user->created_at?->format('d/m/Y') }}</span>
                    <span class="chip"><i class="bi bi-hash"></i> ID {{ $user->id }}</span>
                    @if ($user->avatar)
                        <form action="{{ route('perfil.foto.delete') }}" method="POST" style="display:inline"
                              onsubmit="return confirm('¿Quitar tu foto de perfil?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="chip" style="border:none;cursor:pointer;color:#dc2626">
                                <i class="bi bi-trash"></i> Quitar foto
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if ($user->esSuperAdmin())
        {{-- ===== Acceso exclusivo Super Admin ===== --}}
        <div class="card" style="margin-bottom:24px;background:linear-gradient(135deg,#1e3a8a,#2563eb);border:none;color:#fff">
            <div class="card__body" style="display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap">
                <div style="display:flex;align-items:center;gap:16px">
                    <div style="width:54px;height:54px;border-radius:14px;background:rgba(255,255,255,.15);display:grid;place-items:center;font-size:26px;flex-shrink:0">
                        <i class="bi bi-shield-lock-fill"></i>
                    </div>
                    <div>
                        <div style="font-size:17px;font-weight:800">Centro de Control de la Plataforma</div>
                        <div style="font-size:13px;opacity:.9">Tienes privilegios de super administrador: métricas globales y administración central.</div>
                    </div>
                </div>
                <a href="{{ route('superadmin.index') }}" class="btn btn-light"><i class="bi bi-box-arrow-up-right"></i> Abrir panel</a>
            </div>
        </div>
    @endif

    @if ($resumen)
        {{-- ===== Panel de administracion (solo admin) ===== --}}
        <h2 class="section-title"><i class="bi bi-speedometer2"></i> Panel de Administración</h2>
        <div class="stats-grid" style="grid-template-columns:repeat(3,1fr)">
            <div class="stat-card bg-blue">
                <i class="bi bi-people-fill stat-icon"></i>
                <div class="stat-label">USUARIOS</div>
                <div class="stat-value">{{ $resumen['usuarios'] }}</div>
                <div class="stat-foot">{{ $resumen['usuarios_activos'] }} activos</div>
            </div>
            <div class="stat-card bg-teal">
                <i class="bi bi-person-vcard stat-icon"></i>
                <div class="stat-label">CLIENTES</div>
                <div class="stat-value">{{ $resumen['clientes'] }}</div>
            </div>
            <div class="stat-card bg-purple">
                <i class="bi bi-cash-stack stat-icon"></i>
                <div class="stat-label">PRÉSTAMOS ACTIVOS</div>
                <div class="stat-value">{{ $resumen['prestamos_activos'] }}</div>
            </div>
            <div class="stat-card bg-orange">
                <i class="bi bi-gem stat-icon"></i>
                <div class="stat-label">EMPEÑOS VIGENTES</div>
                <div class="stat-value">{{ $resumen['empenos_vigentes'] }}</div>
            </div>
            <div class="stat-card bg-cyan">
                <i class="bi bi-activity stat-icon"></i>
                <div class="stat-label">ACCIONES HOY</div>
                <div class="stat-value">{{ $resumen['acciones_hoy'] }}</div>
            </div>
            <a href="{{ route('usuarios.index') }}" class="stat-card bg-red" style="text-decoration:none">
                <i class="bi bi-gear-wide-connected stat-icon"></i>
                <div class="stat-label">GESTIÓN</div>
                <div class="stat-value" style="font-size:18px">Administrar</div>
                <div class="stat-foot">Usuarios y accesos</div>
            </a>
        </div>

        <div class="quick-grid" style="grid-template-columns:repeat(4,1fr);margin-bottom:24px">
            <a href="{{ route('usuarios.create') }}" class="quick-card"><div class="q-icon bg-blue"><i class="bi bi-person-plus"></i></div><div><div class="q-title">Nuevo usuario</div><div class="q-desc">Crear acceso</div></div></a>
            <a href="{{ route('config.index') }}" class="quick-card"><div class="q-icon bg-purple"><i class="bi bi-sliders"></i></div><div><div class="q-title">Configuración</div><div class="q-desc">Parámetros</div></div></a>
            <a href="{{ route('auditoria.index') }}" class="quick-card"><div class="q-icon bg-teal"><i class="bi bi-shield-check"></i></div><div><div class="q-title">Auditoría</div><div class="q-desc">Bitácora</div></div></a>
            <a href="{{ route('reportes.index') }}" class="quick-card"><div class="q-icon bg-orange"><i class="bi bi-file-earmark-spreadsheet"></i></div><div><div class="q-title">Reportes</div><div class="q-desc">Exportar</div></div></a>
        </div>
    @endif

    {{-- ===== Tabs ===== --}}
    <div class="tabs">
        <button class="tab-btn active" data-tab="datos" onclick="showTab(this,'datos')"><i class="bi bi-person"></i> Datos personales</button>
        <button class="tab-btn" data-tab="seguridad" onclick="showTab(this,'seguridad')"><i class="bi bi-lock"></i> Seguridad</button>
        <button class="tab-btn" data-tab="actividad" onclick="showTab(this,'actividad')"><i class="bi bi-clock-history"></i> Actividad</button>
    </div>

    {{-- Datos --}}
    <div class="tab-pane active" id="tab-datos">
        <div class="grid-2" style="align-items:start">
            <div class="card">
                <div class="card__header">Editar datos personales</div>
                <div class="card__body">
                    @if ($errors->updatePerfil ?? false) @endif
                    <form action="{{ route('perfil.update') }}" method="POST">
                        @csrf @method('PUT')
                        <div class="form-group" style="margin-bottom:16px">
                            <label>Nombre completo *</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                        </div>
                        <div class="form-group" style="margin-bottom:16px">
                            <label>Correo electrónico *</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                        </div>
                        <div class="form-group">
                            <label>Teléfono</label>
                            <input type="text" name="telefono" class="form-control" value="{{ old('telefono', $user->telefono) }}">
                        </div>
                        <div class="form-actions">
                            <button class="btn btn-primary"><i class="bi bi-check-lg"></i> Guardar cambios</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card">
                <div class="card__header">Información de la cuenta</div>
                <div class="card__body">
                    <table class="info-list">
                        <tr><th>Rol</th><td><span class="badge-pill {{ $rmap[$user->rol] ?? 'b-gray' }}">{{ $roles[$user->rol] ?? ucfirst($user->rol) }}</span></td></tr>
                        <tr><th>Estado</th><td>{{ $user->activo ? 'Activo' : 'Inactivo' }}</td></tr>
                        <tr><th>Fecha de registro</th><td>{{ $user->created_at?->format('d/m/Y H:i') }}</td></tr>
                        <tr><th>Última actualización</th><td>{{ $user->updated_at?->format('d/m/Y H:i') }}</td></tr>
                        <tr><th>Identificador</th><td>#{{ $user->id }}</td></tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Seguridad --}}
    <div class="tab-pane" id="tab-seguridad">
        <div class="card" style="max-width:560px">
            <div class="card__header">Cambiar contraseña</div>
            <div class="card__body">
                @if ($errors->any())
                    <div class="alert alert-error"><i class="bi bi-exclamation-circle"></i> {{ $errors->first() }}</div>
                @endif
                <form action="{{ route('perfil.password') }}" method="POST">
                    @csrf @method('PUT')
                    <div class="form-group" style="margin-bottom:16px">
                        <label>Contraseña actual *</label>
                        <input type="password" name="current_password" class="form-control" required autocomplete="current-password">
                    </div>
                    <div class="form-group" style="margin-bottom:16px">
                        <label>Nueva contraseña *</label>
                        <input type="password" name="password" class="form-control" required autocomplete="new-password">
                    </div>
                    <div class="form-group">
                        <label>Confirmar nueva contraseña *</label>
                        <input type="password" name="password_confirmation" class="form-control" required autocomplete="new-password">
                    </div>
                    <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:10px;padding:12px;margin-top:16px;font-size:12px;color:#92400e">
                        <i class="bi bi-shield-lock"></i> Usa una contraseña de al menos 6 caracteres. No la compartas con nadie.
                    </div>
                    <div class="form-actions">
                        <button class="btn btn-primary"><i class="bi bi-key"></i> Actualizar contraseña</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Actividad --}}
    <div class="tab-pane" id="tab-actividad">
        <div class="card">
            <div class="card__header">Mi actividad reciente</div>
            <div class="card__body">
                @if ($actividad->isEmpty())
                    <p style="color:var(--text-muted);text-align:center;padding:24px">Aún no hay actividad registrada.</p>
                @else
                    @php $amap = ['creo'=>'#22c55e','actualizo'=>'#3b82f6','elimino'=>'#ef4444','inicio sesion'=>'#8b5cf6','cierre sesion'=>'#94a3b8']; @endphp
                    <div class="timeline">
                        @foreach ($actividad as $a)
                            <div class="timeline-item">
                                <span class="timeline-dot" style="background:{{ $amap[$a->accion] ?? '#94a3b8' }}"></span>
                                <div class="t-title">{{ ucfirst($a->accion) }} @if($a->modulo) · {{ $a->modulo }} @endif @if($a->referencia) <span style="color:var(--text-muted)">({{ $a->referencia }})</span> @endif</div>
                                <div class="t-time"><i class="bi bi-clock"></i> {{ $a->created_at->format('d/m/Y H:i:s') }} · IP {{ $a->ip ?? '—' }}</div>
                            </div>
                        @endforeach
                    </div>
                    @if (auth()->user()->esAdmin())
                        <div style="margin-top:16px"><a href="{{ route('auditoria.index') }}" class="btn btn-light btn-sm"><i class="bi bi-shield-check"></i> Ver auditoría completa</a></div>
                    @endif
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function showTab(btn, tab) {
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
        btn.classList.add('active');
        document.getElementById('tab-' + tab).classList.add('active');
    }
    // Si hay errores de contraseña, abrir pestana seguridad
    @if ($errors->any())
        document.addEventListener('DOMContentLoaded', () => {
            const segBtn = document.querySelector('[data-tab="seguridad"]');
            if (segBtn) showTab(segBtn, 'seguridad');
        });
    @endif
</script>
@endpush
