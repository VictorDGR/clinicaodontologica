<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RegistroClinico extends Model
{
    protected $table = 'registros_clinicos';

    public $timestamps = false;

    protected $fillable = ['historial_clinico_id', 'atencion_id', 'fecha', 'descripcion'];

    protected function casts(): array
    {
        return ['fecha' => 'datetime'];
    }

    public function historialClinico(): BelongsTo
    {
        return $this->belongsTo(HistorialClinico::class);
    }

    public function atencion(): BelongsTo
    {
        return $this->belongsTo(Atencion::class);
    }
}
