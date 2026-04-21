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
        Schema::create('recommendation_trackings', function (Blueprint $table) {
        $table->uuid('id')->primary();
        $table->foreignUuid('recommendation_id')->constrained()->cascadeOnDelete();
        $table->foreignUuid('user_id')->nullable()->constrained()->nullOnDelete();
        $table->string('status');
        $table->text('comment')->nullable();
        $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recommendation_trackings');
    }
};
