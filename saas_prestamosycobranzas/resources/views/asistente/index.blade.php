@extends('layouts.app')

@section('title', 'Asistente IA')
@section('topbar', 'Asistente IA')

@section('content')
<div class="ia-head">
    <div>
        <h1 class="page-title">Asistente de reportes</h1>
        <p class="page-subtitle">Pregunta en lenguaje natural y obtén tablas, gráficos y resúmenes de tu cartera de préstamos y cobranzas</p>
    </div>
    <div class="ia-actions">
        <button type="button" class="ia-soft" id="btnEsq"><span data-ico="db"></span> Qué datos ve la IA</button>
        <button type="button" class="ia-soft" id="btnCfg"><span data-ico="gear"></span> Configurar IA</button>
    </div>
</div>

<div class="ia-hero">
    <h2><span data-ico="sparkles"></span> ¿Qué quieres saber de tu negocio?</h2>
    <p>Escribe tu pregunta como se la harías a un analista. Ejemplo: «¿Qué clientes tienen más de 15 días de atraso?»</p>
    <form class="ia-ask" id="frmAsk">
        <textarea id="iaQ" rows="1" maxlength="500" placeholder="Escribe tu pregunta aquí…" aria-label="Pregunta"></textarea>
        <button type="submit" id="iaGo"><span data-ico="send"></span> Preguntar</button>
    </form>
    <div class="ia-chips" id="iaChips"></div>
</div>

<div id="iaSetup" class="ia-card" style="margin-bottom:16px" hidden>
    <h6><span data-ico="sparkles"></span> Activa el asistente en 2 minutos (gratis)</h6>
    <div class="ia-step"><b>1</b><div>Entra a <a class="ia-link" href="https://aistudio.google.com/apikey" target="_blank" rel="noopener">aistudio.google.com/apikey</a> con tu cuenta de Google y crea una clave gratuita.</div></div>
    <div class="ia-step"><b>2</b>
        <div>Pega la clave aquí y pulsa <b>Activar</b>:
            <form class="ia-form-row" id="frmQuick">
                <input type="password" class="ia-input" id="qKey" style="max-width:340px" placeholder="Clave de Gemini" autocomplete="new-password">
                <button class="ia-btn" id="qGo">Activar</button>
            </form>
            <div class="ia-xs ia-bad" id="qErr"></div>
        </div>
    </div>
    <p class="ia-xs ia-mut" style="margin:6px 0 10px">También puedes usar Groq, OpenRouter u Ollama (local, sin enviar datos a internet) desde la configuración avanzada.</p>
    <button type="button" class="ia-soft" onclick="document.getElementById('btnCfg').click()"><span data-ico="gear"></span> Configuración avanzada</button>
</div>

<div class="ia-grid">
    <div>
        <div id="iaOut">
            <div class="ia-card ia-empty" style="padding:44px 18px">
                <i class="bi bi-chat-dots ia-big"></i>
                <div style="margin-top:6px">Aquí aparecerán tus resultados</div>
                <div class="ia-xs">Puedes hacer preguntas de seguimiento, como «ahora por cobrador».</div>
            </div>
        </div>
    </div>
    <div class="ia-stack">
        <div class="ia-card ia-side">
            <h6><span data-ico="star" data-fill="1" data-cls="ia-star-on"></span> Favoritas</h6>
            <div id="iaFav"></div>
        </div>
        <div class="ia-card ia-side">
            <h6><span data-ico="clock"></span> Recientes</h6>
            <div id="iaHis"></div>
        </div>
    </div>
</div>

{{-- Modal: configuración --}}
<div class="ia-modal" id="mdlCfg" hidden>
    <div>
        <form id="frmCfg" novalidate>
            <div class="ia-mh">
                <h5><span data-ico="gear"></span> Configurar el asistente de IA</h5>
                <button type="button" data-close="mdlCfg" aria-label="Cerrar"><span data-ico="x"></span></button>
            </div>
            <div class="ia-mb">
                <div class="c12">
                    <label class="ia-lbl">Proveedor</label>
                    <select class="ia-select" id="cProv"></select>
                    <div class="ia-xs ia-mut" id="cAyuda" style="margin-top:4px"></div>
                </div>
                <div class="c7"><label class="ia-lbl">URL de la API</label><input class="ia-input" id="cUrl" autocomplete="off"></div>
                <div class="c5"><label class="ia-lbl">Modelo</label><input class="ia-input" id="cMod" autocomplete="off"></div>
                <div class="c12">
                    <label class="ia-lbl">Clave (API key)</label>
                    <input type="password" class="ia-input" id="cKey" autocomplete="new-password">
                    <div class="ia-xs ia-mut" id="cKeyH" style="margin-top:4px"></div>
                </div>
                <div class="c12">
                    <label class="ia-check"><input type="checkbox" id="cRes"> Generar un resumen en lenguaje natural de cada resultado (usa una segunda consulta a la IA)</label>
                </div>
                <div class="c12">
                    <div class="ia-note"><b>Privacidad y seguridad.</b> La IA solo ve la estructura de unas vistas de lectura (sin contraseñas, usuarios ni credenciales SUNAT) y su consulta se valida antes de ejecutarse: solo SELECT, en modo lectura, con límite de tiempo y de filas. Al proveedor se envía tu pregunta y, si activas el resumen, hasta 25 filas del resultado (pueden incluir nombres y documentos de clientes). Con <b>Ollama</b> nada sale de tu PC. La clave se guarda cifrada.</div>
                </div>
                <div class="c12 ia-sm ia-bad" id="cErr"></div>
            </div>
            <div class="ia-mf">
                <button type="button" class="ia-soft" id="cTest"><span data-ico="bolt"></span> Probar conexión</button>
                <button type="button" class="ia-soft" data-close="mdlCfg">Cerrar</button>
                <button type="submit" class="ia-btn"><span data-ico="check"></span> Guardar</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal: datos que ve la IA --}}
<div class="ia-modal" id="mdlEsq" hidden>
    <div>
        <div class="ia-mh">
            <h5><span data-ico="db"></span> Datos que ve la IA</h5>
            <button type="button" data-close="mdlEsq" aria-label="Cerrar"><span data-ico="x"></span></button>
        </div>
        <div style="padding:16px 24px 24px">
            <p class="ia-sm ia-mut" style="margin:0 0 8px">Solo estas vistas de lectura. Nunca accede a las tablas reales, usuarios ni contraseñas.</p>
            <pre class="ia-sql" id="esqTxt" style="margin:0"></pre>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/asistente.js') }}?v=1"></script>
@endpush
