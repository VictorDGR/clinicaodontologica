@extends('layouts.app')

@section('title', 'Historial clínico')
@section('section', 'Historial clínico')
@section('heading', 'Historial clínico')

@section('content')
    <div class="panel table-panel">
        <div class="toolbar">
            <form class="search-form" method="GET" action="{{ route('historial.index') }}">
                <label class="sr-only" for="buscar">Buscar paciente</label>
                <span class="search-icon">⌕</span>
                <input id="buscar" name="buscar" type="search" value="{{ $buscar }}" placeholder="Buscar paciente por CI, nombre o apellido...">
            </form>
        </div>
        <div class="table-scroll">
            <table>
                <thead><tr><th>Paciente</th><th>CI</th><th>Fecha de nacimiento</th><th>Acciones</th></tr></thead>
                <tbody>
                @forelse ($pacientes as $paciente)
                    <tr>
                        <td><span class="person-cell"><span class="avatar avatar-teal">{{ mb_substr($paciente->nombre, 0, 1) }}{{ mb_substr($paciente->apellido, 0, 1) }}</span>{{ $paciente->nombre_completo }}</span></td>
                        <td>{{ $paciente->ci }}</td>
                        <td>{{ \Illuminate\Support\Carbon::parse($paciente->fecha_nacimiento)->format('d/m/Y') }}</td>
                        <td><a class="icon-button" href="{{ route('historial.show', $paciente) }}">Ver historial</a></td>
                    </tr>
                @empty
                    <tr><td class="empty-state" colspan="4">{{ $buscar ? 'No se encontraron pacientes.' : 'Aún no hay pacientes registrados.' }}</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="table-footer"><span>Mostrando {{ $pacientes->count() }} {{ $pacientes->count() === 1 ? 'paciente' : 'pacientes' }}</span></div>
    </div>
@endsection
