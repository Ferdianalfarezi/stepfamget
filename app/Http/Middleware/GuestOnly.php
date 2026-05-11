<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Pastikan hanya role 'guest' yang bisa akses route guest.
 */
class GuestOnly
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check() || !Auth::user()->isGuest()) {
            return redirect()->route('landing');
        }

        return $next($request);
    }
}