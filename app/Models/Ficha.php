<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Ficha extends Model
{
    public const ESTADOS = ['Pendiente', 'Atendida', 'Cancelada'];

    protected $table = 'fichas';

    protected $fillable = ['paciente_id', 'doctor_id', 'secretaria_id', 'fecha', 'hora', 'motivo', 'estado'];

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function secretaria(): BelongsTo
    {
        return $this->belongsTo(Secretaria::class);
    }

    public function atencion(): HasOne
    {
        return $this->hasOne(Atencion::class);
    }

    /** MySQL devuelve TIME como HH:MM:SS; las vistas y formularios usan HH:MM. */
    protected function hora(): Attribute
    {
        return Attribute::get(fn (?string $value): ?string => $value === null ? null : substr($value, 0, 5));
    }
}
