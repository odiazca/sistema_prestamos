@extends('layouts.app')

@section('title', 'Chat IA')
@section('topbar', 'Chat IA')

@section('content')
<div class="ia-head">
    <div>
        <h1 class="page-title">Chat IA</h1>
        <p class="page-subtitle">Conversa con tu sistema: pregunta por préstamos, cuotas, mora, cobros, clientes, empeños o cómo usar cada módulo</p>
    </div>
    <a class="ia-soft" href="{{ route('asistente.index') }}"><span data-ico="gear"></span> Configurar IA</a>
</div>
<div id="chatPage"></div>
<script>document.addEventListener('DOMContentLoaded', () => window.IAU && IAU.hydrate());</script>
@endsection
