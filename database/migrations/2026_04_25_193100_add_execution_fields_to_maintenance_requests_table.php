<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('maintenance_requests', 'required_tools')) {
            Schema::table('maintenance_requests', function (Blueprint $table) {
                $table->text('required_tools')->nullable()->after('employee_notes');
            });
        }

        if (! Schema::hasColumn('maintenance_requests', 'requires_external_worker')) {
            Schema::table('maintenance_requests', function (Blueprint $table) {
                $table->boolean('requires_external_worker')->default(false)->after('required_tools');
            });
        }

        if (! Schema::hasColumn('maintenance_requests', 'external_worker_name')) {
            Schema::table('maintenance_requests', function (Blueprint $table) {
                $table->string('external_worker_name')->nullable()->after('requires_external_worker');
            });
        }

        if (! Schema::hasColumn('maintenance_requests', 'external_worker_cost')) {
            Schema::table('maintenance_requests', function (Blueprint $table) {
                $table->decimal('external_worker_cost', 10, 2)->nullable()->after('external_worker_name');
            });
        }
    }

    public function down(): void
    {
        $hasRequiredTools = Schema::hasColumn('maintenance_requests', 'required_tools');
        $hasRequiresExternalWorker = Schema::hasColumn('maintenance_requests', 'requires_external_worker');
        $hasExternalWorkerName = Schema::hasColumn('maintenance_requests', 'external_worker_name');
        $hasExternalWorkerCost = Schema::hasColumn('maintenance_requests', 'external_worker_cost');

        if (! ($hasRequiredTools || $hasRequiresExternalWorker || $hasExternalWorkerName || $hasExternalWorkerCost)) {
            return;
        }

        Schema::table('maintenance_requests', function (Blueprint $table) use ($hasRequiredTools, $hasRequiresExternalWorker, $hasExternalWorkerName, $hasExternalWorkerCost) {
            $columns = [];

            if ($hasRequiredTools) {
                $columns[] = 'required_tools';
            }
            if ($hasRequiresExternalWorker) {
                $columns[] = 'requires_external_worker';
            }
            if ($hasExternalWorkerName) {
                $columns[] = 'external_worker_name';
            }
            if ($hasExternalWorkerCost) {
                $columns[] = 'external_worker_cost';
            }

            if (! empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
