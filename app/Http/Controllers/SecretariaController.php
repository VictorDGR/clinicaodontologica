<?php

namespace App\Http\Controllers;

use App\Models\Secretaria;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SecretariaController extends Controller
{
    public const TURNOS = ['Mañana', 'Tarde'];

    public function index(): View
    {
        return view('secretarias.index', ['secretarias' => Secretaria::with('user')->orderBy('apellido')->orderBy('nombre')->get()]);
    }

    public function create(): View
    {
        return view('secretarias.form', ['secretaria' => null, 'turnos' => self::TURNOS]);
    }

    public function store(Request $request): RedirectResponse
    {
        $datos = $this->validar($request);

        DB::transaction(function () use ($datos): void {
            $user = User::create(['usuario' => $datos['usuario'], 'password' => $datos['password'], 'rol' => 'secretaria']);
            $user->secretaria()->create(collect($datos)->except(['usuario', 'password'])->all());
        });

        return redirect()->route('secretarias.index')->with('success', 'Secretaria registrada correctamente.');
    }

    public function edit(Secretaria $secretaria): View
    {
        return view('secretarias.form', ['secretaria' => $secretaria->load('user'), 'turnos' => self::TURNOS]);
    }

    public function update(Request $request, Secretaria $secretaria): RedirectResponse
    {
        $datos = $this->validar($request, $secretaria);

        DB::transaction(function () use ($datos, $secretaria): void {
            $secretaria->update(collect($datos)->except(['usuario', 'password'])->all());
            $secretaria->user->update(array_filter(
                ['usuario' => $datos['usuario'], 'password' => $datos['password'] ?? null],
                fn ($valor): bool => filled($valor),
            ));
        });

        return redirect()->route('secretarias.index')->with('success', 'Datos de la secretaria actualizados.');
    }

    public function destroy(Secretaria $secretaria): RedirectResponse
    {
        // Al borrar el usuario se borra también la secretaria (cascada); sus fichas y compras se conservan.
        $secretaria->user->delete();

        return redirect()->route('secretarias.index')->with('success', 'Secretaria eliminada.');
    }

    private function validar(Request $request, ?Secretaria $secretaria = null): array
    {
        return $request->validate([
            'nombre' => ['required', 'string', 'min:2', 'max:80'],
            'apellido' => ['required', 'string', 'min:2', 'max:80'],
            'ci' => ['required', 'string', 'min:4', 'max:20', Rule::unique('secretarias', 'ci')->ignore($secretaria)],
            'telefono' => ['required', 'string', 'max:30'],
            'turno' => ['required', Rule::in(self::TURNOS)],
            'usuario' => ['required', 'string', 'alpha_dash', 'max:50', Rule::unique('users', 'usuario')->ignore($secretaria?->user_id)],
            'password' => [$secretaria ? 'nullable' : 'required', 'string', 'min:6'],
        ], [
            'required' => 'Este campo es obligatorio.',
            'ci.unique' => 'Ya existe una secretaria con este CI.',
            'usuario.unique' => 'Este nombre de usuario ya está en uso.',
            'usuario.alpha_dash' => 'El usuario solo puede tener letras, números, guiones y guiones bajos.',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
        ]);
    }
}
