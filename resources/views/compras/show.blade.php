@extends('layouts.app')

@section('title', 'Compra N.º '.$compra->id)
@section('section', 'Compras')
@section('heading', 'Compra N.º '.$compra->id)

@section('content')
    <div class="stack">
        <div class="form-card detail-card">
            <dl class="detail-grid">
                <div><dt>Proveedor</dt><dd>{{ $compra->proveedor->nombre }} · NIT {{ $compra->proveedor->nit }}</dd></div>
                <div><dt>Fecha</dt><dd>{{ \Illuminate\Support\Carbon::parse($compra->fecha)->format('d/m/Y') }}</dd></div>
                <div><dt>Total</dt><dd>Bs {{ number_format($compra->total, 2) }}</dd></div>
                <div><dt>Registrada por</dt><dd>{{ $compra->secretaria?->nombre_completo ?? 'Administrador' }}</dd></div>
            </dl>
        </div>
        <div class="panel table-panel">
            <div class="table-scroll">
                <table>
                    <thead><tr><th>Código</th><th>Producto</th><th class="text-right">Cantidad</th><th class="text-right">Costo unitario (Bs)</th><th class="text-right">Subtotal (Bs)</th></tr></thead>
                    <tbody>
                    @foreach ($compra->detalles as $detalle)
                        <tr>
                            <td>{{ $detalle->producto->codigo }}</td>
                            <td>{{ $detalle->producto->nombre }}</td>
                            <td class="text-right">{{ $detalle->cantidad }}</td>
                            <td class="text-right">{{ number_format($detalle->costo_unitario, 2) }}</td>
                            <td class="text-right">{{ number_format($detalle->cantidad * $detalle->costo_unitario, 2) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div><a class="button button-secondary" href="{{ route('compras.index') }}">Volver a compras</a></div>
    </div>
@endsection
