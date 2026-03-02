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
                    <p class="text-base font-semibold text-gray-800 mb-0 leading-snug">
                        {{ $product['product_name'] }}
                    </p>
                  	<small class="text-gray-700">{{ $product['category'] }}</small>

                    {{-- PRICE --}}
                    <p class="text-green-600 font-bold text-lg">
                        Rp {{ number_format((float) $product['price'], 0, ',', '.') }}
                    </p>

                    {{-- RATING --}}
                    <!-- <div class="flex items-center text-xs space-x-2">
                        <span class="text-yellow-500">★★★★★</span>
                        <span class="text-gray-600">5.0</span>
                        <span class="text-gray-400">(122 Reviews)</span>
                    </div> -->

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

    {{-- ==================== DESKTOP VIEW ==================== --}}
    <x-slot:desktop>
        <x-desktop.layout title="Detail Produk">
            <div class="max-w-5xl mx-auto">
                @if (!$product)
                    <div class="flex flex-col items-center justify-center py-20 text-gray-400">
                        <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-green-600 mb-4"></div>
                        <p class="text-sm">Memuat detail produk...</p>
                    </div>
                @else
                    <div class="flex items-center gap-2 text-sm text-gray-400 mb-6">
                        <a href="{{ route('mobile.marketplace.index') }}" class="hover:text-green-600 transition">&larr; Kembali ke Marketplace</a>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        {{-- IMAGE --}}
                        <div x-data="{ index: 0, photos: {{ json_encode($product['photos'] ?? []) }} }" class="space-y-4">
                            <div class="rounded-2xl overflow-hidden shadow-lg">
                                <img :src="photos.length
                                    ? '{{ rtrim(env('STORAGE_BASE_URL'), '/') }}/storage/' + photos[index].file_path
                                    : '{{ asset('images/no-image.png') }}'"
                                    class="w-full h-96 object-cover">
                            </div>

                            {{-- THUMBNAILS --}}
                            <div class="flex gap-2 overflow-x-auto" x-show="photos.length > 1">
                                <template x-for="(p, i) in photos" :key="i">
                                    <img @click="index = i"
                                        :src="'{{ rtrim(env('STORAGE_BASE_URL'), '/') }}/storage/' + p.file_path"
                                        :class="i === index ? 'ring-2 ring-green-500' : 'opacity-60 hover:opacity-100'"
                                        class="w-16 h-16 rounded-lg object-cover cursor-pointer transition">
                                </template>
                            </div>
                        </div>

                        {{-- PRODUCT INFO --}}
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 space-y-5 h-fit">
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900">{{ $product['product_name'] }}</h1>
                                <p class="text-sm text-gray-500 mt-1">{{ $product['category'] }}</p>
                            </div>

                            <p class="text-green-600 font-bold text-2xl">Rp {{ number_format((float) $product['price'], 0, ',', '.') }}</p>

                            <!-- <div class="flex items-center text-sm space-x-2">
                                <span class="text-yellow-500">★★★★★</span>
                                <span class="text-gray-600">5.0</span>
                                <span class="text-gray-400">(122 Reviews)</span>
                            </div> -->

                            <p class="text-sm text-gray-500">Oleh <strong>{{ data_get($product, 'umkm.user.name', '-') }}</strong></p>

                            <hr>

                            <div>
                                <h3 class="font-semibold text-gray-800 mb-2">Detail Produk</h3>
                                <p class="text-sm text-gray-600 leading-relaxed">{{ $product['description'] ?: 'Tidak ada deskripsi produk.' }}</p>
                            </div>

                            @if (!empty($product['youtube_link']))
                                <a href="{{ $product['youtube_link'] }}" target="_blank"
                                    class="inline-flex items-center gap-2 text-green-600 font-semibold hover:text-green-700 transition">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                    Lihat Video Produk
                                </a>
                            @endif

                            @php
                                $wa = preg_replace('/[^0-9]/', '', data_get($product, 'umkm.contact_wa'));
                            @endphp

                            <a href="https://wa.me/{{ $wa }}" target="_blank"
                                class="flex items-center justify-center gap-2 w-full bg-green-600 hover:bg-green-700 text-white py-4 rounded-xl font-semibold text-base transition shadow-md shadow-green-200">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M20.52 3.48A11.92 11.92 0 0012.05 0C5.43 0 0 5.43 0 12.05c0 2.12.55 4.19 1.6 6.02L0 24l6.12-1.58a11.98 11.98 0 005.93 1.52h.01c6.62 0 12.05-5.43 12.05-12.05 0-3.22-1.25-6.24-3.54-8.41z"/></svg>
                                Pesan Sekarang
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </x-desktop.layout>
    </x-slot:desktop>

</x-layouts.mobile>