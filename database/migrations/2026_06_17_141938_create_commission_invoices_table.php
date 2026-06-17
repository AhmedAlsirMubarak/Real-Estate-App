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
        Schema::create('commission_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->string('invoice_number')->unique();
            $table->enum('invoice_for', ['owner', 'client']);
            $table->string('recipient_name');
            $table->decimal('duration_months', 8, 2);
            $table->decimal('monthly_rent', 12, 3);
            $table->decimal('commission_rate', 5, 2);
            $table->decimal('total_rent', 12, 3);
            $table->decimal('commission_amount', 12, 3);
            $table->date('invoice_date');
            $table->text('notes')->nullable();
            $table->string('file_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commission_invoices');
    }
};
