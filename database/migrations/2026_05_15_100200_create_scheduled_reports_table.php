<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scheduled_reports', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('section', ['hoa', 'management'])->index();
            $table->foreignId('property_id')
                ->nullable()
                ->constrained('properties')
                ->cascadeOnDelete();
            $table->foreignId('association_id')
                ->nullable()
                ->constrained('associations')
                ->cascadeOnDelete();
            $table->unsignedSmallInteger('period_months');
            $table->date('next_run_at');
            $table->date('last_run_at')->nullable();
            $table->json('recipients')->nullable();
            $table->enum('status', ['active', 'paused'])->default('active')->index();
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['status', 'next_run_at']);
        });

        Schema::create('scheduled_report_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scheduled_report_id')->constrained()->cascadeOnDelete();
            $table->date('period_start');
            $table->date('period_end');
            $table->timestamp('generated_at');
            $table->string('file_path')->nullable();
            $table->enum('status', ['success', 'failed'])->default('success');
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scheduled_report_runs');
        Schema::dropIfExists('scheduled_reports');
    }
};
