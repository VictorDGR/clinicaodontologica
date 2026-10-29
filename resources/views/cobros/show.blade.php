@extends('layouts.app')

@php($ficha = $cobro->atencion->ficha)

@section('title', 'Cobro N.º '.$cobro->id)
@section('section', 'Cobros')
@section('heading', 'Cobro N.º '.$cobro->id)

@section('content')
    <div class="stack">
        <div class="stats-grid" style="margin:0">
            <div class="stat-card"><span class="stat-icon">$</span><span class="stat-label">Monto total</span><strong>Bs {{ number_format($cobro->monto, 2) }}</strong><small>{{ \Illuminate\Support\Carbon::parse($cobro->fecha)->format('d/m/Y') }}</small></div>
            <div class="stat-card"><span class="stat-icon">✓</span><span class="stat-label">Pagado</span><strong>Bs {{ number_format($cobro->pagado, 2) }}</strong><small>{{ $cobro->pagos->count() }} pago(s)</small></div>
            <div class="stat-card"><span class="stat-icon">◷</span><span class="stat-label">Saldo pendiente</span><strong>Bs {{ number_format($cobro->saldo, 2) }}</strong><small><span class="status status-{{ \Illuminate\Support\Str::slug($cobro->estado) }}">{{ $cobro->estado }}</span></small></div>
        </div>

        <div class="form-card detail-card">
            <div class="form-intro"><h2>Detalle de la atención</h2><p>Ficha N.º {{ $ficha->id }}</p></div>
            <dl class="detail-grid">
                <div><dt>Paciente</dt><dd>{{ $ficha->paciente->nombre_completo }} · CI {{ $ficha->paciente->ci }}</dd></div>
                <div><dt>Doctor</dt><dd>{{ $cobro->atencion->doctor->nombre_completo }}</dd></div>
                <div><dt>Diente</dt><dd>{{ $cobro->atencion->tipo_diente }}</dd></div>
                <div><dt>Procedimiento</dt><dd>{{ $cobro->atencion->procedimiento }}</dd></div>
            </dl>
        </div>

        <div class="panel table-panel">
            <div class="toolbar"><div class="panel-heading" style="margin:0"><h2>Detalle del cobro</h2><p>Costo del procedimiento más los materiales usados.</p></div></div>
            <div class="table-scroll">
                <table>
                    <thead><tr><th>Concepto</th><th class="text-right">Cantidad</th><th class="text-right">Precio unitario (Bs)</th><th class="text-right">Subtotal (Bs)</th></tr></thead>
                    <tbody>
                        <tr>
                            <td>Procedimiento: {{ $cobro->atencion->procedimiento }}</td>
                            <td class="text-right">1</td>
                            <td class="text-right">{{ number_format($cobro->atencion->costoProcedimiento(), 2) }}</td>
                            <td class="text-right">{{ number_format($cobro->atencion->costoProcedimiento(), 2) }}</td>
                        </tr>
                        @foreach ($cobro->atencion->materiales as $material)
                            <tr>
                                <td>Material: {{ $material->producto->nombre }}</td>
                                <td class="text-right">{{ $material->cantidad }}</td>
                                <td class="text-right">{{ number_format($material->precio_unitario, 2) }}</td>
                                <td class="text-right">{{ number_format($material->subtotal(), 2) }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="3" class="text-right"><strong>Total</strong></td>
                            <td class="text-right"><strong>{{ number_format($cobro->monto, 2) }}</strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        @if ($cobro->saldo > 0)
            <div class="form-card">
                <div class="form-intro"><h2>Registrar pago</h2><p>Saldo pendiente: Bs {{ number_format($cobro->saldo, 2) }}</p></div>
                <form method="POST" action="{{ route('cobros.pagar', $cobro) }}">
                    @csrf
                    <div class="form-grid">
                        <div class="field"><label for="monto">Monto pagado (Bs)</label><input id="monto" name="monto" type="number" min="0.01" max="{{ $cobro->saldo }}" step="0.01" value="{{ old('monto', $cobro->saldo) }}" required><x-error field="monto" /></div>
                        <div class="field"><label for="fecha">Fecha de pago</label><input id="fecha" name="fecha" type="date" max="{{ now()->toDateString() }}" value="{{ old('fecha', now()->toDateString()) }}" required><x-error field="fecha" /></div>
                        <div class="field"><label for="metodo">Método de pago</label><select id="metodo" name="metodo" required>@foreach (\App\Models\Cobro::METODOS as $metodo)<option value="{{ $metodo }}" @selected(old('metodo') === $metodo)>{{ $metodo }}</option>@endforeach</select><x-error field="metodo" /></div>
                    </div>
                    <div class="form-actions"><button class="button button-primary" type="submit">Registrar pago</button></div>
                </form>
            </div>
        @endif

        <div class="panel table-panel">
            <div class="toolbar"><div class="panel-heading" style="margin:0"><h2>Pagos realizados</h2></div></div>
            <div class="table-scroll">
                <table>
                    <thead><tr><th>Fecha</th><th>Método</th><th class="text-right">Monto (Bs)</th><th>Registrado por</th></tr></thead>
                    <tbody>
                    @forelse ($cobro->pagos as $pago)
                        <tr>
                            <td>{{ \Illuminate\Support\Carbon::parse($pago->fecha)->format('d/m/Y') }}</td>
                            <td>{{ $pago->metodo }}</td>
                            <td class="text-right">{{ number_format($pago->monto, 2) }}</td>
                            <td>{{ $pago->secretaria?->nombre_completo ?? 'Administrador' }}</td>
                        </tr>
                    @empty
                        <tr><td class="empty-state" colspan="4">Aún no se registraron pagos.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div><a class="button button-secondary" href="{{ route('cobros.index') }}">Volver a cobros</a></div>
    </div>
@endsection
