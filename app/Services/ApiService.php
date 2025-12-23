<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ApiService
{
    public static function post($endpoint, $data)
    {
        return Http::post(env('API_BASE_URL') . $endpoint, $data);
    }
}
