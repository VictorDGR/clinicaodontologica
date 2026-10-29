@extends('layouts.app')

@section('title', 'Secretarias')
@section('section', 'Secretarias')
@section('heading', 'Secretarias')

@section('content')
    <div class="panel table-panel">
        <div class="toolbar">
            <div class="panel-heading" style="margin:0"><h2>Secretarias de la clínica</h2><p>Cada secretaria ingresa al sistema con su usuario.</p></div>
            <a class="button button-primary" href="{{ route('secretarias.create') }}">＋ Nueva secretaria</a>
        </div>
        <div class="table-scroll">
            <table>
                <thead><tr><th>Nombre</th><th>CI</th><th>Teléfono</th><th>Turno</th><th>Usuario</th><th>Acciones</th></tr></thead>
                <tbody>
                @forelse ($secretarias as $secretaria)
                    <tr>
                        <td><span class="person-cell"><span class="avatar avatar-teal">{{ mb_substr($secretaria->nombre, 0, 1) }}{{ mb_substr($secretaria->apellido, 0, 1) }}</span>{{ $secretaria->nombre_completo }}</span></td>
                        <td>{{ $secretaria->ci }}</td>
                        <td>{{ $secretaria->telefono }}</td>
                        <td>{{ $secretaria->turno }}</td>
                        <td>{{ $secretaria->user->usuario }}</td>
                        <td><div class="action-group">
                            <a class="icon-button" href="{{ route('secretarias.edit', $secretaria) }}">Editar</a>
                            <form method="POST" action="{{ route('secretarias.destroy', $secretaria) }}" onsubmit="return confirm('¿Eliminar esta secretaria y su usuario?')">
                                @csrf @method('DELETE')
                                <button class="icon-button danger" type="submit">Eliminar</button>
                            </form>
                        </div></td>
                    </tr>
                @empty
                    <tr><td class="empty-state" colspan="6">Aún no hay secretarias registradas.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="table-footer"><span>Mostrando {{ $secretarias->count() }} {{ $secretarias->count() === 1 ? 'registro' : 'registros' }}</span></div>
    </div>
@endsection
