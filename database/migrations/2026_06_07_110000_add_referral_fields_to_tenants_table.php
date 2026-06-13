<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->foreignId('referral_employee_id')
                  ->nullable()
                  ->after('emergency_contact')
                  ->constrained('users')
                  ->nullOnDelete();

            $table->decimal('referral_commission_rate', 5, 2)
                  ->nullable()
                  ->after('referral_employee_id')
                  ->comment('% commission for the employee who referred this tenant');
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropForeign(['referral_employee_id']);
            $table->dropColumn(['referral_employee_id', 'referral_commission_rate']);
        });
    }
};
