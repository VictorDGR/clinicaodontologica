<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Doctor extends Persona
{
    protected $table = 'doctores';

    protected $fillable = ['user_id', 'nombre', 'apellido', 'ci', 'telefono', 'especialidad', 'matricula'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function fichas(): HasMany
    {
        return $this->hasMany(Ficha::class);
    }

    public function atenciones(): HasMany
    {
        return $this->hasMany(Atencion::class);
    }
}
