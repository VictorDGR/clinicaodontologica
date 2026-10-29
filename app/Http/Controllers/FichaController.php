<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Ficha;
use App\Models\Paciente;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator as ValidatorFactory;
use Illuminate\Validation\Validator;
use Illuminate\View\View;

class FichaController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $buscar = trim((string) $request->query('buscar', ''));
        $fichas = Ficha::with(['paciente', 'doctor', 'atencion'])
            // El doctor solo ve las fichas que tiene asignadas.
            ->when($user->esDoctor(), fn ($query) => $query->where('doctor_id', $user->doctor?->id))
            ->when($buscar !== '', function ($query) use ($buscar): void {
                $like = "%{$buscar}%";
                $query->where(fn ($query) => $query
                    ->whereLike('fecha', $like)
                    ->orWhereLike('motivo', $like)
                    ->orWhereLike('estado', $like)
                    ->orWhereHas('paciente', fn ($query) => $query
                        ->whereLike('nombre', $like)
                        ->orWhereLike('apellido', $like)
                        ->orWhereLike('ci', $like))
                    ->orWhereHas('doctor', fn ($query) => $query
                        ->whereLike('nombre', $like)
                        ->orWhereLike('apellido', $like)));
            })
            ->orderByDesc('fecha')
            ->orderBy('hora')
            ->get();

        return view('fichas.index', compact('fichas', 'buscar'));
    }

    public function create(): View
    {
        return view('fichas.form', [
            'ficha' => null,
            'pacientes' => Paciente::orderBy('apellido')->orderBy('nombre')->get(),
            'doctores' => Doctor::orderBy('nombre')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Ficha::create([
            ...$this->validar($request),
            'secretaria_id' => $request->user()->secretaria?->id,
            'estado' => 'Pendiente',
        ]);

        return redirect()->route('fichas.index')->with('success', 'Ficha asignada correctamente.');
    }

    public function show(Request $request, Ficha $ficha): View
    {
        $this->autorizarDoctor($request, $ficha);
        $ficha->load(['paciente', 'doctor', 'secretaria', 'atencion.materiales.producto', 'atencion.cobro']);

        return view('fichas.show', compact('ficha'));
    }

    public function edit(Ficha $ficha): View|RedirectResponse
    {
        if ($ficha->atencion()->exists()) {
            return redirect()->route('fichas.show', $ficha)->with('error', 'La ficha ya fue atendida y no se puede modificar.');
        }

        return view('fichas.form', [
            'ficha' => $ficha,
            'pacientes' => Paciente::orderBy('apellido')->orderBy('nombre')->get(),
            'doctores' => Doctor::orderBy('nombre')->get(),
        ]);
    }

    public function update(Request $request, Ficha $ficha): RedirectResponse
    {
        if ($ficha->atencion()->exists()) {
            return redirect()->route('fichas.show', $ficha)->with('error', 'La ficha ya fue atendida y no se puede modificar.');
        }

        $datos = $this->validar($request, $ficha);
        $datos['estado'] = $request->validate(['estado' => ['required', 'in:Pendiente,Cancelada']])['estado'];
        $ficha->update($datos);

        return redirect()->route('fichas.index')->with('success', 'Ficha actualizada correctamente.');
    }

    public function destroy(Ficha $ficha): RedirectResponse
    {
        if ($ficha->atencion()->exists()) {
            return redirect()->route('fichas.index')->with('error', 'No se puede eliminar una ficha que ya fue atendida.');
        }

        $ficha->delete();

        return redirect()->route('fichas.index')->with('success', 'Ficha eliminada.');
    }

    /** Tarea 5.2: doctores sin otra ficha activa en la fecha y hora indicadas. */
    public function doctoresDisponibles(Request $request): JsonResponse
    {
        $datos = $request->validate([
            'fecha' => ['required', 'date'],
            'hora' => ['required', 'date_format:H:i'],
            'ignorar' => ['nullable', 'integer'],
        ]);
        $ocupados = $this->fichasActivasEn($datos['fecha'], $datos['hora'], (int) ($datos['ignorar'] ?? 0))->pluck('doctor_id');

        return response()->json(
            Doctor::whereNotIn('id', $ocupados)->orderBy('nombre')->get()
                ->map(fn (Doctor $doctor): array => [
                    'id' => $doctor->id,
                    'nombre' => $doctor->nombre_completo,
                    'especialidad' => $doctor->especialidad,
                ])
        );
    }

    private function validar(Request $request, ?Ficha $ficha = null): array
    {
        $reglasFecha = ['required', 'date'];
        if ($ficha === null || $request->input('fecha') !== $ficha->fecha) {
            $reglasFecha[] = 'after_or_equal:today';
        }

        $validator = ValidatorFactory::make($request->all(), [
            'paciente_id' => ['required', 'integer', 'exists:pacientes,id'],
            'doctor_id' => ['required', 'integer', 'exists:doctores,id'],
            'fecha' => $reglasFecha,
            'hora' => ['required', 'date_format:H:i'],
            'motivo' => ['required', 'string', 'min:3', 'max:255'],
        ], [
            'required' => 'Este campo es obligatorio.',
            'paciente_id.exists' => 'Selecciona un paciente registrado.',
            'doctor_id.exists' => 'Selecciona un doctor válido.',
            'fecha.after_or_equal' => 'La fecha debe ser hoy o posterior.',
            'hora.date_format' => 'Indica una hora válida.',
        ]);

        $validator->after(function (Validator $validator) use ($request, $ficha): void {
            // Tarea 5.4: el doctor no puede tener dos fichas activas en el mismo horario.
            $ocupado = $this->fichasActivasEn((string) $request->input('fecha'), (string) $request->input('hora'), $ficha?->id ?? 0)
                ->where('doctor_id', (int) $request->input('doctor_id'))
                ->exists();

            if ($ocupado) {
                $validator->errors()->add('doctor_id', 'El doctor ya tiene una ficha asignada en ese horario. Elige otro doctor u hora.');
            }
        });

        return $validator->validate();
    }

    private function fichasActivasEn(string $fecha, string $hora, int $ignorarId): Builder
    {
        return Ficha::query()
            ->whereDate('fecha', $fecha)
            ->whereTime('hora', $hora)
            ->where('estado', '!=', 'Cancelada')
            ->whereKeyNot($ignorarId);
    }

    private function autorizarDoctor(Request $request, Ficha $ficha): void
    {
        $user = $request->user();
        abort_if($user->esDoctor() && $ficha->doctor_id !== $user->doctor?->id, 403, 'Esta ficha no está asignada a ti.');
    }
}
