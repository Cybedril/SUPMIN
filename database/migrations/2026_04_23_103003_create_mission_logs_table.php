<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mission_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('mission_id');

            // ✅ OK (fait déjà la FK)
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('action'); // created / updated
            $table->json('changes')->nullable();

            $table->timestamps();

            // FK mission (à garder)
            $table->foreign('mission_id')
                ->references('id')
                ->on('missions')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mission_logs');
    }
};