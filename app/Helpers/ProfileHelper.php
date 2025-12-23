<?php

if (!function_exists('api_profile_url')) {
    function api_profile_url($filename)
    {
        $base = rtrim(env('STORAGE_BASE_URL', ''), '/');

        if (!$filename) {
            return asset('images/assets/default-avatar.png');
        }

        return "{$base}/storage/profile_photos/{$filename}";
    }
    function api_proof_url($filename)
    {
        $base = rtrim(env('STORAGE_BASE_URL', ''), '/');

        if (!$filename) {
            return asset('images/assets/default-avatar.png');
        }

        return "{$base}/storage/{$filename}";
    }
    function api_product_url($path = null)
    {
        $base = rtrim(env('STORAGE_BASE_URL', ''), '/');

        if (!$path) {
            return asset('images/no-image.png');
        }

        return "{$base}/storage/{$path}";
    }

}
