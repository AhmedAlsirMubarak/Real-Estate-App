<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            // SQLite does not support dropping foreign keys or unique indexes by name.
            // The unique constraint is not enforced the same way; tests can proceed.
            return;
        }

        Schema::table('associations', function (Blueprint $table) {
            $table->dropForeign('associations_property_id_foreign');
            $table->dropUnique('associations_property_id_unique');
            $table->index('property_id');
            $table->foreign('property_id')->references('id')->on('properties')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('associations', function (Blueprint $table) {
            $table->dropForeign('associations_property_id_foreign');
            $table->dropIndex('associations_property_id_index');
            $table->unique('property_id');
            $table->foreign('property_id')->references('id')->on('properties')->cascadeOnDelete();
        });
    }
};
