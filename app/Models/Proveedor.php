<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Proveedor extends Model
{
    protected $table = 'proveedores';

    protected $fillable = ['nombre', 'nit', 'telefono'];

    public function productos(): HasMany
    {
        return $this->hasMany(Producto::class);
    }

    public function compras(): HasMany
    {
        return $this->hasMany(Compra::class);
    }
}
