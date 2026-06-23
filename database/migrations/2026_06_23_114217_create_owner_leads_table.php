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
        Schema::create('owner_leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name');
            $table->string('mobile')->nullable();
            $table->string('email')->nullable();
            $table->string('location')->nullable();
            $table->string('property_type')->default('any');
            $table->string('purpose')->default('both');
            $table->unsignedBigInteger('min_budget')->nullable();
            $table->unsignedBigInteger('max_budget')->nullable();
            $table->unsignedTinyInteger('bedrooms')->nullable();
            $table->text('notes')->nullable();
            $table->string('status')->default('new');
            $table->string('source')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('owner_leads');
    }
};
