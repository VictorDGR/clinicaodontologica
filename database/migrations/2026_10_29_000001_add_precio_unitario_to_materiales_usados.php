<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Precio del producto al momento de usarlo; así el cobro no cambia si luego cambia el precio.
        Schema::table('materiales_usados', function (Blueprint $table): void {
            $table->decimal('precio_unitario', 10, 2)->default(0)->after('cantidad');
        });

        // Los materiales ya registrados toman el precio actual de su producto.
        // Subconsulta en lugar de UPDATE ... JOIN para que funcione en MySQL y PostgreSQL.
        DB::table('materiales_usados')->update([
            'precio_unitario' => DB::raw('(select productos.precio from productos where productos.id = materiales_usados.producto_id)'),
        ]);
    }

    public function down(): void
    {
        Schema::table('materiales_usados', function (Blueprint $table): void {
            $table->dropColumn('precio_unitario');
        });
    }
};
