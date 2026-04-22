<?php

namespace App\Modules\Entity\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Entity\Models\Entity;
use App\Modules\Entity\Requests\CreateEntityRequest;
use App\Modules\Shared\Helpers\ApiResponse;

class EntityController extends Controller
{
    public function index()
    {
        return ApiResponse::success(
            Entity::latest()->get(),
            'Liste des entités'
        );
    }

    public function store(CreateEntityRequest $request)
    {
        $entity = Entity::create($request->validated());

        return ApiResponse::created($entity, 'Entité créée');
    }
}