<?php

use App\Services\PoinApiService;
use function Livewire\Volt\{state, mount};

state([
    'produkId' => null,
    'produk' => null,
    'saldo' => 0,
]);

$load = function ($id) {

    $this->produkId = $id;

    // user dari session (sesuai arsitektur kamu)
    $user = session('user');
    if (!$user || !isset($user['id']))
        return;
    // dd($user);
    $this->saldo = (int) ($user['point'] ?? 0);

    $res = PoinApiService::detail($id);

    if ($res->successful()) {
        $this->produk = $res->json('data');
    }
};

mount(fn($id) => $this->load($id));
?>

<x-layouts.mobile title="Detail Produk">

    {{-- HEADER --}}
    <div class="bg-green-600 px-4 py-4 flex items-center gap-3 rounded-b-2xl">
        <button onclick="history.back()">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M15 19l-7-7 7-7" />
            </svg>
        </button>
        <p class="text-white font-semibold text-base">Detail Tukar Poin</p>
    </div>

    <div class="px-4 mt-4 space-y-5">

        @if ($produk)

            {{-- GAMBAR PRODUK --}}
            <div class="rounded-2xl overflow-hidden bg-gray-100">
                <img src="{{ api_product_url($produk['image'] ?? null) }}"
                    onerror="this.src='/images/assets/placeholder.png'" class="w-full h-56 object-cover">
            </div>

            {{-- INFO PRODUK --}}
            <div class="space-y-2">
                <h2 class="text-lg font-bold text-gray-800">
                    {{ $produk['produk'] }}
                </h2>

                <div class="flex items-center gap-2">
                    <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm font-semibold">
                        {{ number_format($produk['jumlah_poin']) }} poin
                    </span>

                    @if ($saldo < $produk['jumlah_poin'])
                        <span class="text-xs text-red-600 font-medium">
                            Poin tidak mencukupi
                        </span>
                    @endif
                </div>
            </div>

            {{-- DESKRIPSI --}}
            @if (!empty($produk['keterangan']))
                <div class="bg-gray-50 rounded-xl p-4 text-sm text-gray-700 leading-relaxed">
                    {{ $produk['keterangan'] }}
                </div>
            @endif

            {{-- INFO SALDO --}}
            <div class="flex justify-between items-center bg-white border rounded-xl p-4">
                <div>
                    <p class="text-xs text-gray-500">Saldo Poin Anda</p>
                    <p class="font-bold text-gray-800 text-lg">
                        {{ number_format($saldo) }} poin
                    </p>
                </div>
                <div class="text-3xl">🪙</div>
            </div>

            {{-- CTA --}}
            <div class="pt-2">
                @if ($saldo >= $produk['jumlah_poin'])
                        <a href="https://wa.me/6281222222222?text=
                        Halo Admin 👋%0A%0A
                        Saya ingin menukar poin dengan detail berikut:%0A%0A
                        Nama User : {{ session('user')['name'] ?? '-' }}%0A
                        ID User   : {{ session('user')['id'] ?? '-' }}%0A%0A
                        Produk    : {{ $produk['produk'] }}%0A
                        Poin      : {{ $produk['jumlah_poin'] }} poin%0A%0A
                        Mohon diproses. Terima kasih 🙏
                    " target="_blank" class="block w-full text-center bg-green-600 text-white py-3 rounded-xl font-semibold">
                            Tukar Poin Sekarang
                        </a>
                @else
                    <button disabled
                        class="block w-full bg-gray-300 text-gray-600 py-3 rounded-xl font-semibold cursor-not-allowed">
                        Poin Tidak Mencukupi
                    </button>
                @endif
            </div>

        @else
            <div class="text-center text-sm text-gray-500 py-10">
                Produk tidak ditemukan
            </div>
        @endif

    </div>

    <div class="h-24"></div>
    <x-mobile.navbar active="poin" />

    {{-- ==================== DESKTOP VIEW ==================== --}}
    <x-slot:desktop>
        <x-desktop.layout title="Detail Tukar Poin">
            <div class="max-w-xl mx-auto">
                <div class="flex items-center gap-2 text-sm text-gray-400 mb-6">
                    <a href="{{ route('mobile.poin.tukar') }}" class="hover:text-green-600 transition">&larr; Kembali ke Tukar Poin</a>
                </div>

                @if ($produk)
                    <div class="rounded-2xl overflow-hidden shadow-lg mb-6">
                        <img src="{{ api_product_url($produk['image'] ?? null) }}" onerror="this.src='/images/assets/placeholder.png'" class="w-full h-64 object-cover">
                    </div>
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4 mb-6">
                        <h1 class="text-2xl font-bold text-gray-900">{{ $produk['produk'] }}</h1>
                        <div class="flex items-center gap-2">
                            <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm font-semibold">{{ number_format($produk['jumlah_poin']) }} poin</span>
                            @if ($saldo < $produk['jumlah_poin'])<span class="text-xs text-red-600 font-medium">Poin tidak mencukupi</span>@endif
                        </div>
                        @if (!empty($produk['keterangan']))
                            <div class="bg-gray-50 rounded-xl p-4 text-sm text-gray-700">{{ $produk['keterangan'] }}</div>
                        @endif
                        <div class="flex justify-between items-center bg-gray-50 border rounded-xl p-4">
                            <div><p class="text-xs text-gray-500">Saldo Poin Anda</p><p class="font-bold text-gray-800 text-lg">{{ number_format($saldo) }} poin</p></div>
                            <div class="text-3xl">🪙</div>
                        </div>
                    </div>
                    @if ($saldo >= $produk['jumlah_poin'])
                        <a href="https://wa.me/6281222222222?text=Halo Admin 👋%0A%0ASaya ingin menukar poin:%0A%0ANama User : {{ session('user')['name'] ?? '-' }}%0AID User   : {{ session('user')['id'] ?? '-' }}%0A%0AProduk    : {{ $produk['produk'] }}%0APoin      : {{ $produk['jumlah_poin'] }} poin%0A%0AMohon diproses. Terima kasih 🙏" target="_blank"
                            class="block w-full bg-green-600 hover:bg-green-700 text-white py-4 rounded-xl font-semibold text-center transition shadow-md shadow-green-200">Tukar Poin Sekarang</a>
                    @else
                        <button disabled class="block w-full bg-gray-300 text-gray-600 py-4 rounded-xl font-semibold cursor-not-allowed">Poin Tidak Mencukupi</button>
                    @endif
                @else
                    <div class="text-center text-sm text-gray-500 py-10">Produk tidak ditemukan</div>
                @endif
            </div>
        </x-desktop.layout>
    </x-slot:desktop>

</x-layouts.mobile>