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
        Schema::create('entities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code', 50)->unique(); // Code unique d'identification
            $table->string('name', 255); // Nom de l'entité
            $table->enum('type', ['administrative_structure', 'autonomous_agency', 'program_project']); // RG-ENT-003
            $table->enum('status', ['active', 'suspended'])->default('active'); // RG-ENT-004
            $table->text('description')->nullable();
            $table->string('address', 500)->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('email', 255)->nullable();
            $table->uuid('responsable_id')->nullable(); // RG-ENT-002
            $table->uuid('entite_parente_id')->nullable(); // Hiérarchie
            $table->json('metadata')->nullable(); // Champs supplémentaires
            $table->timestamps();

            // Foreign keys
            $table->foreign('responsable_id')->references('id')->on('users')->onDelete('SET NULL');
            $table->foreign('entite_parente_id')->references('id')->on('entities')->onDelete('SET NULL');

            // Indexes
            $table->index('type');
            $table->index('status');
            $table->index('responsable_id');
            $table->index('entite_parente_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entities');
    }
};
