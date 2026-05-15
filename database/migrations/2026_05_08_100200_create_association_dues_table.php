<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('association_dues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('association_id')->constrained('associations')->cascadeOnDelete();
            $table->foreignId('owner_id')->constrained('owners')->cascadeOnDelete();
            $table->unsignedTinyInteger('period_month');
            $table->unsignedSmallInteger('period_year');
            $table->decimal('amount', 12, 2);
            $table->date('due_date');
            $table->date('paid_at')->nullable();
            $table->enum('status', ['pending', 'paid', 'overdue', 'waived'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['association_id', 'owner_id', 'period_month', 'period_year'], 'assoc_due_unique');
            $table->index(['status', 'due_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('association_dues');
    }
};
