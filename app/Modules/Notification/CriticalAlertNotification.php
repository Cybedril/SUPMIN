<?php

namespace App\Notification;

use App\Modules\Recommendation\Models\Recommendation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CriticalAlertNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Recommendation $recommendation,
        public string $raison
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type'              => 'alerte',
            'titre'             => 'Alerte critique',
            'message'           => "{$this->raison} : recommandation {$this->recommendation->reference} — {$this->recommendation->intitule}",
            'recommendation_id' => $this->recommendation->id,
            'reference'         => $this->recommendation->reference,
        ];
    }
}