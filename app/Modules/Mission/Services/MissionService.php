<?php

namespace App\Modules\Mission\Services;

use App\Modules\Mission\Models\Mission;

class MissionService
{
    public function create(array $data, $user)
    {
        $mission = Mission::create([
            'entity_id' => $data['entity_id'],
            'user_id' => $user->id,
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'start_date' => $data['start_date'] ?? null,
            'end_date' => $data['end_date'] ?? null,
            'status' => $data['status'],
        ]);

        // 🔥 LIAISON FORMULAIRES
        if (!empty($data['form_ids'])) {
            $mission->forms()->sync($data['form_ids']);
        }

        return $mission->load('forms');
    }
}