<?php

namespace App\Notification;

use App\Modules\Mission\Models\Mission;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class MissionValidatedNotification extends Notification
{
    use Queueable;

    public function __construct(public Mission $mission)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type'       => 'mission',
            'titre'      => 'Mission démarrée',
            'message'    => "La mission {$this->mission->reference} est validée et démarrée. Vous pouvez commencer la collecte de données.",
            'mission_id' => $this->mission->id,
            'reference'  => $this->mission->reference,
        ];
    }
}