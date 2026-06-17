<?php

namespace App\Notification;

use App\Modules\Recommendation\Models\Recommendation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class RecommendationStatusChangedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Recommendation $recommendation,
        public string $ancienStatut,
        public string $nouveauStatut
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $labels = [
            'formulee'           => 'Formulée',
            'transmise'          => 'Transmise',
            'en_cours'           => 'En cours',
            'mise_en_oeuvre'     => 'Mise en œuvre',
            'cloturee'           => 'Clôturée',
            'reportee'           => 'Reportée',
            'non_mise_en_oeuvre' => 'Non mise en œuvre',
        ];

        $ancien   = $labels[$this->ancienStatut] ?? $this->ancienStatut;
        $nouveau  = $labels[$this->nouveauStatut] ?? $this->nouveauStatut;

        return [
            'type'              => 'recommandation',
            'titre'             => 'Statut mis à jour',
            'message'           => "Recommandation {$this->recommendation->reference} : statut passé de \"{$ancien}\" à \"{$nouveau}\".",
            'recommendation_id' => $this->recommendation->id,
            'reference'         => $this->recommendation->reference,
            'ancien_statut'     => $this->ancienStatut,
            'nouveau_statut'    => $this->nouveauStatut,
        ];
    }
}