<?php

namespace App\Modules\Entity\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEntityRequest extends FormRequest
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
        $entityId = $this->route('entity');

        return [
            'code' => ['sometimes', 'string', 'max:50', Rule::unique('entities', 'code')->ignore($entityId)],
            'name' => ['sometimes', 'string', 'max:255'],
            'type' => ['sometimes', 'string', Rule::in(['administrative_structure', 'autonomous_agency', 'program_project'])],
            'status' => ['sometimes', 'string', Rule::in(['active', 'suspended'])],
            'description' => ['nullable', 'string', 'max:1000'],
            'address' => ['nullable', 'string', 'max:500'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'responsable_id' => ['nullable', 'exists:users,id'],
            'entite_parente_id' => ['nullable', 'exists:entities,id'],
            'metadata' => ['nullable', 'array'],
        ];
    }

    /**
     * Get custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'code.unique' => 'Ce code d\'entité existe déjà.',
            'type.in' => 'Le type d\'entité doit être: structure administrative, agence autonome ou programme/projet.',
            'status.in' => 'Le statut doit être: actif ou suspendu.',
            'responsable_id.exists' => 'Le responsable sélectionné n\'existe pas.',
            'entite_parente_id.exists' => 'L\'entité parente sélectionnée n\'existe pas.',
            'email.email' => 'L\'adresse email n\'est pas valide.',
        ];
    }
}
