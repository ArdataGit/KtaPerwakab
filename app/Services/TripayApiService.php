<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TripayApiService
{
    /**
     * Ambil daftar payment method dari Tripay (via backend API)
     */
    public static function paymentMethods()
    {
        return Http::withToken(session('token'))
            ->acceptJson()
            ->get(
                config('services.kta_api.base_url') . '/tripay/payment-methods'
            );
    }
}
