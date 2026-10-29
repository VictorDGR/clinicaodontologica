<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credenciales = $request->validate([
            'usuario' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], ['required' => 'Este campo es obligatorio.']);

        if (! Auth::attempt($credenciales)) {
            throw ValidationException::withMessages(['usuario' => 'Usuario o contraseña incorrectos.']);
        }

        $request->session()->regenerate();

        return redirect()->route('inicio');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
