<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('association_meetings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('association_id')->constrained('associations')->cascadeOnDelete();
            $table->string('title_ar');
            $table->string('title_en');
            $table->dateTime('scheduled_at');
            $table->string('location_ar')->nullable();
            $table->string('location_en')->nullable();
            $table->text('agenda_ar')->nullable();
            $table->text('agenda_en')->nullable();
            $table->text('minutes_ar')->nullable();
            $table->text('minutes_en')->nullable();
            $table->enum('status', ['scheduled', 'completed', 'cancelled'])->default('scheduled');
            $table->timestamps();

            $table->index(['association_id', 'scheduled_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('association_meetings');
    }
};
