<?php

namespace App\Http\Middleware;

use Closure;
use Filament\Pages\Dashboard;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Filament\Facades\Filament;

class RedirectByRoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();
            $currentPanel = Filament::getCurrentPanel()->getId();

            // Only redirect if user is in the wrong panel
            if ($user->roles === 'admin' && $currentPanel !== 'admin') {
                return redirect()->to(Dashboard::getUrl(panel: 'admin'));
            }

            if ($user->roles === 'assessor' && $currentPanel !== 'assessor') {
                return redirect()->to(Dashboard::getUrl(panel: 'assessor'));
            }
        }

        return $next($request);
    }
}
