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
        Schema::create('missions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('reference', 50)->unique(); // Référence unique de mission
            $table->string('title', 255); // Titre de la mission
            $table->text('objective'); // Objectif de la mission - RG-MIS-007
            $table->text('priority_axes')->nullable(); // Axes prioritaires - RG-MIS-007
            $table->enum('status', ['planned', 'in_progress', 'suspended', 'completed'])->default('planned'); // RG-MIS-004
            $table->uuid('entite_id'); // RG-MIS-002
            $table->uuid('coordinateur_id'); // RG-MIS-001
            $table->date('start_date');
            $table->date('end_date');
            $table->text('team_composition')->nullable(); // Composition équipe - RG-MIS-007
            $table->text('location')->nullable();
            $table->decimal('budget', 15, 2)->nullable();
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable(); // Champs supplémentaires
            $table->timestamps();

            // Foreign keys
            $table->foreign('entite_id')->references('id')->on('entities')->onDelete('CASCADE');
            $table->foreign('coordinateur_id')->references('id')->on('users')->onDelete('RESTRICT');

            // Indexes
            $table->index('status');
            $table->index('entite_id');
            $table->index('coordinateur_id');
            $table->index('start_date');
            $table->index('end_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('missions');
    }
};
