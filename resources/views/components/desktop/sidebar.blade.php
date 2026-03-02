@props(['active' => 'home'])

<div class="hidden md:flex md:w-72 md:flex-col border-r border-gray-200 bg-white">
    <!-- Logo Sidebar -->
    <div class="flex flex-col items-center justify-center pt-8 pb-4 border-b border-gray-100">
        <img src="/images/assets/logo.png" onerror="this.src='/images/assets/iuran.png'" class="h-16 w-auto mb-2" alt="Logo Perwakab">
        <h1 class="text-lg font-bold text-green-700 tracking-wide mt-2">KTA PERWAKAB</h1>
        <p class="text-xs text-gray-500 font-medium">Platform Layanan Anggota</p>
    </div>

    <!-- Navigation Menu -->
    <div class="flex-1 flex flex-col overflow-y-auto mt-4 px-4 space-y-1 scrollbar-hide">
        
        <x-desktop.nav-link route="mobile.home" active="home" icon="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
            Beranda
        </x-desktop.nav-link>

        <x-desktop.nav-link route="mobile.kta" active="kta" icon="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2">
            KTA Digital
        </x-desktop.nav-link>

        <div class="mt-6 mb-2">
            <span class="text-xs font-semibold text-gray-400 tracking-wider uppercase px-3">Informasi</span>
        </div>

        <x-desktop.nav-link route="mobile.articles" active="articles" icon="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z">
            Artikel Terbaru
        </x-desktop.nav-link>

        <x-desktop.nav-link route="mobile.info-duka.index" active="info-duka" icon="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
            Info Duka
        </x-desktop.nav-link>

        <x-desktop.nav-link route="mobile.struktur-organisasi" active="struktur" icon="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
            Struktur Organisasi
        </x-desktop.nav-link>

        <x-desktop.nav-link route="mobile.history" active="history" icon="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
            Tentang Kami
        </x-desktop.nav-link>

        <div class="mt-8 mb-2">
            <span class="text-xs font-semibold text-gray-400 tracking-wider uppercase px-3">Ekosistem</span>
        </div>

        <x-desktop.nav-link route="mobile.karya.index" active="karya" icon="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
            Karya Anggota
        </x-desktop.nav-link>

        <x-desktop.nav-link route="mobile.marketplace.index" active="marketplace" icon="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
            UMKM
        </x-desktop.nav-link>

        <x-desktop.nav-link route="mobile.bisnis.explore" active="bisnis" icon="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
            Jejaring Bisnis
        </x-desktop.nav-link>

        <x-desktop.nav-link route="mobile.donation.index" active="donation" icon="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z">
            Donasi Peduli
        </x-desktop.nav-link>

        <x-desktop.nav-link route="mobile.poin.index" active="poin" icon="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
            Poin Saya
        </x-desktop.nav-link>
    </div>

    <!-- User Profile & Logout -->
    <div class="border-t border-gray-100 p-4 shrink-0 bg-gray-50/50">
        <x-desktop.nav-link route="mobile.profile" active="profile" icon="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" :logout="true">
            Pengaturan Profil
        </x-desktop.nav-link>

        <form method="POST" action="{{ route('mobile.logout') }}" class="mt-2">
            @csrf
            <button type="submit" class="group flex items-center w-full px-3 py-3 gap-3 text-sm font-medium text-red-600 rounded-xl hover:bg-red-50 hover:text-red-700 transition duration-200">
                <svg class="w-5 h-5 text-red-500 group-hover:text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                Keluar Aplikasi
            </button>
        </form>
    </div>
</div>
