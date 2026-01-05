<?php

use App\Services\AuthApiService;
use function Livewire\Volt\state;
use function Livewire\Volt\mount;

state([
    'user' => session('user') ?? [],
    'token' => session('token'),
]);

mount(function () {

    if (!$this->token) {
        return;
    }

    $response = AuthApiService::me($this->token);

    if ($response->successful()) {
        $user = $response->json('data');

        session(['user' => $user]);
        $this->user = $user;
    }
});
?>

@php
    use Carbon\Carbon;

    /*
    |--------------------------------------------------------------------------
    | BASIC DATA
    |--------------------------------------------------------------------------
    */
    $role = $user['role'] ?? null;
    $expiredAtRaw = $user['expired_at'] ?? null;

    $isAnggota = $role === 'anggota';

    /*
    |--------------------------------------------------------------------------
    | DATE NORMALIZATION (UTC → ASIA/JAKARTA)
    |--------------------------------------------------------------------------
    */
    $today = Carbon::now('Asia/Jakarta')->startOfDay();

    $expiredAt = $expiredAtRaw
        ? Carbon::parse($expiredAtRaw)->timezone('Asia/Jakarta')->startOfDay()
        : null;

    /*
    |--------------------------------------------------------------------------
    | BUSINESS LOGIC
    |--------------------------------------------------------------------------
    */

    // 1. Belum pernah iuran
    $isFirstIuran = $isAnggota && is_null($expiredAt);

    // 2. Sudah expired (lewat hari ini)
    $isExpired = $isAnggota && $expiredAt && $expiredAt->lessThan($today);

    // 3. Hitung sisa hari ke expired (H-N)
    $daysUntilExpired = ($isAnggota && $expiredAt)
        ? $today->diffInDays($expiredAt, false)
        : null;

    // 4. H-7 sebelum expired (1 s/d 7 hari)
    $isH7BeforeExpired = $isAnggota
        && $expiredAt
        && $daysUntilExpired !== null
        && $daysUntilExpired >= 1
        && $daysUntilExpired <= 7;

    /*
    |--------------------------------------------------------------------------
    | FINAL FLAG
    |--------------------------------------------------------------------------
    */
    $showIuranPopup = $isFirstIuran || $isExpired || $isH7BeforeExpired;

    // Popup bisa ditutup jika bukan first iuran
    $closablePopup = !$isFirstIuran;
@endphp


<x-layouts.mobile title="Beranda">
    <div>
        <!-- {{  $expiredAt}}
        {{  $today}} -->
        @if($showIuranPopup)
            <div x-data="{ open: true }" x-show="open" x-transition
                class="fixed inset-0 z-100 flex items-center justify-center bg-black/50 px-4">
                <div class="bg-white rounded-2xl w-full max-w-sm p-5 text-center">

                    <div class="mb-4">
                        <img src="/images/assets/iuran.png" class="mx-auto w-20">
                    </div>

                    {{-- JUDUL --}}
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">
                        @if($isFirstIuran)
                            Wajib Iuran Tahunan Pertama
                        @elseif($isExpired)
                            Keanggotaan Anda Telah Berakhir
                        @else
                            Iuran Tahunan Akan Segera Berakhir
                        @endif
                    </h3>

                    {{-- DESKRIPSI --}}
                    <p class="text-sm text-gray-600 mb-5">
                        @if($isFirstIuran)
                            Untuk mengaktifkan status keanggotaan Anda, silakan lakukan iuran tahunan pertama.
                        @elseif($isExpired)
                            Masa berlaku keanggotaan Anda telah habis. Segera lakukan iuran agar tetap aktif.
                        @else
                            Masa berlaku keanggotaan Anda akan berakhir dalam waktu dekat. Segera lakukan iuran.
                        @endif
                    </p>

                    <a href="{{ route('mobile.iuran') }}"
                        class="block w-full bg-green-600 text-white py-3 rounded-xl font-semibold mb-3">
                        @if($isFirstIuran)
                            Bayar Iuran Pertama
                        @else
                            Iuran Sekarang
                        @endif
                    </a>

                    {{-- CLOSE BUTTON (HANYA JIKA BOLEH) --}}
                    @if($closablePopup)
                        <button @click="open = false" class="text-sm text-gray-500">
                            Nanti Saja
                        </button>
                    @endif

                </div>
            </div>
        @endif

        {{-- HERO --}}
        <x-mobile.home.hero :name="$user['name'] ?? 'Pengguna'" :photo="$user['profile_photo'] ?? null"
            :fullname="$user['name'] ?? 'Pengguna'" :city="$user['city'] ?? 'Kota Anda'" :role="$user['role'] ?? 'User'" />

        {{-- MENU --}}
        <x-mobile.home.menu :items="[
            ['icon' => 'kta', 'label' => 'KTA DIGITAL', 'route' => route('mobile.kta')],
            ['icon' => 'artikel', 'label' => 'ARTIKEL', 'route' => route('mobile.articles')],
            ['icon' => 'karya', 'label' => 'KARYA & BISNIS', 'route' => route('mobile.karya.index')],
            ['icon' => 'martketplace', 'label' => 'MARKETPLACE', 'route' => route('mobile.marketplace.index')],
            ['icon' => 'info', 'label' => 'INFO DUKA', 'route' => route('mobile.info-duka.index')],
            ['icon' => 'struktur', 'label' => 'STRUKTUR ORGANISASI', 'route' => route('mobile.struktur-organisasi')],
            ['icon' => 'donasi', 'label' => 'DONASI'],
            ['icon' => 'poin', 'label' => 'POINT'],
        ]" />

        {{-- BANNER --}}
        <x-mobile.home.banner />

        {{-- ARTICLE --}}
        <x-mobile.home.article-card image="/images/assets/default-article.png"
            title="Ingin Berorganisasi? Ini Cara Berorganisasi" link="#" />

        <x-mobile.navbar active="home" />
    </div>
</x-layouts.mobile>