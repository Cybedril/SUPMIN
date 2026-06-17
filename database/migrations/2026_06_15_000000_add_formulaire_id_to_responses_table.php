<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('responses', function (Blueprint $table) {

            // Ajout de la clé étrangère vers forms (formulaire)
            // Nullable pour permettre la migration sur des données existantes,
            // puis sera renseignée automatiquement via question -> section -> formulaire
            $table->uuid('formulaire_id')->nullable()->after('mission_id');

            $table->foreign('formulaire_id')
                ->references('id')
                ->on('forms')
                ->cascadeOnDelete();
        });

        // Remplissage automatique du formulaire_id pour les réponses existantes
        // en remontant : response -> question -> section -> form
        DB::statement("
            UPDATE responses r
            INNER JOIN questions q ON q.id = r.question_id
            INNER JOIN sections s ON s.id = q.section_id
            SET r.formulaire_id = s.form_id
            WHERE r.formulaire_id IS NULL
        ");
    }

    public function down(): void
    {
        Schema::table('responses', function (Blueprint $table) {
            $table->dropForeign(['formulaire_id']);
            $table->dropColumn('formulaire_id');
        });
    }
};