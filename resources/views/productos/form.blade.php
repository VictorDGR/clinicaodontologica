@extends('layouts.app')

@section('title', $producto ? 'Editar producto' : 'Nuevo producto')
@section('section', 'Productos')
@section('heading', $producto ? 'Editar producto' : 'Nuevo producto')

@section('content')
    <div class="form-card">
        <div class="form-intro"><h2>{{ $producto ? 'Editar producto' : 'Registrar producto' }}</h2><p>{{ $producto ? 'El stock se actualiza con las compras y las atenciones.' : 'Indique el stock inicial del producto.' }}</p></div>
        <form method="POST" action="{{ $producto ? route('productos.update', $producto) : route('productos.store') }}">
            @csrf
            @if ($producto) @method('PUT') @endif
            <div class="form-grid">
                <div class="field"><label for="codigo">Código</label><input id="codigo" name="codigo" value="{{ old('codigo', $producto?->codigo) }}" required maxlength="30"><x-error field="codigo" /></div>
                <div class="field"><label for="nombre">Nombre</label><input id="nombre" name="nombre" value="{{ old('nombre', $producto?->nombre) }}" required maxlength="120"><x-error field="nombre" /></div>
                <div class="field"><label for="precio">Precio (Bs)</label><input id="precio" name="precio" type="number" min="0" step="0.01" value="{{ old('precio', $producto?->precio) }}" required><x-error field="precio" /></div>
                @if ($producto)
                    <div class="field"><label>Stock actual</label><input value="{{ $producto->stock }}" disabled></div>
                @else
                    <div class="field"><label for="stock">Stock inicial</label><input id="stock" name="stock" type="number" min="0" value="{{ old('stock', 0) }}" required><x-error field="stock" /></div>
                @endif
                <div class="field field-wide"><label for="proveedor_id">Proveedor</label><select id="proveedor_id" name="proveedor_id" required><option value="">Selecciona un proveedor</option>@foreach ($proveedores as $proveedor)<option value="{{ $proveedor->id }}" @selected((string) old('proveedor_id', $producto?->proveedor_id) === (string) $proveedor->id)>{{ $proveedor->nombre }} · NIT {{ $proveedor->nit }}</option>@endforeach</select><x-error field="proveedor_id" />@if ($proveedores->isEmpty())<small class="field-help">Primero debes <a href="{{ route('proveedores.create') }}">registrar un proveedor</a>.</small>@endif</div>
            </div>
            <div class="form-actions"><a class="button button-secondary" href="{{ route('productos.index') }}">Cancelar</a><button class="button button-primary" type="submit">{{ $producto ? 'Guardar cambios' : 'Guardar' }}</button></div>
        </form>
    </div>
@endsection
