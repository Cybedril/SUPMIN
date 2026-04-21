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
        Schema::create('recommendations', function (Blueprint $table) {
        $table->uuid('id')->primary();
        $table->foreignUuid('mission_id')->constrained()->cascadeOnDelete();
        $table->text('content');
        $table->string('priority')->nullable();
        $table->date('due_date')->nullable();
        $table->string('status');
        $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recommendations');
    }
};
