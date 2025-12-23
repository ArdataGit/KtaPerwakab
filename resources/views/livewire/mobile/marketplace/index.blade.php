<?php

use App\Services\MarketplaceApiService;
use function Livewire\Volt\state;
use function Livewire\Volt\mount;

state([
    'products' => [],
]);

mount(function () {
    $response = MarketplaceApiService::products();
    // dd([
    //     'status' => $response->status(),
    //     'successful' => $response->successful(),
    //     'headers' => $response->headers(),
    //     'raw_body' => $response->body(),
    //     'json' => $response->json(),
    //     'data_data' => $response->json('data.data'),
    // ]);
    if ($response->successful()) {
        $this->products = $response->json('data.data') ?? [];
    } else {
        $this->products = [];
    }
});
?>

@php
    $image = api_product_url($product['photos'][0]['file_path'] ?? null);
@endphp

<x-layouts.mobile title="Marketplace">

    <!-- HEADER -->
    <div class="bg-green-600 px-4 py-4 flex items-center space-x-3 rounded-b-2xl">
        <button onclick="window.history.back()">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M15 19l-7-7 7-7" />
            </svg>
        </button>
        <p class="text-white font-semibold text-base">Marketplace</p>
    </div>

    <div class="px-4 mt-4 space-y-4">

        <!-- BANNER -->
        <div class="rounded-xl overflow-hidden shadow">
            <img src="/images/assets/banner.png" class="w-full h-36 object-cover">
        </div>

        <!-- SEARCH (UI ONLY dulu) -->
        <div class="flex items-center space-x-2">
            <input type="text" placeholder="Cari produk UMKM"
                class="flex-1 px-4 py-2 rounded-full border text-sm focus:ring focus:ring-green-200">

            <button class="bg-green-600 text-white px-4 py-2 rounded-full text-sm font-semibold">
                Tampilkan
            </button>
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
                            {{ $product['umkm']['umkm_name'] ?? '-' }}
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