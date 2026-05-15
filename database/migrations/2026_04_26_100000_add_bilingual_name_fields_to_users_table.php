<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'name_ar')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('name_ar')->nullable()->after('name');
            });
        }

        if (! Schema::hasColumn('users', 'name_en')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('name_en')->nullable()->after('name_ar');
            });
        }

        if (Schema::hasColumn('users', 'name_ar')) {
            DB::table('users')
                ->whereNull('name_ar')
                ->orWhere('name_ar', '')
                ->update(['name_ar' => DB::raw('name')]);
        }

        if (Schema::hasColumn('users', 'name_en')) {
            DB::table('users')
                ->whereNull('name_en')
                ->orWhere('name_en', '')
                ->update(['name_en' => DB::raw('name')]);
        }
    }

    public function down(): void
    {
        $hasNameEn = Schema::hasColumn('users', 'name_en');
        $hasNameAr = Schema::hasColumn('users', 'name_ar');

        if (! $hasNameEn && ! $hasNameAr) {
            return;
        }

        Schema::table('users', function (Blueprint $table) use ($hasNameEn, $hasNameAr) {
            if ($hasNameEn) {
                $table->dropColumn('name_en');
            }
            if ($hasNameAr) {
                $table->dropColumn('name_ar');
            }
        });
    }
};
