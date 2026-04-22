<?php

namespace App\Modules\Response\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Shared\Traits\HasUuid;

class Response extends Model
{
    use HasUuid;

    protected $fillable = [
        'mission_id',
        'question_id',
        'user_id',
        'answer',
    ];

    public function mission()
    {
        return $this->belongsTo(\App\Modules\Mission\Models\Mission::class);
    }

    public function question()
    {
        return $this->belongsTo(\App\Modules\Form\Models\Question::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}