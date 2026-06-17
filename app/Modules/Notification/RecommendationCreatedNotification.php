<?php

namespace App\Notification;

use App\Modules\Recommendation\Models\Recommendation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class RecommendationCreatedNotification extends Notification
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
            'titre'             => 'Nouvelle recommandation',
            'message'           => "Une recommandation {$this->recommendation->priorite} vous a été assignée : {$this->recommendation->intitule}",
            'recommendation_id' => $this->recommendation->id,
            'reference'         => $this->recommendation->reference,
            'priorite'          => $this->recommendation->priorite,
        ];
    }
}