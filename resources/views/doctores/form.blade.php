@extends('layouts.app')

@section('title', $doctor ? 'Editar doctor' : 'Nuevo doctor')
@section('section', 'Doctores')
@section('heading', $doctor ? 'Editar doctor' : 'Nuevo doctor')

@section('content')
    <div class="form-card">
        <div class="form-intro"><h2>{{ $doctor ? 'Editar datos del doctor' : 'Registrar doctor' }}</h2><p>Datos personales y cuenta de acceso al sistema.</p></div>
        <form method="POST" action="{{ $doctor ? route('doctores.update', $doctor) : route('doctores.store') }}">
            @csrf
            @if ($doctor) @method('PUT') @endif
            <div class="form-grid">
                <div class="field"><label for="nombre">Nombre</label><input id="nombre" name="nombre" value="{{ old('nombre', $doctor?->nombre) }}" required maxlength="80"><x-error field="nombre" /></div>
                <div class="field"><label for="apellido">Apellido</label><input id="apellido" name="apellido" value="{{ old('apellido', $doctor?->apellido) }}" required maxlength="80"><x-error field="apellido" /></div>
                <div class="field"><label for="ci">CI</label><input id="ci" name="ci" value="{{ old('ci', $doctor?->ci) }}" required maxlength="20"><x-error field="ci" /></div>
                <div class="field"><label for="telefono">Teléfono</label><input id="telefono" name="telefono" value="{{ old('telefono', $doctor?->telefono) }}" required maxlength="30"><x-error field="telefono" /></div>
                <div class="field"><label for="especialidad">Especialidad</label><input id="especialidad" name="especialidad" value="{{ old('especialidad', $doctor?->especialidad) }}" required maxlength="120"><x-error field="especialidad" /></div>
                <div class="field"><label for="matricula">Matrícula</label><input id="matricula" name="matricula" value="{{ old('matricula', $doctor?->matricula) }}" required maxlength="30"><x-error field="matricula" /></div>
                <div class="field"><label for="usuario">Usuario</label><input id="usuario" name="usuario" value="{{ old('usuario', $doctor?->user->usuario) }}" required maxlength="50" autocomplete="off"><x-error field="usuario" /></div>
                <div class="field"><label for="password">Contraseña</label><input id="password" name="password" type="password" minlength="6" {{ $doctor ? '' : 'required' }} autocomplete="new-password">@if ($doctor)<small class="field-help">Déjala vacía para mantener la actual.</small>@endif<x-error field="password" /></div>
            </div>
            <div class="form-actions"><a class="button button-secondary" href="{{ route('doctores.index') }}">Cancelar</a><button class="button button-primary" type="submit">{{ $doctor ? 'Guardar cambios' : 'Guardar' }}</button></div>
        </form>
    </div>
@endsection
