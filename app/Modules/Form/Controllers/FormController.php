<?php

namespace App\Modules\Form\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\Form\Models\Form;
use App\Modules\Form\Models\Section;
use App\Modules\Form\Models\Question;
use App\Modules\Form\Services\FormSchemaService;
use Illuminate\Support\Facades\DB;

class FormController extends Controller
{
    // POST /forms
    public function store(Request $request, FormSchemaService $schema)
    {
        // 1. validation
    $schema->validate($request->all());

    // 2. transaction
    $form = DB::transaction(function () use ($request) {

        // créer form
        $form = Form::create([
            'title' => $request->title,
            'description' => $request->description
        ]);

        foreach ($request->sections as $sIndex => $sectionData) {

            $section = Section::create([
                'form_id' => $form->id,
                'title' => $sectionData['title'],
                'order' => $sIndex
            ]);

            foreach ($sectionData['questions'] as $qIndex => $questionData) {

                Question::create([
                    'section_id' => $section->id,
                    'label' => $questionData['label'],
                    'type' => $questionData['type'],
                    'required' => $questionData['required'],
                    'options' => $questionData['options'] ?? null,
                    'order' => $qIndex
                ]);
            }
        }

        return $form;
    });

    return response()->json([
        'success' => true,
        'data' => $form->load('sections.questions'),
        'message' => 'Formulaire créé',
        'errors' => null
    ]);
    }

    //  GET /forms
    public function index()
    {
        $forms = Form::with('sections.questions')->get();

        return response()->json([
            'success' => true,
            'data' => $forms,
            'message' => 'Liste des formulaires',
            'errors' => null
        ]);
    }
    

    // POST /forms/{id}/duplicate
    public function duplicate($id)
{
    $original = Form::with('sections.questions')->find($id);

    if (!$original) {
        return response()->json([
            'success' => false,
            'data' => null,
            'message' => 'Formulaire introuvable',
            'errors' => null
        ], 404);
    }

    $newForm = DB::transaction(function () use ($original) {

        // duplication form
        $form = Form::create([
            'title' => $original->title . ' (copie)',
            'description' => $original->description
        ]);

        foreach ($original->sections as $section) {

            $newSection = Section::create([
                'form_id' => $form->id,
                'title' => $section->title,
                'order' => $section->order
            ]);

            foreach ($section->questions as $question) {

                Question::create([
                    'section_id' => $newSection->id,
                    'label' => $question->label,
                    'type' => $question->type,
                    'required' => $question->required,
                    'options' => $question->options,
                    'order' => $question->order
                ]);
            }
        }

        return $form;
    });

    return response()->json([
        'success' => true,
        'data' => $newForm->load('sections.questions'),
        'message' => 'Formulaire dupliqué',
        'errors' => null
    ]);
}
}