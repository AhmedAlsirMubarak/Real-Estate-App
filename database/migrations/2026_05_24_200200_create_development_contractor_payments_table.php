<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('development_contractor_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('development_contractor_id');
            $table->foreign('development_contractor_id', 'dev_contractor_payments_fk')
                ->references('id')->on('development_contractors')->cascadeOnDelete();
            $table->decimal('amount', 14, 2);
            $table->string('description')->nullable();
            $table->date('paid_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('development_contractor_payments');
    }
};
