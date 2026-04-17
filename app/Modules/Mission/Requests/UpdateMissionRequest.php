<?php

namespace App\Modules\Mission\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMissionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // TODO: Implémenter les permissions plus tard
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $missionId = $this->route('mission');

        return [
            'reference' => ['sometimes', 'string', 'max:50', Rule::unique('missions', 'reference')->ignore($missionId)],
            'title' => ['sometimes', 'string', 'max:255'],
            'objective' => ['sometimes', 'string', 'max:1000'],
            'priority_axes' => ['nullable', 'string', 'max:1000'],
            'status' => ['sometimes', 'string', Rule::in(['planned', 'in_progress', 'suspended', 'completed'])],
            'entite_id' => ['sometimes', 'exists:entities,id'],
            'coordinateur_id' => ['sometimes', 'exists:users,id'],
            'start_date' => ['sometimes', 'date'],
            'end_date' => ['sometimes', 'date', 'after:start_date'],
            'team_composition' => ['nullable', 'string', 'max:1000'],
            'location' => ['nullable', 'string', 'max:500'],
            'budget' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'metadata' => ['nullable', 'array'],
        ];
    }

    /**
     * Get custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'reference.unique' => 'Cette référence de mission existe déjà.',
            'entite_id.exists' => 'L\'entité sélectionnée n\'existe pas.',
            'coordinateur_id.exists' => 'Le coordinateur sélectionné n\'existe pas.',
            'end_date.after' => 'La date de fin doit être après la date de début.',
            'status.in' => 'Le statut doit être: planifiée, en cours, suspendue ou clôturée.',
            'budget.numeric' => 'Le budget doit être un nombre.',
            'budget.min' => 'Le budget doit être positif.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $mission = $this->route('mission');
            
            // Vérifier que l'entité peut recevoir des missions
            if ($this->entite_id) {
                $entity = \App\Modules\Entity\Models\Entity::find($this->entite_id);
                if ($entity && !$entity->canReceiveMissions()) {
                    $validator->errors()->add('entite_id', 'Cette entité ne peut pas recevoir de missions car elle est suspendue.');
                }
            }

            // Vérifier les transitions de statut (RG-MIS-004)
            if ($this->status && $mission) {
                $currentStatus = \App\Modules\Mission\Enums\MissionStatusEnum::tryFrom($mission->status);
                $newStatus = \App\Modules\Mission\Enums\MissionStatusEnum::tryFrom($this->status);
                
                if ($currentStatus && $newStatus && !$currentStatus->canTransitionTo($newStatus)) {
                    $validator->errors()->add('status', 'La transition de statut de "' . $currentStatus->getLabel() . '" vers "' . $newStatus->getLabel() . '" n\'est pas autorisée.');
                }
            }
        });
    }
}
