<?php

use App\Http\Controllers\MultiStepFormController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\AssessmentController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


Route::get('/dashboard', [MultiStepFormController::class, 'index'])->name('Dashboard');
Route::get('/', function () {
    return Inertia::render('Welcome');
});

Route::get('/track-application', function () {
    return Inertia::render('TrackApplication');
})->name('track-application');

Route::get('/track-applications', [ApplicationController::class, 'trackApplication'])->name('track-applications');

Route::get('/api/track-application', [ApplicationController::class, 'trackApplication']);

Route::get('/assessment/{student}', [AssessmentController::class, 'generate'])->name('assessment.pdf');

Route::get('/applicant/{record}/pdf', [ApplicationController::class, 'generateApplicantPdf'])
    ->name('applicant.pdf');

require __DIR__.'/auth.php';
