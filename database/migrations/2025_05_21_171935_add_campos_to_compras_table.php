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
        Schema::table('compras', function (Blueprint $table) {
            if (!Schema::hasColumn('compras', 'total')) {
                $table->decimal('total', 8, 2)->after('id');
            }
            if (!Schema::hasColumn('compras', 'estado')) {
                $table->string('estado')->default('pendiente')->after('total');
            }
            if (!Schema::hasColumn('compras', 'metodo_pago')) {
                $table->string('metodo_pago')->after('estado');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('compras', function (Blueprint $table) {
            //
        });
    }
};
