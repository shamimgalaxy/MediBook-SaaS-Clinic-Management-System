<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
       Schema::create('invoices', function (Blueprint $table) {
    $table->id();
    $table->string('invoice_number')->unique(); // INV-2025-00001
    $table->foreignId('appointment_id')->constrained()->cascadeOnDelete(); // tenant ✅
    $table->unsignedBigInteger('patient_id'); // user in central DB, no constraint
    $table->foreignId('doctor_id')->constrained()->cascadeOnDelete(); // tenant ✅
    $table->decimal('subtotal', 10, 2)->default(0);
    $table->decimal('tax', 10, 2)->default(0);        // amount, not %
    $table->decimal('discount', 10, 2)->default(0);   // amount, not %
    $table->decimal('total', 10, 2)->default(0);
    $table->enum('status', ['draft', 'sent', 'paid'])->default('draft');
    $table->date('due_date')->nullable();
    $table->timestamp('paid_at')->nullable();
    $table->text('notes')->nullable();
    $table->timestamps();
});
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};