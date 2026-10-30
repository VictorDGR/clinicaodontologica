@extends('layouts.app')

@section('title', 'Historial de '.$paciente->nombre_completo)
@section('section', 'Historial clínico')
@section('heading', 'Historial clínico')

@section('content')
    <div class="stack">
        <div class="form-card detail-card">
            <div class="form-intro"><h2>{{ $paciente->nombre_completo }}</h2><p>Historial abierto el {{ \Illuminate\Support\Carbon::parse($historial->fecha_apertura)->format('d/m/Y') }}.</p></div>
            <dl class="detail-grid">
                <div><dt>CI</dt><dd>{{ $paciente->ci }}</dd></div>
                <div><dt>Edad</dt><dd>{{ \Illuminate\Support\Carbon::parse($paciente->fecha_nacimiento)->age }} años</dd></div>
                <div><dt>Teléfono</dt><dd>{{ $paciente->telefono }}</dd></div>
                <div><dt>Atenciones registradas</dt><dd>{{ $registros->count() }}</dd></div>
            </dl>
        </div>

        <div>
            <div class="panel-heading"><h2 class="section-title" style="margin:0 0 4px">Atenciones</h2><p class="atencion-meta">Revisiones registradas, de la más reciente a la más antigua.</p></div>
            <div class="atencion-grid">
                @forelse ($registros as $registro)
                    @php($atencion = $registro->atencion)
                    <article class="atencion-card">
                        <header>
                            <span class="stat-icon">🦷</span>
                            <div class="atencion-titulo">
                                <strong>{{ $registro->fecha->format('d/m/Y') }}</strong>
                                <span class="atencion-meta">{{ $registro->fecha->format('H:i') }} · {{ $atencion->doctor->nombre_completo }}</span>
                            </div>
                            <span class="atencion-diente">{{ $atencion->tipo_diente }}</span>
                        </header>
                        <dl class="detail-grid">
                            <div><dt>Diagnóstico</dt><dd>{{ $atencion->diagnostico }}</dd></div>
                            <div><dt>Procedimiento</dt><dd>{{ $atencion->procedimiento }}</dd></div>
                            <div><dt>Observaciones</dt><dd>{{ $atencion->observaciones ?: '—' }}</dd></div>
                            <div><dt>Materiales usados</dt><dd>{{ $atencion->materiales->map(fn ($m) => $m->producto->nombre.' ×'.$m->cantidad)->join(', ') ?: '—' }}</dd></div>
                        </dl>
                        <footer>
                            @if ($atencion->ruta_analisis_radiografico)
                                <a class="icon-button" href="{{ route('atenciones.radiografia', $atencion) }}" target="_blank">Ver radiografía</a>
                            @else
                                <span class="atencion-meta">Sin radiografía</span>
                            @endif
                        </footer>
                    </article>
                @empty
                    <div class="panel empty-state">El paciente aún no tiene atenciones registradas.</div>
                @endforelse
            </div>
        </div>

        <div><a class="button button-secondary" href="{{ url()->previous() === url()->current() ? route('historial.index') : url()->previous() }}">Volver</a></div>
    </div>
@endsection
