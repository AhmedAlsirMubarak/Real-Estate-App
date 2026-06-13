<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'type')) {
                $table->enum('type', ['rent', 'deposit'])->default('rent')->after('tenant_id');
            }
            // Add standalone index so the FK keeps its supporting index after the unique is dropped
            $table->index('rental_contract_id', 'payments_rental_contract_id_index');
            $table->dropUnique('payments_rental_contract_id_month_year_unique');
            $table->unique(['rental_contract_id', 'month', 'year', 'type'], 'payments_contract_month_year_type_unique');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropUnique('payments_contract_month_year_type_unique');
            $table->index('rental_contract_id');
            $table->dropIndex('payments_rental_contract_id_index');
            $table->dropColumn('type');
        });
    }
};
