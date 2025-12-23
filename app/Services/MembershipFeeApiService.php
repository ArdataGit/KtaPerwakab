<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class MembershipFeeApiService
{
    public static function create(string $token, int $amount)
    {
        return Http::withToken($token)
            ->post(
                config('services.kta_api.base_url') . '/membership-fee',
                [
                    'amount' => $amount,
                    'type' => 'tahunan',
                    'payment_method' => 'manual',
                ]
            );
    }

    public static function myFees(string $token)
    {
        return Http::withToken($token)
            ->get(config('services.kta_api.base_url') . '/membership-fee/my');
    }


    public static function uploadProof($feeId, $file)
    {
        return Http::withToken(session('token'))
            ->attach(
                'proof_image',
                fopen($file->getRealPath(), 'r'),
                $file->getClientOriginalName()
            )
            ->post(
                config('services.kta_api.base_url') . "/membership-fee/{$feeId}/upload-proof"
            );
    }

    public static function detail(string $token, int|string $id)
    {
        return Http::withToken($token)
            ->get(
                config('services.kta_api.base_url') . '/membership-fee/' . (int) $id
            );
    }

}
