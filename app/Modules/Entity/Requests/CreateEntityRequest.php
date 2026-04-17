<?php

namespace App\Modules\Entity\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateEntityRequest extends FormRequest
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
            'code' => ['required', 'string', 'max:50', 'unique:entities,code'],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', Rule::in(['administrative_structure', 'autonomous_agency', 'program_project'])], // RG-ENT-003
            'status' => ['sometimes', 'string', Rule::in(['active', 'suspended'])],
            'description' => ['nullable', 'string', 'max:1000'],
            'address' => ['nullable', 'string', 'max:500'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'responsable_id' => ['nullable', 'exists:users,id'], // RG-ENT-002
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
            'code.required' => 'Le code de l\'entité est obligatoire.',
            'code.unique' => 'Ce code d\'entité existe déjà.',
            'name.required' => 'Le nom de l\'entité est obligatoire.',
            'type.required' => 'Le type d\'entité est obligatoire.',
            'type.in' => 'Le type d\'entité doit être: structure administrative, agence autonome ou programme/projet.',
            'status.in' => 'Le statut doit être: actif ou suspendu.',
            'responsable_id.exists' => 'Le responsable sélectionné n\'existe pas.',
            'entite_parente_id.exists' => 'L\'entité parente sélectionnée n\'existe pas.',
            'email.email' => 'L\'adresse email n\'est pas valide.',
        ];
    }

    /**
     * Get custom attributes for validation errors.
     */
    public function attributes(): array
    {
        return [
            'code' => 'code',
            'name' => 'nom',
            'type' => 'type',
            'status' => 'statut',
            'description' => 'description',
            'address' => 'adresse',
            'phone' => 'téléphone',
            'email' => 'email',
            'responsable_id' => 'responsable',
            'entite_parente_id' => 'entité parente',
            'metadata' => 'métadonnées',
        ];
    }
}
