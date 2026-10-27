@extends('layouts.app')

@section('title', 'Facturación Electrónica')
@section('topbar', 'Facturación Electrónica')

@section('content')
    @if ($errors->any())
        <div class="alert alert-error"><i class="bi bi-exclamation-circle"></i> {{ $errors->first() }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-error"><i class="bi bi-x-circle"></i> {{ session('error') }}</div>
    @endif

    {{-- ===================== HERO / ESTADO ===================== --}}
    <div class="fe-hero">
        <div class="fe-hero__top">
            <div class="fe-hero__icon"><i class="bi bi-receipt"></i></div>
            <div>
                <div class="fe-hero__title">
                    Facturación Electrónica
                    <span class="fe-hero__flag">🇵🇪 Perú</span>
                </div>
                <div class="fe-hero__sub">
                    Emisión de comprobantes electrónicos ante SUNAT · UBL 2.1 · Boletas, facturas y notas de crédito.
                </div>
            </div>
            <div class="fe-hero__brand">
                <div class="b1">SUNAT</div>
                <div class="b2">Comprobantes de Pago Electrónicos</div>
            </div>
        </div>

        <div class="fe-pills">
            <span class="fe-pill {{ $config->habilitado ? 'is-on' : 'is-off' }}">
                <i class="bi {{ $config->habilitado ? 'bi-check-circle-fill' : 'bi-slash-circle' }}"></i>
                {{ $config->habilitado ? 'Habilitada' : 'Deshabilitada' }}
            </span>
            <span class="fe-pill is-off">
                <i class="bi bi-cpu"></i> Driver: {{ $config->driver }}
            </span>
            <span class="fe-pill {{ $config->entorno === 'produccion' ? 'is-warn' : 'is-off' }}">
                <i class="bi bi-diagram-3"></i> Modo: {{ $config->entorno }}
            </span>
            <span class="fe-pill {{ $config->certificadoExiste() ? 'is-on' : 'is-bad' }}">
                <i class="bi {{ $config->certificadoExiste() ? 'bi-shield-check' : 'bi-shield-exclamation' }}"></i>
                {{ $config->certificadoExiste() ? 'Certificado OK' : 'Certificado no encontrado' }}
            </span>
        </div>

        <div class="fe-hero__actions">
            <form action="{{ route('facturacion.probar') }}" method="POST" style="display:inline">
                @csrf
                <button type="submit" class="btn btn-white"><i class="bi bi-lightning-charge-fill"></i> Probar conexión con SUNAT</button>
            </form>
            @if ($config->driver === 'sunat')
                <form action="{{ route('facturacion.prueba') }}" method="POST" style="display:inline; margin-left:6px">
                    @csrf
                    <button type="submit" class="btn btn-light"><i class="bi bi-send-check"></i> Emitir boleta de prueba (Beta)</button>
                </form>
            @endif
        </div>
    </div>

    {{-- ===================== FORMULARIO ===================== --}}
    <form action="{{ route('facturacion.update') }}" method="POST" enctype="multipart/form-data">
        @csrf @method('PUT')

        {{-- ---------- Estado y modo ---------- --}}
        <div class="card" style="margin-bottom:22px">
            <div class="card__header"><i class="bi bi-lightning-charge"></i> &nbsp;Estado y modo
                <span style="font-weight:400; color:var(--text-muted); font-size:12px">&nbsp;· Activación, forma de emisión y entorno de SUNAT</span>
            </div>
            <div class="card__body">
                <label class="fe-check-row">
                    <input type="checkbox" name="habilitado" value="1" {{ $config->habilitado ? 'checked' : '' }}>
                    <span>
                        <span class="t">Habilitar facturación electrónica</span>
                        <span class="d">Si está desactivada, las operaciones no generan comprobante ante SUNAT.</span>
                    </span>
                </label>

                <label class="fe-check-row">
                    <input type="checkbox" name="emitir_automatico" value="1" {{ $config->emitir_automatico ? 'checked' : '' }}>
                    <span>
                        <span class="t">Emitir automáticamente al registrar</span>
                        <span class="d">Cada boleta o factura se envía apenas se registra la operación.</span>
                    </span>
                </label>

                <div class="form-grid" style="margin-top:6px">
                    <div class="form-group">
                        <label>Driver de emisión</label>
                        <select name="driver" class="form-control">
                            @foreach ($drivers as $val => $label)
                                <option value="{{ $val }}" {{ $config->driver === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Entorno SUNAT</label>
                        <select name="entorno" class="form-control">
                            @foreach ($entornos as $val => $label)
                                <option value="{{ $val }}" {{ $config->entorno === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- ---------- Datos del emisor ---------- --}}
        <div class="card" style="margin-bottom:22px">
            <div class="card__header"><i class="bi bi-building"></i> &nbsp;Datos del emisor
                <span style="font-weight:400; color:var(--text-muted); font-size:12px">&nbsp;· Aparecen en el comprobante electrónico</span>
            </div>
            <div class="card__body">
                <div class="form-grid">
                    <div class="form-group">
                        <label>RUC *</label>
                        <input type="text" name="ruc" class="form-control" maxlength="11" placeholder="20000000001"
                               value="{{ old('ruc', $config->ruc) }}">
                    </div>
                    <div class="form-group">
                        <label>Razón social *</label>
                        <input type="text" name="razon_social" class="form-control"
                               value="{{ old('razon_social', $config->razon_social) }}">
                    </div>
                    <div class="form-group">
                        <label>Nombre comercial</label>
                        <input type="text" name="nombre_comercial" class="form-control"
                               value="{{ old('nombre_comercial', $config->nombre_comercial) }}">
                    </div>
                    <div class="form-group">
                        <label>Dirección fiscal</label>
                        <input type="text" name="direccion" class="form-control"
                               value="{{ old('direccion', $config->direccion) }}">
                    </div>
                    <div class="form-group">
                        <label>Ubigeo</label>
                        <input type="text" name="ubigeo" class="form-control" maxlength="6" placeholder="150101"
                               value="{{ old('ubigeo', $config->ubigeo) }}">
                    </div>
                    <div class="form-group">
                        <label>Departamento</label>
                        <input type="text" name="departamento" class="form-control"
                               value="{{ old('departamento', $config->departamento) }}">
                    </div>
                    <div class="form-group">
                        <label>Provincia</label>
                        <input type="text" name="provincia" class="form-control"
                               value="{{ old('provincia', $config->provincia) }}">
                    </div>
                    <div class="form-group">
                        <label>Distrito</label>
                        <input type="text" name="distrito" class="form-control"
                               value="{{ old('distrito', $config->distrito) }}">
                    </div>
                    <div class="form-group">
                        <label>Serie de factura</label>
                        <input type="text" name="serie_factura" class="form-control" maxlength="4" placeholder="F001"
                               value="{{ old('serie_factura', $config->serie_factura) }}">
                    </div>
                    <div class="form-group">
                        <label>Serie de boleta</label>
                        <input type="text" name="serie_boleta" class="form-control" maxlength="4" placeholder="B001"
                               value="{{ old('serie_boleta', $config->serie_boleta) }}">
                    </div>
                    <div class="form-group">
                        <label>Afectación IGV de los conceptos</label>
                        <select name="afectacion_igv" class="form-control">
                            @foreach (['10' => 'Gravado (con IGV 18%)', '20' => 'Exonerado', '30' => 'Inafecto'] as $val => $lbl)
                                <option value="{{ $val }}" {{ $config->afectacion_igv === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- ---------- Credenciales SUNAT ---------- --}}
        <div class="card" style="margin-bottom:22px">
            <div class="card__header"><i class="bi bi-key"></i> &nbsp;Credenciales SUNAT
                <span style="font-weight:400; color:var(--text-muted); font-size:12px">&nbsp;· Clave SOL y certificado digital</span>
            </div>
            <div class="card__body">
                <div class="fe-note">
                    <i class="bi bi-info-circle"></i>
                    <span>En <strong>Beta</strong> puedes usar el RUC <strong>20000000001</strong> con usuario y clave <strong>MODDATOS</strong>.</span>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label>Usuario Clave SOL</label>
                        <input type="text" name="sol_usuario" class="form-control" placeholder="MODDATOS"
                               value="{{ old('sol_usuario', $config->sol_usuario) }}">
                    </div>
                    <div class="form-group">
                        <label>Clave SOL</label>
                        <input type="password" name="sol_clave" class="form-control" autocomplete="new-password"
                               placeholder="{{ $config->sol_clave ? '•••••••• (guardada)' : '' }}">
                    </div>
                    <div class="form-group full">
                        <label>Certificado digital (.pem)</label>
                        <input type="file" name="certificado" class="form-control" accept=".pem,.txt">
                        @if ($config->certificado_path)
                            @if ($config->certificadoExiste())
                                <div class="fe-cert-ok"><i class="bi bi-check-circle"></i> Certificado cargado: {{ $config->certificado_path }}</div>
                            @else
                                <div class="fe-cert-warn"><i class="bi bi-exclamation-triangle"></i> No se encontró el certificado en la ruta guardada: {{ $config->certificado_path }}</div>
                            @endif
                        @else
                            <div class="d" style="font-size:12px; color:var(--text-muted); margin-top:6px">
                                Sube el certificado en formato PEM (certificado + llave privada). Se guarda de forma privada en el servidor.
                            </div>
                        @endif
                    </div>
                    <div class="form-group">
                        <label>Clave del certificado <span style="font-weight:400;color:var(--text-muted)">(opcional)</span></label>
                        <input type="password" name="certificado_clave" class="form-control" autocomplete="new-password"
                               placeholder="{{ $config->certificado_clave ? '•••••••• (guardada)' : '' }}">
                    </div>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <a href="{{ route('config.index') }}" class="btn btn-light"><i class="bi bi-arrow-left"></i> Volver</a>
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Guardar configuración</button>
        </div>
    </form>
@endsection
