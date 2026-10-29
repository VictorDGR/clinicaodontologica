<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: dirname(__DIR__).'/routes/web.php',
        commands: dirname(__DIR__).'/routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias(['rol' => \App\Http\Middleware\VerificarRol::class]);
        // Render atiende HTTPS en su proxy; así Laravel genera enlaces https correctos.
        $middleware->trustProxies(at: '*');
        $middleware->redirectGuestsTo(fn () => route('login'));
        $middleware->redirectUsersTo(fn () => route('inicio'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
