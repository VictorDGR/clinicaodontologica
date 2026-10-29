@extends('layouts.app')

@section('title', 'Inicio')
@section('section', 'Inicio')
@section('heading', 'Resumen de la clínica')

@section('content')
    <div class="welcome-card">
        <div><span class="eyebrow">SONRISA SANA</span><h2>Gestión clínica, simple y ordenada</h2><p>Administra pacientes, fichas, cobros e inventario desde un solo lugar.</p></div>
        <a class="button button-primary" href="{{ route('fichas.create') }}">＋ Asignar ficha</a>
    </div>
    <div class="stats-grid">
        <a class="stat-card" href="{{ route('pacientes.index') }}"><span class="stat-icon">♙</span><span class="stat-label">Pacientes registrados</span><strong>{{ $pacientes }}</strong><small>Ver pacientes →</small></a>
        <a class="stat-card" href="{{ route('fichas.index') }}"><span class="stat-icon">◷</span><span class="stat-label">Fichas de hoy</span><strong>{{ $fichasHoy }}</strong><small>Ver fichas →</small></a>
        <a class="stat-card" href="{{ route('cobros.index') }}"><span class="stat-icon">$</span><span class="stat-label">Cobros pendientes</span><strong>{{ $cobrosPendientes }}</strong><small>Ver cobros →</small></a>
        <a class="stat-card" href="{{ route('inventario.index') }}"><span class="stat-icon">▥</span><span class="stat-label">Productos con stock bajo</span><strong>{{ $stockBajo }}</strong><small>Ver inventario →</small></a>
    </div>
    <div class="panel doctors-panel">
        <div class="panel-heading"><div><h2>Equipo médico</h2><p>Doctores registrados en la clínica</p></div></div>
        <div class="doctor-list">
            @forelse ($doctores as $doctor)
                <div class="doctor-item"><span class="avatar avatar-teal">{{ mb_substr($doctor->nombre, 0, 1) }}{{ mb_substr($doctor->apellido, 0, 1) }}</span><span><strong>{{ $doctor->nombre_completo }}</strong><small>{{ $doctor->especialidad }}</small></span></div>
            @empty
                <p class="field-help">Aún no hay doctores registrados.</p>
            @endforelse
        </div>
    </div>
@endsection
