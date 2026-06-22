<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE development_expenses MODIFY COLUMN category ENUM(
            'construction', 'manpower', 'materials', 'contractor_fees',
            'permits', 'equipment_rental', 'design_engineering', 'utilities',
            'land_cost'
        ) NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE development_expenses MODIFY COLUMN category ENUM(
            'construction', 'manpower', 'materials', 'contractor_fees',
            'permits', 'equipment_rental', 'design_engineering', 'utilities'
        ) NOT NULL");
    }
};
