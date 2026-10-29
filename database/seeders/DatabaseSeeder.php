<?php

namespace Database\Seeders;

use App\Models\Atencion;
use App\Models\Cobro;
use App\Models\Compra;
use App\Models\Ficha;
use App\Models\Paciente;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\User;
use Illuminate\Database\Seeder;

/** Datos de ejemplo basados en el diagrama de objetos. */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Solo se cargan una vez: en cada despliegue se ejecuta el seeder y no debe duplicar datos.
        if (User::exists()) {
            return;
        }

        // En producción (Render) la contraseña del administrador viene de la variable ADMIN_PASSWORD.
        User::create(['usuario' => 'admin', 'password' => env('ADMIN_PASSWORD', 'admin123'), 'rol' => 'administrador']);

        $secretaria = User::create(['usuario' => 'lucia', 'password' => 'secretaria123', 'rol' => 'secretaria'])
            ->secretaria()->create(['nombre' => 'Lucía', 'apellido' => 'Mamani', 'ci' => '5123456', 'telefono' => '71234567', 'turno' => 'Mañana']);

        $carlos = User::create(['usuario' => 'carlos', 'password' => 'doctor123', 'rol' => 'doctor'])
            ->doctor()->create(['nombre' => 'Carlos', 'apellido' => 'Rojas', 'ci' => '4567890', 'telefono' => '72345678', 'especialidad' => 'Endodoncia', 'matricula' => 'MP-1021']);

        User::create(['usuario' => 'sofia', 'password' => 'doctor123', 'rol' => 'doctor'])
            ->doctor()->create(['nombre' => 'Sofía', 'apellido' => 'López', 'ci' => '4987654', 'telefono' => '73456789', 'especialidad' => 'Ortodoncia', 'matricula' => 'MP-1187']);

        $maria = Paciente::create(['nombre' => 'María', 'apellido' => 'Gutiérrez', 'ci' => '7845123', 'telefono' => '70745321', 'fecha_nacimiento' => '1990-03-12', 'direccion' => 'Zona Central']);
        $maria->historialClinico->update(['fecha_apertura' => '2026-03-03']);
        $jorge = Paciente::create(['nombre' => 'Jorge', 'apellido' => 'Fernández', 'ci' => '6123098', 'telefono' => '72011548', 'fecha_nacimiento' => '1985-08-21', 'direccion' => 'Av. Ilustres']);
        Paciente::create(['nombre' => 'Ana', 'apellido' => 'Vargas', 'ci' => '9034571', 'telefono' => '63490812', 'fecha_nacimiento' => '2000-01-05', 'direccion' => 'Barrio Norte']);

        $dentalSur = Proveedor::create(['nombre' => 'DentalSur', 'nit' => '1023456018', 'telefono' => '22441122']);

        // Stock final tras la compra (+20) y la atención (−2) del diagrama de objetos.
        $resina = Producto::create(['proveedor_id' => $dentalSur->id, 'codigo' => 'RES-001', 'nombre' => 'Resina compuesta', 'precio' => 45, 'stock' => 18]);
        Producto::create(['proveedor_id' => $dentalSur->id, 'codigo' => 'ANE-001', 'nombre' => 'Anestesia lidocaína', 'precio' => 30, 'stock' => 3]);
        Producto::create(['proveedor_id' => $dentalSur->id, 'codigo' => 'GUA-001', 'nombre' => 'Guantes de látex (caja)', 'precio' => 60, 'stock' => 12]);

        $compra = Compra::create(['proveedor_id' => $dentalSur->id, 'secretaria_id' => $secretaria->id, 'fecha' => '2026-10-10', 'total' => 900, 'estado' => 'Registrada']);
        $compra->detalles()->create(['producto_id' => $resina->id, 'cantidad' => 20, 'costo_unitario' => 45]);

        $ficha = Ficha::create(['paciente_id' => $maria->id, 'doctor_id' => $carlos->id, 'secretaria_id' => $secretaria->id, 'fecha' => '2026-10-14', 'hora' => '09:00', 'motivo' => 'Dolor de muela', 'estado' => 'Atendida']);

        $atencion = Atencion::create([
            'ficha_id' => $ficha->id,
            'doctor_id' => $carlos->id,
            'fecha' => '2026-10-14',
            'tipo_diente' => 'Molar 26',
            'diagnostico' => 'Caries profunda',
            'procedimiento' => 'Tratamiento de conducto',
            'observaciones' => 'Control en 15 días.',
        ]);
        // Cobro de 350 = procedimiento 260 + resina 2 × 45.
        $atencion->materiales()->create(['producto_id' => $resina->id, 'cantidad' => 2, 'precio_unitario' => 45]);
        $maria->historialClinico->registros()->create(['atencion_id' => $atencion->id, 'fecha' => '2026-10-14 09:30:00', 'descripcion' => 'Tratamiento de conducto']);

        $cobro = Cobro::create(['atencion_id' => $atencion->id, 'fecha' => '2026-10-14', 'monto' => 350, 'estado' => 'Pagado']);
        $cobro->pagos()->create(['secretaria_id' => $secretaria->id, 'fecha' => '2026-10-14', 'monto' => 350, 'metodo' => 'Efectivo']);

        // Ficha pendiente para probar el registro de atención.
        Ficha::create(['paciente_id' => $jorge->id, 'doctor_id' => $carlos->id, 'secretaria_id' => $secretaria->id, 'fecha' => now()->addDay()->toDateString(), 'hora' => '10:00', 'motivo' => 'Control', 'estado' => 'Pendiente']);
    }
}
