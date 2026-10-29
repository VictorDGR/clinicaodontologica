<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaterialUsado extends Model
{
    protected $table = 'materiales_usados';

    public $timestamps = false;

    protected $fillable = ['atencion_id', 'producto_id', 'cantidad', 'precio_unitario'];

    protected function casts(): array
    {
        return ['precio_unitario' => 'decimal:2'];
    }

    public function atencion(): BelongsTo
    {
        return $this->belongsTo(Atencion::class);
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }

    public function subtotal(): float
    {
        return round($this->cantidad * (float) $this->precio_unitario, 2);
    }
}
