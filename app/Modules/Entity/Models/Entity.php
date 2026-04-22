<?php

namespace App\Modules\Entity\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Shared\Traits\HasUuid;

class Entity extends Model
{
    use HasUuid;

   protected $fillable = [
    'name',
    'type',
    'location',
];

    public function missions()
    {
        return $this->hasMany(\App\Modules\Mission\Models\Mission::class);
    }
}