<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComprasTable extends Migration
{
    public function up(): void
    {
        Schema::create('compras', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('proveedor_id');
            $table->unsignedBigInteger('metodo_pago_id');
            $table->decimal('total', 10, 2);
            $table->timestamps(); // crea created_at y updated_at

            // Claves foráneas
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('proveedor_id')->references('id')->on('proveedores');
            $table->foreign('metodo_pago_id')->references('id')->on('metodos_pago');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('compras');
    }
}