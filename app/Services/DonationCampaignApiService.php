<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class DonationCampaignApiService
{
    protected static function client()
    {
        return Http::withToken(session('token'))
            ->acceptJson()
            ->timeout(10);
    }

    /**
     * LIST DONATION CAMPAIGN
     * GET /donation-campaigns
     */
    public static function list(array $params = [])
    {
        return self::client()->get(
            config('services.kta_api.base_url') . '/donation-campaigns',
            array_filter($params)
        );
    }

    /**
     * DETAIL DONATION CAMPAIGN
     * GET /donation-campaigns/{id}
     */
    public static function detail(int $id)
    {
        return self::client()->get(
            config('services.kta_api.base_url') . "/donation-campaigns/{$id}"
        );
    }
}
