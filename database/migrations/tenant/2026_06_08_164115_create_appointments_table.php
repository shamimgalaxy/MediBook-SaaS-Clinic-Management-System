<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('patient_id');
            $table->date('appointment_date');
            $table->time('appointment_time');
            $table->string('visit_type')->default('new'); // new, follow_up, emergency
            $table->string('status')->default('pending'); // pending, confirmed, in_progress, completed, cancelled
            $table->text('reason')->nullable();
            $table->text('notes')->nullable();
            $table->decimal('fee', 10, 2)->default(0);
            $table->string('payment_status')->default('unpaid'); // unpaid, paid
            $table->string('payment_method')->nullable(); // cash, bkash, card
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};