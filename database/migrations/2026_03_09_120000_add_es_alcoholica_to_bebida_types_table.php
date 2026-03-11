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
        Schema::table('bebida_types', function (Blueprint $table) {
            $table->boolean('es_alcoholica')->default(true)->after('descripcion');
            $table->index('es_alcoholica');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bebida_types', function (Blueprint $table) {
            $table->dropIndex(['es_alcoholica']);
            $table->dropColumn('es_alcoholica');
        });
    }
};
