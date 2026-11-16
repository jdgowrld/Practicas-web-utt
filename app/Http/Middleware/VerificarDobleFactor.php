<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerificarDobleFactor
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if ($user && $user->rol === 'admin' && !session('2fa_verified')) {
            return redirect()->route('admin.2fa.vista');
        }

        return $next($request);
    }
}