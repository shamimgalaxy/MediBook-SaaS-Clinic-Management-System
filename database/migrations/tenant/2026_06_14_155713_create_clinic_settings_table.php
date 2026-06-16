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
    Schema::create('clinic_settings', function (Blueprint $table) {
        $table->id();

        // ── General info ───────────────────────────────────────
        $table->string('clinic_name')->nullable();
        $table->string('tagline')->nullable();
        $table->string('phone')->nullable();
        $table->string('email')->nullable();
        $table->string('website')->nullable();
        $table->text('address')->nullable();
        $table->string('logo')->nullable();         // storage path

        // ── Working hours (JSON per day) ───────────────────────
        $table->json('working_hours')->nullable();
        // Format: {"monday":{"open":true,"start":"09:00","end":"17:00"}, ...}

        // ── Notification preferences ───────────────────────────
        $table->boolean('notify_appointment_booked')->default(true);
        $table->boolean('notify_appointment_status')->default(true);
        $table->boolean('notify_payment_received')->default(true);
        $table->boolean('notify_sms_enabled')->default(false);

        // ── Invoice / prescription settings ────────────────────
        $table->string('invoice_prefix')->default('INV');
        $table->decimal('default_tax', 5, 2)->default(0);
        $table->text('invoice_footer_note')->nullable();

        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('clinic_settings');
}
};
