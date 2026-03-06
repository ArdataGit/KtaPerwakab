<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class UmkmTransactionApiService
{
    /**
     * Checkout produk UMKM via Tripay
     */
    public static function checkout(array $data)
    {
        return Http::withToken(session('token'))
            ->asJson()
            ->post(
            config('services.kta_api.base_url') . '/checkout-product',
            $data
        );
    }

    /**
     * Riwayat transaksi milik user yang login
     */
    public static function myTransactions(array $params = [])
    {
        return Http::withToken(session('token'))
            ->get(
            config('services.kta_api.base_url') . '/my-transactions',
            $params
        );
    }

    /**
     * Detail transaksi berdasarkan ID
     */
    public static function transactionDetail(int|string $id)
    {
        return Http::withToken(session('token'))
            ->get(
            config('services.kta_api.base_url') . '/my-transactions/' . $id
        );
    }

    /**
     * Daftar penjualan UMKM milik user yang login
     */
    public static function umkmSales(array $params = [])
    {
        return Http::withToken(session('token'))
            ->get(
            config('services.kta_api.base_url') . '/umkm/sales',
            $params
        );
    }
}
