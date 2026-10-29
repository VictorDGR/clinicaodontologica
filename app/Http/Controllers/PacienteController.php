<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PacienteController extends Controller
{
    public function index(Request $request): View
    {
        $buscar = trim((string) $request->query('buscar', ''));
        $pacientes = Paciente::query()
            ->when($buscar !== '', fn ($query) => $query->where(fn ($query) => $query
                ->whereLike('ci', "%{$buscar}%")
                ->orWhereLike('nombre', "%{$buscar}%")
                ->orWhereLike('apellido', "%{$buscar}%")))
            ->orderBy('apellido')
            ->orderBy('nombre')
            ->get();

        return view('pacientes.index', compact('pacientes', 'buscar'));
    }

    public function create(): View
    {
        return view('pacientes.form', ['paciente' => null]);
    }

    public function store(Request $request): RedirectResponse
    {
        Paciente::create($this->validar($request));

        return redirect()->route('pacientes.index')->with('success', 'Paciente registrado correctamente.');
    }

    public function show(Paciente $paciente): View
    {
        return view('pacientes.show', compact('paciente'));
    }

    public function edit(Paciente $paciente): View
    {
        return view('pacientes.form', compact('paciente'));
    }

    public function update(Request $request, Paciente $paciente): RedirectResponse
    {
        $paciente->update($this->validar($request, $paciente));

        return redirect()->route('pacientes.index')->with('success', 'Datos del paciente actualizados.');
    }

    public function destroy(Paciente $paciente): RedirectResponse
    {
        if ($paciente->fichas()->exists()) {
            return redirect()->route('pacientes.index')
                ->with('error', 'No se puede eliminar este paciente porque tiene fichas registradas.');
        }

        $paciente->delete();

        return redirect()->route('pacientes.index')->with('success', 'Paciente eliminado.');
    }

    private function validar(Request $request, ?Paciente $paciente = null): array
    {
        $request->merge(['ci' => trim((string) $request->input('ci'))]);

        return $request->validate([
            'nombre' => ['required', 'string', 'min:2', 'max:80'],
            'apellido' => ['required', 'string', 'min:2', 'max:80'],
            'ci' => ['required', 'string', 'min:4', 'max:20', Rule::unique('pacientes', 'ci')->ignore($paciente)],
            'telefono' => ['required', 'string', 'max:30'],
            'fecha_nacimiento' => ['required', 'date', 'before_or_equal:today'],
            'direccion' => ['required', 'string', 'max:255'],
        ], [
            'required' => 'Este campo es obligatorio.',
            'fecha_nacimiento.before_or_equal' => 'La fecha de nacimiento no puede ser futura.',
            'ci.min' => 'El CI debe tener al menos 4 caracteres.',
            'ci.unique' => 'Ya existe un paciente registrado con este CI.',
        ]);
    }
}
