<?php

use App\Services\PoinApiService;
use function Livewire\Volt\{state, mount};

state([
    'user' => null,
    'saldo' => 0,
    'items' => [],
]);

$load = function () {

    $this->user = session('user');

    if (!$this->user) {
        return;
    }

    // saldo bisa dari session atau API lain
    $this->saldo = $this->user['point'] ?? 0;

    $res = PoinApiService::list([
        'per_page' => 20,
    ]);

    $this->items = $res->successful()
        ? $res->json('data.data') ?? $res->json('data') ?? []
        : [];
};

mount(fn() => $this->load());
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

</x-layouts.mobile>