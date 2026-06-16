<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');                    // Basic, Pro, Enterprise
            $table->string('slug')->unique();          // basic, pro, enterprise
            $table->decimal('price', 10, 2);           // 500.00, 1200.00, 2500.00
            $table->integer('max_doctors');            // 1, 5, -1 (unlimited)
            $table->integer('max_appointments');       // 50, -1, -1
            $table->boolean('sms_notifications')->default(false);
            $table->boolean('custom_domain')->default(false);
            $table->boolean('excel_reports')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Also add plan_id to tenants table
        Schema::table('tenants', function (Blueprint $table) {
            $table->foreignId('plan_id')->nullable()->constrained('subscription_plans')->nullOnDelete();
            $table->timestamp('plan_expires_at')->nullable();
            $table->boolean('on_trial')->default(true);
            $table->timestamp('trial_ends_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropConstrainedForeignId('plan_id');
            $table->dropColumn(['plan_expires_at', 'on_trial', 'trial_ends_at']);
        });

        Schema::dropIfExists('subscription_plans');
    }
};