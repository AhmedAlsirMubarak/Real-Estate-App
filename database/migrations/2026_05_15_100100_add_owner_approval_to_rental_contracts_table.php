<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rental_contracts', function (Blueprint $table) {
            $table->boolean('owner_approval_required')->default(false)->after('status');
            $table->enum('owner_approval_status', ['pending', 'approved', 'rejected'])
                ->nullable()
                ->after('owner_approval_required');
            $table->timestamp('owner_approval_at')->nullable()->after('owner_approval_status');
            $table->foreignId('approved_by_owner_id')
                ->nullable()
                ->after('owner_approval_at')
                ->constrained('owners')
                ->nullOnDelete();
            $table->text('owner_approval_notes')->nullable()->after('approved_by_owner_id');
        });
    }

    public function down(): void
    {
        Schema::table('rental_contracts', function (Blueprint $table) {
            $table->dropConstrainedForeignId('approved_by_owner_id');
            $table->dropColumn([
                'owner_approval_required',
                'owner_approval_status',
                'owner_approval_at',
                'owner_approval_notes',
            ]);
        });
    }
};
