<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\Producto;
use App\Models\Proveedor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CompraController extends Controller
{
    public function index(): View
    {
        $compras = Compra::with('proveedor')->withCount('detalles')->orderByDesc('fecha')->orderByDesc('id')->get();

        return view('compras.index', compact('compras'));
    }

    public function create(): View
    {
        return view('compras.form', [
            'proveedores' => Proveedor::orderBy('nombre')->get(),
            'productos' => Producto::orderBy('nombre')->get(),
        ]);
    }

    /** Tarea 6.4: registra la compra y aumenta el stock. */
    public function store(Request $request): RedirectResponse
    {
        $request->merge(['detalles' => collect($request->input('detalles', []))
            ->filter(fn ($fila): bool => filled($fila['producto_id'] ?? null))
            ->values()
            ->all()]);

        $datos = $request->validate([
            'proveedor_id' => ['required', 'integer', 'exists:proveedores,id'],
            'fecha' => ['required', 'date', 'before_or_equal:today'],
            'detalles' => ['required', 'array', 'min:1'],
            'detalles.*.producto_id' => ['required', 'integer', 'exists:productos,id'],
            'detalles.*.cantidad' => ['required', 'integer', 'min:1'],
            'detalles.*.costo_unitario' => ['required', 'numeric', 'min:0'],
        ], [
            'required' => 'Este campo es obligatorio.',
            'detalles.required' => 'Agrega al menos un producto a la compra.',
            'detalles.*.cantidad.min' => 'La cantidad debe ser al menos 1.',
            'fecha.before_or_equal' => 'La fecha de compra no puede ser futura.',
        ]);

        $compra = Compra::registrar(
            (int) $datos['proveedor_id'],
            $datos['fecha'],
            $datos['detalles'],
            $request->user()->secretaria?->id,
        );

        return redirect()->route('compras.show', $compra)->with('success', 'Compra registrada. Se aumentó el stock de los productos.');
    }

    public function show(Compra $compra): View
    {
        $compra->load(['proveedor', 'secretaria', 'detalles.producto']);

        return view('compras.show', compact('compra'));
    }
}
