<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class MarketplaceApiService
{
    public static function products(array $params = [])
    {
        return Http::withToken(session('token'))
            ->get(
                config('services.kta_api.base_url') . '/marketplace/products',
                $params
            );
    }

    public static function productDetail(int|string $id)
    {
        return Http::withToken(session('token'))
            ->get(
                config('services.kta_api.base_url') . '/marketplace/products/' . $id
            );
    }
}
