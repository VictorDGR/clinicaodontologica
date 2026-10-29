<?php

namespace App\Http\Controllers;

use App\Models\Cobro;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CobroController extends Controller
{
    /** Tarea 4.2: por defecto se muestran los cobros pendientes de pago. */
    public function index(Request $request): View
    {
        $estado = $request->query('estado', 'pendientes');
        $buscar = trim((string) $request->query('buscar', ''));

        $cobros = Cobro::with(['atencion.ficha.paciente', 'pagos'])
            ->when($estado === 'pendientes', fn ($query) => $query->where('estado', '!=', 'Pagado'))
            ->when($estado === 'pagados', fn ($query) => $query->where('estado', 'Pagado'))
            ->when($buscar !== '', fn ($query) => $query->whereHas('atencion.ficha.paciente', fn ($query) => $query
                ->whereLike('nombre', "%{$buscar}%")
                ->orWhereLike('apellido', "%{$buscar}%")
                ->orWhereLike('ci', "%{$buscar}%")))
            ->orderByDesc('fecha')
            ->orderByDesc('id')
            ->get();

        return view('cobros.index', compact('cobros', 'estado', 'buscar'));
    }

    public function show(Cobro $cobro): View
    {
        $cobro->load(['atencion.ficha.paciente', 'atencion.doctor', 'atencion.materiales.producto', 'atencion.cobro', 'pagos.secretaria']);

        return view('cobros.show', compact('cobro'));
    }

    /** Tareas 4.3 y 4.4: registra el pago y actualiza estado y saldo. */
    public function pagar(Request $request, Cobro $cobro): RedirectResponse
    {
        $saldo = $cobro->saldo;

        if ($saldo <= 0) {
            return redirect()->route('cobros.show', $cobro)->with('error', 'Este cobro ya está pagado.');
        }

        $datos = $request->validate([
            'monto' => ['required', 'numeric', 'min:0.01', 'max:'.$saldo],
            'fecha' => ['required', 'date', 'before_or_equal:today'],
            'metodo' => ['required', Rule::in(Cobro::METODOS)],
        ], [
            'required' => 'Este campo es obligatorio.',
            'monto.max' => 'El monto no puede ser mayor al saldo pendiente (Bs '.number_format($saldo, 2).').',
            'monto.min' => 'El monto debe ser mayor a 0.',
            'fecha.before_or_equal' => 'La fecha de pago no puede ser futura.',
        ]);

        $cobro->cobrar((float) $datos['monto'], $datos['fecha'], $datos['metodo'], $request->user()->secretaria?->id);

        return redirect()->route('cobros.show', $cobro)->with('success', 'Pago registrado correctamente.');
    }
}
