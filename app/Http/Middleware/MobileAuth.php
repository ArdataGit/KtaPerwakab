<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\AuthApiService;

class MobileAuth
{
    public function handle(Request $request, Closure $next)
    {
        $token = session('token');

        if (!$token) {
            return redirect()->route('mobile.login');
        }

        // Validasi token ke API (cache 5 menit agar tidak hit API tiap request)
        $cacheKey = 'token_valid_' . md5($token);
        $isValid = cache()->remember($cacheKey, now()->addMinutes(5), function () use ($token) {
            try {
                $response = AuthApiService::me($token);
                return $response->successful();
            }
            catch (\Throwable $e) {
                return false;
            }
        });

        if (!$isValid) {
            // Hapus cache & session, redirect ke login
            cache()->forget($cacheKey);
            session()->forget(['user', 'token', 'membership_fee_id', 'membership_fee_amount']);
            session()->invalidate();
            session()->regenerateToken();
            return redirect()->route('mobile.login')->with('info', 'Sesi Anda telah berakhir. Silakan login kembali.');
        }

        return $next($request);
    }
}
