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
    Schema::table('users', function (Blueprint $table) {
        $table->string('phone')->nullable()->after('email');
        $table->date('date_of_birth')->nullable()->after('phone');
        $table->enum('gender', ['male', 'female', 'other'])->nullable()->after('date_of_birth');
        $table->string('address')->nullable()->after('gender');
        $table->string('blood_group')->nullable()->after('address');
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn(['phone', 'date_of_birth', 'gender', 'address', 'blood_group']);
    });
}
};
