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
        $user = auth()->user();

        if ($user) {
            if ($user->roles === 'admin') {
                return redirect()->to(Filament::getPanel('admin')->getLoginUrl());
            }

            if ($user->roles === 'assessor') {
                return redirect()->to(Filament::getPanel('assessor')->getLoginUrl());
            }
            if ($user->roles === 'bao') {
                return redirect()->to(Filament::getPanel('bao')->getLoginUrl());
            }
        }

        return parent::toResponse($request);
    }
}
