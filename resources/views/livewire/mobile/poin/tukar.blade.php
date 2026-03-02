<?php

use App\Services\PoinApiService;
use App\Services\AuthApiService;
use function Livewire\Volt\{state, mount};

state([
    'user'  => null,
    'token' => null,
    'saldo' => 0,
    'items' => [],
]);

$load = function () {

    $this->token = session('token');
    if (!$this->token) {
        return;
    }

    /**
     * 🔄 FETCH USER TERBARU DARI API
     */
    $userResponse = AuthApiService::me($this->token);

    if ($userResponse->successful()) {
        $user = $userResponse->json('data');
        session(['user' => $user]);
        $this->user = $user;
        $this->saldo = $user['point'] ?? 0;
    } else {
        return;
    }

    /**
     * 📦 FETCH PRODUK POIN
     */
    $res = PoinApiService::list([
        'per_page' => 20,
    ]);

    $this->items = $res->successful()
        ? $res->json('data.data') ?? $res->json('data') ?? []
        : [];
};

mount(fn () => $this->load());
?>


<x-layouts.mobile title="Tukar Poin">

    {{-- HEADER --}}
    <div class="bg-green-600 px-4 py-4 flex items-center gap-3 rounded-b-2xl">
        <button onclick="history.back()">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M15 19l-7-7 7-7" />
            </svg>
        </button>
        <p class="text-white font-semibold text-base">TUKAR POIN</p>
    </div>

    <div class="px-4 mt-4 space-y-4">

        {{-- SALDO CARD --}}
        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-2xl p-4 text-white">
            <p class="text-xs opacity-80">Total Poin</p>

            <div class="flex items-center justify-between mt-1">
                <p class="text-3xl font-bold">
                    {{ number_format($saldo) }}
                    <span class="text-base font-medium">Poin</span>
                </p>

                <div class="text-yellow-300 text-4xl">🪙</div>
            </div>

            <p class="text-xs mt-2 bg-green-400/40 inline-block px-3 py-1 rounded-full">
                Tukar poin dengan berbagai reward menarik
            </p>
        </div>

        {{-- TITLE --}}
        <p class="text-sm font-semibold text-gray-800">Produk</p>

        {{-- LIST PRODUK --}}
        <div class="space-y-4">

            @forelse ($items as $item)
                        @php
                            $img = $item['image']
                                ? api_product_url($item['image'])
                                : '/images/assets/placeholder.png';
                        @endphp


                        <div class="bg-white rounded-xl shadow-sm p-3 flex gap-3">

                            {{-- IMAGE --}}
                            <img src="{{ $img }}" onerror="this.src='/images/assets/placeholder.png'"
                                class="w-16 h-16 object-cover rounded-lg">

                            {{-- INFO --}}
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-gray-800 leading-tight line-clamp-2">
                                    {{ $item['produk'] }}
                                </p>

                                <p class="text-xs text-gray-500 mt-1">
                                    {{ $item['keterangan'] ?? 'Reward penukaran poin' }}
                                </p>

                                <div class="flex items-center justify-between mt-2">
                                    <p class="text-xs font-semibold text-green-600 flex items-center gap-1">
                                        🪙 {{ number_format($item['jumlah_poin']) }} poin
                                    </p>

                                    <div class="flex gap-2">
                                        <a href="{{ route('mobile.poin.detail', $item['id']) }}"
                                            class="text-xs px-3 py-1 rounded-full bg-gray-100 text-gray-700">
                                            Detail
                                        </a>

                                        <a href="https://wa.me/6285712340504?text=
                    Halo Admin 👋%0A%0A
                    Saya ingin menukar poin dengan detail berikut:%0A%0A
                    Nama User : {{ $user['name'] }}%0A
                    ID User   : {{ $user['id'] }}%0A%0A
                    Produk    : {{ $item['produk'] }}%0A
                    Poin      : {{ $item['jumlah_poin'] }} poin%0A%0A
                    Mohon diproses. Terima kasih 🙏
                " target="_blank" class="text-xs px-3 py-1 rounded-full bg-green-600 text-white">
                                            Tukar
                                        </a>

                                    </div>
                                </div>
                            </div>

                        </div>
            @empty
                <p class="text-center text-sm text-gray-500 py-8">
                    Belum ada produk penukaran poin
                </p>
            @endforelse

        </div>

    </div>

    <div class="h-24"></div>
    <x-mobile.navbar active="poin" />

    {{-- ==================== DESKTOP VIEW ==================== --}}
    <x-slot:desktop>
        <x-desktop.layout title="Tukar Poin">
            <div class="max-w-5xl mx-auto">
                <div class="flex items-center gap-2 text-sm text-gray-400 mb-6">
                    <a href="{{ route('mobile.poin.index') }}" class="hover:text-green-600 transition">&larr; Kembali ke Poin</a>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 mb-8">Tukar Poin</h1>

                <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-2xl p-6 text-white flex items-center justify-between mb-8 shadow-lg">
                    <div>
                        <p class="text-xs opacity-70">Total Poin</p>
                        <p class="text-3xl font-bold">{{ number_format($saldo) }} <span class="text-base font-medium">Poin</span></p>
                        <p class="text-xs mt-2 bg-green-400/40 inline-block px-3 py-1 rounded-full">Tukar poin dengan berbagai reward menarik</p>
                    </div>
                    <div class="text-yellow-300 text-5xl">🪙</div>
                </div>

                <h3 class="font-semibold text-gray-800 mb-4">Produk Reward</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @forelse ($items as $item)
                        @php $img = $item['image'] ? api_product_url($item['image']) : '/images/assets/placeholder.png'; @endphp
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 hover:shadow-md transition">
                            <div class="flex gap-4">
                                <img src="{{ $img }}" onerror="this.src='/images/assets/placeholder.png'" class="w-20 h-20 object-cover rounded-lg shrink-0">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-800 line-clamp-2">{{ $item['produk'] }}</p>
                                    <p class="text-xs text-gray-500 mt-1">{{ $item['keterangan'] ?? 'Reward penukaran poin' }}</p>
                                    <p class="text-xs font-semibold text-green-600 mt-2">🪙 {{ number_format($item['jumlah_poin']) }} poin</p>
                                </div>
                            </div>
                            <div class="flex gap-2 mt-3 justify-end">
                                <a href="{{ route('mobile.poin.detail', $item['id']) }}" class="text-xs px-3 py-1.5 rounded-full bg-gray-100 text-gray-700 hover:bg-gray-200 transition">Detail</a>
                                <a href="https://wa.me/6285712340504?text=Halo Admin 👋%0A%0ASaya ingin menukar poin dengan detail berikut:%0A%0ANama User : {{ $user['name'] }}%0AID User   : {{ $user['id'] }}%0A%0AProduk    : {{ $item['produk'] }}%0APoin      : {{ $item['jumlah_poin'] }} poin%0A%0AMohon diproses. Terima kasih 🙏" target="_blank" class="text-xs px-3 py-1.5 rounded-full bg-green-600 text-white hover:bg-green-700 transition">Tukar</a>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full bg-white rounded-xl p-8 text-center text-sm text-gray-500 border border-gray-100">Belum ada produk penukaran poin</div>
                    @endforelse
                </div>
            </div>
        </x-desktop.layout>
    </x-slot:desktop>

</x-layouts.mobile>