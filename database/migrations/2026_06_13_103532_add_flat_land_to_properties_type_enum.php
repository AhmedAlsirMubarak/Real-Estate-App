<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE properties MODIFY COLUMN type ENUM('apartment_building','villa','farm','chalet','flat','land') NOT NULL DEFAULT 'apartment_building'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE properties MODIFY COLUMN type ENUM('apartment_building','villa','farm','chalet') NOT NULL DEFAULT 'apartment_building'");
    }
};
