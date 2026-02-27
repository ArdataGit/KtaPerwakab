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
            <div class="relative bg-white rounded-2xl w-full max-w-sm p-6 text-center shadow-2xl">
                <button @click="open = false" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
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
    <div class="fixed bottom-24 left-1/2 -translate-x-1/2 z-[9999] pointer-events-none flex justify-end px-4"
         style="width: min(100vw, 420px);">
            <a href="https://wa.me/628567895905" target="_blank"
            class="pointer-events-auto bg-white text-[#25D366] p-3 rounded-full shadow-lg hover:bg-gray-100 transition-transform hover:scale-105 flex items-center justify-center border border-gray-100">
                <img src="images/assets/icon/whatsapp.svg" 
                    alt="WhatsApp" 
                    class="w-8 h-8 object-contain">
            </a>
    </div>

    <x-mobile.navbar active="home" />
</x-layouts.mobile>