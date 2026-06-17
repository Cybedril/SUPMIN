<?php

namespace App\Modules\Response\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Shared\Traits\HasUuid;

class Response extends Model
{
    use HasUuid;

    protected $fillable = [
        'id',
        'question_id',
        'mission_id',
        'formulaire_id',     
        'agent_id',
        'valeur_texte',
        'valeur_json',
        'fichiers_joints',
        'latitude',
        'longitude',
        'submitted_at',
        'mode_collecte'
    ];

    protected $casts = [
        'valeur_json' => 'array',
        'fichiers_joints' => 'array',
        'submitted_at' => 'datetime'
    ];

    public $incrementing = false;
    protected $keyType = 'string';

    public function question()
    {
        return $this->belongsTo(
            \App\Modules\Form\Models\Question::class
        );
    }

    public function mission()
    {
        return $this->belongsTo(
            \App\Modules\Mission\Models\Mission::class
        );
    }

    /**
     * Le formulaire auquel appartient cette réponse.
     * Lien direct ajouté pour faciliter les requêtes
     * (sans avoir à passer par question -> section -> formulaire).
     */
    public function formulaire()
    {
        return $this->belongsTo(
            \App\Modules\Form\Models\Form::class,
            'formulaire_id'
        );
    }

    public function agent()
    {
        return $this->belongsTo(
            \App\Models\User::class,
            'agent_id'
        );
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}