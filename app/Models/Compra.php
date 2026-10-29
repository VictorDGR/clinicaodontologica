<?php

namespace App\Models;

use App\Services\Inventario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Compra extends Model
{
    protected $table = 'compras';

    protected $fillable = ['proveedor_id', 'secretaria_id', 'fecha', 'total', 'estado'];

    protected function casts(): array
    {
        return ['total' => 'decimal:2'];
    }

    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Proveedor::class);
    }

    public function secretaria(): BelongsTo
    {
        return $this->belongsTo(Secretaria::class);
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(DetalleCompra::class);
    }

    /**
     * Registra la compra con sus detalles y aumenta el stock de cada producto.
     *
     * @param  array<int, array{producto_id: int, cantidad: int, costo_unitario: float}>  $detalles
     */
    public static function registrar(int $proveedorId, string $fecha, array $detalles, ?int $secretariaId): self
    {
        return DB::transaction(function () use ($proveedorId, $fecha, $detalles, $secretariaId): self {
            $compra = self::create([
                'proveedor_id' => $proveedorId,
                'secretaria_id' => $secretariaId,
                'fecha' => $fecha,
                'total' => collect($detalles)->sum(fn (array $d): float => $d['cantidad'] * $d['costo_unitario']),
                'estado' => 'Registrada',
            ]);

            foreach ($compra->detalles()->createMany($detalles) as $detalle) {
                Inventario::aumentarStock($detalle->producto, $detalle->cantidad);
            }

            return $compra;
        });
    }
}
