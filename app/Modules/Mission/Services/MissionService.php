<?php

namespace App\Modules\Mission\Services;

use App\Modules\Mission\Models\Mission;
use App\Modules\Mission\Models\MissionLog;
use App\Notifications\MissionCreatedNotification;
use App\Notifications\MissionUpdatedNotification;
use App\Models\User;

class MissionService
{
    public function create(array $data, $user)
    {
       $mission = Mission::create([
    'entity_id' => $data['entity_id'],
    'user_id' => $user->id, // créateur
    'title' => $data['title'],
    'description' => $data['description'] ?? null,
    'status' => $data['status'],
]);

// assignation multiple
if (!empty($data['agents'])) {

    // 🔒 Vérifier un seul leader
    $leaders = collect($data['agents'])
        ->filter(fn($role) => $role === 'leader');

    if ($leaders->count() > 1) {
        throw new \Exception("Une mission ne peut avoir qu’un seul leader");
    }

    // 🔗 sync pivot
    $syncData = [];

    foreach ($data['agents'] as $userId => $role) {
        $syncData[$userId] = ['role' => $role];
    }

    $mission->agents()->sync($syncData);
}

        // ✅ LOG CREATION
        MissionLog::create([
            'mission_id' => $mission->id,
            'user_id' => $user->id,
            'action' => 'created',
            'changes' => $mission->toArray(),
        ]);

        // 🔔 NOTIFICATION (simple)
        $admins = User::role('admin')->get();

        foreach ($admins as $admin) {
            $admin->notify(new MissionCreatedNotification($mission));
        }

        return $mission->load('forms');
    }

    public function update($mission, array $data, $user)
    {
        $oldData = $mission->getOriginal();

        $mission->update($data);

        if (!empty($data['agent_ids'])) {
    $mission->agents()->sync($data['agent_ids']);
}
        // ✅ LOG UPDATE
        MissionLog::create([
            'mission_id' => $mission->id,
            'user_id' => $user->id,
            'action' => 'updated',
            'changes' => [
                'before' => $oldData,
                'after' => $mission->fresh()->toArray(),
            ],
        ]);

        // 🔔 NOTIFICATION UPDATE
        $admins = User::role('admin')->get();

        foreach ($admins as $admin) {
            $admin->notify(new MissionUpdatedNotification($mission));
        }

        return $mission->load('forms');
    }
    
}