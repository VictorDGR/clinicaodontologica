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

        <div class="panel table-panel">
            <div class="toolbar"><div class="panel-heading" style="margin:0"><h2>Atenciones</h2><p>Revisiones registradas, de la más reciente a la más antigua.</p></div></div>
            <div class="table-scroll">
                <table>
                    <thead><tr><th>Fecha</th><th>Doctor</th><th>Diente</th><th>Diagnóstico</th><th>Procedimiento</th><th>Observaciones</th><th>Materiales</th><th>Radiografía</th></tr></thead>
                    <tbody>
                    @forelse ($registros as $registro)
                        @php($atencion = $registro->atencion)
                        <tr>
                            <td>{{ $registro->fecha->format('d/m/Y H:i') }}</td>
                            <td>{{ $atencion->doctor->nombre_completo }}</td>
                            <td>{{ $atencion->tipo_diente }}</td>
                            <td style="white-space:normal">{{ $atencion->diagnostico }}</td>
                            <td style="white-space:normal">{{ $atencion->procedimiento }}</td>
                            <td style="white-space:normal">{{ $atencion->observaciones ?: '—' }}</td>
                            <td style="white-space:normal">{{ $atencion->materiales->map(fn ($m) => $m->producto->nombre.' ×'.$m->cantidad)->join(', ') ?: '—' }}</td>
                            <td>@if ($atencion->ruta_analisis_radiografico)<a class="icon-button" href="{{ route('atenciones.radiografia', $atencion) }}" target="_blank">Ver</a>@else — @endif</td>
                        </tr>
                    @empty
                        <tr><td class="empty-state" colspan="8">El paciente aún no tiene atenciones registradas.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div><a class="button button-secondary" href="{{ url()->previous() === url()->current() ? route('historial.index') : url()->previous() }}">Volver</a></div>
    </div>
@endsection
