<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class DonationApiService
{
    protected static function client()
    {
        return Http::withToken(session('token'))
            ->acceptJson()
            ->timeout(15);
    }

    /**
     * CREATE DONATION
     */
    public static function donate(array $payload)
    {
        return self::client()->post(
            config('services.kta_api.base_url') . '/donations',
            $payload
        );
    }

    /**
     * CEK STATUS DONASI
     */
    public static function status(int $donationId)
    {
        return self::client()->get(
            config('services.kta_api.base_url') . "/donations/{$donationId}/status"
        );
    }
  
      /**
     * Get donation detail (for checkout page)
     */
    public static function detail(int $donationId)
    {
        return Http::withToken(session('token'))
            ->acceptJson()
            ->get(
                config('services.kta_api.base_url') . "/donations/{$donationId}"
            );
    }
  
    public static function myDonations()
    {
        return Http::withToken(session('token'))
            ->acceptJson()
            ->get(config('services.kta_api.base_url') . '/donations/my');
    }
}
