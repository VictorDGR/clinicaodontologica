@extends('layouts.app')

@php($usuario = auth()->user())
@php($titulo = $usuario->esDoctor() ? 'Mis fichas' : 'Fichas')

@section('title', $titulo)
@section('section', $titulo)
@section('heading', $titulo)

@section('content')
    <div class="panel table-panel">
        <div class="toolbar">
            <form class="search-form" method="GET" action="{{ route('fichas.index') }}">
                <label class="sr-only" for="buscar">Buscar fichas</label>
                <span class="search-icon">⌕</span>
                <input id="buscar" name="buscar" type="search" value="{{ $buscar }}" placeholder="Buscar por paciente, doctor, fecha o estado...">
            </form>
            @unless ($usuario->esDoctor())
                <a class="button button-primary" href="{{ route('fichas.create') }}">＋ Asignar ficha</a>
            @endunless
        </div>
        <div class="table-scroll">
            <table>
                <thead><tr><th>N.º</th><th>Fecha</th><th>Hora</th><th>Paciente</th><th>Doctor</th><th>Motivo</th><th>Estado</th><th>Acciones</th></tr></thead>
                <tbody>
                @forelse ($fichas as $ficha)
                    <tr>
                        <td>{{ $ficha->id }}</td>
                        <td>{{ \Illuminate\Support\Carbon::parse($ficha->fecha)->format('d/m/Y') }}</td>
                        <td>{{ $ficha->hora }}</td>
                        <td>{{ $ficha->paciente->nombre_completo }}</td>
                        <td>{{ $ficha->doctor->nombre_completo }}</td>
                        <td>{{ $ficha->motivo }}</td>
                        <td><span class="status status-{{ \Illuminate\Support\Str::slug($ficha->estado) }}">{{ $ficha->estado }}</span></td>
                        <td><div class="action-group">
                            <a class="icon-button" href="{{ route('fichas.show', $ficha) }}">Ver</a>
                            @if (($usuario->esDoctor() || $usuario->esAdministrador()) && $ficha->estado === 'Pendiente')
                                <a class="icon-button" href="{{ route('atenciones.create', $ficha) }}">Atender</a>
                            @endif
                            @if ($usuario->esDoctor())
                                <a class="icon-button" href="{{ route('historial.show', $ficha->paciente_id) }}">Historial</a>
                            @endif
                            @if (! $usuario->esDoctor() && ! $ficha->atencion)
                                <a class="icon-button" href="{{ route('fichas.edit', $ficha) }}">Editar</a>
                                <form method="POST" action="{{ route('fichas.destroy', $ficha) }}" onsubmit="return confirm('¿Eliminar esta ficha?')">
                                    @csrf @method('DELETE')
                                    <button class="icon-button danger" type="submit">Eliminar</button>
                                </form>
                            @endif
                        </div></td>
                    </tr>
                @empty
                    <tr><td class="empty-state" colspan="8">{{ $buscar ? 'No se encontraron fichas.' : 'Aún no hay fichas registradas.' }}</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="table-footer"><span>Mostrando {{ $fichas->count() }} {{ $fichas->count() === 1 ? 'registro' : 'registros' }}</span></div>
    </div>
@endsection
