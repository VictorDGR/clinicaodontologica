<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Iniciar sesión · Sonrisa Sana</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
<main class="login-page">
    <div class="form-card login-card">
        <div class="brand">
            <span class="brand-mark">S</span>
            <span><strong>Clínica Dental</strong><small>Sonrisa Sana</small></span>
        </div>
        <div class="form-intro"><h2>Iniciar sesión</h2><p>Ingresa con tu usuario y contraseña.</p></div>
        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="form-grid">
                <div class="field"><label for="usuario">Usuario</label><input id="usuario" name="usuario" value="{{ old('usuario') }}" required autofocus autocomplete="username"><x-error field="usuario" /></div>
                <div class="field"><label for="password">Contraseña</label><input id="password" name="password" type="password" required autocomplete="current-password"><x-error field="password" /></div>
            </div>
            <button class="button button-primary" type="submit">Ingresar</button>
        </form>
    </div>
</main>
</body>
</html>
