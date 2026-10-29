@extends('layouts.app')

@section('title', 'Registrar atención')
@section('section', 'Atención')
@section('heading', 'Registrar atención')

@php($filas = old('materiales', [['producto_id' => '', 'cantidad' => 1]]))

@section('content')
    <div class="form-card">
        <div class="form-intro">
            <h2>Ficha N.º {{ $ficha->id }} · {{ $ficha->paciente->nombre_completo }}</h2>
            <p>{{ \Illuminate\Support\Carbon::parse($ficha->fecha)->format('d/m/Y') }} {{ $ficha->hora }} · Motivo: {{ $ficha->motivo }} · <a class="field-help" href="{{ route('historial.show', $ficha->paciente_id) }}" target="_blank">ver historial</a></p>
        </div>
        <form method="POST" action="{{ route('atenciones.store', $ficha) }}" enctype="multipart/form-data">
            @csrf
            <div class="form-grid">
                <div class="field"><label for="tipo_diente">Diente tratado</label><input id="tipo_diente" name="tipo_diente" value="{{ old('tipo_diente') }}" required maxlength="60" placeholder="Ej.: Molar 26"><x-error field="tipo_diente" /></div>
                <div class="field"><label for="diagnostico">Diagnóstico</label><input id="diagnostico" name="diagnostico" value="{{ old('diagnostico') }}" required maxlength="255"><x-error field="diagnostico" /></div>
                <div class="field field-wide"><label for="procedimiento">Procedimiento realizado</label><input id="procedimiento" name="procedimiento" value="{{ old('procedimiento') }}" required maxlength="255"><x-error field="procedimiento" /></div>
                <div class="field field-wide"><label for="observaciones">Observaciones</label><textarea id="observaciones" name="observaciones" maxlength="2000">{{ old('observaciones') }}</textarea><x-error field="observaciones" /></div>
                <div class="field"><label for="radiografia">Análisis radiográfico (opcional)</label><input id="radiografia" name="radiografia" type="file" accept=".jpg,.jpeg,.png,.pdf"><small class="field-help">JPG, PNG o PDF de hasta 5 MB.</small><x-error field="radiografia" /></div>
                <div class="field"><label for="costo_procedimiento">Costo del procedimiento (Bs)</label><input id="costo_procedimiento" name="costo_procedimiento" type="number" min="0" step="0.01" value="{{ old('costo_procedimiento') }}" required><small class="field-help">Los materiales usados se suman aparte al cobro.</small><x-error field="costo_procedimiento" /></div>
            </div>

            <h3 class="section-title">Materiales usados</h3>
            <div class="item-rows" id="materiales">
                @foreach ($filas as $i => $fila)
                    <div class="item-row">
                        <select name="materiales[{{ $i }}][producto_id]" aria-label="Material">
                            <option value="">Sin material</option>
                            @foreach ($productos as $producto)
                                <option value="{{ $producto->id }}" data-precio="{{ $producto->precio }}" @selected((string) ($fila['producto_id'] ?? '') === (string) $producto->id)>{{ $producto->nombre }} · Bs {{ number_format($producto->precio, 2) }} c/u (stock: {{ $producto->stock }})</option>
                            @endforeach
                        </select>
                        <input name="materiales[{{ $i }}][cantidad]" type="number" min="1" value="{{ $fila['cantidad'] ?? 1 }}" aria-label="Cantidad">
                        <button class="icon-button danger" type="button" data-quitar>Quitar</button>
                    </div>
                @endforeach
            </div>
            <x-error field="materiales" />
            @foreach ($errors->get('materiales.*') as $mensajes)
                <small class="field-error">{{ $mensajes[0] }}</small>
            @endforeach
            <p><button class="button button-secondary" type="button" id="agregar-material">＋ Agregar material</button></p>
            @if ($productos->isEmpty())
                <small class="field-help">No hay productos con stock en el inventario.</small>
            @endif

            <dl class="detail-grid" style="margin-top:20px">
                <div><dt>Procedimiento</dt><dd>Bs <span id="total-procedimiento">0.00</span></dd></div>
                <div><dt>Materiales</dt><dd>Bs <span id="total-materiales">0.00</span></dd></div>
                <div class="detail-wide"><dt>Total a cobrar</dt><dd><strong>Bs <span id="total-cobro">0.00</span></strong></dd></div>
            </dl>

            <div class="form-actions"><a class="button button-secondary" href="{{ route('fichas.show', $ficha) }}">Cancelar</a><button class="button button-primary" type="submit">Guardar atención</button></div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
(() => {
    const contenedor = document.getElementById('materiales');
    const costo = document.getElementById('costo_procedimiento');
    let indice = contenedor.children.length;

    function calcularTotal() {
        let materiales = 0;
        contenedor.querySelectorAll('.item-row').forEach((fila) => {
            const opcion = fila.querySelector('select').selectedOptions[0];
            const cantidad = Number(fila.querySelector('input').value) || 0;
            materiales += (Number(opcion?.dataset.precio) || 0) * cantidad;
        });
        const procedimiento = Number(costo.value) || 0;
        document.getElementById('total-procedimiento').textContent = procedimiento.toFixed(2);
        document.getElementById('total-materiales').textContent = materiales.toFixed(2);
        document.getElementById('total-cobro').textContent = (procedimiento + materiales).toFixed(2);
    }

    document.getElementById('agregar-material').addEventListener('click', () => {
        const fila = contenedor.firstElementChild.cloneNode(true);
        fila.querySelectorAll('select, input').forEach((campo) => {
            campo.name = campo.name.replace(/\[\d+\]/, `[${indice}]`);
            campo.value = campo.tagName === 'INPUT' ? 1 : '';
        });
        contenedor.appendChild(fila);
        indice++;
    });

    contenedor.addEventListener('click', (evento) => {
        if (!evento.target.matches('[data-quitar]')) return;
        const fila = evento.target.closest('.item-row');
        if (contenedor.children.length > 1) {
            fila.remove();
        } else {
            fila.querySelector('select').value = '';
        }
        calcularTotal();
    });

    contenedor.addEventListener('input', calcularTotal);
    contenedor.addEventListener('change', calcularTotal);
    costo.addEventListener('input', calcularTotal);
    calcularTotal();
})();
</script>
@endpush
