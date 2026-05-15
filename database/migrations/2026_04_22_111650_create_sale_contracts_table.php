<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sale_contracts', function (Blueprint $table) {
            $table->id();
            $table->string('contract_number')->unique();
            $table->foreignId('unit_id')->constrained()->cascadeOnDelete();
            $table->foreignId('buyer_id')->constrained()->cascadeOnDelete();
            $table->decimal('total_price', 12, 2);
            $table->decimal('down_payment', 12, 2)->default(0);
            $table->enum('payment_plan', ['full', 'installments'])->default('full');
            $table->unsignedInteger('installment_count')->nullable();
            $table->decimal('installment_amount', 10, 2)->nullable();
            $table->date('contract_date');
            $table->date('handover_date')->nullable();
            $table->enum('status', ['draft', 'active', 'completed', 'cancelled'])->default('draft');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_contracts');
    }
};
