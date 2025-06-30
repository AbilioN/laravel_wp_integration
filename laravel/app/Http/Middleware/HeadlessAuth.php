<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class HeadlessAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if request is from API
        if ($request->expectsJson() || $request->is('api/*')) {
            // For API requests, we'll handle authentication in the API controllers
            return $next($request);
        }

        // For web requests, check if user is authenticated
        if (!Auth::check()) {
            // If not authenticated and trying to access protected routes
            if ($request->is('my-account*') || $request->is('checkout*')) {
                return redirect()->route('login')->with('error', 'Você precisa estar logado para acessar esta página.');
            }
        }

        return $next($request);
    }
} 