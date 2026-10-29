@extends('layouts.app')

@section('title', $proveedor ? 'Editar proveedor' : 'Nuevo proveedor')
@section('section', 'Proveedores')
@section('heading', $proveedor ? 'Editar proveedor' : 'Nuevo proveedor')

@section('content')
    <div class="form-card">
        <div class="form-intro"><h2>{{ $proveedor ? 'Editar proveedor' : 'Registrar proveedor' }}</h2><p>Complete los campos solicitados para guardar el registro.</p></div>
        <form method="POST" action="{{ $proveedor ? route('proveedores.update', $proveedor) : route('proveedores.store') }}">
            @csrf
            @if ($proveedor) @method('PUT') @endif
            <div class="form-grid">
                <div class="field field-wide"><label for="nombre">Nombre</label><input id="nombre" name="nombre" value="{{ old('nombre', $proveedor?->nombre) }}" required maxlength="120"><x-error field="nombre" /></div>
                <div class="field"><label for="nit">NIT</label><input id="nit" name="nit" value="{{ old('nit', $proveedor?->nit) }}" required maxlength="20" inputmode="numeric"><x-error field="nit" /></div>
                <div class="field"><label for="telefono">Teléfono</label><input id="telefono" name="telefono" value="{{ old('telefono', $proveedor?->telefono) }}" required maxlength="30"><x-error field="telefono" /></div>
            </div>
            <div class="form-actions"><a class="button button-secondary" href="{{ route('proveedores.index') }}">Cancelar</a><button class="button button-primary" type="submit">{{ $proveedor ? 'Guardar cambios' : 'Guardar' }}</button></div>
        </form>
    </div>
@endsection
