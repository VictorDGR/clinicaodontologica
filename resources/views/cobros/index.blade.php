@extends('layouts.app')

@section('title', 'Cobros')
@section('section', 'Cobros')
@section('heading', 'Cobros')

@section('content')
    <div class="panel table-panel">
        <div class="toolbar">
            <form class="search-form" method="GET" action="{{ route('cobros.index') }}">
                <label class="sr-only" for="buscar">Buscar cobros</label>
                <span class="search-icon">⌕</span>
                <input type="hidden" name="estado" value="{{ $estado }}">
                <input id="buscar" name="buscar" type="search" value="{{ $buscar }}" placeholder="Buscar por paciente o CI...">
            </form>
            <div class="action-group">
                @foreach (['pendientes' => 'Pendientes', 'pagados' => 'Pagados', 'todos' => 'Todos'] as $valor => $etiqueta)
                    <a class="button {{ $estado === $valor ? 'button-primary' : 'button-secondary' }}" href="{{ route('cobros.index', ['estado' => $valor, 'buscar' => $buscar ?: null]) }}">{{ $etiqueta }}</a>
                @endforeach
            </div>
        </div>
        <div class="table-scroll">
            <table>
                <thead><tr><th>N.º</th><th>Fecha</th><th>Paciente</th><th class="text-right">Monto (Bs)</th><th class="text-right">Pagado (Bs)</th><th class="text-right">Saldo (Bs)</th><th>Estado</th><th>Acciones</th></tr></thead>
                <tbody>
                @forelse ($cobros as $cobro)
                    <tr>
                        <td>{{ $cobro->id }}</td>
                        <td>{{ \Illuminate\Support\Carbon::parse($cobro->fecha)->format('d/m/Y') }}</td>
                        <td>{{ $cobro->atencion->ficha->paciente->nombre_completo }}</td>
                        <td class="text-right">{{ number_format($cobro->monto, 2) }}</td>
                        <td class="text-right">{{ number_format($cobro->pagado, 2) }}</td>
                        <td class="text-right"><strong>{{ number_format($cobro->saldo, 2) }}</strong></td>
                        <td><span class="status status-{{ \Illuminate\Support\Str::slug($cobro->estado) }}">{{ $cobro->estado }}</span></td>
                        <td><a class="icon-button" href="{{ route('cobros.show', $cobro) }}">{{ $cobro->estado === 'Pagado' ? 'Ver' : 'Registrar pago' }}</a></td>
                    </tr>
                @empty
                    <tr><td class="empty-state" colspan="8">{{ $estado === 'pendientes' ? 'No hay cobros pendientes.' : 'No se encontraron cobros.' }}</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="table-footer"><span>Mostrando {{ $cobros->count() }} {{ $cobros->count() === 1 ? 'registro' : 'registros' }}</span></div>
    </div>
@endsection
