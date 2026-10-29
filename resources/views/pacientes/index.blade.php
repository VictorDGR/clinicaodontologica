@extends('layouts.app')

@section('title', 'Pacientes')
@section('section', 'Pacientes')
@section('heading', 'Pacientes')

@section('content')
    <div class="panel table-panel">
        <div class="toolbar">
            <form class="search-form" method="GET" action="{{ route('pacientes.index') }}">
                <label class="sr-only" for="buscar">Buscar pacientes</label>
                <span class="search-icon">⌕</span>
                <input id="buscar" name="buscar" type="search" value="{{ $buscar }}" placeholder="Buscar por CI, nombre o apellido...">
            </form>
            <a class="button button-primary" href="{{ route('pacientes.create') }}">＋ Nuevo paciente</a>
        </div>
        <div class="table-scroll">
            <table>
                <thead><tr><th>Nombre</th><th>Apellido</th><th>CI</th><th>Teléfono</th><th>Acciones</th></tr></thead>
                <tbody>
                @forelse ($pacientes as $paciente)
                    <tr>
                        <td><span class="person-cell"><span class="avatar avatar-teal">{{ mb_substr($paciente->nombre, 0, 1) }}{{ mb_substr($paciente->apellido, 0, 1) }}</span>{{ $paciente->nombre }}</span></td>
                        <td>{{ $paciente->apellido }}</td><td>{{ $paciente->ci }}</td><td>{{ $paciente->telefono }}</td>
                        <td><div class="action-group">
                            <a class="icon-button" href="{{ route('pacientes.show', $paciente) }}">Ver</a>
                            <a class="icon-button" href="{{ route('pacientes.edit', $paciente) }}">Editar</a>
                            <form method="POST" action="{{ route('pacientes.destroy', $paciente) }}" onsubmit="return confirm('¿Eliminar este paciente?')">
                                @csrf @method('DELETE')
                                <button class="icon-button danger" type="submit">Eliminar</button>
                            </form>
                        </div></td>
                    </tr>
                @empty
                    <tr><td class="empty-state" colspan="5">{{ $buscar ? 'No se encontraron pacientes.' : 'Aún no hay pacientes registrados.' }}</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="table-footer"><span>Mostrando {{ $pacientes->count() }} {{ $pacientes->count() === 1 ? 'registro' : 'registros' }}</span></div>
    </div>
@endsection
