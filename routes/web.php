<?php

use App\Http\Controllers\MultiStepFormController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\UnifiedLoginController;
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

// Unified Login Routes
Route::get('/login', [UnifiedLoginController::class, 'showLoginForm'])->name('unified.login.form');
Route::post('/login', [UnifiedLoginController::class, 'login'])->name('unified.login');

// Define a standard 'login' route that redirects to unified login
Route::get('breeze/login', function() {
    return redirect()->route('unified.login.form');
})->name('login');

Route::post('/logout', [UnifiedLoginController::class, 'logout'])->name('unified.logout');

require __DIR__.'/auth.php';
