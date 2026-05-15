<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('property_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('payment_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('sale_contract_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('type', ['rent_collection', 'property_sale'])->default('rent_collection');
            $table->decimal('base_amount', 12, 2);
            $table->decimal('rate', 5, 2);
            $table->decimal('commission_amount', 12, 2);
            $table->text('notes')->nullable();
            $table->timestamp('recorded_at')->nullable();
            $table->timestamps();

            $table->index(['employee_id', 'type']);
            $table->index('recorded_at');
            $table->unique(['payment_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_commissions');
    }
};
