<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pago extends Model
{
    protected $table = 'pagos';

    protected $fillable = ['cobro_id', 'secretaria_id', 'fecha', 'monto', 'metodo'];

    protected function casts(): array
    {
        return ['monto' => 'decimal:2'];
    }

    public function cobro(): BelongsTo
    {
        return $this->belongsTo(Cobro::class);
    }

    public function secretaria(): BelongsTo
    {
        return $this->belongsTo(Secretaria::class);
    }
}
