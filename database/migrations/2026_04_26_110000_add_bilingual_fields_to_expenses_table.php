<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('expenses', 'title_ar')) {
            Schema::table('expenses', function (Blueprint $table) {
                $table->string('title_ar')->nullable()->after('title');
            });
        }

        if (! Schema::hasColumn('expenses', 'title_en')) {
            Schema::table('expenses', function (Blueprint $table) {
                $table->string('title_en')->nullable()->after('title_ar');
            });
        }

        if (! Schema::hasColumn('expenses', 'description_ar')) {
            Schema::table('expenses', function (Blueprint $table) {
                $table->text('description_ar')->nullable()->after('description');
            });
        }

        if (! Schema::hasColumn('expenses', 'description_en')) {
            Schema::table('expenses', function (Blueprint $table) {
                $table->text('description_en')->nullable()->after('description_ar');
            });
        }

        DB::table('expenses')
            ->whereNull('title_ar')
            ->orWhere('title_ar', '')
            ->update(['title_ar' => DB::raw('title')]);

        DB::table('expenses')
            ->whereNull('title_en')
            ->orWhere('title_en', '')
            ->update(['title_en' => DB::raw('title')]);

        DB::table('expenses')
            ->whereNull('description_ar')
            ->orWhere('description_ar', '')
            ->update(['description_ar' => DB::raw('description')]);

        DB::table('expenses')
            ->whereNull('description_en')
            ->orWhere('description_en', '')
            ->update(['description_en' => DB::raw('description')]);
    }

    public function down(): void
    {
        $hasTitleAr = Schema::hasColumn('expenses', 'title_ar');
        $hasTitleEn = Schema::hasColumn('expenses', 'title_en');
        $hasDescriptionAr = Schema::hasColumn('expenses', 'description_ar');
        $hasDescriptionEn = Schema::hasColumn('expenses', 'description_en');

        if (! $hasTitleAr && ! $hasTitleEn && ! $hasDescriptionAr && ! $hasDescriptionEn) {
            return;
        }

        Schema::table('expenses', function (Blueprint $table) use ($hasTitleAr, $hasTitleEn, $hasDescriptionAr, $hasDescriptionEn) {
            if ($hasDescriptionEn) {
                $table->dropColumn('description_en');
            }
            if ($hasDescriptionAr) {
                $table->dropColumn('description_ar');
            }
            if ($hasTitleEn) {
                $table->dropColumn('title_en');
            }
            if ($hasTitleAr) {
                $table->dropColumn('title_ar');
            }
        });
    }
};
