<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class MarketplaceApiService
{
    public static function products(array $params = [])
    {
        return Http::withToken(session('token'))
            ->get(
                config('services.kta_api.base_url') . '/marketplace/products',
                $params
            );
    }

    public static function productDetail(int|string $id)
    {
        return Http::withToken(session('token'))
            ->get(
                config('services.kta_api.base_url') . '/marketplace/products/' . $id
            );
    }

    public static function myProducts(array $params = [])
    {
        return Http::withToken(session('token'))
            ->get(
                config('services.kta_api.base_url') . '/my-products',
                $params
            );
    }

    public static function store(array $data)
    {
        $request = Http::withToken(session('token'));

        if (isset($data['photos'])) {
            foreach ($data['photos'] as $photo) {
                $request->attach(
                    'photos[]',
                    file_get_contents($photo->getRealPath()),
                    $photo->getClientOriginalName()
                );
            }

            unset($data['photos']);
        }

        return $request->post(
            config('services.kta_api.base_url') . '/my-products',
            $data
        );
    }

    public static function update(int|string $id, array $data)
    {
        $request = Http::withToken(session('token'));

        if (isset($data['photos']) && !empty($data['photos'])) {
            foreach ($data['photos'] as $photo) {
                $request->attach(
                    'photos[]',
                    file_get_contents($photo->getRealPath()),
                    $photo->getClientOriginalName()
                );
            }

            unset($data['photos']);
        }

        // Gunakan POST dengan _method untuk form-data
        $data['_method'] = 'PUT';

        return $request->post(
            config('services.kta_api.base_url') . '/my-products/' . $id,
            $data
        );
    }

    public static function delete(int|string $id)
    {
        return Http::withToken(session('token'))
            ->delete(
                config('services.kta_api.base_url') . '/my-products/' . $id
            );
    }

    public static function deletePhoto(int|string $photoId)
    {
        return Http::withToken(session('token'))
            ->delete(
                config('services.kta_api.base_url') . '/my-products/photos/' . $photoId
            );
    }
}
