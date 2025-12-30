<?php

use App\Services\MarketplaceApiService;
use function Livewire\Volt\state;
use function Livewire\Volt\mount;

state([
    'product' => null,
]);

mount(function ($id) {
    $response = MarketplaceApiService::productDetail($id);

    

    if ($response->successful()) {
        $this->product = $response->json('data');
    } else {
        $this->product = null;
    }
});
?>

@php
    $cover = api_product_url(data_get($product, 'photos.0.file_path'));
@endphp
<x-layouts.mobile title="Detail Produk">

    @if (!$product)
        <div class="p-6 text-center text-sm text-gray-500">
            Memuat detail produk...
        </div>
    @else

        {{-- HEADER --}}
        <div class="bg-green-600 px-4 py-4 flex items-center space-x-3 rounded-b-2xl">
            <button onclick="window.history.back()">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M15 19l-7-7 7-7" />
                </svg>
            </button>
            <p class="text-white font-semibold text-base">Detail</p>
        </div>

        <div class="px-4 mt-4 space-y-6">

            {{-- IMAGE SLIDER --}}
            <div x-data="{
                        index: 0,
                        photos: {{ json_encode($product['photos'] ?? []) }}
                    }" class="relative">
                <img :src="photos.length
                            ? '{{ rtrim(env('STORAGE_BASE_URL'), '/') }}/storage/' + photos[index].file_path
                            : '{{ asset('images/no-image.png') }}'" class="w-full h-64 object-cover">

                {{-- DOTS --}}
                <div class="absolute bottom-3 left-1/2 -translate-x-1/2 flex space-x-1" x-show="photos.length > 1">
                    <template x-for="(p, i) in photos" :key="i">
                        <span @click="index = i" class="w-2 h-2 rounded-full cursor-pointer"
                            :class="i === index ? 'bg-white' : 'bg-white/50'">
                        </span>
                    </template>
                </div>
            </div>
            {{-- CARD UTAMA --}}
            <div class="bg-white rounded-2xl shadow overflow-hidden">


                {{-- CONTENT --}}
                <div class="p-4 space-y-3">

                    {{-- TITLE --}}
                    <p class="text-base font-semibold text-gray-800 leading-snug">
                        {{ $product['product_name'] }}
                    </p>

                    {{-- PRICE --}}
                    <p class="text-green-600 font-bold text-lg">
                        Rp {{ number_format((float) $product['price'], 0, ',', '.') }}
                    </p>

                    {{-- RATING --}}
                    <div class="flex items-center text-xs space-x-2">
                        <span class="text-yellow-500">★★★★★</span>
                        <span class="text-gray-600">5.0</span>
                        <span class="text-gray-400">(122 Reviews)</span>
                    </div>

                    {{-- UMKM --}}
                    <p class="text-xs text-gray-500">
                        Oleh <strong>{{ data_get($product, 'umkm.user.name', '-') }}</strong>
                    </p>

                    <hr>

                    {{-- DESCRIPTION --}}
                    <div class="space-y-1">
                        <p class="font-semibold text-sm text-gray-800">Detail Produk</p>
                        <p class="text-sm text-gray-600 leading-relaxed">
                            {{ $product['description'] ?: 'Tidak ada deskripsi produk.' }}
                        </p>
                    </div>

                    {{-- YOUTUBE --}}
                    @if (!empty($product['youtube_link']))
                        <a href="{{ $product['youtube_link'] }}" target="_blank"
                            class="text-sm text-green-600 font-semibold inline-block">
                            ▶ Lihat Video Produk
                        </a>
                    @endif
                </div>

                {{-- FOOTER CARD (PESAN SEKARANG) --}}
                @php
                    $wa = preg_replace('/[^0-9]/', '', data_get($product, 'umkm.contact_wa'));
                @endphp

                <div class="border-t p-4 flex items-center space-x-3">
                    <div class="flex-1">
                        <p class="text-xs text-gray-500">Harga</p>
                        <p class="text-green-600 font-bold">
                            Rp {{ number_format((float) $product['price'], 0, ',', '.') }}
                        </p>
                    </div>

                    <a href="https://wa.me/{{ $wa }}" target="_blank"
                        class="bg-green-600 text-white px-4 py-2 rounded-full font-semibold text-sm flex items-center space-x-2">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M20.52 3.48A11.92 11.92 0 0012.05 0C5.43 0 0 5.43 0 12.05c0 2.12.55 4.19 1.6 6.02L0 24l6.12-1.58a11.98 11.98 0 005.93 1.52h.01c6.62 0 12.05-5.43 12.05-12.05 0-3.22-1.25-6.24-3.54-8.41z" />
                        </svg>
                        <span>Pesan Sekarang</span>
                    </a>
                </div>

            </div>

        </div>

        <div class="h-10"></div>

    @endif
</x-layouts.mobile>