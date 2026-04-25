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

    $table->uuid('section_id');

    $table->string('label');
    $table->string('type'); // text, number, select, checkbox, image, date

    $table->boolean('required')->default(false);

    $table->json('options')->nullable(); // pour select, checkbox

    $table->integer('order')->default(0);

    $table->timestamps();

    $table->foreign('section_id')
        ->references('id')
        ->on('sections')
        ->cascadeOnDelete();
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
