<?php

namespace App\Http\Responses;

use Filament\Facades\Filament;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;
use Filament\Http\Responses\Auth\LogoutResponse as BaseLogoutResponse;

class LogoutResponse extends BaseLogoutResponse
{
    public function toResponse($request): \Illuminate\Http\RedirectResponse
    {
        // Redirect to unified login instead of panel-specific login
        return redirect()->route('unified.login.form');
    }
}
