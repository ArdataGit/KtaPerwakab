<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class BannerApiService
{
    public static function getBanners()
    {
        return Http::withToken(session('token'))
            ->get(config('services.kta_api.base_url') . '/home/banners');
    }
}
