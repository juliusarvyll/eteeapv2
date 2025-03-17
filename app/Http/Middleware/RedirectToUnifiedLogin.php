<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectToUnifiedLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // If the request is for a Filament login page and the user is not authenticated
        if (str_contains($request->path(), '/login') && !Auth::check() &&
            (str_contains($request->path(), 'admin/login') ||
             str_contains($request->path(), 'assessment/login') ||
             str_contains($request->path(), 'bao/login'))) {
            // Redirect to our unified login
            return redirect()->route('unified.login.form');
        }

        return $next($request);
    }
}
