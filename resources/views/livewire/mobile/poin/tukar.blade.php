<?php

use App\Services\PoinApiService;
use App\Services\AuthApiService;
use function Livewire\Volt\{state, mount};

state([
    'user'  => null,
    'token' => null,
    'saldo' => 0,
    'items' => [],
    'isLoading' => false,
    'snackbar' => ['message' => '', 'type' => ''],
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

$redeem = function ($produkId) {
    if ($this->isLoading) return;
    $this->isLoading = true;

    $res = PoinApiService::redeem([
        'master_penukaran_poin_id' => $produkId,
    ]);

    if ($res->successful()) {
        $this->snackbar = ['message' => $res->json('message') ?? 'Request berhasil dikirim.', 'type' => 'success'];
        
        // Refresh session user to reflect point change
        $meRes = \App\Services\AuthApiService::me(session('token'));
        if ($meRes->successful()) {
            session(['user' => $meRes->json('data')]);
        }
        
        return redirect()->route('mobile.poin.index');
    } else {
        $this->snackbar = ['message' => $res->json('message') ?? 'Gagal menukar poin.', 'type' => 'error'];
    }

    $this->isLoading = false;
};

mount(fn () => $this->load());
?>


<x-layouts.mobile title="Tukar Poin">

    {{-- SNACKBAR --}}
    @if($snackbar['message'])
        <div class="fixed top-0 left-1/2 -translate-x-1/2 w-[390px]
                {{ $snackbar['type'] === 'error' ? 'bg-red-500' : 'bg-green-600' }}
                text-white px-4 py-3 text-sm font-medium shadow-lg rounded-b-lg z-[9999]"
                x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">
            {{ $snackbar['message'] }}
        </div>
    @endif

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

                                        @if($saldo >= $item['jumlah_poin'])
                                            <button wire:click="redeem({{ $item['id'] }})" wire:loading.attr="disabled"
                                                class="text-xs px-3 py-1 rounded-full bg-green-600 hover:bg-green-700 text-white flex items-center gap-1 disabled:opacity-50">
                                                <span wire:loading.remove wire:target="redeem({{ $item['id'] }})">Tukar</span>
                                                <span wire:loading wire:target="redeem({{ $item['id'] }})" class="flex items-center gap-1">
                                                    <svg class="animate-spin h-3 w-3 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                                    Proses
                                                </span>
                                            </button>
                                        @else
                                            <button disabled class="text-xs px-3 py-1 rounded-full bg-gray-300 text-gray-500 cursor-not-allowed">
                                                Poin Kurang
                                            </button>
                                        @endif
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
                                @if($saldo >= $item['jumlah_poin'])
                                    <button wire:click="redeem({{ $item['id'] }})" wire:loading.attr="disabled"
                                        class="text-xs px-3 py-1.5 rounded-full bg-green-600 hover:bg-green-700 text-white flex items-center gap-1 transition disabled:opacity-50">
                                        <span wire:loading.remove wire:target="redeem({{ $item['id'] }})">Tukar</span>
                                        <span wire:loading wire:target="redeem({{ $item['id'] }})" class="flex items-center gap-1">
                                            <svg class="animate-spin h-3 w-3 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                            Proses
                                        </span>
                                    </button>
                                @else
                                    <button disabled class="text-xs px-3 py-1.5 rounded-full bg-gray-300 text-gray-500 cursor-not-allowed">
                                        Poin Kurang
                                    </button>
                                @endif
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