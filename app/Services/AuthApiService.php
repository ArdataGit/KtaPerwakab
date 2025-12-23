<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class AuthApiService
{
    public static function login($email, $password)
    {
        return Http::post(env('API_BASE_URL') . '/login', [
            'email' => $email,
            'password' => $password
        ]);
    }

    public static function register($payload)
    {
        return Http::post(env('API_BASE_URL') . '/register', $payload);
    }

    public static function me(string $token)
    {
        return Http::withToken($token)
            ->get(env('API_BASE_URL') . '/me');
    }

    public static function logout(string $token)
    {
        return Http::withToken($token)
            ->post(env('API_BASE_URL') . '/logout');
    }
}
