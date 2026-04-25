<?php

namespace App\Modules\Form\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Shared\Traits\HasUuid;


class Section extends Model
{
    use HasUuid;

    protected $fillable = [
        'form_id',
        'title',
        'order',
    ];

     // 🔗 Section → Form
    public function form()
    {
        return $this->belongsTo(Form::class);
    }

    // 🔗 Section → Questions
    public function questions()
    {
        return $this->hasMany(Question::class)->orderBy('order');
    }
}