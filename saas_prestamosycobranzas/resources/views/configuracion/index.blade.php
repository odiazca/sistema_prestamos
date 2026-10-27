@extends('layouts.app')

@section('title', 'Configuracion')
@section('topbar', 'Configuracion')

@section('content')
    <h1 class="page-title">Configuración del Sistema</h1>
    <p class="page-subtitle">Parámetros generales, datos de la empresa y valores por defecto.</p>

    @if ($errors->any())
        <div class="alert alert-error"><i class="bi bi-exclamation-circle"></i> {{ $errors->first() }}</div>
    @endif

    <form action="{{ route('config.update') }}" method="POST">
        @csrf @method('PUT')

        <div class="grid-2" style="align-items:start">
            <div class="card">
                <div class="card__header"><i class="bi bi-building"></i> &nbsp;Datos de la empresa</div>
                <div class="card__body">
                    <div class="form-group" style="margin-bottom:16px">
                        <label>Nombre de la empresa *</label>
                        <input type="text" name="empresa_nombre" class="form-control" value="{{ old('empresa_nombre', $config['empresa_nombre']) }}" required>
                    </div>
                    <div class="form-grid">
                        <div class="form-group">
                            <label>RUC</label>
                            <input type="text" name="empresa_ruc" class="form-control" value="{{ old('empresa_ruc', $config['empresa_ruc']) }}">
                        </div>
                        <div class="form-group">
                            <label>Teléfono</label>
                            <input type="text" name="empresa_telefono" class="form-control" value="{{ old('empresa_telefono', $config['empresa_telefono']) }}">
                        </div>
                        <div class="form-group full">
                            <label>Dirección</label>
                            <input type="text" name="empresa_direccion" class="form-control" value="{{ old('empresa_direccion', $config['empresa_direccion']) }}">
                        </div>
                        <div class="form-group full">
                            <label>Correo</label>
                            <input type="email" name="empresa_email" class="form-control" value="{{ old('empresa_email', $config['empresa_email']) }}">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card__header"><i class="bi bi-sliders"></i> &nbsp;Parámetros financieros</div>
                <div class="card__body">
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Moneda *</label>
                            <input type="text" name="moneda" class="form-control" value="{{ old('moneda', $config['moneda']) }}" required>
                        </div>
                        <div class="form-group">
                            <label>Tasa de interés por defecto (%) *</label>
                            <input type="number" step="0.01" min="0" max="100" name="tasa_default" class="form-control" value="{{ old('tasa_default', $config['tasa_default']) }}" required>
                        </div>
                        <div class="form-group">
                            <label>Mora diaria (%) *</label>
                            <input type="number" step="0.01" min="0" max="100" name="mora_diaria" class="form-control" value="{{ old('mora_diaria', $config['mora_diaria']) }}" required>
                        </div>
                        <div class="form-group">
                            <label>Días de gracia *</label>
                            <input type="number" min="0" max="60" name="dias_gracia" class="form-control" value="{{ old('dias_gracia', $config['dias_gracia']) }}" required>
                        </div>
                    </div>
                    <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:10px;padding:12px;margin-top:16px;font-size:12px;color:#1e40af">
                        <i class="bi bi-info-circle"></i> Estos valores se usan como sugerencia al crear nuevos préstamos y para el cálculo de mora.
                    </div>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Guardar configuración</button>
        </div>
    </form>
@endsection
