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
            // Columna para identificar si el usuario es empleado
            $table->boolean('is_employee')->default(false)->after('is_admin')->index();
            
            // Campo para referencia a qué departamento pertenece (opcional, para mejor tracking)
            $table->enum('employee_department', [
                'recepcion',
                'minibar',
                'mantenimiento',
                'reservas',
                'administrador'
            ])->nullable()->after('is_employee');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_employee', 'employee_department']);
        });
    }
};
