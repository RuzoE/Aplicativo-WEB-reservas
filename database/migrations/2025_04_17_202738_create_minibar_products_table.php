<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('minibar_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bebida_type_id')
                  ->constrained('bebida_types')
                  ->onDelete('cascade');
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->decimal('precio', 8, 2);
            $table->integer('stock')->default(0);
            $table->string('imagen')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('minibar_products', function (Blueprint $table) {
            $table->dropColumn('tipo');
        });
    }
};
