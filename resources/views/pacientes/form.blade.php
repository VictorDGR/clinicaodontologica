@extends('layouts.app')

@section('title', $paciente ? 'Editar paciente' : 'Nuevo paciente')
@section('section', 'Pacientes')
@section('heading', $paciente ? 'Editar paciente' : 'Nuevo paciente')

@section('content')
    <div class="form-card">
        <div class="form-intro"><h2>{{ $paciente ? 'Editar datos del paciente' : 'Registrar paciente' }}</h2><p>Complete los campos solicitados para guardar el registro.</p></div>
        <form method="POST" action="{{ $paciente ? route('pacientes.update', $paciente) : route('pacientes.store') }}" novalidate>
            @csrf
            @if ($paciente) @method('PUT') @endif
            <div class="form-grid">
                <div class="field"><label for="nombre">Nombre</label><input id="nombre" name="nombre" value="{{ old('nombre', $paciente?->nombre) }}" required maxlength="80"><x-error field="nombre" /></div>
                <div class="field"><label for="apellido">Apellido</label><input id="apellido" name="apellido" value="{{ old('apellido', $paciente?->apellido) }}" required maxlength="80"><x-error field="apellido" /></div>
                <div class="field"><label for="ci">CI</label><input id="ci" name="ci" value="{{ old('ci', $paciente?->ci) }}" required maxlength="20" inputmode="numeric"><x-error field="ci" /></div>
                <div class="field"><label for="telefono">Teléfono</label><input id="telefono" name="telefono" value="{{ old('telefono', $paciente?->telefono) }}" required maxlength="30"><x-error field="telefono" /></div>
                <div class="field"><label for="fecha_nacimiento">Fecha de nacimiento</label><input id="fecha_nacimiento" name="fecha_nacimiento" type="date" max="{{ now()->toDateString() }}" value="{{ old('fecha_nacimiento', $paciente?->fecha_nacimiento) }}" required><x-error field="fecha_nacimiento" /></div>
                <div class="field"><label for="direccion">Dirección</label><input id="direccion" name="direccion" value="{{ old('direccion', $paciente?->direccion) }}" required maxlength="255"><x-error field="direccion" /></div>
            </div>
            <div class="form-actions"><a class="button button-secondary" href="{{ route('pacientes.index') }}">Cancelar</a><button class="button button-primary" type="submit">{{ $paciente ? 'Guardar cambios' : 'Guardar' }}</button></div>
        </form>
    </div>
@endsection
