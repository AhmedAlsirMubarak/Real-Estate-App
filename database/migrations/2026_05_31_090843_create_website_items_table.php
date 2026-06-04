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
        Schema::create('website_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')->constrained('website_sections')->cascadeOnDelete();
            $table->string('title_ar')->nullable();
            $table->string('title_en')->nullable();
            $table->string('subtitle_ar')->nullable();
            $table->string('subtitle_en')->nullable();
            $table->text('body_ar')->nullable();
            $table->text('body_en')->nullable();
            $table->string('image')->nullable();
            $table->string('icon')->nullable();          // named icon key (building, users, wrench…)
            $table->string('value')->nullable();         // stat value e.g. "50+", "98%"
            $table->string('url')->nullable();
            $table->json('extra')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('website_items');
    }
};
