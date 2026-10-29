<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // HU7: usuarios y roles
        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('usuario', 50)->unique();
            $table->string('password');
            $table->enum('rol', ['administrador', 'secretaria', 'doctor']);
            $table->rememberToken();
            $table->timestamps();
        });

        // Persona -> Paciente
        Schema::create('pacientes', function (Blueprint $table): void {
            $table->id();
            $table->string('nombre', 80);
            $table->string('apellido', 80);
            $table->string('ci', 20)->unique();
            $table->string('telefono', 30);
            $table->date('fecha_nacimiento');
            $table->string('direccion');
            $table->timestamps();
        });

        Schema::create('historiales_clinicos', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('paciente_id')->unique()->constrained('pacientes')->cascadeOnDelete();
            $table->date('fecha_apertura');
            $table->timestamps();
        });

        // Persona -> Doctor
        Schema::create('doctores', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->string('nombre', 80);
            $table->string('apellido', 80);
            $table->string('ci', 20)->unique();
            $table->string('telefono', 30);
            $table->string('especialidad', 120);
            $table->string('matricula', 30)->unique();
            $table->timestamps();
        });

        // Persona -> Secretaria
        Schema::create('secretarias', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->string('nombre', 80);
            $table->string('apellido', 80);
            $table->string('ci', 20)->unique();
            $table->string('telefono', 30);
            $table->enum('turno', ['Mañana', 'Tarde']);
            $table->timestamps();
        });

        Schema::create('proveedores', function (Blueprint $table): void {
            $table->id();
            $table->string('nombre', 120);
            $table->string('nit', 20)->unique();
            $table->string('telefono', 30);
            $table->timestamps();
        });

        Schema::create('productos', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('proveedor_id')->constrained('proveedores');
            $table->string('codigo', 30)->unique();
            $table->string('nombre', 120);
            $table->decimal('precio', 10, 2);
            $table->unsignedInteger('stock')->default(0);
            $table->timestamps();
        });

        // HU5: fichas (nro = id)
        Schema::create('fichas', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('paciente_id')->constrained('pacientes');
            $table->foreignId('doctor_id')->constrained('doctores');
            $table->foreignId('secretaria_id')->nullable()->constrained('secretarias')->nullOnDelete();
            $table->date('fecha');
            $table->time('hora');
            $table->string('motivo');
            $table->enum('estado', ['Pendiente', 'Atendida', 'Cancelada'])->default('Pendiente');
            $table->timestamps();

            $table->index(['fecha', 'hora', 'doctor_id']);
        });

        // HU3: atención
        Schema::create('atenciones', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('ficha_id')->unique()->constrained('fichas');
            $table->foreignId('doctor_id')->constrained('doctores');
            $table->date('fecha');
            $table->string('tipo_diente', 60);
            $table->string('diagnostico');
            $table->string('procedimiento');
            $table->text('observaciones')->nullable();
            $table->string('ruta_analisis_radiografico')->nullable();
            $table->timestamps();
        });

        Schema::create('materiales_usados', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('atencion_id')->constrained('atenciones')->cascadeOnDelete();
            $table->foreignId('producto_id')->constrained('productos');
            $table->unsignedInteger('cantidad');
        });

        Schema::create('registros_clinicos', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('historial_clinico_id')->constrained('historiales_clinicos')->cascadeOnDelete();
            $table->foreignId('atencion_id')->unique()->constrained('atenciones')->cascadeOnDelete();
            $table->dateTime('fecha');
            $table->string('descripcion');
        });

        // HU4: cobros (nro = id) y sus pagos
        Schema::create('cobros', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('atencion_id')->unique()->constrained('atenciones');
            $table->date('fecha');
            $table->decimal('monto', 10, 2);
            $table->enum('estado', ['Pendiente', 'Parcial', 'Pagado'])->default('Pendiente');
            $table->timestamps();
        });

        Schema::create('pagos', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('cobro_id')->constrained('cobros')->cascadeOnDelete();
            $table->foreignId('secretaria_id')->nullable()->constrained('secretarias')->nullOnDelete();
            $table->date('fecha');
            $table->decimal('monto', 10, 2);
            $table->enum('metodo', ['Efectivo', 'Tarjeta', 'Transferencia', 'QR']);
            $table->timestamps();
        });

        // HU6: compras (nro = id)
        Schema::create('compras', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('proveedor_id')->constrained('proveedores');
            $table->foreignId('secretaria_id')->nullable()->constrained('secretarias')->nullOnDelete();
            $table->date('fecha');
            $table->decimal('total', 10, 2);
            $table->string('estado', 20)->default('Registrada');
            $table->timestamps();
        });

        Schema::create('detalle_compras', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('compra_id')->constrained('compras')->cascadeOnDelete();
            $table->foreignId('producto_id')->constrained('productos');
            $table->unsignedInteger('cantidad');
            $table->decimal('costo_unitario', 10, 2);
        });
    }

    public function down(): void
    {
        foreach ([
            'detalle_compras', 'compras', 'pagos', 'cobros', 'registros_clinicos', 'materiales_usados',
            'atenciones', 'fichas', 'productos', 'proveedores', 'secretarias', 'doctores',
            'historiales_clinicos', 'pacientes', 'users',
        ] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
