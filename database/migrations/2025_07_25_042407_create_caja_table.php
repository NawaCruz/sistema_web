<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCajaTable extends Migration
{
    public function up(): void
    {
        Schema::create('caja', function (Blueprint $table) {
            $table->id('id_caja');
            $table->unsignedBigInteger('id_user');

            $table->decimal('monto_apertura', 10, 2);
            $table->decimal('monto_total', 10, 2)->default(0);
            $table->decimal('monto_cierre', 10, 2)->nullable();

            $table->enum('estado', ['Abierta', 'Cerrada'])->default('Abierta');

            $table->timestamps(); // incluye created_at (apertura) y updated_at (cierre)

            // Clave foránea
            $table->foreign('id_user')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('caja');
    }
}
