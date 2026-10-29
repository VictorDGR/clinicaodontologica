@extends('layouts.app')

@section('title', 'Compras')
@section('section', 'Compras')
@section('heading', 'Compras')

@section('content')
    <div class="panel table-panel">
        <div class="toolbar">
            <div class="panel-heading" style="margin:0"><h2>Compras a proveedores</h2><p>Cada compra registrada aumenta el stock del inventario.</p></div>
            <a class="button button-primary" href="{{ route('compras.create') }}">＋ Registrar compra</a>
        </div>
        <div class="table-scroll">
            <table>
                <thead><tr><th>N.º</th><th>Fecha</th><th>Proveedor</th><th class="text-right">Productos</th><th class="text-right">Total (Bs)</th><th>Estado</th><th>Acciones</th></tr></thead>
                <tbody>
                @forelse ($compras as $compra)
                    <tr>
                        <td>{{ $compra->id }}</td>
                        <td>{{ \Illuminate\Support\Carbon::parse($compra->fecha)->format('d/m/Y') }}</td>
                        <td>{{ $compra->proveedor->nombre }}</td>
                        <td class="text-right">{{ $compra->detalles_count }}</td>
                        <td class="text-right">{{ number_format($compra->total, 2) }}</td>
                        <td><span class="status status-ok">{{ $compra->estado }}</span></td>
                        <td><a class="icon-button" href="{{ route('compras.show', $compra) }}">Ver</a></td>
                    </tr>
                @empty
                    <tr><td class="empty-state" colspan="7">Aún no hay compras registradas.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="table-footer"><span>Mostrando {{ $compras->count() }} {{ $compras->count() === 1 ? 'registro' : 'registros' }}</span></div>
    </div>
@endsection
