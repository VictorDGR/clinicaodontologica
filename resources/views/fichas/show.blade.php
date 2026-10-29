@extends('layouts.app')

@php($usuario = auth()->user())
@php($atencion = $ficha->atencion)

@section('title', 'Ficha N.º '.$ficha->id)
@section('section', $usuario->esDoctor() ? 'Mis fichas' : 'Fichas')
@section('heading', 'Detalle de ficha')

@section('content')
    <div class="stack">
        <div class="form-card detail-card">
            <div class="form-intro"><h2>Ficha N.º {{ $ficha->id }}</h2><p>Consulta asignada al paciente.</p></div>
            <dl class="detail-grid">
                <div><dt>Paciente</dt><dd>{{ $ficha->paciente->nombre_completo }} · CI {{ $ficha->paciente->ci }}</dd></div>
                <div><dt>Doctor</dt><dd>{{ $ficha->doctor->nombre_completo }} — {{ $ficha->doctor->especialidad }}</dd></div>
                <div><dt>Fecha</dt><dd>{{ \Illuminate\Support\Carbon::parse($ficha->fecha)->format('d/m/Y') }}</dd></div>
                <div><dt>Hora</dt><dd>{{ $ficha->hora }}</dd></div>
                <div><dt>Motivo</dt><dd>{{ $ficha->motivo }}</dd></div>
                <div><dt>Estado</dt><dd><span class="status status-{{ \Illuminate\Support\Str::slug($ficha->estado) }}">{{ $ficha->estado }}</span></dd></div>
                <div class="detail-wide"><dt>Asignada por</dt><dd>{{ $ficha->secretaria?->nombre_completo ?? 'Administrador' }}</dd></div>
            </dl>
            <div class="form-actions">
                <a class="button button-secondary" href="{{ route('fichas.index') }}">Volver</a>
                @if ($usuario->esDoctor() || $usuario->esAdministrador())
                    <a class="button button-secondary" href="{{ route('historial.show', $ficha->paciente_id) }}">Ver historial</a>
                    @if ($ficha->estado === 'Pendiente')
                        <a class="button button-primary" href="{{ route('atenciones.create', $ficha) }}">Registrar atención</a>
                    @endif
                @endif
                @if (! $usuario->esDoctor() && ! $atencion)
                    <a class="button button-primary" href="{{ route('fichas.edit', $ficha) }}">Editar ficha</a>
                @endif
            </div>
        </div>

        @if ($atencion)
            <div class="form-card detail-card">
                <div class="form-intro"><h2>Atención registrada</h2><p>Registrada el {{ \Illuminate\Support\Carbon::parse($atencion->fecha)->format('d/m/Y') }}.</p></div>
                <dl class="detail-grid">
                    <div><dt>Diente</dt><dd>{{ $atencion->tipo_diente }}</dd></div>
                    <div><dt>Diagnóstico</dt><dd>{{ $atencion->diagnostico }}</dd></div>
                    <div class="detail-wide"><dt>Procedimiento</dt><dd>{{ $atencion->procedimiento }}</dd></div>
                    <div class="detail-wide"><dt>Observaciones</dt><dd>{{ $atencion->observaciones ?: '—' }}</dd></div>
                    <div class="detail-wide"><dt>Materiales usados</dt><dd>{{ $atencion->materiales->map(fn ($m) => $m->producto->nombre.' ×'.$m->cantidad.' (Bs '.number_format($m->subtotal(), 2).')')->join(', ') ?: '—' }}</dd></div>
                    @if ($atencion->cobro)
                        <div><dt>Costo del procedimiento</dt><dd>Bs {{ number_format($atencion->costoProcedimiento(), 2) }}</dd></div>
                        <div><dt>Costo de materiales</dt><dd>Bs {{ number_format($atencion->totalMateriales(), 2) }}</dd></div>
                    @endif
                    <div><dt>Cobro generado</dt><dd>
                        @if ($atencion->cobro)
                            Bs {{ number_format($atencion->cobro->monto, 2) }} · <span class="status status-{{ \Illuminate\Support\Str::slug($atencion->cobro->estado) }}">{{ $atencion->cobro->estado }}</span>
                            @unless ($usuario->esDoctor()) · <a class="field-help" href="{{ route('cobros.show', $atencion->cobro) }}">ver cobro</a>@endunless
                        @else — @endif
                    </dd></div>
                    <div><dt>Radiografía</dt><dd>
                        @if ($atencion->ruta_analisis_radiografico && ($usuario->esDoctor() || $usuario->esAdministrador()))
                            <a class="icon-button" href="{{ route('atenciones.radiografia', $atencion) }}" target="_blank">Ver archivo</a>
                        @else
                            {{ $atencion->ruta_analisis_radiografico ? 'Adjunta' : '—' }}
                        @endif
                    </dd></div>
                </dl>
            </div>
        @endif
    </div>
@endsection
