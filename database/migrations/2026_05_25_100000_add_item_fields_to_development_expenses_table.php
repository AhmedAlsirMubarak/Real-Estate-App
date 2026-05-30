<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('development_expenses', function (Blueprint $table) {
            $table->string('item_name')->after('category');
            $table->decimal('quantity', 10, 2)->default(1)->after('item_name');
            $table->string('unit', 50)->nullable()->after('quantity');
            $table->decimal('unit_cost', 14, 2)->default(0)->after('unit');
            $table->string('description')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('development_expenses', function (Blueprint $table) {
            $table->dropColumn(['item_name', 'quantity', 'unit', 'unit_cost']);
            $table->string('description')->nullable(false)->change();
        });
    }
};
