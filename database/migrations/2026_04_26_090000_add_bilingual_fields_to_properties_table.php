<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->string('name_ar')->nullable()->after('name');
            $table->string('name_en')->nullable()->after('name_ar');
            $table->string('address_ar')->nullable()->after('address');
            $table->string('address_en')->nullable()->after('address_ar');
            $table->string('city_ar')->nullable()->after('city');
            $table->string('city_en')->nullable()->after('city_ar');
            $table->text('description_ar')->nullable()->after('description');
            $table->text('description_en')->nullable()->after('description_ar');
        });

        DB::table('properties')->update([
            'name_ar' => DB::raw('name'),
            'name_en' => DB::raw('name'),
            'address_ar' => DB::raw('address'),
            'address_en' => DB::raw('address'),
            'city_ar' => DB::raw('city'),
            'city_en' => DB::raw('city'),
            'description_ar' => DB::raw('description'),
            'description_en' => DB::raw('description'),
        ]);
    }

    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn([
                'name_ar',
                'name_en',
                'address_ar',
                'address_en',
                'city_ar',
                'city_en',
                'description_ar',
                'description_en',
            ]);
        });
    }
};

