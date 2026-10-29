@extends('layouts.app')

@section('title', 'Registrar compra')
@section('section', 'Compras')
@section('heading', 'Registrar compra')

@php($filas = old('detalles', [['producto_id' => '', 'cantidad' => 1, 'costo_unitario' => '']]))

@section('content')
    <div class="form-card">
        <div class="form-intro"><h2>Registrar compra</h2><p>Al guardar, el stock de cada producto aumentará con la cantidad comprada.</p></div>
        <form method="POST" action="{{ route('compras.store') }}">
            @csrf
            <div class="form-grid">
                <div class="field"><label for="proveedor_id">Proveedor</label><select id="proveedor_id" name="proveedor_id" required><option value="">Selecciona un proveedor</option>@foreach ($proveedores as $proveedor)<option value="{{ $proveedor->id }}" @selected((string) old('proveedor_id') === (string) $proveedor->id)>{{ $proveedor->nombre }}</option>@endforeach</select><x-error field="proveedor_id" /></div>
                <div class="field"><label for="fecha">Fecha</label><input id="fecha" name="fecha" type="date" max="{{ now()->toDateString() }}" value="{{ old('fecha', now()->toDateString()) }}" required><x-error field="fecha" /></div>
            </div>

            <h3 class="section-title">Productos comprados</h3>
            <div class="item-rows" id="detalles">
                @foreach ($filas as $i => $fila)
                    <div class="item-row item-row-compra">
                        <select name="detalles[{{ $i }}][producto_id]" aria-label="Producto">
                            <option value="">Selecciona un producto</option>
                            @foreach ($productos as $producto)
                                <option value="{{ $producto->id }}" @selected((string) ($fila['producto_id'] ?? '') === (string) $producto->id)>{{ $producto->codigo }} · {{ $producto->nombre }}</option>
                            @endforeach
                        </select>
                        <input name="detalles[{{ $i }}][cantidad]" type="number" min="1" value="{{ $fila['cantidad'] ?? 1 }}" aria-label="Cantidad" placeholder="Cantidad">
                        <input name="detalles[{{ $i }}][costo_unitario]" type="number" min="0" step="0.01" value="{{ $fila['costo_unitario'] ?? '' }}" aria-label="Costo unitario (Bs)" placeholder="Costo unit. (Bs)">
                        <button class="icon-button danger" type="button" data-quitar>Quitar</button>
                    </div>
                @endforeach
            </div>
            <x-error field="detalles" />
            @foreach ($errors->get('detalles.*') as $mensajes)
                <small class="field-error">{{ $mensajes[0] }}</small>
            @endforeach
            <p><button class="button button-secondary" type="button" id="agregar-detalle">＋ Agregar producto</button> <strong style="margin-left:12px">Total: Bs <span id="total">0.00</span></strong></p>

            <div class="form-actions"><a class="button button-secondary" href="{{ route('compras.index') }}">Cancelar</a><button class="button button-primary" type="submit">Registrar compra</button></div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
(() => {
    const contenedor = document.getElementById('detalles');
    const total = document.getElementById('total');
    let indice = contenedor.children.length;

    function calcularTotal() {
        let suma = 0;
        contenedor.querySelectorAll('.item-row').forEach((fila) => {
            const [cantidad, costo] = fila.querySelectorAll('input');
            if (fila.querySelector('select').value) suma += (Number(cantidad.value) || 0) * (Number(costo.value) || 0);
        });
        total.textContent = suma.toFixed(2);
    }

    document.getElementById('agregar-detalle').addEventListener('click', () => {
        const fila = contenedor.firstElementChild.cloneNode(true);
        fila.querySelectorAll('select, input').forEach((campo) => {
            campo.name = campo.name.replace(/\[\d+\]/, `[${indice}]`);
            campo.value = campo.name.endsWith('[cantidad]') ? 1 : '';
        });
        contenedor.appendChild(fila);
        indice++;
    });

    contenedor.addEventListener('click', (evento) => {
        if (!evento.target.matches('[data-quitar]')) return;
        const fila = evento.target.closest('.item-row');
        if (contenedor.children.length > 1) fila.remove();
        else fila.querySelector('select').value = '';
        calcularTotal();
    });

    contenedor.addEventListener('input', calcularTotal);
    contenedor.addEventListener('change', calcularTotal);
    calcularTotal();
})();
</script>
@endpush
