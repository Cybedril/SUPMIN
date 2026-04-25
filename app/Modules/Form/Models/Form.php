<?php

namespace App\Modules\Form\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Shared\Traits\HasUuid;


class Form extends Model
{
    use HasUuid;

    protected $fillable = [
        'title',
        'description',
    ];

     // 🔗 Form → Sections
    public function sections()
    {
        return $this->hasMany(Section::class)->orderBy('order');
    }

    public function missions()
    {
        return $this->belongsToMany(
            \App\Modules\Mission\Models\Mission::class,
            'mission_forms'
        );
    }
}