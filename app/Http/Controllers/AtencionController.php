<?php

namespace App\Http\Controllers;

use App\Models\Atencion;
use App\Models\Ficha;
use App\Models\Producto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AtencionController extends Controller
{
    public function create(Request $request, Ficha $ficha): View|RedirectResponse
    {
        if ($redireccion = $this->verificarFicha($request, $ficha)) {
            return $redireccion;
        }

        return view('atenciones.form', [
            'ficha' => $ficha->load(['paciente', 'doctor']),
            'productos' => Producto::where('stock', '>', 0)->orderBy('nombre')->get(),
        ]);
    }

    public function store(Request $request, Ficha $ficha): RedirectResponse
    {
        if ($redireccion = $this->verificarFicha($request, $ficha)) {
            return $redireccion;
        }

        // Se ignoran las filas de materiales que quedaron vacías.
        $request->merge(['materiales' => collect($request->input('materiales', []))
            ->filter(fn ($fila): bool => filled($fila['producto_id'] ?? null))
            ->values()
            ->all()]);

        $datos = $request->validate([
            'tipo_diente' => ['required', 'string', 'max:60'],
            'diagnostico' => ['required', 'string', 'max:255'],
            'procedimiento' => ['required', 'string', 'max:255'],
            'observaciones' => ['nullable', 'string', 'max:2000'],
            'radiografia' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'costo_procedimiento' => ['required', 'numeric', 'min:0', 'max:99999999'],
            'materiales' => ['array'],
            'materiales.*.producto_id' => ['required', 'integer', 'exists:productos,id'],
            'materiales.*.cantidad' => ['required', 'integer', 'min:1'],
        ], [
            'required' => 'Este campo es obligatorio.',
            'radiografia.mimes' => 'La radiografía debe ser una imagen (JPG, PNG) o un PDF.',
            'radiografia.max' => 'La radiografía no puede pesar más de 5 MB.',
            'materiales.*.cantidad.min' => 'La cantidad debe ser al menos 1.',
        ]);

        $this->verificarStock($datos['materiales'] ?? []);

        Atencion::registrar(
            $ficha,
            collect($datos)->only(['tipo_diente', 'diagnostico', 'procedimiento', 'observaciones'])->all(),
            $datos['materiales'] ?? [],
            (float) $datos['costo_procedimiento'],
            $request->file('radiografia'),
        );

        return redirect()->route('fichas.show', $ficha)
            ->with('success', 'Atención registrada. Se actualizó el historial, se descontó el inventario y se generó el cobro.');
    }

    public function radiografia(Atencion $atencion): StreamedResponse
    {
        abort_if($atencion->ruta_analisis_radiografico === null, 404);

        return Storage::response($atencion->ruta_analisis_radiografico);
    }

    private function verificarFicha(Request $request, Ficha $ficha): ?RedirectResponse
    {
        $user = $request->user();
        abort_if($user->esDoctor() && $ficha->doctor_id !== $user->doctor?->id, 403, 'Esta ficha no está asignada a ti.');

        if ($ficha->estado !== 'Pendiente' || $ficha->atencion()->exists()) {
            return redirect()->route('fichas.show', $ficha)->with('error', 'Solo se puede registrar la atención de una ficha pendiente.');
        }

        return null;
    }

    /** Comprueba antes de guardar que haya stock suficiente de cada material. */
    private function verificarStock(array $materiales): void
    {
        $productos = Producto::whereIn('id', array_column($materiales, 'producto_id'))->get()->keyBy('id');

        collect($materiales)
            ->groupBy('producto_id')
            ->each(function ($filas, $productoId) use ($productos): void {
                $producto = $productos[$productoId];
                $cantidad = $filas->sum('cantidad');

                if ($producto->stock < $cantidad) {
                    throw ValidationException::withMessages([
                        'materiales' => "Stock insuficiente de {$producto->nombre}: hay {$producto->stock} y se necesitan {$cantidad}.",
                    ]);
                }
            });
    }
}
