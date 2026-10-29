<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetalleCompra extends Model
{
    protected $table = 'detalle_compras';

    public $timestamps = false;

    protected $fillable = ['compra_id', 'producto_id', 'cantidad', 'costo_unitario'];

    protected function casts(): array
    {
        return ['costo_unitario' => 'decimal:2'];
    }

    public function compra(): BelongsTo
    {
        return $this->belongsTo(Compra::class);
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }
}
