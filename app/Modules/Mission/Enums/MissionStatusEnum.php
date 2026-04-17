<?php

namespace App\Modules\Mission\Enums;

/**
 * Énumération des statuts de mission selon RG-MIS-004
 */
enum MissionStatusEnum: string
{
    case PLANNED = 'planned';
    case IN_PROGRESS = 'in_progress';
    case SUSPENDED = 'suspended';
    case COMPLETED = 'completed';

    /**
     * Obtenir le libellé en français
     */
    public function getLabel(): string
    {
        return match($this) {
            self::PLANNED => 'Planifiée',
            self::IN_PROGRESS => 'En cours',
            self::SUSPENDED => 'Suspendue',
            self::COMPLETED => 'Clôturée',
        };
    }

    /**
     * Obtenir la description du statut
     */
    public function getDescription(): string
    {
        return match($this) {
            self::PLANNED => 'Mission planifiée et validée, en attente de démarrage',
            self::IN_PROGRESS => 'Mission actuellement en cours de réalisation',
            self::SUSPENDED => 'Mission temporairement suspendue',
            self::COMPLETED => 'Mission terminée et clôturée',
        };
    }

    /**
     * Vérifier si le statut permet des modifications
     */
    public function canBeModified(): bool
    {
        return match($this) {
            self::PLANNED => true,
            self::IN_PROGRESS => false,
            self::SUSPENDED => true,
            self::COMPLETED => false,
        };
    }

    /**
     * Obtenir les transitions possibles depuis ce statut
     */
    public function getAllowedTransitions(): array
    {
        return match($this) {
            self::PLANNED => [self::IN_PROGRESS, self::SUSPENDED],
            self::IN_PROGRESS => [self::SUSPENDED, self::COMPLETED],
            self::SUSPENDED => [self::PLANNED, self::IN_PROGRESS],
            self::COMPLETED => [],
        };
    }

    /**
     * Vérifier si la transition vers un autre statut est autorisée
     */
    public function canTransitionTo(self $newStatus): bool
    {
        return in_array($newStatus, $this->getAllowedTransitions());
    }

    /**
     * Obtenir toutes les valeurs possibles
     */
    public static function getAllValues(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }

    /**
     * Obtenir toutes les options pour un select
     */
    public static function getSelectOptions(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->getLabel();
        }
        return $options;
    }
}
