@extends('layouts.app')

@section('title', 'Detalle del paciente')
@section('section', 'Pacientes')
@section('heading', 'Detalle del paciente')

@section('content')
    <div class="form-card detail-card">
        <div class="form-intro"><h2>{{ $paciente->nombre_completo }}</h2><p>Información del paciente registrado.</p></div>
        <dl class="detail-grid">
            <div><dt>Nombre</dt><dd>{{ $paciente->nombre }}</dd></div>
            <div><dt>Apellido</dt><dd>{{ $paciente->apellido }}</dd></div>
            <div><dt>CI</dt><dd>{{ $paciente->ci }}</dd></div>
            <div><dt>Teléfono</dt><dd>{{ $paciente->telefono }}</dd></div>
            <div><dt>Fecha de nacimiento</dt><dd>{{ \Illuminate\Support\Carbon::parse($paciente->fecha_nacimiento)->format('d/m/Y') }}</dd></div>
            <div><dt>Dirección</dt><dd>{{ $paciente->direccion }}</dd></div>
        </dl>
        <div class="form-actions">
            <a class="button button-secondary" href="{{ route('pacientes.index') }}">Volver</a>
            <a class="button button-primary" href="{{ route('pacientes.edit', $paciente) }}">Editar paciente</a>
        </div>
    </div>
@endsection
