<?php

namespace App\Modules\Mission\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Shared\Traits\HasUuid;

class Mission extends Model
{
    use HasUuid;

    protected $fillable = [
        'entity_id',
        'user_id',
        'title',
        'description',
        'start_date',
        'end_date',
        'status',
    ];

    public function entity()
    {
        return $this->belongsTo(\App\Modules\Entity\Models\Entity::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function forms()
    {
        return $this->belongsToMany(
            \App\Modules\Form\Models\Form::class,
            'mission_forms'
        );
    }

    public function responses()
    {
        return $this->hasMany(\App\Modules\Response\Models\Response::class);
    }

    public function recommendations()
    {
        return $this->hasMany(\App\Modules\Recommendation\Models\Recommendation::class);
    }
}