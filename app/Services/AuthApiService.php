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

public static function register(array $payload, array $files = [])
{
    $request = Http::asMultipart();

    // field biasa
    foreach ($payload as $key => $value) {
        if (!is_null($value)) {
            $request->attach($key, (string) $value);
        }
    }

    // file upload
    foreach ($files as $key => $file) {

        if (!$file) continue;

        $path = $file->getRealPath();

        if (!$path || !file_exists($path)) {
            continue; // hindari error contents required
        }

        $request->attach(
            $key,
            fopen($path, 'r'), // 🔥 lebih aman daripada file_get_contents
            $file->getClientOriginalName()
        );
    }

    return $request->post(env('API_BASE_URL') . '/register');
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

    public static function forgotPassword(string $email)
    {
        return Http::withHeaders([
                'Accept' => 'application/json',
            ])
            ->timeout(30)
            ->post(env('API_BASE_URL') . '/forgot-password', [
                'email' => $email
            ]);
    }

    public static function validateResetToken(string $token)
    {
        return Http::withHeaders([
                'Accept' => 'application/json',
            ])
            ->timeout(10)
            ->post(env('API_BASE_URL') . '/validate-reset-token', [
                'token' => $token
            ]);
    }

    public static function resetPassword(string $token, string $password, string $passwordConfirmation)
    {
        return Http::withHeaders([
                'Accept' => 'application/json',
            ])
            ->timeout(30)
            ->post(env('API_BASE_URL') . '/reset-password', [
                'token' => $token,
                'password' => $password,
                'password_confirmation' => $passwordConfirmation
            ]);
    }
}
