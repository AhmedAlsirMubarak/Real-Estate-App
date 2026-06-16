<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->enum('type', ['apartment_building', 'villa', 'farm', 'chalet', 'flat', 'land'])
                ->default('apartment_building')
                ->change();
        });
    }

    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->enum('type', ['apartment_building', 'villa', 'farm', 'chalet'])
                ->default('apartment_building')
                ->change();
        });
    }
};
