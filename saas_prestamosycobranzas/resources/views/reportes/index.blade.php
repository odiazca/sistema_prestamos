@extends('layouts.app')

@section('title', 'Reportes')
@section('topbar', 'Reportes')

@section('content')
    <h1 class="page-title">Reportes</h1>
    <p class="page-subtitle">Genera reportes de la operación. Exporta a Excel (CSV) o imprime en PDF.</p>

    <div class="quick-grid" style="grid-template-columns:repeat(3,1fr)">
        @php $colors = ['bg-blue','bg-teal','bg-red','bg-purple','bg-orange','bg-cyan']; $i=0; @endphp
        @foreach ($tipos as $key => $t)
            <a href="{{ route('reportes.ver', $key) }}" class="quick-card">
                <div class="q-icon {{ $colors[$i++ % count($colors)] }}"><i class="bi {{ $t['icono'] }}"></i></div>
                <div>
                    <div class="q-title">{{ $t['titulo'] }}</div>
                    <div class="q-desc">{{ $t['fecha'] ? 'Filtrable por fecha' : 'Listado completo' }}</div>
                </div>
            </a>
        @endforeach
    </div>
@endsection
