<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class StrukturOrganisasiApiService
{
    public static function get(string $token)
    {
        return Http::withToken($token)
            ->get(env('API_BASE_URL') . '/struktur-organisasi');
    }
}
