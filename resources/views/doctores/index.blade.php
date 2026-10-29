@extends('layouts.app')

@section('title', 'Doctores')
@section('section', 'Doctores')
@section('heading', 'Doctores')

@section('content')
    <div class="panel table-panel">
        <div class="toolbar">
            <div class="panel-heading" style="margin:0"><h2>Doctores de la clínica</h2><p>Cada doctor ingresa al sistema con su usuario.</p></div>
            <a class="button button-primary" href="{{ route('doctores.create') }}">＋ Nuevo doctor</a>
        </div>
        <div class="table-scroll">
            <table>
                <thead><tr><th>Nombre</th><th>CI</th><th>Teléfono</th><th>Especialidad</th><th>Matrícula</th><th>Usuario</th><th>Acciones</th></tr></thead>
                <tbody>
                @forelse ($doctores as $doctor)
                    <tr>
                        <td><span class="person-cell"><span class="avatar avatar-teal">{{ mb_substr($doctor->nombre, 0, 1) }}{{ mb_substr($doctor->apellido, 0, 1) }}</span>{{ $doctor->nombre_completo }}</span></td>
                        <td>{{ $doctor->ci }}</td>
                        <td>{{ $doctor->telefono }}</td>
                        <td>{{ $doctor->especialidad }}</td>
                        <td>{{ $doctor->matricula }}</td>
                        <td>{{ $doctor->user->usuario }}</td>
                        <td><div class="action-group">
                            <a class="icon-button" href="{{ route('doctores.edit', $doctor) }}">Editar</a>
                            <form method="POST" action="{{ route('doctores.destroy', $doctor) }}" onsubmit="return confirm('¿Eliminar este doctor y su usuario?')">
                                @csrf @method('DELETE')
                                <button class="icon-button danger" type="submit">Eliminar</button>
                            </form>
                        </div></td>
                    </tr>
                @empty
                    <tr><td class="empty-state" colspan="7">Aún no hay doctores registrados.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="table-footer"><span>Mostrando {{ $doctores->count() }} {{ $doctores->count() === 1 ? 'registro' : 'registros' }}</span></div>
    </div>
@endsection
