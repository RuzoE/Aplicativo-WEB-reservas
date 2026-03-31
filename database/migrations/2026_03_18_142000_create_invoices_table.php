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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('stay_id');
            $table->unsignedBigInteger('folio_id');
            $table->string('invoice_number')->unique();
            $table->decimal('subtotal', 15, 2);
            $table->decimal('tax', 15, 2);
            $table->decimal('total', 15, 2);
            $table->decimal('discount', 15, 2)->default(0);
            $table->boolean('is_paid')->default(true);
            $table->string('payment_method')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('stay_id')->references('id')->on('stays')->onDelete('cascade');
            $table->foreign('folio_id')->references('id')->on('folios')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
