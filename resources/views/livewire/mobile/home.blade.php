<?php

use App\Services\AuthApiService;
use App\Services\NewsArticleApiService;
use function Livewire\Volt\state;
use function Livewire\Volt\mount;

state([
    'user' => session('user') ?? [],
    'token' => session('token'),
  'latestArticles' => [],
    'search' => '',
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

    // Fetch 3 artikel terbaru untuk beranda
    $articleResponse = NewsArticleApiService::list([
        'search' => $this->search ?: null,
    ]);

    if ($articleResponse->successful()) {
        $articles = $articleResponse->json('data.featured') ?? [];
        // Ambil maksimal 3 artikel pertama
        $this->latestArticles = collect($articles)->take(3)->all();
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
              
                <a href="/iuran/saya"
                   class="block w-full border border-green-600 text-green-700 py-3 rounded-xl font-semibold mb-4">
                    Lihat Riwayat Iuran
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
<div class="pb-20">
    {{-- HERO --}}
    <x-mobile.home.hero :name="$user['name'] ?? 'Pengguna'" :photo="$user['profile_photo'] ?? null"
        :fullname="$user['name'] ?? 'Pengguna'" :city="$user['city'] ?? 'Kota Anda'" :role="$user['role'] ?? 'User'" />

    {{-- MENU --}}
    <x-mobile.home.menu :items="[
        ['icon' => 'kta', 'label' => 'KTA DIGITAL', 'route' => route('mobile.kta')],
        ['icon' => 'artikel', 'label' => 'ARTIKEL', 'route' => route('mobile.articles')],
        ['icon' => 'karya', 'label' => 'KARYA ', 'route' => route('mobile.karya.index')],
        ['icon' => 'martketplace', 'label' => 'MARKETPLACE', 'route' => route('mobile.marketplace.index')],
        ['icon' => 'Info', 'label' => 'INFO DUKA', 'route' => route('mobile.info-duka.index')],
        ['icon' => 'struktur', 'label' => 'STRUKTUR ORGANISASI', 'route' => route('mobile.struktur-organisasi')],
        ['icon' => 'donasi', 'label' => 'DONASI', 'route' => route('mobile.donation.index')],
        ['icon' => 'karya', 'label' => 'BISNIS', 'route' => route('mobile.bisnis.explore')],
    ]" />

    {{-- BANNER --}}
    <livewire:mobile.home.banner />

    {{-- ARTICLE --}}
    <div class="px-6 mt-3 ">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Artikel Terbaru</h3>

        @forelse($latestArticles as $article)
            <x-mobile.home.article-card
                :image="$article['cover_image'] ?? '/images/assets/default-article.png'"
                :title="$article['title'] ?? 'Judul artikel belum tersedia'"
                :link="route('mobile.article.detail', $article['id'])"
            />
        @empty
            <div class="px-6 mt-10 pb-20">
                <div class="bg-white rounded-xl shadow overflow-hidden p-8 text-center">
                    <p class="text-gray-500">Belum ada artikel terbaru.</p>
                </div>
            </div>
        @endforelse
    </div>
</div>
    <x-mobile.navbar active="home" />
</x-layouts.mobile>