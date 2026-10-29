<?php

namespace App\Http\Controllers;

use App\Models\Cobro;
use App\Models\Doctor;
use App\Models\Ficha;
use App\Models\Paciente;
use App\Models\Producto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View|RedirectResponse
    {
        // Cada rol entra a su lugar de trabajo: el doctor va directo a sus fichas.
        if ($request->user()->esDoctor()) {
            return redirect()->route('fichas.index');
        }

        return view('dashboard', [
            'pacientes' => Paciente::count(),
            'fichasHoy' => Ficha::whereDate('fecha', now()->toDateString())->where('estado', '!=', 'Cancelada')->count(),
            'cobrosPendientes' => Cobro::where('estado', '!=', 'Pagado')->count(),
            'stockBajo' => Producto::where('stock', '<=', Producto::STOCK_MINIMO)->count(),
            'doctores' => Doctor::orderBy('nombre')->get(),
        ]);
    }
}
