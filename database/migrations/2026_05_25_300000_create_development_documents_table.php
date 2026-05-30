<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('development_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('development_project_id');
            $table->foreign('development_project_id', 'dev_docs_project_fk')
                ->references('id')->on('development_projects')->cascadeOnDelete();
            $table->string('type'); // contract, invoice, other
            $table->string('title');
            $table->string('file_path');
            $table->string('original_name');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('development_documents');
    }
};
