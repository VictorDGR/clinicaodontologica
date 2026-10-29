<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DoctorController extends Controller
{
    public function index(): View
    {
        return view('doctores.index', ['doctores' => Doctor::with('user')->orderBy('apellido')->orderBy('nombre')->get()]);
    }

    public function create(): View
    {
        return view('doctores.form', ['doctor' => null]);
    }

    public function store(Request $request): RedirectResponse
    {
        $datos = $this->validar($request);

        DB::transaction(function () use ($datos): void {
            $user = User::create(['usuario' => $datos['usuario'], 'password' => $datos['password'], 'rol' => 'doctor']);
            $user->doctor()->create(collect($datos)->except(['usuario', 'password'])->all());
        });

        return redirect()->route('doctores.index')->with('success', 'Doctor registrado correctamente.');
    }

    public function edit(Doctor $doctor): View
    {
        return view('doctores.form', ['doctor' => $doctor->load('user')]);
    }

    public function update(Request $request, Doctor $doctor): RedirectResponse
    {
        $datos = $this->validar($request, $doctor);

        DB::transaction(function () use ($datos, $doctor): void {
            $doctor->update(collect($datos)->except(['usuario', 'password'])->all());
            $doctor->user->update(array_filter(
                ['usuario' => $datos['usuario'], 'password' => $datos['password'] ?? null],
                fn ($valor): bool => filled($valor),
            ));
        });

        return redirect()->route('doctores.index')->with('success', 'Datos del doctor actualizados.');
    }

    public function destroy(Doctor $doctor): RedirectResponse
    {
        if ($doctor->fichas()->exists()) {
            return redirect()->route('doctores.index')
                ->with('error', 'No se puede eliminar este doctor porque tiene fichas registradas.');
        }

        // Al borrar el usuario se borra también el doctor (cascada).
        $doctor->user->delete();

        return redirect()->route('doctores.index')->with('success', 'Doctor eliminado.');
    }

    private function validar(Request $request, ?Doctor $doctor = null): array
    {
        return $request->validate([
            'nombre' => ['required', 'string', 'min:2', 'max:80'],
            'apellido' => ['required', 'string', 'min:2', 'max:80'],
            'ci' => ['required', 'string', 'min:4', 'max:20', Rule::unique('doctores', 'ci')->ignore($doctor)],
            'telefono' => ['required', 'string', 'max:30'],
            'especialidad' => ['required', 'string', 'max:120'],
            'matricula' => ['required', 'string', 'max:30', Rule::unique('doctores', 'matricula')->ignore($doctor)],
            'usuario' => ['required', 'string', 'alpha_dash', 'max:50', Rule::unique('users', 'usuario')->ignore($doctor?->user_id)],
            'password' => [$doctor ? 'nullable' : 'required', 'string', 'min:6'],
        ], [
            'required' => 'Este campo es obligatorio.',
            'ci.unique' => 'Ya existe un doctor con este CI.',
            'matricula.unique' => 'Ya existe un doctor con esta matrícula.',
            'usuario.unique' => 'Este nombre de usuario ya está en uso.',
            'usuario.alpha_dash' => 'El usuario solo puede tener letras, números, guiones y guiones bajos.',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
        ]);
    }
}
