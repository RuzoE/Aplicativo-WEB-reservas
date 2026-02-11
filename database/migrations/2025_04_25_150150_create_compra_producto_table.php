<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('compras', function (Blueprint $table) {
            $table->id();
            $table->decimal('total', 8, 2); // Total de la compra
            $table->string('estado')->default('pendiente'); // Estado de la compra
            $table->string('metodo_pago'); // Método de pago
            $table->timestamp('fecha_compra')->useCurrent(); // Fecha de compra
            $table->timestamps(); // created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compras');
    }
};

