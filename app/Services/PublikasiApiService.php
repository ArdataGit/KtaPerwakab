<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PublikasiApiService
{
    /**
     * Ambil list publikasi (paginated)
     */
    public static function list(array $params = [])
    {
        return Http::withToken(session('token'))
            ->get(
                config('services.kta_api.base_url') . '/publikasi',
                $params
            );
    }

    /**
     * Ambil detail publikasi
     */
    public static function detail(int|string $id)
    {
        return Http::withToken(session('token'))
            ->get(
                config('services.kta_api.base_url') . '/publikasi/' . $id
            );
    }
}
