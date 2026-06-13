<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('users')->cascadeOnDelete();
            $table->date('date');
            $table->enum('status', ['present', 'absent', 'late', 'half_day', 'holiday'])->default('present');
            $table->time('check_in')->nullable();
            $table->time('check_out')->nullable();
            $table->decimal('hours_worked', 4, 2)->nullable();
            $table->string('notes', 500)->nullable();
            $table->timestamps();
            $table->unique(['employee_id', 'date']);
            $table->index('date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_attendance');
    }
};
