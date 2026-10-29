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
    <div style="background: #f4f6f9; border: 1px solid #cbd5e1; padding: 15px; border-radius: 8px; max-width: 400px; margin: 20px auto 0 auto; font-family: sans-serif;">
            <p style="margin-top: 0; font-weight: bold; color: #334155;">Credenciales de acceso para pruebas:</p>
            <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                <thead>
                    <tr style="border-bottom: 2px solid #cbd5e1; text-align: left; color: #64748b;">
                        <th style="padding-bottom: 5px;">Usuario</th>
                        <th style="padding-bottom: 5px;">Contraseña</th>
                    </tr>
                </thead>
                <tbody style="color: #334155;">
                    <tr>
                        <td style="padding: 5px 0;"><strong>admin</strong></td>
                        <td style="padding: 5px 0;">admin</td>
                    </tr>
                    <tr>
                        <td style="padding: 5px 0;"><strong>lucia</strong></td>
                        <td style="padding: 5px 0;">secretaria</td>
                    </tr>
                    <tr>
                        <td style="padding: 5px 0;"><strong>carlos</strong></td>
                        <td style="padding: 5px 0;">doctor</td>
                    </tr>
                </tbody>
            </table>
        </div>
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
