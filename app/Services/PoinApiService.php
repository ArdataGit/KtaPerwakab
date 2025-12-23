<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PoinApiService
{
    /* =====================================================
     * MASTER PENUKARAN POIN
     * ===================================================== */

    /**
     * Ambil list master penukaran poin
     * support: search, per_page, sort_by, sort_dir
     */
    public static function list(array $params = [])
    {
        return Http::withToken(session('token'))
            ->get(
                config('services.kta_api.base_url') . '/master-penukaran-poin',
                $params
            );
    }

    /**
     * Ambil detail master penukaran poin
     */
    public static function detail(int|string $id)
    {
        return Http::withToken(session('token'))
            ->get(
                config('services.kta_api.base_url') . '/master-penukaran-poin/' . $id
            );
    }

    /* =====================================================
     * HISTORY PENUKARAN POIN (KELUAR)
     * ===================================================== */

    /**
     * Ambil history penukaran poin user
     * support: per_page, from, to
     */
    public static function historyPenukaran(int|string $userId, array $params = [])
    {
        return Http::withToken(session('token'))
            ->get(
                config('services.kta_api.base_url')
                . '/users/' . $userId . '/tukar-point',
                $params
            );
    }

    /* =====================================================
     * HISTORY PENAMBAHAN POIN (MASUK)
     * ===================================================== */

    /**
     * Ambil history penambahan poin user
     * support: per_page, from, to, category_id
     */
    public static function historyPenambahan(int|string $userId, array $params = [])
    {
        return Http::withToken(session('token'))
            ->get(
                config('services.kta_api.base_url')
                . '/users/' . $userId . '/point-history',
                $params
            );
    }
}
