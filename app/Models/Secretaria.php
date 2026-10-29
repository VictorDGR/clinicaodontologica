<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Secretaria extends Persona
{
    protected $table = 'secretarias';

    protected $fillable = ['user_id', 'nombre', 'apellido', 'ci', 'telefono', 'turno'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function fichas(): HasMany
    {
        return $this->hasMany(Ficha::class);
    }
}
