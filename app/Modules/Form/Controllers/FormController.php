<?php

namespace App\Modules\Form\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\Form\Models\Form; 
use App\Modules\Form\Services\FormSchemaService;

class FormController extends Controller
{
    public function store(Request $request, FormSchemaService $schema)
    {
        $schema->validate($request->all());

        $form = Form::create([
            'title' => $request->title,
            'description' => $request->description
        ]);

        return response()->json([
            'success' => true,
            'data' => $form,
            'message' => 'Formulaire créé',
            'errors' => null
        ]);
    }
}