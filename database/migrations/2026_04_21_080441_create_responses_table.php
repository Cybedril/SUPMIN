<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('responses', function (Blueprint $table) {
    $table->uuid('id')->primary();

    // UUID OK
    $table->uuid('mission_id');
    $table->uuid('question_id');

    // ✅ CORRECTION ICI (BIGINT)
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();

    // FK UUID manuelles
    $table->foreign('mission_id')
        ->references('id')
        ->on('missions')
        ->cascadeOnDelete();

    $table->foreign('question_id')
        ->references('id')
        ->on('questions')
        ->cascadeOnDelete();

    $table->text('answer')->nullable();

    $table->timestamps();
});
    }

    public function down(): void
    {
        Schema::dropIfExists('responses');
    }
};