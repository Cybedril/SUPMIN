<?php

namespace App\Modules\Mission\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Mission\Models\Mission;
use App\Modules\Mission\Requests\CreateMissionRequest;
use App\Modules\Mission\Requests\UpdateMissionRequest;
use App\Modules\Shared\Helpers\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class MissionController extends Controller
{
    /**
     * Display a listing of the missions.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Mission::with(['entite', 'coordinateur']);

            // Filtrage par statut
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            // Filtrage par entité
            if ($request->has('entite_id')) {
                $query->where('entite_id', $request->entite_id);
            }

            // Filtrage par coordinateur
            if ($request->has('coordinateur_id')) {
                $query->where('coordinateur_id', $request->coordinateur_id);
            }

            // Filtrage par période
            if ($request->has('start_date_from')) {
                $query->where('start_date', '>=', $request->start_date_from);
            }
            if ($request->has('start_date_to')) {
                $query->where('start_date', '<=', $request->start_date_to);
            }

            // Recherche par titre ou référence
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('reference', 'like', "%{$search}%");
                });
            }

            $missions = $query->paginate($request->get('per_page', 15));

            return ApiResponse::success($missions, 'Missions récupérées avec succès');

        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des missions: ' . $e->getMessage());
            return ApiResponse::error('Erreur lors de la récupération des missions', 500);
        }
    }

    /**
     * Store a newly created mission in storage.
     */
    public function store(CreateMissionRequest $request): JsonResponse
    {
        try {
            $mission = Mission::create($request->validated());
            
            // Charger les relations pour la réponse
            $mission->load(['entite', 'coordinateur']);

            return ApiResponse::success($mission, 'Mission créée avec succès', 201);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la création de la mission: ' . $e->getMessage());
            return ApiResponse::error('Erreur lors de la création de la mission', 500);
        }
    }

    /**
     * Display the specified mission.
     */
    public function show(Mission $mission): JsonResponse
    {
        try {
            $mission->load(['entite', 'coordinateur', 'formulaires', 'reponses', 'recommendations', 'rapports']);

            return ApiResponse::success($mission, 'Mission récupérée avec succès');

        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération de la mission: ' . $e->getMessage());
            return ApiResponse::error('Erreur lors de la récupération de la mission', 500);
        }
    }

    /**
     * Update the specified mission in storage.
     */
    public function update(UpdateMissionRequest $request, Mission $mission): JsonResponse
    {
        try {
            $mission->update($request->validated());
            
            // Charger les relations pour la réponse
            $mission->load(['entite', 'coordinateur']);

            return ApiResponse::success($mission, 'Mission mise à jour avec succès');

        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour de la mission: ' . $e->getMessage());
            return ApiResponse::error('Erreur lors de la mise à jour de la mission', 500);
        }
    }

    /**
     * Remove the specified mission from storage.
     */
    public function destroy(Mission $mission): JsonResponse
    {
        try {
            // Vérifier si la mission a des formulaires associés
            if ($mission->formulaires()->count() > 0) {
                return ApiResponse::error('Impossible de supprimer cette mission car elle a des formulaires associés', 400);
            }

            // Vérifier si la mission a des réponses
            if ($mission->reponses()->count() > 0) {
                return ApiResponse::error('Impossible de supprimer cette mission car elle a des réponses enregistrées', 400);
            }

            // Vérifier si la mission a des recommandations
            if ($mission->recommendations()->count() > 0) {
                return ApiResponse::error('Impossible de supprimer cette mission car elle a des recommandations associées', 400);
            }

            $mission->delete();

            return ApiResponse::success(null, 'Mission supprimée avec succès');

        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression de la mission: ' . $e->getMessage());
            return ApiResponse::error('Erreur lors de la suppression de la mission', 500);
        }
    }

    /**
     * Get active missions (planned or in progress)
     */
    public function getActiveMissions(Request $request): JsonResponse
    {
        try {
            $missions = Mission::active()
                ->with(['entite', 'coordinateur'])
                ->paginate($request->get('per_page', 15));

            return ApiResponse::success($missions, 'Missions actives récupérées avec succès');

        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des missions actives: ' . $e->getMessage());
            return ApiResponse::error('Erreur lors de la récupération des missions actives', 500);
        }
    }

    /**
     * Get missions by entity
     */
    public function getMissionsByEntity(string $entityId, Request $request): JsonResponse
    {
        try {
            $missions = Mission::byEntity($entityId)
                ->with(['entite', 'coordinateur'])
                ->paginate($request->get('per_page', 15));

            return ApiResponse::success($missions, "Missions de l'entité {$entityId} récupérées avec succès");

        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des missions par entité: ' . $e->getMessage());
            return ApiResponse::error('Erreur lors de la récupération des missions par entité', 500);
        }
    }

    /**
     * Get missions by coordinator
     */
    public function getMissionsByCoordinator(string $coordinatorId, Request $request): JsonResponse
    {
        try {
            $missions = Mission::byCoordinator($coordinatorId)
                ->with(['entite', 'coordinateur'])
                ->paginate($request->get('per_page', 15));

            return ApiResponse::success($missions, "Missions du coordinateur {$coordinatorId} récupérées avec succès");

        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des missions par coordinateur: ' . $e->getMessage());
            return ApiResponse::error('Erreur lors de la récupération des missions par coordinateur', 500);
        }
    }

    /**
     * Get overdue missions
     */
    public function getOverdueMissions(Request $request): JsonResponse
    {
        try {
            $missions = Mission::where('end_date', '<', now())
                ->where('status', '!=', 'completed')
                ->with(['entite', 'coordinateur'])
                ->paginate($request->get('per_page', 15));

            return ApiResponse::success($missions, 'Missions en retard récupérées avec succès');

        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des missions en retard: ' . $e->getMessage());
            return ApiResponse::error('Erreur lors de la récupération des missions en retard', 500);
        }
    }
}
