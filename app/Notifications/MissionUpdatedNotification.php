<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class MissionUpdatedNotification extends Notification
{
    use Queueable;

    public function __construct(public $mission) {}

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'message' => 'Une mission a été mise à jour',
            'mission_id' => $this->mission->id,
        ];
    }
}