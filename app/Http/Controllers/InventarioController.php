<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\View\View;

class InventarioController extends Controller
{
    /** Tarea 6.6: stock actual con alerta de stock bajo (primero los más bajos). */
    public function __invoke(): View
    {
        $productos = Producto::with('proveedor')->orderBy('stock')->orderBy('nombre')->get();

        return view('inventario.index', [
            'productos' => $productos,
            'stockBajo' => $productos->filter->stockBajo()->count(),
        ]);
    }
}
