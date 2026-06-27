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
        Schema::table('associations', function (Blueprint $table) {
            $table->string('unit_number', 100)->nullable()->after('water_account_number');
            $table->string('phone_number', 50)->nullable()->after('unit_number');
        });
    }

    public function down(): void
    {
        Schema::table('associations', function (Blueprint $table) {
            $table->dropColumn(['unit_number', 'phone_number']);
        });
    }
};
