<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('development_expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('development_project_id')->constrained()->cascadeOnDelete();
            $table->enum('category', [
                'construction', 'manpower', 'materials', 'contractor_fees',
                'permits', 'equipment_rental', 'design_engineering', 'utilities',
            ]);
            $table->string('description');
            $table->decimal('amount', 14, 2);
            $table->date('expense_date');
            $table->timestamps();

            $table->index(['development_project_id', 'category']);
            $table->index('expense_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('development_expenses');
    }
};
