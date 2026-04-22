<?php

namespace App\Modules\Recommendation\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Shared\Traits\HasUuid;

class RecommendationTracking extends Model
{
    use HasUuid;

    protected $fillable = [
        'recommendation_id',
        'user_id',
        'status',
        'comment',
    ];

    public function recommendation()
    {
        return $this->belongsTo(Recommendation::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}