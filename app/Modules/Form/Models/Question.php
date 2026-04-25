<?php

namespace App\Modules\Form\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Shared\Traits\HasUuid;

class Question extends Model
{
    use HasUuid;

    protected $fillable = [
        'section_id',
        'label',
        'type',
        'options',
        'is_required',
        'order',
    ];

    protected $casts = [
        'options' => 'array',
        'is_required' => 'boolean',
    ];

     // 🔗 Question → Section
    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function responses()
    {
        return $this->hasMany(\App\Modules\Response\Models\Response::class);
    }
}