<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Convert existing plain string values to JSON arrays before changing column type
        DB::table('associations')
            ->whereNotNull('unit_number')
            ->where('unit_number', '!=', '')
            ->whereRaw("unit_number NOT LIKE '[%'")
            ->get(['id', 'unit_number'])
            ->each(function ($row) {
                DB::table('associations')
                    ->where('id', $row->id)
                    ->update(['unit_number' => json_encode([$row->unit_number])]);
            });

        Schema::table('associations', function (Blueprint $table) {
            $table->text('unit_number')->nullable()->change();
        });
    }

    public function down(): void
    {
        // Convert JSON arrays back to single strings (first element only)
        DB::table('associations')
            ->whereNotNull('unit_number')
            ->where('unit_number', 'like', '[%')
            ->get(['id', 'unit_number'])
            ->each(function ($row) {
                $arr = json_decode($row->unit_number, true);
                DB::table('associations')
                    ->where('id', $row->id)
                    ->update(['unit_number' => $arr[0] ?? null]);
            });

        Schema::table('associations', function (Blueprint $table) {
            $table->string('unit_number', 100)->nullable()->change();
        });
    }
};
