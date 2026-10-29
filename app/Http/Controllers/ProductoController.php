<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Proveedor;
use App\Services\Inventario;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProductoController extends Controller
{
    public function index(Request $request): View
    {
        $buscar = trim((string) $request->query('buscar', ''));
        $productos = Producto::with('proveedor')
            ->when($buscar !== '', fn ($query) => $query->where(fn ($query) => $query
                ->whereLike('codigo', "%{$buscar}%")
                ->orWhereLike('nombre', "%{$buscar}%")))
            ->orderBy('nombre')
            ->get();

        return view('productos.index', compact('productos', 'buscar'));
    }

    public function create(): View
    {
        return view('productos.form', ['producto' => null, 'proveedores' => Proveedor::orderBy('nombre')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        Inventario::agregarProducto($this->validar($request) + $request->validate([
            'stock' => ['required', 'integer', 'min:0'],
        ], ['required' => 'Este campo es obligatorio.']));

        return redirect()->route('productos.index')->with('success', 'Producto registrado correctamente.');
    }

    public function edit(Producto $producto): View
    {
        return view('productos.form', ['producto' => $producto, 'proveedores' => Proveedor::orderBy('nombre')->get()]);
    }

    public function update(Request $request, Producto $producto): RedirectResponse
    {
        // El stock no se edita a mano: cambia con las compras y las atenciones.
        $producto->update($this->validar($request, $producto));

        return redirect()->route('productos.index')->with('success', 'Producto actualizado correctamente.');
    }

    public function destroy(Producto $producto): RedirectResponse
    {
        try {
            $producto->delete();
        } catch (QueryException) {
            return redirect()->route('productos.index')
                ->with('error', 'No se puede eliminar este producto porque está registrado en compras o atenciones.');
        }

        return redirect()->route('productos.index')->with('success', 'Producto eliminado.');
    }

    private function validar(Request $request, ?Producto $producto = null): array
    {
        return $request->validate([
            'codigo' => ['required', 'string', 'max:30', Rule::unique('productos', 'codigo')->ignore($producto)],
            'nombre' => ['required', 'string', 'max:120'],
            'precio' => ['required', 'numeric', 'min:0'],
            'proveedor_id' => ['required', 'integer', 'exists:proveedores,id'],
        ], [
            'required' => 'Este campo es obligatorio.',
            'codigo.unique' => 'Ya existe un producto con este código.',
            'proveedor_id.exists' => 'Selecciona un proveedor registrado.',
        ]);
    }
}
