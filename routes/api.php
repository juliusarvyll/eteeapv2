<?php

use App\Http\Controllers\ApplicationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Application routes without middleware
Route::prefix('application')->group(function () {
    // Save individual steps
    Route::post('/step/{step}', [ApplicationController::class, 'saveStep'])
        ->where('step', '[1-8]')
        ->name('application.save-step');

    // Finalize application
    Route::post('/finalize', [ApplicationController::class, 'finalizeApplication'])
        ->name('application.finalize');

    // Load saved application
    Route::get('/{applicantId}', [ApplicationController::class, 'loadApplication'])
        ->name('application.load');

    // Get application status
    Route::get('/{applicantId}/status', [ApplicationController::class, 'getStatus'])
        ->name('application.status');
});

// Auth check route
Route::get('/auth-check', function () {
    return response()->json(['authenticated' => true]);
});
