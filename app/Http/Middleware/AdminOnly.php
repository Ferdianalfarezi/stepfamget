<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Pastikan hanya role 'admin' yang bisa akses route admin.
 */
class AdminOnly
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            if (Auth::check() && Auth::user()->isGuest()) {
                return redirect()->route('guest.dashboard');
            }
            return redirect()->route('landing');
        }

        return $next($request);
    }
}