<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('folio_id');
            $table->string('method');
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3)->default('USD');
            $table->unsignedBigInteger('received_by')->nullable();
            $table->dateTime('received_at')->nullable();
            $table->string('external_ref')->nullable();
            $table->timestamps();

            $table->index(['folio_id', 'received_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
