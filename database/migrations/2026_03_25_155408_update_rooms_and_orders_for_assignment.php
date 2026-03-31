<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->string('status')->default('disponible')->change();
        });

        // Map existing numeric status to string in rooms
        DB::table('rooms')->where('status', '1')->update(['status' => 'disponible']);
        DB::table('rooms')->where('status', '0')->update(['status' => 'mantenimiento']);

        Schema::table('orders', function (Blueprint $table) {
            $table->string('nombre_cliente')->nullable()->after('id');
            $table->unsignedBigInteger('room_id')->nullable()->change();
            
            // Si la columna room_type_id no existe, la añadimos
            if (!Schema::hasColumn('orders', 'room_type_id')) {
                $table->unsignedBigInteger('room_type_id')->nullable()->after('room_id');
                $table->foreign('room_type_id')->references('id')->on('room_types')->onDelete('cascade');
            }
        });

        // Optional: Populate room_type_id from room_id for existing orders
        DB::table('orders')
            ->join('rooms', 'orders.room_id', '=', 'rooms.id')
            ->whereNull('orders.room_type_id')
            ->update(['orders.room_type_id' => DB::raw('rooms.room_type_id')]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['room_type_id']);
            $table->dropColumn(['room_type_id', 'nombre_cliente']);
            $table->unsignedBigInteger('room_id')->nullable(false)->change();
        });

        Schema::table('rooms', function (Blueprint $table) {
            $table->boolean('status')->default(true)->change();
        });
    }
};
