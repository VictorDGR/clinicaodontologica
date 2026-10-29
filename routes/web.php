<?php

use App\Http\Controllers\AtencionController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CobroController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\FichaController;
use App\Http\Controllers\HistorialController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\PacienteController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\SecretariaController;
use Illuminate\Support\Facades\Route;

foreach (['paciente', 'ficha', 'atencion', 'cobro', 'producto', 'proveedor', 'compra', 'doctor', 'secretaria'] as $parametro) {
    Route::pattern($parametro, '[0-9]+');
}

// HU7: inicio de sesión
Route::middleware('guest')->group(function (): void {
    Route::get('login', [AuthController::class, 'create'])->name('login');
    Route::post('login', [AuthController::class, 'store']);
});

Route::middleware('auth')->group(function (): void {
    Route::post('logout', [AuthController::class, 'destroy'])->name('logout');
    Route::get('/', DashboardController::class)->name('inicio');

    // Secretaria (y administrador)
    Route::middleware('rol:secretaria')->group(function (): void {
        // HU1: pacientes
        Route::resource('pacientes', PacienteController::class);

        // HU5: fichas
        Route::get('fichas/doctores-disponibles', [FichaController::class, 'doctoresDisponibles'])->name('fichas.doctores-disponibles');
        Route::resource('fichas', FichaController::class)->except(['index', 'show']);

        // HU4: cobros
        Route::get('cobros', [CobroController::class, 'index'])->name('cobros.index');
        Route::get('cobros/{cobro}', [CobroController::class, 'show'])->name('cobros.show');
        Route::post('cobros/{cobro}/pagos', [CobroController::class, 'pagar'])->name('cobros.pagar');

        // HU6: productos, proveedores, compras e inventario
        Route::resource('productos', ProductoController::class)->except('show');
        Route::resource('proveedores', ProveedorController::class)->except('show')->parameters(['proveedores' => 'proveedor']);
        Route::resource('compras', CompraController::class)->only(['index', 'create', 'store', 'show']);
        Route::get('inventario', InventarioController::class)->name('inventario.index');
    });

    // Secretaria y doctor: listado y detalle de fichas (el doctor solo ve las suyas)
    Route::middleware('rol:secretaria,doctor')->group(function (): void {
        Route::get('fichas', [FichaController::class, 'index'])->name('fichas.index');
        Route::get('fichas/{ficha}', [FichaController::class, 'show'])->name('fichas.show');
    });

    // Doctor (y administrador)
    Route::middleware('rol:doctor')->group(function (): void {
        // HU2: historial clínico
        Route::get('historial', [HistorialController::class, 'index'])->name('historial.index');
        Route::get('historial/{paciente}', [HistorialController::class, 'show'])->name('historial.show');

        // HU3: atención
        Route::get('fichas/{ficha}/atencion', [AtencionController::class, 'create'])->name('atenciones.create');
        Route::post('fichas/{ficha}/atencion', [AtencionController::class, 'store'])->name('atenciones.store');
        Route::get('atenciones/{atencion}/radiografia', [AtencionController::class, 'radiografia'])->name('atenciones.radiografia');
    });

    // Administrador: HU7
    Route::middleware('rol:administrador')->group(function (): void {
        Route::resource('doctores', DoctorController::class)->except('show')->parameters(['doctores' => 'doctor']);
        Route::resource('secretarias', SecretariaController::class)->except('show');
    });
});
