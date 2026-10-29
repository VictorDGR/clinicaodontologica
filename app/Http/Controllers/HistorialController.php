<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HistorialController extends Controller
{
    public function index(Request $request): View
    {
        $buscar = trim((string) $request->query('buscar', ''));
        $pacientes = Paciente::query()
            ->when($buscar !== '', fn ($query) => $query->where(fn ($query) => $query
                ->whereLike('ci', "%{$buscar}%")
                ->orWhereLike('nombre', "%{$buscar}%")
                ->orWhereLike('apellido', "%{$buscar}%")))
            ->orderBy('apellido')
            ->orderBy('nombre')
            ->get();

        return view('historial.index', compact('pacientes', 'buscar'));
    }

    public function show(Paciente $paciente): View
    {
        $historial = $paciente->historialClinico;

        return view('historial.show', [
            'paciente' => $paciente,
            'historial' => $historial,
            'registros' => $historial->consultar(),
        ]);
    }
}
