<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetalleComprasTable extends Migration
{
    public function up(): void
    {
        Schema::create('detalle_compras', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_compra');
            $table->unsignedBigInteger('id_producto');
            $table->integer('cantidad');
            $table->decimal('precio_unitario', 10, 2);
            $table->decimal('subtotal', 10, 2);

            $table->timestamps();

            // Relaciones foráneas
            $table->foreign('id_compra')->references('id')->on('compras')->onDelete('cascade');
            $table->foreign('id_producto')->references('id')->on('productos');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detalle_compras');
    }
}
