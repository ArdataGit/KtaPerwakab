<?php
use function Livewire\Volt\state;

state([
    'user' => session('user') ?? [],
]);
?>

@php
    $avatar = api_profile_url($user['profile_photo'] ?? null);
    $role = $user['role'] ?? 'publik';
@endphp

<x-layouts.mobile title="Akun Saya">

    <div x-data="{ showLogoutModal: false }">

        {{-- HEADER --}}
        <div class="w-full bg-green-600 px-4 py-4 flex items-center space-x-3 rounded-b-2xl">
            <button onclick="window.history.back()">
                <img src="/images/assets/icon/back.svg" class="w-5 h-5">
            </button>
            <p class="text-white font-semibold text-base">Akun Saya</p>
        </div>

        <div class="px-4 mt-4 space-y-4">

            {{-- CARD PROFILE --}}
            <div class="rounded-2xl p-5 text-white" style="background: linear-gradient(135deg, #4CAF50, #66BB6A)">
                <div class="flex flex-col items-center text-center">
                    <div class="w-20 h-20 rounded-full bg-white flex items-center justify-center mb-3 overflow-hidden">
                        @if(!empty($user['profile_photo']))
                            <img src="{{ $avatar }}" class="w-full h-full object-cover">
                        @else
                            <img src="/images/assets/default-avatar.png" class="w-10 h-10">
                        @endif
                    </div>

                    <p class="font-semibold text-lg">
                        {{ $user['name'] ?? 'Pengguna' }}
                    </p>

                    <p class="text-sm opacity-90">
                        {{ $user['email'] ?? '-' }}
                    </p>
                </div>
            </div>

            {{-- MENU LIST --}}
            <div class="bg-gray-100 rounded-2xl divide-y">

                {{-- Edit Profile --}}
                <a href="{{ route('mobile.profile.edit') }}" class="flex items-center justify-between px-4 py-4">
                    <div class="flex items-center space-x-3">
                        <img src="/images/assets/icon/user.svg" class="w-5 h-5">
                        <span class="text-sm font-medium">Edit Profile</span>
                    </div>
                    <img src="/images/assets/icon/chevron-right.svg" class="w-4 h-4">
                </a>

                {{-- Data Keluarga --}}
                <a href="{{ route('mobile.family') }}" class="flex items-center justify-between px-4 py-4">
                    <div class="flex items-center space-x-3">
                        <img src="/images/assets/icon/user.svg" class="w-5 h-5">
                        <span class="text-sm font-medium">Data Keluarga</span>
                    </div>
                    <img src="/images/assets/icon/chevron-right.svg" class="w-4 h-4">
                </a>

                {{-- Riwayat Donasi --}}
                <a href="/my-donation" class="flex items-center justify-between px-4 py-4">
                    <div class="flex items-center space-x-3">
                        <img src="/images/assets/icon/riwayat-donasi.svg" class="w-5 h-5">
                        <span class="text-sm font-medium">Riwayat Donasi</span>
                    </div>
                    <img src="/images/assets/icon/chevron-right.svg" class="w-4 h-4">
                </a>

                {{-- Riwayat Iuran (ANGGOTA ONLY) --}}
                @if($role === 'anggota')
                    <a href="{{ route('mobile.iuran.saya') }}" class="flex items-center justify-between px-4 py-4">
                        <div class="flex items-center space-x-3">
                            <img src="/images/assets/icon/iuran.svg" class="w-5 h-5">
                            <span class="text-sm font-medium">Riwayat Iuran</span>
                        </div>
                        <img src="/images/assets/icon/chevron-right.svg" class="w-4 h-4">
                    </a>
                @endif

                {{-- Tukar Poin (ANGGOTA ONLY) --}}
                @if($role === 'anggota')
                    <a href="/poin-saya" class="flex items-center justify-between px-4 py-4">
                        <div class="flex items-center space-x-3">
                            <img src="/images/assets/icon/point.svg" class="w-5 h-5">
                            <span class="text-sm font-medium">Tukar Poin</span>
                        </div>
                        <img src="/images/assets/icon/chevron-right.svg" class="w-4 h-4">
                    </a>
                @endif

                @if($role === 'anggota')
                {{-- Produk Saya --}}
                <a href="{{ route('mobile.my-products.index') }}" class="flex items-center justify-between px-4 py-4">
                    <div class="flex items-center space-x-3">
                        <img src="/images/assets/icon/produk.svg" class="w-5 h-5">
                        <span class="text-sm font-medium">Produk Saya</span>
                    </div>
                    <img src="/images/assets/icon/chevron-right.svg" class="w-4 h-4">
                </a>
                @endif
              
                {{-- LOGOUT --}}
                <button type="button" @click="showLogoutModal = true"
                    class="w-full flex items-center justify-between px-4 py-4 text-left">
                    <div class="flex items-center space-x-3">
                        <img src="/images/assets/icon/logout.svg" class="w-5 h-5">
                        <span class="text-sm font-medium">Logout</span>
                    </div>
                    <img src="/images/assets/icon/chevron-right.svg" class="w-4 h-4">
                </button>

            </div>
        </div>

        {{-- MODAL LOGOUT --}}
        <div x-show="showLogoutModal" x-transition.opacity x-cloak
            class="fixed inset-0 z-100 flex items-center justify-center bg-black/50 px-4">
            <div @click.away="showLogoutModal = false" class="bg-white rounded-2xl w-full max-w-sm p-5 text-center">

                <div class="mx-auto mb-3 w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                    <img src="/images/assets/icon/Info.svg" class="w-5 h-5">
                </div>

                <p class="text-sm text-gray-700 mb-1 font-semibold">
                    Setelah logout anda harus login kembali dengan akun yang telah ada
                </p>

                <p class="text-xs text-gray-500 mb-4">
                    Anda yakin ingin melakukan logout?
                </p>

                <div class="space-y-2">
                    <form method="POST" action="{{ route('mobile.logout') }}">
                        @csrf
                        <button type="submit" class="w-full bg-red-500 text-white py-2.5 rounded-xl font-semibold">
                            Logout
                        </button>
                    </form>

                    <button type="button" @click="showLogoutModal = false"
                        class="w-full bg-gray-100 text-gray-700 py-2.5 rounded-xl font-medium">
                        Batal
                    </button>
                </div>

            </div>
        </div>

    </div>

    <x-mobile.navbar active="profile" />

    {{-- ==================== DESKTOP VIEW ==================== --}}
    <x-slot:desktop>
        <x-desktop.layout title="Profil Saya">
            <div x-data="{ showLogoutModal: false }" class="max-w-5xl mx-auto">
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900">Profil Saya</h1>
                    <p class="text-gray-500 mt-1">Kelola informasi akun dan pengaturan Anda.</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    {{-- LEFT: Profile Card --}}
                    <div class="space-y-6">
                        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl p-8 text-white text-center shadow-lg">
                            <div class="w-24 h-24 rounded-full bg-white mx-auto mb-4 overflow-hidden shadow-md border-4 border-white/30">
                                @if(!empty($user['profile_photo']))
                                    <img src="{{ $avatar }}" class="w-full h-full object-cover">
                                @else
                                    <img src="/images/assets/default-avatar.png" class="w-full h-full object-cover p-4">
                                @endif
                            </div>
                            <h2 class="text-xl font-bold">{{ $user['name'] ?? 'Pengguna' }}</h2>
                            <p class="text-white/80 text-sm mt-1">{{ $user['email'] ?? '-' }}</p>
                            <div class="mt-3">
                                <span class="inline-block bg-white/20 text-white text-xs font-semibold px-4 py-1.5 rounded-full capitalize">
                                    {{ $role }}
                                </span>
                            </div>
                        </div>

                        {{-- Quick Info --}}
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">
                            <h3 class="font-semibold text-gray-800 text-sm">Informasi Singkat</h3>
                            @if(!empty($user['phone']))
                                <div class="flex items-center gap-3 text-sm">
                                    <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                    <span class="text-gray-600">{{ $user['phone'] }}</span>
                                </div>
                            @endif
                            @if(!empty($user['city']))
                                <div class="flex items-center gap-3 text-sm">
                                    <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <span class="text-gray-600">{{ $user['city'] }}</span>
                                </div>
                            @endif
                            @if(!empty($user['occupation']))
                                <div class="flex items-center gap-3 text-sm">
                                    <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    <span class="text-gray-600">{{ $user['occupation'] }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- RIGHT: Menu --}}
                    <div class="lg:col-span-2 space-y-6">
                        {{-- Account Menu --}}
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-100">
                                <h3 class="font-semibold text-gray-800">Pengaturan Akun</h3>
                            </div>

                            <a href="{{ route('mobile.profile.edit') }}"
                                class="flex items-center justify-between px-6 py-4 hover:bg-gray-50 transition group">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center">
                                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-800 group-hover:text-green-700 transition">Edit Profil</p>
                                        <p class="text-xs text-gray-400">Ubah foto, nama, dan data profil</p>
                                    </div>
                                </div>
                                <svg class="w-5 h-5 text-gray-300 group-hover:text-green-500 transition" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg>
                            </a>

                            <a href="{{ route('mobile.family') }}"
                                class="flex items-center justify-between px-6 py-4 hover:bg-gray-50 transition group border-t border-gray-50">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-xl bg-orange-50 flex items-center justify-center">
                                        <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 20h5V10l-10-8L2 10v10h5m10 0v-4C17 14.895 15.657 14 14 14H10C8.343 14 7 14.895 7 16v4m10 0H7"/></svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-800 group-hover:text-green-700 transition">Data Keluarga</p>
                                        <p class="text-xs text-gray-400">Kelola info anggota keluarga / tertanggung</p>
                                    </div>
                                </div>
                                <svg class="w-5 h-5 text-gray-300 group-hover:text-green-500 transition" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg>
                            </a>

                            <a href="/my-donation"
                                class="flex items-center justify-between px-6 py-4 hover:bg-gray-50 transition group border-t border-gray-50">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center">
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-800 group-hover:text-green-700 transition">Riwayat Donasi</p>
                                        <p class="text-xs text-gray-400">Lihat riwayat donasi Anda</p>
                                    </div>
                                </div>
                                <svg class="w-5 h-5 text-gray-300 group-hover:text-green-500 transition" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg>
                            </a>

                            @if($role === 'anggota')
                                <a href="{{ route('mobile.iuran.saya') }}"
                                    class="flex items-center justify-between px-6 py-4 hover:bg-gray-50 transition group border-t border-gray-50">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center">
                                            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-800 group-hover:text-green-700 transition">Riwayat Iuran</p>
                                            <p class="text-xs text-gray-400">Lihat histori pembayaran iuran</p>
                                        </div>
                                    </div>
                                    <svg class="w-5 h-5 text-gray-300 group-hover:text-green-500 transition" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg>
                                </a>

                                <a href="/poin-saya"
                                    class="flex items-center justify-between px-6 py-4 hover:bg-gray-50 transition group border-t border-gray-50">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-xl bg-purple-50 flex items-center justify-center">
                                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-800 group-hover:text-green-700 transition">Tukar Poin</p>
                                            <p class="text-xs text-gray-400">Kelola dan tukarkan poin reward Anda</p>
                                        </div>
                                    </div>
                                    <svg class="w-5 h-5 text-gray-300 group-hover:text-green-500 transition" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg>
                                </a>

                                <a href="{{ route('mobile.my-products.index') }}"
                                    class="flex items-center justify-between px-6 py-4 hover:bg-gray-50 transition group border-t border-gray-50">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-xl bg-teal-50 flex items-center justify-center">
                                            <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-800 group-hover:text-green-700 transition">Produk Saya</p>
                                            <p class="text-xs text-gray-400">Kelola produk UMKM yang Anda jual</p>
                                        </div>
                                    </div>
                                    <svg class="w-5 h-5 text-gray-300 group-hover:text-green-500 transition" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg>
                                </a>
                            @endif
                        </div>

                        {{-- Logout --}}
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                            <button type="button" @click="showLogoutModal = true"
                                class="w-full flex items-center justify-between px-6 py-4 hover:bg-red-50 transition group">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center">
                                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                    </div>
                                    <div class="text-left">
                                        <p class="text-sm font-semibold text-red-600">Keluar Aplikasi</p>
                                        <p class="text-xs text-gray-400">Logout dari akun Anda</p>
                                    </div>
                                </div>
                                <svg class="w-5 h-5 text-gray-300 group-hover:text-red-400 transition" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- MODAL LOGOUT --}}
                <div x-show="showLogoutModal" x-transition.opacity x-cloak
                    class="fixed inset-0 z-[100] flex items-center justify-center bg-black/50 px-4">
                    <div @click.away="showLogoutModal = false" class="bg-white rounded-2xl w-full max-w-sm p-6 text-center shadow-2xl">
                        <div class="mx-auto mb-4 w-12 h-12 rounded-full bg-red-100 flex items-center justify-center">
                            <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        </div>
                        <p class="text-sm text-gray-700 mb-1 font-semibold">Setelah logout anda harus login kembali</p>
                        <p class="text-xs text-gray-500 mb-5">Anda yakin ingin keluar?</p>
                        <div class="space-y-2">
                            <form method="POST" action="{{ route('mobile.logout') }}">
                                @csrf
                                <button type="submit" class="w-full bg-red-500 hover:bg-red-600 text-white py-3 rounded-xl font-semibold transition">Logout</button>
                            </form>
                            <button type="button" @click="showLogoutModal = false" class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 py-3 rounded-xl font-medium transition">Batal</button>
                        </div>
                    </div>
                </div>
            </div>
        </x-desktop.layout>
    </x-slot:desktop>

</x-layouts.mobile>