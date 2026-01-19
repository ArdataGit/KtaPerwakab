<?php
namespace App\Services;
use Illuminate\Support\Facades\Http;
class NewsArticleApiService
{
    protected static function client()
    {
        return Http::withToken(session('token'))
            ->acceptJson()
            ->timeout(10);
    }
    /**
     * LIST ARTIKEL
     */
    public static function list(array $params = [])
    {
        return self::client()->get(
            config('services.kta_api.base_url') . '/news',
            array_filter($params)
        );
    }
    /**
     * DETAIL ARTIKEL
     */
    public static function detail($id)
    {
        return self::client()->get(
            config('services.kta_api.base_url') . "/news/{$id}"
        );
    }
}
?>