<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Synchronise la table mission_forms avec les formulaires existants
     * qui ont un mission_id direct mais ne sont pas dans mission_forms.
     */
    public function up(): void
    {
        // Récupérer tous les formulaires qui ont un mission_id
        $forms = DB::table('forms')->whereNotNull('mission_id')->get();

        foreach ($forms as $form) {
            // Vérifier si déjà dans mission_forms
            $exists = DB::table('mission_forms')
                ->where('mission_id', $form->mission_id)
                ->where('form_id', $form->id)
                ->exists();

            if (!$exists) {
                DB::table('mission_forms')->insert([
                    'id'         => (string) Str::uuid(),
                    'mission_id' => $form->mission_id,
                    'form_id'    => $form->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        // Pas de rollback - on garde les données synchronisées
    }
};