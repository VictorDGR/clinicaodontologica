<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** Permite el acceso solo a los roles indicados; el administrador tiene acceso total. */
class VerificarRol
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        abort_unless(
            $user !== null && ($user->esAdministrador() || in_array($user->rol, $roles, true)),
            403,
            'No tienes permiso para acceder a esta sección.'
        );

        return $next($request);
    }
}
