<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Cobro extends Model
{
    public const METODOS = ['Efectivo', 'Tarjeta', 'Transferencia', 'QR'];

    protected $table = 'cobros';

    protected $fillable = ['atencion_id', 'fecha', 'monto', 'estado'];

    protected function casts(): array
    {
        return ['monto' => 'decimal:2'];
    }

    public function atencion(): BelongsTo
    {
        return $this->belongsTo(Atencion::class);
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(Pago::class);
    }

    public static function generar(Atencion $atencion, float $monto): self
    {
        return self::create([
            'atencion_id' => $atencion->id,
            'fecha' => now()->toDateString(),
            'monto' => $monto,
            'estado' => 'Pendiente',
        ]);
    }

    /** Registra un pago y actualiza el estado según el saldo pendiente. */
    public function cobrar(float $monto, string $fecha, string $metodo, ?int $secretariaId): Pago
    {
        return DB::transaction(function () use ($monto, $fecha, $metodo, $secretariaId): Pago {
            $pago = $this->pagos()->create([
                'monto' => $monto,
                'fecha' => $fecha,
                'metodo' => $metodo,
                'secretaria_id' => $secretariaId,
            ]);

            $this->update(['estado' => $this->fresh()->saldo <= 0 ? 'Pagado' : 'Parcial']);

            return $pago;
        });
    }

    protected function pagado(): Attribute
    {
        return Attribute::get(fn (): float => (float) ($this->relationLoaded('pagos')
            ? $this->pagos->sum('monto')
            : $this->pagos()->sum('monto')));
    }

    protected function saldo(): Attribute
    {
        return Attribute::get(fn (): float => round((float) $this->monto - $this->pagado, 2));
    }
}
