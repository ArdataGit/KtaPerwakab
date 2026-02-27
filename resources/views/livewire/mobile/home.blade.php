<?php
use App\Services\AuthApiService;
use App\Services\NewsArticleApiService;
use function Livewire\Volt\{state, mount};

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

    // Refresh user data terbaru
    $response = AuthApiService::me($this->token);
    if ($response->successful()) {
        $user = $response->json('data');
        session(['user' => $user]);
        $this->user = $user;
    }

    // Fetch artikel terbaru (maks 3)
    $articleResponse = NewsArticleApiService::list([
        'search' => $this->search ?: null,
    ]);
    if ($articleResponse->successful()) {
        $articles = $articleResponse->json('data.featured') ?? [];
        $this->latestArticles = collect($articles)->take(3)->all();
    }
});
?>

@php
    use Carbon\Carbon;
    
    $role = $user['role'] ?? null;
    $expiredAtRaw = $user['expired_at'] ?? null;
    $isAnggota = $role === 'anggota';
    $today = Carbon::now('Asia/Jakarta')->startOfDay();
    $expiredAt = $expiredAtRaw
        ? Carbon::parse($expiredAtRaw)->timezone('Asia/Jakarta')->startOfDay()
        : null;

    $isFirstIuran   = $isAnggota && is_null($expiredAt);
    $isExpired      = $isAnggota && $expiredAt && $expiredAt->lessThan($today);
    $daysUntilExpired = ($isAnggota && $expiredAt)
        ? $today->diffInDays($expiredAt, false)
        : null;
    $isH7BeforeExpired = $isAnggota
        && $expiredAt
        && $daysUntilExpired !== null
        && $daysUntilExpired >= 1
        && $daysUntilExpired <= 7;

    $showIuranPopup = $isFirstIuran || $isExpired || $isH7BeforeExpired;
    $closableIuranPopup = !$isFirstIuran;

    // Hanya cek family jika iuran TIDAK perlu ditampilkan
    $showFamilyPopup = !$showIuranPopup
        && $isAnggota
        && count($user['family_members'] ?? []) === 0;
@endphp

