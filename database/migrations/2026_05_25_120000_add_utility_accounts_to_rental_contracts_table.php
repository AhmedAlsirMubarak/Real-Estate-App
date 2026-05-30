<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rental_contracts', function (Blueprint $table) {
            $table->string('electricity_account_number', 100)->nullable()->after('contract_file');
            $table->string('water_account_number', 100)->nullable()->after('electricity_account_number');
        });
    }

    public function down(): void
    {
        Schema::table('rental_contracts', function (Blueprint $table) {
            $table->dropColumn(['electricity_account_number', 'water_account_number']);
        });
    }
};
