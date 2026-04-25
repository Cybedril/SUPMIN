<?php

namespace App\Modules\Form\Services;

use Illuminate\Validation\ValidationException;

class FormSchemaService
{
    private array $allowedTypes = [
        'text',
        'number',
        'select',
        'checkbox',
        'image',
        'date'
    ];

    public function validate(array $data): void
    {
        //  sections obligatoires
        if (!isset($data['sections']) || !is_array($data['sections']) || empty($data['sections'])) {
            throw ValidationException::withMessages([
                'sections' => ['Les sections sont obligatoires et doivent être un tableau non vide']
            ]);
        }

        foreach ($data['sections'] as $sIndex => $section) {

            //  title obligatoire
            if (!isset($section['title']) || trim($section['title']) === '') {
                throw ValidationException::withMessages([
                    "sections.$sIndex.title" => ['Titre de section requis']
                ]);
            }

            //  questions obligatoires
            if (!isset($section['questions']) || !is_array($section['questions']) || empty($section['questions'])) {
                throw ValidationException::withMessages([
                    "sections.$sIndex.questions" => ['Chaque section doit contenir au moins une question']
                ]);
            }

            foreach ($section['questions'] as $qIndex => $question) {

                //  label obligatoire
                if (!isset($question['label']) || trim($question['label']) === '') {
                    throw ValidationException::withMessages([
                        "sections.$sIndex.questions.$qIndex.label" => ['Label requis']
                    ]);
                }

                //  type obligatoire + autorisé
                if (!isset($question['type']) || !in_array($question['type'], $this->allowedTypes)) {
                    throw ValidationException::withMessages([
                        "sections.$sIndex.questions.$qIndex.type" => ['Type invalide']
                    ]);
                }

                //  required obligatoire (bool)
                if (!array_key_exists('required', $question) || !is_bool($question['required'])) {
                    throw ValidationException::withMessages([
                        "sections.$sIndex.questions.$qIndex.required" => ['Le champ required doit être true ou false']
                    ]);
                }

                // VALIDATION SPÉCIFIQUE PAR TYPE

                // SELECT / CHECKBOX → options obligatoires
                if (in_array($question['type'], ['select', 'checkbox'])) {

                    if (!isset($question['options']) || !is_array($question['options']) || empty($question['options'])) {
                        throw ValidationException::withMessages([
                            "sections.$sIndex.questions.$qIndex.options" => ['Options requises pour ce type']
                        ]);
                    }

                    // chaque option doit être string non vide
                    foreach ($question['options'] as $optIndex => $option) {
                        if (!is_string($option) || trim($option) === '') {
                            throw ValidationException::withMessages([
                                "sections.$sIndex.questions.$qIndex.options.$optIndex" => ['Option invalide']
                            ]);
                        }
                    }
                }

                // IMAGE → aucune option autorisée
                if ($question['type'] === 'image' && isset($question['options'])) {
                    throw ValidationException::withMessages([
                        "sections.$sIndex.questions.$qIndex.options" => ['Options interdites pour image']
                    ]);
                }
            }
        }
    }
}