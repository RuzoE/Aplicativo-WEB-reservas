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
        Schema::table('minibar_products', function (Blueprint $table) {
            // Añadir la columna 'tipo' como clave foránea
            $table->foreignId('tipo')->nullable()->constrained('bebida_types')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('minibar_products', function (Blueprint $table) {
            // Eliminar la columna 'tipo'
            $table->dropColumn('tipo');
        });
    }
};
