<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Polymorphic index on expenses — speeds up scope=property filter queries
        Schema::table('expenses', function (Blueprint $table) {
            $table->index(['expensable_type', 'expensable_id'], 'expenses_expensable_index');
            $table->index('expense_date', 'expenses_date_index');
        });

        // Status index on maintenance_requests — most queries filter by status
        Schema::table('maintenance_requests', function (Blueprint $table) {
            $table->index('status', 'maintenance_requests_status_index');
            $table->index(['unit_id', 'status'], 'maintenance_requests_unit_status_index');
        });

        // Period + status on payments — the most common filter combination
        Schema::table('payments', function (Blueprint $table) {
            $table->index(['year', 'month', 'status'], 'payments_period_status_index');
        });

        // Association dues queries filter by owner + status
        Schema::table('association_dues', function (Blueprint $table) {
            $table->index(['owner_id', 'status'], 'association_dues_owner_status_index');
            $table->index(['period_year', 'period_month'], 'association_dues_period_index');
        });
    }

    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropIndex('expenses_expensable_index');
            $table->dropIndex('expenses_date_index');
        });

        Schema::table('maintenance_requests', function (Blueprint $table) {
            $table->dropIndex('maintenance_requests_status_index');
            $table->dropIndex('maintenance_requests_unit_status_index');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex('payments_period_status_index');
        });

        Schema::table('association_dues', function (Blueprint $table) {
            $table->dropIndex('association_dues_owner_status_index');
            $table->dropIndex('association_dues_period_index');
        });
    }
};
