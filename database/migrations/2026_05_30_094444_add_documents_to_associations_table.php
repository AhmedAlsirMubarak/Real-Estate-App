<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('associations', function (Blueprint $table) {
            $table->string('no_objection_certificate_path')->nullable()->after('water_account_number');
            $table->string('sketch_path')->nullable()->after('no_objection_certificate_path');
            $table->string('association_certificate_path')->nullable()->after('sketch_path');
            $table->string('personal_id_path')->nullable()->after('association_certificate_path');
            $table->string('manager_id_path')->nullable()->after('personal_id_path');
        });
    }

    public function down(): void
    {
        Schema::table('associations', function (Blueprint $table) {
            $table->dropColumn([
                'no_objection_certificate_path',
                'sketch_path',
                'association_certificate_path',
                'personal_id_path',
                'manager_id_path',
            ]);
        });
    }
};
