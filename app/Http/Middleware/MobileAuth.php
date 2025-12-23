<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class MobileAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (!session()->has('token')) {
            return redirect()->route('mobile.login');
        }

        return $next($request);
    }
}
