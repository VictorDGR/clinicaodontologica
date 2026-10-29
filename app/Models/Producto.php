<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Producto extends Model
{
    /** Por debajo o igual a esta cantidad se alerta stock bajo. */
    public const STOCK_MINIMO = 5;

    protected $table = 'productos';

    protected $fillable = ['proveedor_id', 'codigo', 'nombre', 'precio', 'stock'];

    protected function casts(): array
    {
        return ['precio' => 'decimal:2', 'stock' => 'integer'];
    }

    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Proveedor::class);
    }

    public function stockBajo(): bool
    {
        return $this->stock <= self::STOCK_MINIMO;
    }
}
