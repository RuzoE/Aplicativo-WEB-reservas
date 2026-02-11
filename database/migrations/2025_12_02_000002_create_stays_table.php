<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('stays', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reservation_id')->nullable();
            $table->unsignedBigInteger('room_id')->nullable();
            $table->unsignedBigInteger('guest_id');
            $table->string('status')->index();
            $table->dateTime('arrival_at')->nullable();
            $table->dateTime('departure_at')->nullable();
            $table->dateTime('actual_check_in_at')->nullable();
            $table->dateTime('actual_check_out_at')->nullable();
            $table->unsignedInteger('adults')->default(1);
            $table->unsignedInteger('children')->default(0);
            $table->string('rate_plan')->nullable();
            $table->decimal('daily_rate', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stays');
    }
};
