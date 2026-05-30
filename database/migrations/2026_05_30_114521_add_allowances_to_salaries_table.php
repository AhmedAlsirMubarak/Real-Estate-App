<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('salaries', function (Blueprint $table) {
            $table->decimal('housing_allowance',   12, 2)->default(0)->after('base_salary');
            $table->decimal('transport_allowance', 12, 2)->default(0)->after('housing_allowance');
            $table->decimal('food_allowance',      12, 2)->default(0)->after('transport_allowance');
            $table->decimal('other_allowances',    12, 2)->default(0)->after('food_allowance');
        });
    }

    public function down(): void
    {
        Schema::table('salaries', function (Blueprint $table) {
            $table->dropColumn(['housing_allowance', 'transport_allowance', 'food_allowance', 'other_allowances']);
        });
    }
};
