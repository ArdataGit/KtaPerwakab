<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class BisnisApiService
{
    /**
     * Ambil list bisnis (paginated / filter)
     */
    public static function list(array $params = [])
    {
        return Http::withToken(session('token'))
            ->get(
                config('services.kta_api.base_url') . '/bisnis',
                $params
            );
    }

    /**
     * Ambil detail bisnis (by ID atau slug)
     */
    public static function show(string $slug)
    {
        return Http::withToken(session('token'))
            ->get(
                config('services.kta_api.base_url') . '/bisnis/' . $slug
            );
    }
}
