<?php

namespace App\Modules\Entity\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Entity\Models\Entity;
use Illuminate\Http\Request;

class EntityController extends Controller
{
    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => Entity::all(),
            'message' => 'Liste des entités',
            'errors' => null
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:100',
            'location' => 'nullable|string|max:255',
        ]);

        $entity = Entity::create($validated);

        return response()->json([
            'success' => true,
            'data' => $entity,
            'message' => 'Entité créée',
            'errors' => null
        ], 201);
    }

    public function update(Request $request, Entity $entity)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'type' => 'sometimes|string|max:100',
            'location' => 'nullable|string|max:255',
        ]);

        $entity->update($validated);

        return response()->json([
            'success' => true,
            'data' => $entity,
            'message' => 'Entité mise à jour',
            'errors' => null
        ]);
    }

    public function destroy(Entity $entity)
    {
        $entity->delete();

        return response()->json([
            'success' => true,
            'data' => null,
            'message' => 'Entité supprimée',
            'errors' => null
        ]);
    }
}