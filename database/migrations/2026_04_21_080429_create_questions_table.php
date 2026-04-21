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
        Schema::create('questions', function (Blueprint $table) {
        $table->uuid('id')->primary();
        $table->foreignUuid('section_id')->constrained()->cascadeOnDelete();
        $table->string('label');
        $table->string('type');
        $table->json('options')->nullable();
        $table->boolean('is_required')->default(false);
        $table->integer('order')->default(0);
        $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
