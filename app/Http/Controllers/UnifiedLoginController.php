<?php

namespace App\Http\Controllers;

use App\Models\User;
use Filament\Facades\Filament;
use Filament\Pages\Dashboard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class UnifiedLoginController extends Controller
{
    public function showLoginForm()
    {
        // Check if user is already authenticated
        if (Auth::check()) {
            $user = Auth::user();

            // Redirect to appropriate dashboard based on role
            return redirect(match ($user->roles) {
                'admin' => Dashboard::getUrl(panel: 'admin'),
                'assessor' => Dashboard::getUrl(panel: 'assessor'),
                'bao' => Dashboard::getUrl(panel: 'assessment'),
                default => '/dashboard',
            });
        }

        return Inertia::render('Auth/UnifiedLogin');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password], $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Redirect based on role using an alternative approach
            $dashboardUrl = match ($user->roles) {
                'admin' => Dashboard::getUrl(panel: 'admin'),
                'assessor' => Dashboard::getUrl(panel: 'assessor'),
                'bao' => Dashboard::getUrl(panel: 'assessment'),
                default => '/dashboard',
            };

            // Use a response that Inertia treats as a full page visit
            return Inertia::location($dashboardUrl);
        }

        throw ValidationException::withMessages([
            'email' => __('auth.failed'),
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
