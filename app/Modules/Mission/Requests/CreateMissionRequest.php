<?php

namespace App\Modules\Mission\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Modules\Mission\Enums\MissionStatusEnum;

class CreateMissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'entity_id' => ['required', 'exists:entities,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', Rule::in(MissionStatusEnum::values())],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
            'form_ids' => ['nullable', 'array'],
            'agent_ids' => ['nullable', 'array'],
            'agent_ids.*' => ['exists:users,id'],
            'agent_ids.*' => [
               'exists:users,id',
               function ($attr, $value, $fail) {
                    $user = \App\Models\User::find($value);
                    if (!$user || !$user->hasRole('agent')) {
                       $fail("L'utilisateur doit être un agent");
                    }
               }
           ], 
           'agents' => ['nullable', 'array'],
           'agents.*' => ['in:leader,membre'],
        ];
    }
}