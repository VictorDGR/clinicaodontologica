<?php

namespace App\Models;

use App\Services\Inventario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class Atencion extends Model
{
    protected $table = 'atenciones';

    protected $fillable = [
        'ficha_id', 'doctor_id', 'fecha', 'tipo_diente', 'diagnostico',
        'procedimiento', 'observaciones', 'ruta_analisis_radiografico',
    ];

    public function ficha(): BelongsTo
    {
        return $this->belongsTo(Ficha::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function materiales(): HasMany
    {
        return $this->hasMany(MaterialUsado::class);
    }

    public function registroClinico(): HasOne
    {
        return $this->hasOne(RegistroClinico::class);
    }

    public function cobro(): HasOne
    {
        return $this->hasOne(Cobro::class);
    }

    /**
     * Registra la atención de una ficha: guarda los materiales y los descuenta del inventario,
     * la incorpora al historial clínico, genera el cobro (procedimiento + materiales) y marca la ficha como atendida.
     *
     * @param  array<int, array{producto_id: int, cantidad: int}>  $materiales
     */
    public static function registrar(Ficha $ficha, array $datos, array $materiales, float $costoProcedimiento, ?UploadedFile $radiografia): self
    {
        return DB::transaction(function () use ($ficha, $datos, $materiales, $costoProcedimiento, $radiografia): self {
            $precios = Producto::whereIn('id', array_column($materiales, 'producto_id'))->pluck('precio', 'id');
            $materiales = array_map(fn (array $material): array => [
                ...$material,
                'precio_unitario' => $precios[$material['producto_id']],
            ], $materiales);

            $atencion = self::create([
                ...$datos,
                'ficha_id' => $ficha->id,
                'doctor_id' => $ficha->doctor_id,
                'fecha' => now()->toDateString(),
                'ruta_analisis_radiografico' => $radiografia?->store('radiografias'),
            ]);

            $atencion->materiales()->createMany($materiales);
            $atencion->descontarMateriales();

            $ficha->paciente->historialClinico->registros()->create([
                'atencion_id' => $atencion->id,
                'fecha' => now(),
                'descripcion' => $atencion->procedimiento,
            ]);

            Cobro::generar($atencion, $costoProcedimiento + $atencion->totalMateriales());
            $ficha->update(['estado' => 'Atendida']);

            return $atencion;
        });
    }

    public function totalMateriales(): float
    {
        return round($this->materiales->sum(fn (MaterialUsado $material): float => $material->subtotal()), 2);
    }

    /** Lo que el doctor cobró por el procedimiento: el monto del cobro sin los materiales. */
    public function costoProcedimiento(): float
    {
        return round((float) ($this->cobro?->monto ?? 0) - $this->totalMateriales(), 2);
    }

    public function descontarMateriales(): void
    {
        foreach ($this->materiales()->with('producto')->get() as $material) {
            Inventario::descontarStock($material->producto, $material->cantidad);
        }
    }
}
