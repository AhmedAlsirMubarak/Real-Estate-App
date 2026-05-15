<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->string('unit_number')->nullable();
            $table->unsignedInteger('floor')->nullable();
            $table->enum('type', ['apartment', 'shop', 'office', 'studio', 'villa_unit', 'farm_unit', 'chalet_unit'])->default('apartment');
            $table->decimal('area', 8, 2)->nullable();
            $table->unsignedInteger('bedrooms')->nullable();
            $table->unsignedInteger('bathrooms')->nullable();
            $table->enum('listing_type', ['rent', 'sale', 'both'])->default('rent');
            $table->decimal('rent_price', 10, 2)->nullable();
            $table->decimal('sale_price', 12, 2)->nullable();
            $table->enum('status', ['available', 'rented', 'sold', 'reserved', 'maintenance'])->default('available');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['property_id', 'status']);
            $table->index(['listing_type', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
