<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');        // referensi ke User Service
            $table->unsignedBigInteger('event_id');       // referensi ke Event Service
            $table->string('ticket_code')->nullable();    // generate saat confirmed
            $table->enum('status', ['pending', 'confirmed', 'cancelled'])->default('pending');
            $table->integer('quantity')->default(1);
            $table->decimal('total_price', 12, 2);
            $table->unsignedBigInteger('transaction_id')->nullable(); // dari Payment Service
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
