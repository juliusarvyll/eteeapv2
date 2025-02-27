<?php
namespace App\Http\Responses;
use Filament\Pages\Dashboard;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;
use Filament\Http\Responses\Auth\LoginResponse as BaseLoginResponse;

class LoginResponse extends BaseLoginResponse
{
    public function toResponse($request): RedirectResponse|Redirector
    {
        $user = auth()->user();

        if ($user->roles === 'admin') {
            return redirect()->to(Dashboard::getUrl(panel: 'admin'));
        }

        if ($user->roles === 'assessor') {
            return redirect()->to(Dashboard::getUrl(panel: 'assessor'));
        }
        if ($user->roles === 'bao') {
            return redirect()->to(Dashboard::getUrl(panel: 'bao'));
        }

        return parent::toResponse($request);
    }
}