<x-layouts.mobile title="Beranda">

    <!-- ================== POPUP IURAN - Prioritas pertama ================== -->
    @if($showIuranPopup)
        <div x-data="{ open: true }" x-show="open" x-transition
             class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/50 px-4" style="isolation:isolate;">
            <div class="bg-white rounded-2xl w-full max-w-sm p-5 text-center">
                <div class="mb-4">
                    <img src="/images/assets/iuran.png" class="mx-auto w-20">
                </div>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">
                    @if($isFirstIuran) Wajib Iuran Tahunan Pertama
                    @elseif($isExpired) Keanggotaan Anda Telah Berakhir
                    @else Iuran Tahunan Akan Segera Berakhir @endif
                </h3>
                <p class="text-sm text-gray-600 mb-5">
                    @if($isFirstIuran) Untuk mengaktifkan status keanggotaan Anda, silakan lakukan iuran tahunan pertama.
                    @elseif($isExpired) Masa berlaku keanggotaan Anda telah habis. Segera lakukan iuran agar tetap aktif.
                    @else Masa berlaku keanggotaan Anda akan berakhir dalam waktu dekat. Segera lakukan iuran. @endif
                </p>
                <a href="{{ route('mobile.iuran') }}"
                   class="block w-full bg-green-600 text-white py-3 rounded-xl font-semibold mb-3">
                    @if($isFirstIuran) Bayar Iuran Pertama @else Iuran Sekarang @endif
                </a>
                <a href="/iuran/saya"
                   class="block w-full border border-green-600 text-green-700 py-3 rounded-xl font-semibold mb-4">
                    Lihat Riwayat Iuran
                </a>
                @if($closableIuranPopup)
                    <button @click="open = false" class="text-sm text-gray-500">
                        Nanti Saja
                    </button>
                @endif
            </div>
        </div>
    @endif

    <!-- ================== POPUP DATA KELUARGA - Hanya muncul jika iuran tidak muncul ================== -->
    @if($showFamilyPopup)
        <div x-data="{ open: true }" x-show="open" x-transition
             class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/60 px-4" style="isolation:isolate;">
            <div class="bg-white rounded-2xl w-full max-w-sm p-6 text-center shadow-2xl">
                <div class="mb-5">
                </div>
                <h3 class="text-lg font-bold text-gray-800 mb-3">
                    Data Anggota Keluarga Belum Lengkap
                </h3>
                <p class="text-sm text-gray-600 mb-6 leading-relaxed">
                    Untuk kelengkapan data keanggotaan dan manfaat perlindungan,
                    silakan isi data anggota keluarga Anda sekarang.
                </p>
                <a href="/profile/family"
                   class="block w-full bg-green-600 text-white py-3.5 rounded-xl font-semibold mb-4 shadow-md hover:bg-green-700 transition">
                    Isi Data Keluarga Sekarang
                </a>
            </div>
        </div>
    @endif

    <div class="pb-20">
        <!-- HERO -->
        <x-mobile.home.hero 
            :name="$user['name'] ?? 'Pengguna'" 
            :photo="$user['profile_photo'] ?? null"
            :fullname="$user['name'] ?? 'Pengguna'" 
            :city="$user['city'] ?? 'Kota Anda'" 
            :role="$user['role'] ?? 'User'" 
        />

        <!-- MENU -->
        <x-mobile.home.menu :items="[
            ['icon' => 'kta', 'label' => 'KTA DIGITAL', 'route' => route('mobile.kta')],
            ['icon' => 'struktur', 'label' => 'STRUKTUR ORGANISASI', 'route' => route('mobile.struktur-organisasi')],
            ['icon' => 'Info', 'label' => 'SEJARAH', 'route' => route('mobile.history')],
            ['icon' => 'artikel', 'label' => 'ARTIKEL', 'route' => route('mobile.articles')],
            ['icon' => 'karya', 'label' => 'KARYA ', 'route' => route('mobile.karya.index')],
            ['icon' => 'martketplace', 'label' => 'UMKM', 'route' => route('mobile.marketplace.index')],
            ['icon' => 'Info', 'label' => 'INFO DUKA', 'route' => route('mobile.info-duka.index')],
            ['icon' => 'donasi', 'label' => 'DONASI', 'route' => route('mobile.donation.index')],
            ['icon' => 'karya', 'label' => 'BISNIS', 'route' => route('mobile.bisnis.explore')],
        ]" />

        <!-- BANNER -->
        <livewire:mobile.home.banner />

        <!-- ARTICLE -->
        <div class="px-4 mt-3">
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

    <!-- FLOATING WHATSAPP BUTTON -->
    <a href="https://wa.me/628567895905" target="_blank"
       class="fixed bottom-24 right-4 z-[9999] bg-white text-[#25D366] p-3 rounded-full shadow-lg hover:bg-gray-100 transition-transform hover:scale-105 flex items-center justify-center border border-gray-100">
        <svg xmlns="http://www.w3.org/2000/svg"
            class="w-8 h-8"
            fill="currentColor"
            viewBox="0 0 24 24">
            <path d="M12.031 0C5.383 0 0 5.385 0 12.035c0 2.128.553 4.2 1.606 6.02L.03 24l6.105-1.597A11.968 11.968 0 0 0 12.031 24c6.646 0 12.033-5.385 12.033-12.035C24.064 5.385 18.677 0 12.031 0zm7.13 17.293c-.301.85-1.493 1.554-2.316 1.71-.62.115-1.427.185-2.589-.253-.518-.196-1.126-.454-1.849-.806-3.882-1.89-6.38-5.836-6.572-6.096-.192-.26-1.57-2.094-1.57-3.996 0-1.902.99-2.835 1.34-3.23.35-.395.76-.494 1.012-.494.25 0 .502.002.723.013.232.01.543-.09.827.597.291.704.992 2.43 1.079 2.607.087.177.146.383.029.615-.117.23-.176.38-.352.58-.175.2-.363.428-.521.578-.175.168-.358.35-.157.697.202.348.898 1.483 1.933 2.406 1.334 1.192 2.458 1.562 2.808 1.734.35.172.554.144.764-.092.209-.236.902-1.05 1.144-1.41.242-.36.483-.298.806-.182.324.116 2.053.97 2.406 1.146.352.176.586.264.673.41.088.147.088.851-.213 1.7z"/>
        </svg>
    </a>

    <x-mobile.navbar active="home" />
</x-layouts.mobile>