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
</x-layouts.mobile>