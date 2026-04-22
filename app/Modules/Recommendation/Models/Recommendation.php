<?php

namespace App\Modules\Recommendation\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Shared\Traits\HasUuid;

class Recommendation extends Model
{
    use HasUuid;

    protected $fillable = [
        'mission_id',
        'content',
        'priority',
        'due_date',
        'status',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    public function mission()
    {
        return $this->belongsTo(\App\Modules\Mission\Models\Mission::class);
    }

    public function trackings()
    {
        return $this->hasMany(RecommendationTracking::class);
    }
}