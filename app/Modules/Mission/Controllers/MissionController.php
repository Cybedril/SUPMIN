<?php

namespace App\Modules\Mission\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Mission\Models\Mission;
use App\Modules\Mission\Requests\CreateMissionRequest;
use App\Modules\Mission\Services\MissionService;
use App\Modules\Shared\Helpers\ApiResponse;

class MissionController extends Controller
{
    public function __construct(private MissionService $service) {}

    public function index()
    {
        $missions = Mission::with(['entity', 'forms'])->latest()->get();

        return ApiResponse::success($missions, 'Liste des missions');
    }

    public function store(CreateMissionRequest $request)
    {
        $mission = $this->service->create(
            $request->validated(),
            $request->user()
        );

        return ApiResponse::created($mission, 'Mission créée');
    }
}