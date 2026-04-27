<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Form\Controllers\FormController;
use App\Modules\Form\Controllers\UploadController;
use App\Jobs\GeneratePdfJob;
use App\Modules\Report\Services\ReportBuilderService;

// Form endpoints
Route::post('/forms', [FormController::class, 'store']);
Route::get('/forms', [FormController::class, 'index']);
Route::post('/forms/{id}/duplicate', [FormController::class, 'duplicate']);

// Upload endpoint
Route::post('/uploads', [UploadController::class, 'store']);

// PDF generation endpoint
Route::post('/missions/{id}/pdf', function ($id) {

    GeneratePdfJob::dispatch($id);

    return response()->json([
        'success' => true,
        'message' => 'Génération PDF lancée'
    ]);
});

// Report endpoint
Route::get('/missions/{id}/report', function ($id, ReportBuilderService $builder) {

    $report = $builder->build($id);

    if (!$report) {
        return response()->json([
            'success' => false,
            'message' => 'Mission introuvable'
        ], 404);
    }

    return response()->json([
        'success' => true,
        'data' => $report
    ]);
});
