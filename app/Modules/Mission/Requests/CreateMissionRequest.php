<?php

namespace App\Modules\Mission\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Modules\Mission\Enums\MissionStatusEnum;

class CreateMissionRequest extends FormRequest
{
    public function rules()
    {
        return [
            'entity_id' => 'required|exists:entities,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',

            'status' => ['required', Rule::in(MissionStatusEnum::values())],

            'form_ids' => 'nullable|array',
            'form_ids.*' => 'exists:forms,id',
        ];
    }
}