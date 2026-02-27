<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class OrganizationHistoryApiService
{
    /**
     * Get organization history
     */
    public static function get(string $token)
    {
        return Http::withToken($token)
            ->get(env('API_BASE_URL') . '/organization/history');
    }
}
