<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Clínica Dental') · Sonrisa Sana</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
@php($usuario = auth()->user())
<div class="app-shell">
    <aside class="sidebar">
        <a class="brand" href="{{ route('inicio') }}">
            <span class="brand-mark">S</span>
            <span><strong>Clínica Dental</strong><small>Sonrisa Sana</small></span>
        </a>
        <nav class="side-nav" aria-label="Navegación principal">
            @unless ($usuario->esDoctor())
                <span class="nav-label">PRINCIPAL</span>
                <a class="{{ request()->routeIs('inicio') ? 'active' : '' }}" href="{{ route('inicio') }}"><span>⌂</span> Inicio</a>
                <span class="nav-label">GESTIÓN CLÍNICA</span>
                <a class="{{ request()->routeIs('pacientes.*') ? 'active' : '' }}" href="{{ route('pacientes.index') }}"><span>♙</span> Pacientes</a>
                <a class="{{ request()->routeIs('fichas.*') ? 'active' : '' }}" href="{{ route('fichas.index') }}"><span>▤</span> Fichas</a>
                <a class="{{ request()->routeIs('cobros.*') ? 'active' : '' }}" href="{{ route('cobros.index') }}"><span>$</span> Cobros</a>
            @endunless
            @if ($usuario->esDoctor() || $usuario->esAdministrador())
                <span class="nav-label">ATENCIÓN</span>
                @if ($usuario->esDoctor())
                    <a class="{{ request()->routeIs('fichas.*', 'atenciones.*') ? 'active' : '' }}" href="{{ route('fichas.index') }}"><span>▤</span> Mis fichas</a>
                @endif
                <a class="{{ request()->routeIs('historial.*') ? 'active' : '' }}" href="{{ route('historial.index') }}"><span>☰</span> Historial clínico</a>
            @endif
            @unless ($usuario->esDoctor())
                <span class="nav-label">INVENTARIO</span>
                <a class="{{ request()->routeIs('productos.*') ? 'active' : '' }}" href="{{ route('productos.index') }}"><span>◫</span> Productos</a>
                <a class="{{ request()->routeIs('proveedores.*') ? 'active' : '' }}" href="{{ route('proveedores.index') }}"><span>⛟</span> Proveedores</a>
                <a class="{{ request()->routeIs('compras.*') ? 'active' : '' }}" href="{{ route('compras.index') }}"><span>⊕</span> Compras</a>
                <a class="{{ request()->routeIs('inventario.*') ? 'active' : '' }}" href="{{ route('inventario.index') }}"><span>▥</span> Inventario</a>
            @endunless
            @if ($usuario->esAdministrador())
                <span class="nav-label">ADMINISTRACIÓN</span>
                <a class="{{ request()->routeIs('doctores.*') ? 'active' : '' }}" href="{{ route('doctores.index') }}"><span>✚</span> Doctores</a>
                <a class="{{ request()->routeIs('secretarias.*') ? 'active' : '' }}" href="{{ route('secretarias.index') }}"><span>☏</span> Secretarias</a>
            @endif
        </nav>
        <div class="sidebar-foot">
            <span class="avatar">{{ mb_strtoupper(mb_substr($usuario->nombreVisible(), 0, 2)) }}</span>
            <span><strong>{{ $usuario->nombreVisible() }}</strong><small>{{ ucfirst($usuario->rol) }}</small></span>
            <form class="logout-form" method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="logout-button" type="submit">Salir</button>
            </form>
        </div>
    </aside>
    <main class="main-area">
        <header class="topbar"><span>Clínica odontológica Sonrisa Sana</span><span class="topbar-date">{{ now()->translatedFormat('d M Y') }}</span></header>
        <div class="page-heading">
            <div class="breadcrumbs"><a href="{{ route('inicio') }}">Clínica Dental</a><span>/</span><strong>@yield('section', 'Inicio')</strong></div>
            <h1>@yield('heading', 'Inicio')</h1>
        </div>
        <section class="page-content">
            @if (session('success'))
                <div class="alert alert-success" role="status">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-error" role="alert">{{ session('error') }}</div>
            @endif
            @yield('content')
        </section>
    </main>
</div>
@stack('scripts')
</body>
</html>
