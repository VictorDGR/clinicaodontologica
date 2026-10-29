@extends('layouts.app')

@section('title', 'Proveedores')
@section('section', 'Proveedores')
@section('heading', 'Proveedores')

@section('content')
    <div class="panel table-panel">
        <div class="toolbar">
            <form class="search-form" method="GET" action="{{ route('proveedores.index') }}">
                <label class="sr-only" for="buscar">Buscar proveedores</label>
                <span class="search-icon">⌕</span>
                <input id="buscar" name="buscar" type="search" value="{{ $buscar }}" placeholder="Buscar por nombre o NIT...">
            </form>
            <a class="button button-primary" href="{{ route('proveedores.create') }}">＋ Nuevo proveedor</a>
        </div>
        <div class="table-scroll">
            <table>
                <thead><tr><th>Nombre</th><th>NIT</th><th>Teléfono</th><th class="text-right">Productos</th><th>Acciones</th></tr></thead>
                <tbody>
                @forelse ($proveedores as $proveedor)
                    <tr>
                        <td>{{ $proveedor->nombre }}</td>
                        <td>{{ $proveedor->nit }}</td>
                        <td>{{ $proveedor->telefono }}</td>
                        <td class="text-right">{{ $proveedor->productos_count }}</td>
                        <td><div class="action-group">
                            <a class="icon-button" href="{{ route('proveedores.edit', $proveedor) }}">Editar</a>
                            <form method="POST" action="{{ route('proveedores.destroy', $proveedor) }}" onsubmit="return confirm('¿Eliminar este proveedor?')">
                                @csrf @method('DELETE')
                                <button class="icon-button danger" type="submit">Eliminar</button>
                            </form>
                        </div></td>
                    </tr>
                @empty
                    <tr><td class="empty-state" colspan="5">{{ $buscar ? 'No se encontraron proveedores.' : 'Aún no hay proveedores registrados.' }}</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="table-footer"><span>Mostrando {{ $proveedores->count() }} {{ $proveedores->count() === 1 ? 'registro' : 'registros' }}</span></div>
    </div>
@endsection
