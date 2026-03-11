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
        Schema::create('maintenance_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained('rooms')->onDelete('cascade');
            $table->enum('priority', ['baja', 'normal', 'urgente'])->default('normal');
            $table->enum('status', ['asignada', 'en_proceso', 'completada', 'cancelada'])->default('asignada');
            $table->text('description');
            $table->text('notes')->nullable();
            $table->unsignedInteger('estimated_time')->nullable(); // en minutos
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_orders');
    }
};
