<?php

namespace App\Modules\Mission\Models;

use App\Modules\Entity\Models\Entity;
use App\Modules\User\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mission extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'reference',
        'title',
        'objective',
        'priority_axes',
        'status',
        'entite_id',
        'coordinateur_id',
        'start_date',
        'end_date',
        'team_composition',
        'location',
        'budget',
        'notes',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'status' => 'string',
        'start_date' => 'date',
        'end_date' => 'date',
        'budget' => 'decimal:2',
    ];

    /**
     * Relation avec l'entité supervisée (RG-MIS-002)
     */
    public function entite(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Entity::class, 'entite_id');
    }

    /**
     * Relation avec le coordinateur (RG-MIS-001)
     */
    public function coordinateur(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'coordinateur_id');
    }

    /**
     * Relation avec les formulaires associés
     */
    public function formulaires(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Modules\Form\Models\Form::class, 'mission_id');
    }

    /**
     * Relation avec les réponses collectées
     */
    public function reponses(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Modules\Response\Models\Response::class, 'mission_id');
    }

    /**
     * Relation avec les recommandations
     */
    public function recommendations(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Modules\Recommendation\Models\Recommendation::class, 'mission_id');
    }

    /**
     * Relation avec les rapports générés
     */
    public function rapports(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Modules\Report\Models\Report::class, 'mission_id');
    }

    /**
     * Vérifie si la mission est planifiée (RG-MIS-004)
     */
    public function isPlanned(): bool
    {
        return $this->status === 'planned';
    }

    /**
     * Vérifie si la mission est en cours (RG-MIS-004)
     */
    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    /**
     * Vérifie si la mission est suspendue (RG-MIS-004)
     */
    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }

    /**
     * Vérifie si la mission est clôturée (RG-MIS-004)
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Vérifie si la mission peut démarrer (RG-MIS-003)
     */
    public function canStart(): bool
    {
        return $this->isPlanned() && $this->formulaires()->count() > 0;
    }

    /**
     * Obtenir la durée de la mission en jours
     */
    public function getDurationInDays(): int
    {
        return $this->start_date->diffInDays($this->end_date) + 1;
    }

    /**
     * Vérifier si la mission est en retard
     */
    public function isOverdue(): bool
    {
        return $this->end_date->isPast() && !$this->isCompleted();
    }

    /**
     * Scope pour les missions par statut
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope pour les missions actives (non clôturées)
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['planned', 'in_progress']);
    }

    /**
     * Scope pour les missions par entité
     */
    public function scopeByEntity($query, $entityId)
    {
        return $query->where('entite_id', $entityId);
    }

    /**
     * Scope pour les missions par coordinateur
     */
    public function scopeByCoordinator($query, $coordinatorId)
    {
        return $query->where('coordinateur_id', $coordinatorId);
    }
}
