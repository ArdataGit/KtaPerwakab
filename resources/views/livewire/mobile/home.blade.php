<?php
use App\Services\AuthApiService;
use App\Services\NewsArticleApiService;
use Carbon\Carbon;
use function Livewire\Volt\{state, mount, computed};

state([
    'user' => session('user') ?? [],
    'token' => session('token'),
    'latestArticles' => [],
    'search' => '',
    'showIuranPopup' => false,
    'showFamilyPopup' => false,
    'closableIuranPopup' => false,
    'isFirstIuran' => false,
    'isExpired' => false,
    'isH7BeforeExpired' => false,
    'expiredAtRaw' => null,
]);

mount(function () {
    if (!$this->token) {
        $this->redirect(route('mobile.login'));
        return;
    }

    // Refresh user data terbaru
    $response = AuthApiService::me($this->token);

    // Jika token expired / invalid → bersihkan session dan redirect ke login
    if (!$response->successful() || $response->status() === 401) {
        $cacheKey = 'token_valid_' . md5($this->token);
        cache()->forget($cacheKey);
        session()->forget(['user', 'token', 'membership_fee_id', 'membership_fee_amount']);
        session()->invalidate();
        session()->regenerateToken();
        $this->redirect(route('mobile.login'));
        return;
    }

    $user = $response->json('data');
    session(['user' => $user]);
    $this->user = $user;

    // Kalkulasi popup iuran
    $role = $this->user['role'] ?? null;
    $expiredAtRaw = $this->user['expired_at'] ?? null;
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

    $showFamilyPopup = !$showIuranPopup
        && $isAnggota
        && ($this->user['role'] ?? 'publik') !== 'publik'
        && count($this->user['family_members'] ?? []) === 0;

    $this->expiredAtRaw = $expiredAtRaw;
    $this->isFirstIuran = $isFirstIuran;
    $this->isExpired = $isExpired;
    $this->isH7BeforeExpired = $isH7BeforeExpired;
    $this->showIuranPopup = $showIuranPopup;
    $this->closableIuranPopup = $closableIuranPopup;
    $this->showFamilyPopup = $showFamilyPopup;

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
    // Variabel sudah dihitung di mount(), hanya persiapkan variabel lokal untuk template
    $expiredAt = $expiredAtRaw
        ? Carbon::parse($expiredAtRaw)->timezone('Asia/Jakarta')->startOfDay()
        : null;
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
        @php
            $menuItems = [
                ['icon' => 'pengurus', 'label' => 'STRUKTUR ORGANISASI', 'route' => route('mobile.struktur-organisasi')],
                ['icon' => 'tentangkami', 'label' => 'TENTANG KAMI', 'route' => route('mobile.history')],
                ['icon' => 'artikel', 'label' => 'ARTIKEL', 'route' => route('mobile.articles')],
                ['icon' => 'karya', 'label' => 'KARYA ', 'route' => route('mobile.karya.index')],
                ['icon' => 'umkm', 'label' => 'UMKM', 'route' => route('mobile.marketplace.index')],
                ['icon' => 'infoduka', 'label' => 'INFO DUKA', 'route' => route('mobile.info-duka.index')],
                ['icon' => 'donasi', 'label' => 'DONASI', 'route' => route('mobile.donation.index')],
                ['icon' => 'donasi', 'label' => 'JEJAK HIDUP', 'route' => route('mobile.donation.jejak-hidup')],
                ['icon' => 'bisnis', 'label' => 'BISNIS', 'route' => route('mobile.bisnis.explore')],
            ];

            if (($user['role'] ?? 'publik') !== 'publik') {
                array_unshift($menuItems, ['icon' => 'kta', 'label' => 'KTA DIGITAL', 'route' => route('mobile.kta')]);
            }
        @endphp
        
        <x-mobile.home.menu :items="$menuItems" />

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

    <!-- ================== DESKTOP VIEW ================== -->
    <x-slot:desktop>
        <x-desktop.layout title="Beranda KTA Perwakab">
            
            <!-- POPUP IURAN DESKTOP -->
            @if($showIuranPopup)
                <div x-data="{ open: true }" x-show="open" x-transition
                     class="fixed inset-0 z-[60] flex items-center justify-center bg-gray-900/60 backdrop-blur-sm px-4">
                    <div class="bg-white rounded-2xl w-full max-w-lg p-8 text-center shadow-2xl transform transition-all">
                        <div class="mb-6">
                            <img src="/images/assets/iuran.png" class="mx-auto w-24">
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-3">
                            @if($isFirstIuran) Wajib Iuran Tahunan Pertama
                            @elseif($isExpired) Keanggotaan Anda Telah Berakhir
                            @else Iuran Tahunan Akan Segera Berakhir @endif
                        </h3>
                        <p class="text-gray-600 mb-8 max-w-sm mx-auto">
                            @if($isFirstIuran) Untuk mengaktifkan status keanggotaan Anda, silakan lakukan iuran tahunan pertama.
                            @elseif($isExpired) Masa berlaku keanggotaan Anda telah habis. Segera lakukan iuran agar tetap aktif.
                            @else Masa berlaku keanggotaan Anda akan berakhir dalam waktu dekat. Segera lakukan iuran. @endif
                        </p>
                        <div class="grid grid-cols-2 gap-4">
                            <a href="/iuran/saya" class="block w-full border-2 border-green-600 text-green-700 hover:bg-green-50 py-3 rounded-xl font-bold transition">
                                Riwayat Iuran
                            </a>
                            <a href="{{ route('mobile.iuran') }}" class="block w-full bg-green-600 hover:bg-green-700 text-white py-3 rounded-xl font-bold shadow-lg shadow-green-200 transition">
                                @if($isFirstIuran) Bayar Sekarang @else Iuran Baru @endif
                            </a>
                        </div>
                        @if($closableIuranPopup)
                            <button @click="open = false" class="mt-4 text-sm font-medium text-gray-500 hover:text-gray-700 underline underline-offset-2">
                                Nanti Saja
                            </button>
                        @endif
                    </div>
                </div>
            @endif

            <!-- POPUP KELUARGA DESKTOP -->
            @if($showFamilyPopup)
                <div x-data="{ open: true }" x-show="open" x-transition
                     class="fixed inset-0 z-[60] flex items-center justify-center bg-gray-900/60 backdrop-blur-sm px-4">
                    <div class="bg-white rounded-2xl w-full max-w-lg p-8 text-center shadow-2xl">
                        <div class="mb-5">
                            <div class="h-20 w-20 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            </div>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-3">
                            Data Keluarga Belum Lengkap
                        </h3>
                        <p class="text-gray-600 mb-8 max-w-sm mx-auto leading-relaxed">
                            Untuk kelengkapan data keanggotaan dan manfaat perlindungan, silakan isi data anggota keluarga Anda sekarang.
                        </p>
                        <a href="/profile/family"
                           class="block w-full bg-blue-600 text-white py-3.5 hover:bg-blue-700 rounded-xl font-bold shadow-lg shadow-blue-200 transition">
                            Isi Data Keluarga Sekarang
                        </a>
                        <button @click="open = false" class="mt-4 text-sm font-medium text-gray-400 hover:text-gray-600">Nanti Saja</button>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- KIRI: Profil & Main Info -->
                <div class="lg:col-span-2 space-y-6">
                    
                    <!-- Hero Widget -->
                    <div class="bg-gradient-to-br from-green-700 to-green-900 rounded-2xl p-8 text-white relative overflow-hidden shadow-lg">
                        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between">
                            <div>
                                <h2 class="text-3xl font-bold mb-1">Selamat datang, {{ explode(' ', $user['name'])[0] ?? 'Pengguna' }}! 👋</h2>
                                <p class="text-green-100 text-lg mb-6 opacity-90">Senang melihat Anda kembali di Dashboard Perwakab.</p>
                                <div class="flex gap-4">
                                    <div class="bg-white/10 backdrop-blur-md rounded-xl px-4 py-2 border border-white/20">
                                        <div class="text-xs text-green-200 uppercase font-bold">Status</div>
                                        <div class="font-semibold text-white capitalize">{{ $user['role'] ?? 'Anggota' }}</div>
                                    </div>
                                    <div class="bg-white/10 backdrop-blur-md rounded-xl px-4 py-2 border border-white/20">
                                        <div class="text-xs text-green-200 uppercase font-bold">Domisili</div>
                                        <div class="font-semibold text-white capitalize">{{ $user['city'] ?? '-' }}</div>
                                    </div>
                                </div>
                            </div>
                            @if(($user['role'] ?? 'publik') !== 'publik')
                            <div class="mt-6 md:mt-0">
                                <a href="{{ route('mobile.kta') }}" class="group flex items-center justify-center h-40 w-28 bg-white/10 border-2 border-white/30 backdrop-blur-md rounded-xl hover:bg-white/20 transition cursor-pointer">
                                    <div class="rotate-90 text-center font-bold tracking-widest text-lg whitespace-nowrap">
                                        KTA DIGITAL
                                    </div>
                                </a>
                            </div>
                            @endif
                        </div>
                        
                        <!-- Dekorasi Pattern Background -->
                        <div class="absolute right-0 top-0 w-64 h-64 bg-green-500 rounded-full mix-blend-multiply filter blur-3xl opacity-50 translate-x-1/2 -translate-y-1/2"></div>
                        <div class="absolute right-20 bottom-0 w-48 h-48 bg-emerald-400 rounded-full mix-blend-multiply filter blur-3xl opacity-50 translate-x-1/2 translate-y-1/2"></div>
                    </div>

                    <!-- Quick Menus -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                        <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                            Menu Cepat Akses
                        </h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            @php
                                $desktopMenus = [
                                    ['icon' => 'artikel',      'label' => 'Artikel',       'route' => route('mobile.articles'),            'color' => 'green'],
                                    ['icon' => 'karya',        'label' => 'Karya',         'route' => route('mobile.karya.index'),         'color' => 'purple'],
                                    ['icon' => 'umkm',         'label' => 'UMKM',          'route' => route('mobile.marketplace.index'),   'color' => 'orange'],
                                    ['icon' => 'infoduka',     'label' => 'Info Duka',     'route' => route('mobile.info-duka.index'),     'color' => 'gray'],
                                    ['icon' => 'pengurus',     'label' => 'Pengurus',      'route' => route('mobile.struktur-organisasi'), 'color' => 'teal'],
                                    ['icon' => 'donasi',       'label' => 'Donasi',        'route' => route('mobile.donation.index'),      'color' => 'red'],
                                    ['icon' => 'donasi',       'label' => 'Jejak Hidup',   'route' => route('mobile.donation.jejak-hidup'),'color' => 'emerald'],
                                    ['icon' => 'bisnis',       'label' => 'Bisnis',        'route' => route('mobile.bisnis.explore'),      'color' => 'indigo'],
                                    ['icon' => 'tentangkami',  'label' => 'Tentang Kami',  'route' => route('mobile.history'),             'color' => 'blue'],
                                ];

                                if (($user['role'] ?? 'publik') !== 'publik') {
                                    array_unshift($desktopMenus, ['icon' => 'kta', 'label' => 'KTA', 'route' => route('mobile.kta'), 'color' => 'blue']);
                                }
                            @endphp
                            
                            @foreach($desktopMenus as $menu)
                            <a href="{{ $menu['route'] }}" class="flex flex-col items-center p-4 rounded-xl border border-gray-100 hover:border-{{ $menu['color'] }}-300 hover:shadow-md hover:-translate-y-1 transition duration-200 bg-white group">
                                    <img src="/images/assets/icon/new/{{ $menu['icon'] }}.png" class="w-12 h-12" alt="{{ $menu['label'] }} icon">
                                
                                <span class="text-sm font-semibold text-gray-700 whitespace-nowrap">{{ $menu['label'] }}</span>
                            </a>
                            @endforeach
                        </div>
                    </div>

                    <!-- Banner Desktop Wrapper -->
                    <div class="rounded-2xl overflow-hidden shadow-sm">
                        <livewire:mobile.home.banner />
                    </div>
                </div>

                <!-- KANAN: Sidebar Konten -->
                <div class="space-y-6">
                    
                    <!-- Box Membership Status -->
                     
                    @if(($user['role'] ?? 'publik') !== 'publik')
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                        <h3 class="text-lg font-bold text-gray-800 mb-4 border-b border-gray-100 pb-3">Status Iuran</h3>
                        @if($isFirstIuran)
                            <div class="p-4 bg-red-50 text-red-700 rounded-xl border border-red-100 flex items-start">
                                <svg class="w-6 h-6 mr-3 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                                <div>
                                    <div class="font-bold">Iuran Belum Dibayar</div>
                                    <p class="text-sm mt-1 opacity-90">Silakan lakukan iuran tahunan pertama.</p>
                                    <a href="{{ route('mobile.iuran') }}" class="inline-block mt-3 px-4 py-1.5 bg-red-600 text-white text-sm font-semibold rounded-lg hover:bg-red-700 transition">Bayar Iuran</a>
                                </div>
                            </div>
                        @elseif($isExpired)
                            <div class="p-4 bg-orange-50 text-orange-700 rounded-xl border border-orange-100 flex items-start">
                                <svg class="w-6 h-6 mr-3 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                                <div>
                                    <div class="font-bold">Keanggotaan Expired</div>
                                    <p class="text-sm mt-1 opacity-90">Masa berlaku berakhir pada {{ Carbon\Carbon::parse($expiredAtRaw)->format('d M Y') }}</p>
                                    <a href="{{ route('mobile.iuran') }}" class="inline-block mt-3 px-4 py-1.5 bg-orange-600 text-white text-sm font-semibold rounded-lg hover:bg-orange-700 transition">Perbarui Iuran</a>
                                </div>
                            </div>
                        @else
                            <div class="p-4 bg-green-50 text-green-700 rounded-xl border border-green-100 flex items-start">
                                <svg class="w-6 h-6 mr-3 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                <div>
                                    <div class="font-bold">Keanggotaan Aktif</div>
                                    <p class="text-sm mt-1 opacity-90 text-green-600">Terima kasih telah membayar iuran. Status Anda aktif.</p>
                                </div>
                            </div>
                        @endif
                    </div>
                    @endif

    
                    <!-- Artikel Desktop -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                        <div class="flex justify-between items-center mb-5 border-b border-gray-100 pb-3">
                            <h3 class="text-lg font-bold text-gray-800">Artikel Terbaru</h3>
                            <a href="{{ route('mobile.articles') }}" class="text-sm text-green-600 font-semibold hover:text-green-800">Lihat Semua</a>
                        </div>
                        
                        <div class="space-y-4">
                            @forelse($latestArticles as $article)
                                <a href="{{ route('mobile.article.detail', $article['id']) }}" class="flex group cursor-pointer gap-4">
                                    <div class="w-20 h-20 rounded-xl overflow-hidden shrink-0">
                                        <img src="{{ $article['cover_image'] ?? '/images/assets/default-article.png' }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-bold text-gray-800 group-hover:text-green-600 line-clamp-2 leading-tight">
                                            {{ $article['title'] ?? 'Judul Tidak Tersedia' }}
                                        </h4>
                                        <span class="text-xs text-gray-400 mt-2 flex items-center">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            {{ \Carbon\Carbon::parse($article['created_at'] ?? now())->translatedFormat('d M Y') }}
                                        </span>
                                    </div>
                                </a>
                            @empty
                                <div class="py-6 text-center text-gray-500 text-sm">Belum ada artikel.</div>
                            @endforelse
                        </div>
                    </div>

                </div>
            </div>

        </x-desktop.layout>
    </x-slot:desktop>

</x-layouts.mobile>