<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class HomeBannerApiService
{
    protected static function client()
    {
        return Http::withToken(session('token'))
            ->acceptJson()
            ->timeout(15);
    }

    /**
     * Ambil semua banner home (admin / backend use)
     */
    public static function list(array $params = [])
    {
        return self::client()->get(
            config('services.kta_api.base_url') . '/home/banners',
            $params
        );
    }

    /**
     * Ambil banner aktif untuk frontend (home page)
     * - otomatis filter aktif
     * - otomatis filter jadwal tampil
     */
    public static function active()
    {
        return self::client()->get(
            config('services.kta_api.base_url') . '/home/banners/active'
        );
    }

    /**
     * Ambil detail banner (jika suatu saat dibutuhkan)
     */
    public static function detail(int|string $id)
    {
        return self::client()->get(
            config('services.kta_api.base_url') . "/home/banners/{$id}"
        );
    }
}
