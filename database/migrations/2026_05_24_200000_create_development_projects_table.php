<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('development_projects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['residential', 'commercial', 'mixed']);
            $table->string('location');
            $table->enum('status', ['planning', 'foundation', 'structure', 'finishing', 'handover', 'completed'])->default('planning');
            $table->decimal('total_budget', 14, 2);
            $table->json('category_budgets')->nullable();
            $table->tinyInteger('progress_percentage')->default(0)->unsigned();
            $table->date('start_date');
            $table->date('estimated_completion_date');
            $table->string('project_manager_name');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('development_projects');
    }
};
