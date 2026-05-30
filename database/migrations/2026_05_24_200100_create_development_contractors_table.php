<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('development_contractors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('development_project_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('scope_of_work');
            $table->decimal('contract_value', 14, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('development_contractors');
    }
};
