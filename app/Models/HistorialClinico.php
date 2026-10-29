<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HistorialClinico extends Model
{
    protected $table = 'historiales_clinicos';

    protected $fillable = ['paciente_id', 'fecha_apertura'];

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class);
    }

    public function registros(): HasMany
    {
        return $this->hasMany(RegistroClinico::class);
    }

    /** Registros del historial con su atención, del más reciente al más antiguo. */
    public function consultar(): Collection
    {
        return $this->registros()
            ->with(['atencion.doctor', 'atencion.materiales.producto'])
            ->orderByDesc('fecha')
            ->get();
    }
}
