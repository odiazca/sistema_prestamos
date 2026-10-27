<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} — Gestiona tu cartera de préstamos</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="lp">

    {{-- ===== Cabecera + Hero ===== --}}
    <div class="lp-top">
        <nav class="lp-nav">
            <div class="lp-brand">
                <span class="logo"><i class="bi bi-cash-coin"></i></span>
                Préstamos Pro
            </div>
            <div class="lp-navlinks">
                <a href="#funciones">Funciones</a>
                <a href="#precios">Precios</a>
                <a href="{{ route('login') }}">Iniciar sesión</a>
                <a href="{{ route('login') }}" class="lp-btn lp-btn-primary"><i class="bi bi-rocket-takeoff"></i> Prueba gratis</a>
            </div>
        </nav>

        <header class="lp-hero">
            <span class="lp-badge">🚀 Plataforma #1 de gestión de préstamos y cobranzas</span>
            <h1>Gestiona tu cartera<br><span class="grad">de forma inteligente</span></h1>
            <p>Todo lo que necesitas para administrar clientes, préstamos, cuotas, cobranzas, mora y empeños. Sin complicaciones, desde cualquier dispositivo.</p>
            <div class="lp-hero-cta">
                <a href="{{ route('login') }}" class="lp-btn lp-btn-primary"><i class="bi bi-rocket-takeoff"></i> Comenzar gratis — 30 días</a>
                <a href="#precios" class="lp-btn lp-btn-ghost"><i class="bi bi-tag"></i> Ver precios</a>
            </div>
            <div class="lp-stats">
                <div class="lp-stat"><div class="num">500+</div><div class="lbl">Financieras activas</div></div>
                <div class="lp-stat"><div class="num">50k+</div><div class="lbl">Préstamos gestionados</div></div>
                <div class="lp-stat"><div class="num">99.9%</div><div class="lbl">Uptime garantizado</div></div>
                <div class="lp-stat"><div class="num">30 días</div><div class="lbl">Prueba gratuita</div></div>
            </div>
        </header>
    </div>

    {{-- ===== Funciones ===== --}}
    <section class="lp-section" id="funciones">
        <div class="eyebrow">Todo en un solo lugar</div>
        <h2>Funciones que impulsan tu negocio</h2>
        <p class="sub">Una plataforma completa para controlar cada etapa del ciclo de crédito y cobranza.</p>

        <div class="lp-features">
            <div class="lp-feature">
                <div class="ic bg-blue"><i class="bi bi-people-fill"></i></div>
                <h3>Gestión de clientes</h3>
                <p>Registra y consulta el historial crediticio completo de cada cliente en segundos.</p>
            </div>
            <div class="lp-feature">
                <div class="ic bg-teal"><i class="bi bi-cash-stack"></i></div>
                <h3>Préstamos y cuotas</h3>
                <p>Cálculo automático de intereses, cronogramas de pago y saldos siempre actualizados.</p>
            </div>
            <div class="lp-feature">
                <div class="ic bg-orange"><i class="bi bi-exclamation-triangle-fill"></i></div>
                <h3>Control de mora</h3>
                <p>Alertas de vencimientos y seguimiento de cartera vencida para no perder ningún pago.</p>
            </div>
            <div class="lp-feature">
                <div class="ic bg-purple"><i class="bi bi-gem"></i></div>
                <h3>Empeños</h3>
                <p>Administra garantías y empeños con estados, vencimientos y valuación integrada.</p>
            </div>
            <div class="lp-feature">
                <div class="ic bg-cyan"><i class="bi bi-cash-coin"></i></div>
                <h3>Caja y arqueo</h3>
                <p>Movimientos de caja, corte diario y conciliación en tiempo real.</p>
            </div>
            <div class="lp-feature">
                <div class="ic bg-red"><i class="bi bi-file-earmark-spreadsheet"></i></div>
                <h3>Reportes avanzados</h3>
                <p>Métricas del negocio y exportación a Excel para decisiones basadas en datos.</p>
            </div>
        </div>
    </section>

    {{-- ===== Precios (teaser) ===== --}}
    <section class="lp-section" id="precios" style="padding-top:0">
        <div class="eyebrow">Precios simples</div>
        <h2>Empieza hoy, escala cuando quieras</h2>
        <p class="sub">Prueba todas las funciones durante 30 días. Sin tarjeta de crédito.</p>

        <div class="lp-cta">
            <h2>Lleva el control total de tu cartera</h2>
            <p>Únete a cientos de financieras que ya gestionan sus préstamos y cobranzas con Préstamos Pro.</p>
            <a href="{{ route('login') }}" class="lp-btn lp-btn-light"><i class="bi bi-box-arrow-in-right"></i> Acceder al sistema</a>
        </div>
    </section>

    {{-- ===== Footer ===== --}}
    <footer class="lp-footer">
        <div class="lp-brand"><span class="logo"><i class="bi bi-cash-coin"></i></span> Préstamos Pro</div>
        © {{ date('Y') }} {{ config('app.name') }} · Todos los derechos reservados
    </footer>

</body>
</html>
