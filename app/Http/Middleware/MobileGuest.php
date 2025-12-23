<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class MobileGuest
{
    public function handle(Request $request, Closure $next)
    {
        if (session()->has('token')) {
            return redirect()->route('mobile.home');
        }

        return $next($request);
    }
}
