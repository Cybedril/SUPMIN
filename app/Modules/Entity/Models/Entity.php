<?php

namespace App\Modules\Entity\Models;

use App\Modules\User\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Entity extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'type',
        'status',
        'description',
        'address',
        'phone',
        'email',
        'responsable_id',
        'entite_parente_id',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'status' => 'string',
        'type' => 'string',
    ];

    /**
     * Relation avec le responsable de l'entité (RG-ENT-002)
     */
    public function responsable(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }

    /**
     * Relation avec l'entité parente (hiérarchie)
     */
    public function entiteParente(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Entity::class, 'entite_parente_id');
    }

    /**
     * Relation avec les entités enfants
     */
    public function entitesEnfants(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Entity::class, 'entite_parente_id');
    }

    /**
     * Relation avec les missions associées
     */
    public function missions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Modules\Mission\Models\Mission::class, 'entite_id');
    }

    /**
     * Vérifie si l'entité est active (RG-ENT-004)
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Vérifie si l'entité peut recevoir des missions (RG-ENT-004)
     */
    public function canReceiveMissions(): bool
    {
        return $this->isActive();
    }

    /**
     * Scope pour les entités actives
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope pour les entités par type
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Obtenir le chemin hiérarchique complet
     */
    public function getHierarchicalPath(): string
    {
        $path = [];
        $current = $this;
        
        while ($current) {
            array_unshift($path, $current->name);
            $current = $current->entiteParente;
        }
        
        return implode(' > ', $path);
    }
}
