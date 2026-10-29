@extends('layouts.app')

@section('title', $secretaria ? 'Editar secretaria' : 'Nueva secretaria')
@section('section', 'Secretarias')
@section('heading', $secretaria ? 'Editar secretaria' : 'Nueva secretaria')

@section('content')
    <div class="form-card">
        <div class="form-intro"><h2>{{ $secretaria ? 'Editar datos de la secretaria' : 'Registrar secretaria' }}</h2><p>Datos personales y cuenta de acceso al sistema.</p></div>
        <form method="POST" action="{{ $secretaria ? route('secretarias.update', $secretaria) : route('secretarias.store') }}">
            @csrf
            @if ($secretaria) @method('PUT') @endif
            <div class="form-grid">
                <div class="field"><label for="nombre">Nombre</label><input id="nombre" name="nombre" value="{{ old('nombre', $secretaria?->nombre) }}" required maxlength="80"><x-error field="nombre" /></div>
                <div class="field"><label for="apellido">Apellido</label><input id="apellido" name="apellido" value="{{ old('apellido', $secretaria?->apellido) }}" required maxlength="80"><x-error field="apellido" /></div>
                <div class="field"><label for="ci">CI</label><input id="ci" name="ci" value="{{ old('ci', $secretaria?->ci) }}" required maxlength="20"><x-error field="ci" /></div>
                <div class="field"><label for="telefono">Teléfono</label><input id="telefono" name="telefono" value="{{ old('telefono', $secretaria?->telefono) }}" required maxlength="30"><x-error field="telefono" /></div>
                <div class="field field-wide"><label for="turno">Turno</label><select id="turno" name="turno" required>@foreach ($turnos as $turno)<option value="{{ $turno }}" @selected(old('turno', $secretaria?->turno) === $turno)>{{ $turno }}</option>@endforeach</select><x-error field="turno" /></div>
                <div class="field"><label for="usuario">Usuario</label><input id="usuario" name="usuario" value="{{ old('usuario', $secretaria?->user->usuario) }}" required maxlength="50" autocomplete="off"><x-error field="usuario" /></div>
                <div class="field"><label for="password">Contraseña</label><input id="password" name="password" type="password" minlength="6" {{ $secretaria ? '' : 'required' }} autocomplete="new-password">@if ($secretaria)<small class="field-help">Déjala vacía para mantener la actual.</small>@endif<x-error field="password" /></div>
            </div>
            <div class="form-actions"><a class="button button-secondary" href="{{ route('secretarias.index') }}">Cancelar</a><button class="button button-primary" type="submit">{{ $secretaria ? 'Guardar cambios' : 'Guardar' }}</button></div>
        </form>
    </div>
@endsection
