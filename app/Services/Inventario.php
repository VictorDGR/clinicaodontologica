<?php

namespace App\Services;

use App\Models\Producto;
use Illuminate\Validation\ValidationException;

/** Clase Inventario del diagrama: administra el stock de los productos. */
class Inventario
{
    public static function agregarProducto(array $datos): Producto
    {
        return Producto::create($datos);
    }

    public static function descontarStock(Producto $producto, int $cantidad): void
    {
        // Bloquea la fila para que dos atenciones simultáneas no dejen el stock negativo.
        $producto = Producto::lockForUpdate()->findOrFail($producto->id);

        if ($producto->stock < $cantidad) {
            throw ValidationException::withMessages([
                'materiales' => "Stock insuficiente de {$producto->nombre}: hay {$producto->stock} y se necesitan {$cantidad}.",
            ]);
        }

        $producto->decrement('stock', $cantidad);
    }

    public static function aumentarStock(Producto $producto, int $cantidad): void
    {
        $producto->increment('stock', $cantidad);
    }
}
