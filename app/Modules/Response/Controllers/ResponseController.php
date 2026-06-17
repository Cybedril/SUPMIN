<?php

namespace App\Modules\Response\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\Response\Models\Response;
use App\Modules\Form\Models\Question;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ResponseController extends Controller
{
    /**
     * POST /responses
     * RG-FOR-007 : Données horodatées + géolocalisées
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'mission_id'      => 'required|exists:missions,id',
            'question_id'     => 'required|exists:questions,id',
            'valeur_texte'    => 'nullable|string',
            'valeur_json'     => 'nullable|array',
            'fichiers_joints' => 'nullable|array',
            'latitude'        => 'nullable|numeric|between:-90,90',
            'longitude'       => 'nullable|numeric|between:-180,180',
            'mode_collecte'   => 'nullable|in:online,offline',
        ]);

        // Vérifier si l'agent a déjà soumis (donc verrouillé)
        $submitted = DB::table('mission_user')
            ->where('mission_id', $validated['mission_id'])
            ->where('user_id', $request->user()->id)
            ->whereNotNull('submitted_at')
            ->exists();

        if ($submitted) {
            return response()->json([
                'success' => false,
                'message' => 'Le formulaire a déjà été soumis. Vous ne pouvez plus modifier vos réponses.',
                'errors'  => null,
            ], 403);
        }

        // Récupération automatique du formulaire_id
        $question = Question::with('section')->findOrFail($validated['question_id']);
        $formulaireId = $question->section->form_id;

        $response = Response::updateOrCreate(
            [
                'mission_id'  => $validated['mission_id'],
                'question_id' => $validated['question_id'],
                'agent_id'    => $request->user()->id,
            ],
            [
                'id'              => Str::uuid(),
                'formulaire_id'   => $formulaireId,
                'valeur_texte'    => $validated['valeur_texte'] ?? null,
                'valeur_json'     => $validated['valeur_json'] ?? null,
                'fichiers_joints' => $validated['fichiers_joints'] ?? null,
                'latitude'        => $validated['latitude'] ?? null,
                'longitude'       => $validated['longitude'] ?? null,
                'submitted_at'    => now(),
                'mode_collecte'   => $validated['mode_collecte'] ?? 'online',
            ]
        );

        return response()->json([
            'success' => true,
            'data'    => $response,
            'message' => 'Réponse enregistrée',
            'errors'  => null,
        ]);
    }

    /**
     * GET /responses?mission_id=...
     * Récupère les réponses de l'utilisateur connecté pour une mission
     */
    public function index(Request $request)
    {
        $request->validate([
            'mission_id' => 'required|exists:missions,id',
        ]);

        $responses = Response::where('mission_id', $request->mission_id)
            ->where('agent_id', $request->user()->id)
            ->get();

        // Vérifier si soumis
        $submitted = DB::table('mission_user')
            ->where('mission_id', $request->mission_id)
            ->where('user_id', $request->user()->id)
            ->whereNotNull('submitted_at')
            ->value('submitted_at');

        return response()->json([
            'success' => true,
            'data'    => $responses,
            'meta'    => [
                'submitted_at' => $submitted,
                'is_submitted' => !is_null($submitted),
            ],
            'message' => 'Réponses',
            'errors'  => null,
        ]);
    }

    /**
     * POST /responses/submit
     * Soumission finale du formulaire (verrouillage)
     */
    public function submit(Request $request)
    {
        $request->validate([
            'mission_id' => 'required|exists:missions,id',
        ]);

        $userId = $request->user()->id;
        $missionId = $request->mission_id;

        // Vérifier que l'agent est bien affecté à la mission
        $assignment = DB::table('mission_user')
            ->where('mission_id', $missionId)
            ->where('user_id', $userId)
            ->first();

        if (!$assignment) {
            return response()->json([
                'success' => false,
                'message' => 'Vous n\'êtes pas affecté à cette mission.',
                'errors'  => null,
            ], 403);
        }

        if ($assignment->submitted_at) {
            return response()->json([
                'success' => false,
                'message' => 'Vous avez déjà soumis ce formulaire.',
                'errors'  => null,
            ], 422);
        }

        // Vérifier qu'il y a au moins une réponse
        $hasResponses = Response::where('mission_id', $missionId)
            ->where('agent_id', $userId)
            ->exists();

        if (!$hasResponses) {
            return response()->json([
                'success' => false,
                'message' => 'Aucune réponse à soumettre. Veuillez remplir le formulaire.',
                'errors'  => null,
            ], 422);
        }

        // Marquer comme soumis
        DB::table('mission_user')
            ->where('mission_id', $missionId)
            ->where('user_id', $userId)
            ->update([
                'submitted_at' => now(),
                'updated_at'   => now(),
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Formulaire soumis avec succès. Vous ne pouvez plus modifier vos réponses.',
            'data'    => [
                'submitted_at' => now(),
            ],
            'errors'  => null,
        ]);
    }
}