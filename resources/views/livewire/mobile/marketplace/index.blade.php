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

    {{-- ==================== DESKTOP VIEW ==================== --}}
    <x-slot:desktop>
        <x-desktop.layout title="M-UMKM">
            <div class="max-w-7xl mx-auto">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Marketplace UMKM</h1>
                        <p class="text-gray-500 mt-1">Temukan produk-produk unggulan UMKM anggota Perwakab.</p>
                    </div>
                    @if($role === 'anggota')
                        <a href="{{ route('mobile.my-products.create') }}"
                           class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-xl font-semibold transition-all hover:-translate-y-0.5 shadow-md shadow-green-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14" stroke-linecap="round"/></svg>
                            Tambah Produk
                        </a>
                    @endif
                </div>

                {{-- SEARCH + FILTER --}}
                <div class="flex flex-col md:flex-row gap-4 mb-8">
                    <div class="relative flex-1">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z"/></svg>
                        </span>
                        <input type="text" wire:model.live="search" placeholder="Cari produk UMKM..."
                            class="w-full pl-12 pr-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-green-200 focus:border-green-500 transition">
                    </div>
                    <select wire:model.live="category"
                        class="w-full md:w-48 px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-green-200 focus:border-green-500 transition bg-white">
                        <option value="">Semua Kategori</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat }}">{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- PRODUCT GRID --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @forelse ($products as $product)
                        @php
                            $image = api_product_url(data_get($product, 'photos.0.file_path'));
                        @endphp
                        <a href="{{ route('mobile.marketplace.show', $product['id']) }}"
                            class="group bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
                            <div class="relative h-48 overflow-hidden">
                                <img src="{{ $image }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            </div>
                            <div class="p-4 space-y-2">
                                <h3 class="font-semibold text-gray-900 leading-snug line-clamp-2 group-hover:text-green-700 transition-colors">{{ $product['product_name'] }}</h3>
                                <p class="text-xs text-gray-500">{{ $product['umkm']['user']['name'] ?? '-' }}</p>
                                <p class="text-green-600 font-bold text-lg">Rp {{ number_format((float) $product['price'], 0, ',', '.') }}</p>
                            </div>
                        </a>
                    @empty
                        <div class="col-span-full flex flex-col items-center justify-center py-16 text-gray-400">
                            <svg class="w-16 h-16 mb-4 opacity-40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/></svg>
                            <p class="text-sm font-medium">Produk belum tersedia</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </x-desktop.layout>
    </x-slot:desktop>

</x-layouts.mobile>