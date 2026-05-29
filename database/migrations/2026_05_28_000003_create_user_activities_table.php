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
        Schema::create('user_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('action', 50); // login, logout, password_change, permission_change, etc.
            $table->string('activity_type', 100)->nullable(); // Tipo específico de actividad
            $table->text('description')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->string('device_name')->nullable();
            $table->enum('status', ['success', 'failed', 'attempted'])->default('success');
            $table->json('metadata')->nullable(); // Datos adicionales JSON
            $table->timestamp('created_at')->useCurrent();
            
            // Índices
            $table->index('user_id');
            $table->index('action');
            $table->index('created_at');
            $table->index(['user_id', 'created_at']);
            $table->index(['action', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_activities');
    }
};
