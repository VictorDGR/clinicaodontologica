<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Paciente extends Persona
{
    protected $table = 'pacientes';

    protected $fillable = ['nombre', 'apellido', 'ci', 'telefono', 'fecha_nacimiento', 'direccion'];

    protected static function booted(): void
    {
        // Composición: cada paciente posee su historial clínico desde que se registra.
        static::created(function (Paciente $paciente): void {
            $paciente->historialClinico()->create(['fecha_apertura' => now()->toDateString()]);
        });
    }

    public function historialClinico(): HasOne
    {
        return $this->hasOne(HistorialClinico::class);
    }

    public function fichas(): HasMany
    {
        return $this->hasMany(Ficha::class);
    }
}
