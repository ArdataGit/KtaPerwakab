<?php

use App\Services\MarketplaceApiService;
use function Livewire\Volt\{state, mount, updated};

state([
    'products' => [],
    'search' => '',
    'category' => '',
    'user' => session('user') ?? [],
]);

mount(function () {
    $response = MarketplaceApiService::products([
        'per_page' => 10,
        'search' => $this->search ?: null,
        'category' => $this->category ?: null,
    ]);

    if ($response->successful()) {
        $this->products = $response->json('data.data') ?? [];
    } else {
        $this->products = [];
    }
});

updated([
    'search' => function () {
        $response = MarketplaceApiService::products([
            'per_page' => 10,
            'search' => $this->search ?: null,
            'category' => $this->category ?: null,
        ]);

        if ($response->successful()) {
            $this->products = $response->json('data.data') ?? [];
        } else {
            $this->products = [];
        }
    },
    'category' => function () {
        $response = MarketplaceApiService::products([
            'per_page' => 10,
            'search' => $this->search ?: null,
            'category' => $this->category ?: null,
        ]);

        if ($response->successful()) {
            $this->products = $response->json('data.data') ?? [];
        } else {
            $this->products = [];
        }
    },
]);

?>

@php
    $image = api_product_url($product['photos'][0]['file_path'] ?? null);
    $role = $user['role'] ?? 'publik';
    $categories = [
        'Makanan',
        'Minuman',
        'Kerajinan',
        'Fashion',
        'Jasa',
        'Pertanian',
        'Perikanan',
        'Lainnya',
    ];
@endphp

<x-layouts.mobile title="Marketplace">
    <!-- HEADER -->
    <div class="bg-green-600 px-4 py-4 flex items-center justify-between space-x-3 rounded-b-2xl">
        <div class="flex items-center gap-3">
            <button onclick="window.history.back()">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M15 19l-7-7 7-7" />
                </svg>
            </button>
            <p class="text-white font-semibold text-base">
                Marketplace
            </p>
        </div>

        @if($role === 'anggota')
            <a href="{{ route('mobile.my-products.create') }}" class="bg-white text-green-600 rounded-full p-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M12 5v14M5 12h14" stroke-linecap="round"/>
                </svg>
            </a>
        @endif
    </div>

    <div class="px-4 mt-4 space-y-4">
        <!-- BANNER -->
        <div class="rounded-xl overflow-hidden shadow">
            <img src="/images/assets/banner.png" class="w-full h-36 object-cover">
        </div>

        <div class="flex gap-2 w-full overflow-visible">
            <!-- SEARCH -->
            <div class="flex-1 min-w-0">
                <input
                    type="text"
                    wire:model.live="search"
                    placeholder="Cari produk UMKM"
                    class="w-full h-11 px-4 rounded-full border text-sm
                           focus:outline-none focus:ring focus:ring-green-200"
                >
            </div>

            <!-- CATEGORY -->
            <div class="shrink-0">
                <select
                    wire:model.live="category"
                    class="h-11 px-4 rounded-full border text-sm bg-white
                           focus:outline-none focus:ring focus:ring-green-200">
                    <option value="">Semua Kategori</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat }}">{{ $cat }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- PRODUCT GRID -->
        <div class="grid grid-cols-2 gap-4">
            @forelse ($products as $product)
                @php
                    $image = api_product_url(
                        data_get($product, 'photos.0.file_path')
                    );
                @endphp

                <a href="{{ route('mobile.marketplace.show', $product['id']) }}"
                    class="bg-white rounded-xl shadow-sm overflow-hidden active:bg-gray-100">
                    <img src="{{ $image }}" class="w-full h-32 object-cover">

                    <div class="p-2 space-y-1">
                        <p class="text-sm font-semibold text-gray-800 leading-tight line-clamp-2">
                            {{ $product['product_name'] }}
                        </p>
                        <p class="text-xs text-gray-500">
                            {{ $product['umkm']['user']['name'] ?? '-' }}
                        </p>
                        <p class="text-green-600 font-bold text-sm">
                            Rp {{ number_format((float) $product['price'], 0, ',', '.') }}
                        </p>
                    </div>
                </a>
            @empty
                <div class="col-span-2 text-center text-sm text-gray-500 py-10">
                    Produk belum tersedia
                </div>
            @endforelse
        </div>
    </div>

    <div class="h-24"></div>

    <!-- BOTTOM NAV -->
    <x-mobile.navbar active="marketplace" />
</x-layouts.mobile>