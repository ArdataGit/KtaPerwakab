<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class InfoDukaApiService
{
    protected static function client()
    {
        return Http::withToken(session('token'))
            ->acceptJson()
            ->timeout(10);
    }

    /**
     * LIST INFO DUKA
     * Support:
     * - search
     * - tahun
     * - per_page
     */
    public static function list(array $params = [])
    {
        return self::client()->get(
            config('services.kta_api.base_url') . '/info-duka',
            array_filter($params)
        );
    }

    /**
     * DETAIL INFO DUKA
     */
    public static function detail(int|string $id)
    {
        return self::client()->get(
            config('services.kta_api.base_url') . "/info-duka/{$id}"
        );
    }
}
