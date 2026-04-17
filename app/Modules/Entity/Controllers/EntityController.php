<?php

namespace App\Modules\Entity\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Entity\Models\Entity;
use App\Modules\Entity\Requests\CreateEntityRequest;
use App\Modules\Entity\Requests\UpdateEntityRequest;
use App\Modules\Shared\Helpers\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class EntityController extends Controller
{
    /**
     * Display a listing of the entities.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Entity::with(['responsable', 'entiteParente']);

            // Filtrage par statut
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            // Filtrage par type
            if ($request->has('type')) {
                $query->where('type', $request->type);
            }

            // Filtrage par entité parente
            if ($request->has('entite_parente_id')) {
                $query->where('entite_parente_id', $request->entite_parente_id);
            }

            // Recherche par nom ou code
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%");
                });
            }

            $entities = $query->paginate($request->get('per_page', 15));

            return ApiResponse::success($entities, 'Entités récupérées avec succès');

        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des entités: ' . $e->getMessage());
            return ApiResponse::error('Erreur lors de la récupération des entités', 500);
        }
    }

    /**
     * Store a newly created entity in storage.
     */
    public function store(CreateEntityRequest $request): JsonResponse
    {
        try {
            $entity = Entity::create($request->validated());
            
            // Charger les relations pour la réponse
            $entity->load(['responsable', 'entiteParente']);

            return ApiResponse::success($entity, 'Entité créée avec succès', 201);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la création de l\'entité: ' . $e->getMessage());
            return ApiResponse::error('Erreur lors de la création de l\'entité', 500);
        }
    }

    /**
     * Display the specified entity.
     */
    public function show(Entity $entity): JsonResponse
    {
        try {
            $entity->load(['responsable', 'entiteParente', 'entitesEnfants', 'missions']);

            return ApiResponse::success($entity, 'Entité récupérée avec succès');

        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération de l\'entité: ' . $e->getMessage());
            return ApiResponse::error('Erreur lors de la récupération de l\'entité', 500);
        }
    }

    /**
     * Update the specified entity in storage.
     */
    public function update(UpdateEntityRequest $request, Entity $entity): JsonResponse
    {
        try {
            $entity->update($request->validated());
            
            // Charger les relations pour la réponse
            $entity->load(['responsable', 'entiteParente']);

            return ApiResponse::success($entity, 'Entité mise à jour avec succès');

        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour de l\'entité: ' . $e->getMessage());
            return ApiResponse::error('Erreur lors de la mise à jour de l\'entité', 500);
        }
    }

    /**
     * Remove the specified entity from storage.
     */
    public function destroy(Entity $entity): JsonResponse
    {
        try {
            // Vérifier si l'entité a des missions associées
            if ($entity->missions()->count() > 0) {
                return ApiResponse::error('Impossible de supprimer cette entité car elle a des missions associées', 400);
            }

            // Vérifier si l'entité a des entités enfants
            if ($entity->entitesEnfants()->count() > 0) {
                return ApiResponse::error('Impossible de supprimer cette entité car elle a des entités enfants', 400);
            }

            $entity->delete();

            return ApiResponse::success(null, 'Entité supprimée avec succès');

        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression de l\'entité: ' . $e->getMessage());
            return ApiResponse::error('Erreur lors de la suppression de l\'entité', 500);
        }
    }

    /**
     * Get entities that can receive missions (RG-ENT-004)
     */
    public function getActiveEntities(Request $request): JsonResponse
    {
        try {
            $entities = Entity::active()
                ->with(['responsable', 'entiteParente'])
                ->paginate($request->get('per_page', 15));

            return ApiResponse::success($entities, 'Entités actives récupérées avec succès');

        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des entités actives: ' . $e->getMessage());
            return ApiResponse::error('Erreur lors de la récupération des entités actives', 500);
        }
    }

    /**
     * Get entities by type
     */
    public function getEntitiesByType(string $type, Request $request): JsonResponse
    {
        try {
            if (!in_array($type, ['administrative_structure', 'autonomous_agency', 'program_project'])) {
                return ApiResponse::error('Type d\'entité invalide', 400);
            }

            $entities = Entity::byType($type)
                ->with(['responsable', 'entiteParente'])
                ->paginate($request->get('per_page', 15));

            return ApiResponse::success($entities, "Entités de type {$type} récupérées avec succès");

        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des entités par type: ' . $e->getMessage());
            return ApiResponse::error('Erreur lors de la récupération des entités par type', 500);
        }
    }
}
