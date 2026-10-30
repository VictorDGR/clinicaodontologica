@extends('layouts.app')

@section('title', 'Registrar compra')
@section('section', 'Compras')
@section('heading', 'Registrar compra')

@php($filas = old('detalles', [['producto_id' => '', 'cantidad' => 1]]))

@section('content')
    <div class="form-card">
        <div class="form-intro"><h2>Registrar compra</h2><p>Elija el proveedor para ver sus productos. Al guardar, el stock de cada producto aumentará con la cantidad comprada.</p></div>
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
                        <select name="detalles[{{ $i }}][producto_id]" aria-label="Producto" data-seleccionado="{{ $fila['producto_id'] ?? '' }}"></select>
                        <input name="detalles[{{ $i }}][cantidad]" type="number" min="1" value="{{ $fila['cantidad'] ?? 1 }}" aria-label="Cantidad" placeholder="Cantidad">
                        <span class="item-precio">—</span>
                        <button class="icon-button danger" type="button" data-quitar>Quitar</button>
                    </div>
                @endforeach
            </div>
            <x-error field="detalles" />
            @foreach ($errors->get('detalles.*') as $mensajes)
                <small class="field-error">{{ $mensajes[0] }}</small>
            @endforeach
            <small class="field-help" id="aviso-productos"></small>
            <p><button class="button button-secondary" type="button" id="agregar-detalle">＋ Agregar producto</button> <strong style="margin-left:12px">Total: Bs <span id="total">0.00</span></strong></p>

            <div class="form-actions"><a class="button button-secondary" href="{{ route('compras.index') }}">Cancelar</a><button class="button button-primary" type="submit">Registrar compra</button></div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
(() => {
    const productos = @json($productos);
    const proveedor = document.getElementById('proveedor_id');
    const contenedor = document.getElementById('detalles');
    const total = document.getElementById('total');
    const aviso = document.getElementById('aviso-productos');
    let indice = contenedor.children.length;

    const delProveedor = () => productos.filter((p) => String(p.proveedor_id) === proveedor.value);
    const buscar = (id) => productos.find((p) => String(p.id) === String(id));

    // Llena el selector de una fila solo con los productos del proveedor elegido.
    function llenarSelector(select, seleccionado) {
        const lista = delProveedor();
        select.replaceChildren(new Option(proveedor.value ? 'Selecciona un producto' : 'Primero elige un proveedor', ''));
        lista.forEach((p) => select.add(new Option(`${p.codigo} · ${p.nombre}`, p.id)));
        select.value = lista.some((p) => String(p.id) === String(seleccionado)) ? seleccionado : '';
        select.disabled = !proveedor.value;
    }

    function calcular() {
        let suma = 0;
        contenedor.querySelectorAll('.item-row').forEach((fila) => {
            const producto = buscar(fila.querySelector('select').value);
            const cantidad = Number(fila.querySelector('input').value) || 0;
            const precio = fila.querySelector('.item-precio');
            if (producto) {
                const unitario = Number(producto.precio);
                const subtotal = unitario * cantidad;
                precio.textContent = `Bs ${unitario.toFixed(2)} c/u · Bs ${subtotal.toFixed(2)}`;
                suma += subtotal;
            } else {
                precio.textContent = '—';
            }
        });
        total.textContent = suma.toFixed(2);
    }

    function actualizarProveedor() {
        contenedor.querySelectorAll('select').forEach((select) => llenarSelector(select, select.value || select.dataset.seleccionado));
        aviso.textContent = proveedor.value && delProveedor().length === 0
            ? 'Este proveedor aún no tiene productos registrados.'
            : '';
        calcular();
    }

    proveedor.addEventListener('change', actualizarProveedor);

    document.getElementById('agregar-detalle').addEventListener('click', () => {
        const fila = contenedor.firstElementChild.cloneNode(true);
        fila.querySelectorAll('select, input').forEach((campo) => {
            campo.name = campo.name.replace(/\[\d+\]/, `[${indice}]`);
        });
        fila.querySelector('input').value = 1;
        llenarSelector(fila.querySelector('select'), '');
        contenedor.appendChild(fila);
        indice++;
        calcular();
    });

    contenedor.addEventListener('click', (evento) => {
        if (!evento.target.matches('[data-quitar]')) return;
        const fila = evento.target.closest('.item-row');
        if (contenedor.children.length > 1) fila.remove();
        else fila.querySelector('select').value = '';
        calcular();
    });

    contenedor.addEventListener('input', calcular);
    contenedor.addEventListener('change', calcular);
    actualizarProveedor();
})();
</script>
@endpush
