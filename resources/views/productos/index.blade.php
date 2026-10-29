@extends('layouts.app')

@section('title', 'Productos')
@section('section', 'Productos')
@section('heading', 'Productos')

@section('content')
    <div class="panel table-panel">
        <div class="toolbar">
            <form class="search-form" method="GET" action="{{ route('productos.index') }}">
                <label class="sr-only" for="buscar">Buscar productos</label>
                <span class="search-icon">⌕</span>
                <input id="buscar" name="buscar" type="search" value="{{ $buscar }}" placeholder="Buscar por código o nombre...">
            </form>
            <a class="button button-primary" href="{{ route('productos.create') }}">＋ Nuevo producto</a>
        </div>
        <div class="table-scroll">
            <table>
                <thead><tr><th>Código</th><th>Nombre</th><th>Proveedor</th><th class="text-right">Precio (Bs)</th><th class="text-right">Stock</th><th>Acciones</th></tr></thead>
                <tbody>
                @forelse ($productos as $producto)
                    <tr>
                        <td>{{ $producto->codigo }}</td>
                        <td>{{ $producto->nombre }}</td>
                        <td>{{ $producto->proveedor->nombre }}</td>
                        <td class="text-right">{{ number_format($producto->precio, 2) }}</td>
                        <td class="text-right">{{ $producto->stock }}</td>
                        <td><div class="action-group">
                            <a class="icon-button" href="{{ route('productos.edit', $producto) }}">Editar</a>
                            <form method="POST" action="{{ route('productos.destroy', $producto) }}" onsubmit="return confirm('¿Eliminar este producto?')">
                                @csrf @method('DELETE')
                                <button class="icon-button danger" type="submit">Eliminar</button>
                            </form>
                        </div></td>
                    </tr>
                @empty
                    <tr><td class="empty-state" colspan="6">{{ $buscar ? 'No se encontraron productos.' : 'Aún no hay productos registrados.' }}</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="table-footer"><span>Mostrando {{ $productos->count() }} {{ $productos->count() === 1 ? 'registro' : 'registros' }}</span></div>
    </div>
@endsection
