<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('property_owners', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('properties')->cascadeOnDelete();
            $table->foreignId('owner_id')->constrained('owners')->cascadeOnDelete();
            $table->decimal('ownership_percentage', 5, 2)->default(100.00);
            $table->boolean('is_primary')->default(false);
            $table->date('since_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['property_id', 'owner_id']);
            $table->index(['property_id', 'is_primary']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_owners');
    }
};
