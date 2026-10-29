@extends('layouts.app')

@section('title', 'Inventario')
@section('section', 'Inventario')
@section('heading', 'Inventario')

@section('content')
    @if ($stockBajo > 0)
        <div class="alert alert-error" role="alert">Hay {{ $stockBajo }} {{ $stockBajo === 1 ? 'producto' : 'productos' }} con stock bajo ({{ \App\Models\Producto::STOCK_MINIMO }} unidades o menos). Registra una compra para reponerlos.</div>
    @endif
    <div class="panel table-panel">
        <div class="toolbar">
            <div class="panel-heading" style="margin:0"><h2>Stock actual</h2><p>Aumenta con las compras y se descuenta con los materiales usados en las atenciones.</p></div>
            <a class="button button-primary" href="{{ route('compras.create') }}">＋ Registrar compra</a>
        </div>
        <div class="table-scroll">
            <table>
                <thead><tr><th>Código</th><th>Producto</th><th>Proveedor</th><th class="text-right">Stock</th><th>Estado</th></tr></thead>
                <tbody>
                @forelse ($productos as $producto)
                    <tr>
                        <td>{{ $producto->codigo }}</td>
                        <td>{{ $producto->nombre }}</td>
                        <td>{{ $producto->proveedor->nombre }}</td>
                        <td class="text-right"><strong>{{ $producto->stock }}</strong></td>
                        <td>@if ($producto->stockBajo())<span class="status status-bajo">Stock bajo</span>@else<span class="status status-ok">Disponible</span>@endif</td>
                    </tr>
                @empty
                    <tr><td class="empty-state" colspan="5">Aún no hay productos registrados.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
