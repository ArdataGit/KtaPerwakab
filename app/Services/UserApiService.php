<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class UserApiService
{
    /**
     * Get profile user (me)
     */
    public static function me(string $token)
    {
        return Http::withToken($token)
            ->get(env('API_BASE_URL') . '/me');
    }

    /**
     * Update profile (tanpa foto)
     */
    public static function updateProfile(string $token, array $payload)
    {
        return Http::withToken($token)
            ->post(env('API_BASE_URL') . '/user/profile', $payload);
    }

    public static function storeFamilyMember(string $token, array $payload)
    {
        return \Http::withToken($token)
            ->post(env('API_BASE_URL') . '/family-members', $payload);
    }

    public static function updateFamilyMember(string $token, $id, array $payload)
    {
        return \Http::withToken($token)
            ->put(env('API_BASE_URL') . "/family-members/{$id}", $payload);
    }

    public static function deleteFamilyMember(string $token, $id)
    {
        return \Http::withToken($token)
            ->delete(env('API_BASE_URL') . "/family-members/{$id}");
    }

    /**
     * Update profile + photo (multipart)
     */
    public static function updateProfileWithPhoto(string $token, array $payload, $photo = null)
    {
        $request = Http::withToken($token);

        if ($photo) {
            $request = $request->attach(
                'profile_photo',
                file_get_contents($photo->getRealPath()),
                $photo->getClientOriginalName()
            );
        }

        return $request->post(
            env('API_BASE_URL') . '/user/profile',
            $payload
        );
    }


    /**
     * Update photo saja (multipart)
     */
    public static function updatePhoto(string $token, $photo)
    {
        return Http::withToken($token)
            ->attach(
            'profile_photo',
            file_get_contents($photo->getRealPath()),
            $photo->getClientOriginalName()
        )
            ->post(env('API_BASE_URL') . '/user/profile/photo');
    }

    /**
     * Logout user (API)
     */
    public static function logout(string $token)
    {
        return Http::withToken($token)
            ->post(env('API_BASE_URL') . '/logout');
    }
}
