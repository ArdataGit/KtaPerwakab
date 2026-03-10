<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class RekeningIuranApiService
{
    public static function first()
    {
        return Http::get(config('services.kta_api.base_url') . '/rekening-iuran');
    }
}
