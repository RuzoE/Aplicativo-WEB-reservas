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
        Schema::table('users', function (Blueprint $table) {
            // Agregar estado del usuario (activo, inactivo, bloqueado)
            $table->enum('status', ['active', 'inactive', 'blocked'])->default('active')->after('is_admin');
            
            // Último login
            $table->timestamp('last_login_at')->nullable()->after('status');
            $table->string('last_login_ip')->nullable()->after('last_login_at');
            
            // Avatar
            $table->string('avatar_path')->nullable()->after('last_login_ip');
            
            // Índices para consultas
            $table->index('status');
            $table->index('last_login_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['last_login_at']);
            $table->dropColumn(['status', 'last_login_at', 'last_login_ip', 'avatar_path']);
        });
    }
};
