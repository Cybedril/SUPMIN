<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class MissionCreatedNotification extends Notification
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
            'message' => 'Une nouvelle mission a été créée',
            'mission_id' => $this->mission->id,
        ];
    }
}