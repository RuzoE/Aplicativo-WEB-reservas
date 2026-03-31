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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('status')->default('pendiente')->after('user_id');
            $table->string('payment_token')->unique()->nullable()->after('status');
            $table->decimal('down_payment_amount', 12, 2)->default(0)->after('payment_token');
            $table->boolean('is_paid')->default(false)->after('down_payment_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['status', 'payment_token', 'down_payment_amount', 'is_paid']);
        });
    }
};
