<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProveedorController extends Controller
{
    public function index(Request $request): View
    {
        $buscar = trim((string) $request->query('buscar', ''));
        $proveedores = Proveedor::withCount('productos')
            ->when($buscar !== '', fn ($query) => $query->where(fn ($query) => $query
                ->whereLike('nombre', "%{$buscar}%")
                ->orWhereLike('nit', "%{$buscar}%")))
            ->orderBy('nombre')
            ->get();

        return view('proveedores.index', compact('proveedores', 'buscar'));
    }

    public function create(): View
    {
        return view('proveedores.form', ['proveedor' => null]);
    }

    public function store(Request $request): RedirectResponse
    {
        Proveedor::create($this->validar($request));

        return redirect()->route('proveedores.index')->with('success', 'Proveedor registrado correctamente.');
    }

    public function edit(Proveedor $proveedor): View
    {
        return view('proveedores.form', compact('proveedor'));
    }

    public function update(Request $request, Proveedor $proveedor): RedirectResponse
    {
        $proveedor->update($this->validar($request, $proveedor));

        return redirect()->route('proveedores.index')->with('success', 'Proveedor actualizado correctamente.');
    }

    public function destroy(Proveedor $proveedor): RedirectResponse
    {
        if ($proveedor->productos()->exists() || $proveedor->compras()->exists()) {
            return redirect()->route('proveedores.index')
                ->with('error', 'No se puede eliminar este proveedor porque tiene productos o compras registradas.');
        }

        $proveedor->delete();

        return redirect()->route('proveedores.index')->with('success', 'Proveedor eliminado.');
    }

    private function validar(Request $request, ?Proveedor $proveedor = null): array
    {
        return $request->validate([
            'nombre' => ['required', 'string', 'max:120'],
            'nit' => ['required', 'string', 'max:20', Rule::unique('proveedores', 'nit')->ignore($proveedor)],
            'telefono' => ['required', 'string', 'max:30'],
        ], [
            'required' => 'Este campo es obligatorio.',
            'nit.unique' => 'Ya existe un proveedor con este NIT.',
        ]);
    }
}
