<?php

namespace App\Modules\Mission\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateMissionRequest extends FormRequest
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
        return [
            'reference' => ['required', 'string', 'max:50', 'unique:missions,reference'],
            'title' => ['required', 'string', 'max:255'],
            'objective' => ['required', 'string', 'max:1000'], // RG-MIS-007
            'priority_axes' => ['nullable', 'string', 'max:1000'], // RG-MIS-007
            'status' => ['sometimes', 'string', Rule::in(['planned', 'in_progress', 'suspended', 'completed'])], // RG-MIS-004
            'entite_id' => ['required', 'exists:entities,id'], // RG-MIS-002
            'coordinateur_id' => ['required', 'exists:users,id'], // RG-MIS-001
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'team_composition' => ['nullable', 'string', 'max:1000'], // RG-MIS-007
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
            'reference.required' => 'La référence de la mission est obligatoire.',
            'reference.unique' => 'Cette référence de mission existe déjà.',
            'title.required' => 'Le titre de la mission est obligatoire.',
            'objective.required' => 'L\'objectif de la mission est obligatoire.',
            'entite_id.required' => 'L\'entité supervisée est obligatoire.',
            'entite_id.exists' => 'L\'entité sélectionnée n\'existe pas.',
            'coordinateur_id.required' => 'Le coordinateur est obligatoire.',
            'coordinateur_id.exists' => 'Le coordinateur sélectionné n\'existe pas.',
            'start_date.required' => 'La date de début est obligatoire.',
            'start_date.after_or_equal' => 'La date de début doit être aujourd\'ui ou dans le futur.',
            'end_date.required' => 'La date de fin est obligatoire.',
            'end_date.after' => 'La date de fin doit être après la date de début.',
            'status.in' => 'Le statut doit être: planifiée, en cours, suspendue ou clôturée.',
            'budget.numeric' => 'Le budget doit être un nombre.',
            'budget.min' => 'Le budget doit être positif.',
        ];
    }

    /**
     * Get custom attributes for validation errors.
     */
    public function attributes(): array
    {
        return [
            'reference' => 'référence',
            'title' => 'titre',
            'objective' => 'objectif',
            'priority_axes' => 'axes prioritaires',
            'status' => 'statut',
            'entite_id' => 'entité',
            'coordinateur_id' => 'coordinateur',
            'start_date' => 'date de début',
            'end_date' => 'date de fin',
            'team_composition' => 'composition équipe',
            'location' => 'lieu',
            'budget' => 'budget',
            'notes' => 'notes',
            'metadata' => 'métadonnées',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Vérifier que l'entité peut recevoir des missions (RG-ENT-004)
            if ($this->entite_id) {
                $entity = \App\Modules\Entity\Models\Entity::find($this->entite_id);
                if ($entity && !$entity->canReceiveMissions()) {
                    $validator->errors()->add('entite_id', 'Cette entité ne peut pas recevoir de missions car elle est suspendue.');
                }
            }
        });
    }
}
