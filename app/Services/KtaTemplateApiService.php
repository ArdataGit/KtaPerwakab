<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class KtaTemplateApiService
{
    public static function getActive()
    {
        return Http::withToken(session('token'))
            ->get(env('API_BASE_URL') . '/kta-templates/active');
    }
}
