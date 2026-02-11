<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('charges', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('folio_id');
            $table->string('source');
            $table->string('description');
            $table->decimal('amount', 12, 2);
            $table->decimal('tax', 12, 2)->default(0);
            $table->unsignedBigInteger('posted_by')->nullable();
            $table->dateTime('posted_at')->nullable();
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->timestamps();

            $table->index(['folio_id', 'posted_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('charges');
    }
};
