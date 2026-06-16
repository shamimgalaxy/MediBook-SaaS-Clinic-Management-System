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
    Schema::create('prescriptions', function (Blueprint $table) {
        $table->id();
        $table->foreignId('appointment_id')->constrained()->cascadeOnDelete();
        $table->foreignId('doctor_id')->constrained()->cascadeOnDelete();
        $table->unsignedBigInteger('patient_id');
        $table->string('chief_complaint')->nullable();
        $table->string('diagnosis')->nullable();
        $table->text('notes')->nullable();
        $table->date('follow_up_date')->nullable();
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('prescriptions');
}
};
