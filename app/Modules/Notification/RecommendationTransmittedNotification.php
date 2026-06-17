<?php

namespace App\Notification;

use App\Modules\Recommendation\Models\Recommendation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class RecommendationTransmittedNotification extends Notification
{
    use Queueable;

    public function __construct(public Recommendation $recommendation)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type'              => 'recommandation',
            'titre'             => 'Recommandation transmise',
            'message'           => "La recommandation {$this->recommendation->reference} a été validée et vous est officiellement transmise pour mise en œuvre.",
            'recommendation_id' => $this->recommendation->id,
            'reference'         => $this->recommendation->reference,
        ];
    }
}