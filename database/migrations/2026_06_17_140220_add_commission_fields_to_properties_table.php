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
        Schema::table('properties', function (Blueprint $table) {
            $table->decimal('rent_commission_rate', 8, 2)->nullable()->after('bathrooms');
            $table->decimal('sale_commission_rate', 8, 2)->nullable()->after('rent_commission_rate');
            $table->string('commission_payer')->nullable()->after('sale_commission_rate'); // owner, tenant, buyer, shared
            $table->text('commission_notes')->nullable()->after('commission_payer');
        });
    }

    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn(['rent_commission_rate', 'sale_commission_rate', 'commission_payer', 'commission_notes']);
        });
    }
};
