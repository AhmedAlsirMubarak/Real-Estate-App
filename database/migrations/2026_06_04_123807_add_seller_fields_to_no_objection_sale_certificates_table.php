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
        Schema::table('no_objection_sale_certificates', function (Blueprint $table) {
            $table->string('seller_name')->nullable()->after('ref_number');
            $table->string('seller_id', 50)->nullable()->after('seller_name');
            $table->string('unit_number', 50)->nullable()->after('seller_id');
        });
    }

    public function down(): void
    {
        Schema::table('no_objection_sale_certificates', function (Blueprint $table) {
            $table->dropColumn(['seller_name', 'seller_id', 'unit_number']);
        });
    }
};
