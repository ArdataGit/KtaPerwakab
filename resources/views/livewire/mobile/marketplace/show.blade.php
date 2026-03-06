<?php

use App\Services\MarketplaceApiService;
use App\Services\TripayApiService;
use App\Services\UmkmTransactionApiService;
use function Livewire\Volt\state;
use function Livewire\Volt\mount;

state([
    'product'         => null,
    'paymentMethods'  => [],
    'quantity'        => 1,
    'selectedMethod'  => '',
    'customerEmail'   => '',
    'loading'         => false,
    'error'           => '',
    'user'            => session('user') ?? [],
]);

mount(function ($id) {
    $response = MarketplaceApiService::productDetail($id);

    if ($response->successful()) {
        $this->product = $response->json('data');
    } else {
        $this->product = null;
    }

    // Default email dari session user
    $this->customerEmail = session('user.email') ?? session('user')['email'] ?? '';

    // Ambil payment methods Tripay
    $tripay = TripayApiService::paymentMethods();
    if ($tripay->successful()) {
        $this->paymentMethods = $tripay->json('data') ?? [];
    }
});

$checkout = function () {
    $this->error = '';

    if (!$this->selectedMethod) {
        $this->error = 'Pilih metode pembayaran terlebih dahulu.';
        return;
    }

    if ($this->quantity < 1) {
        $this->error = 'Jumlah harus minimal 1.';
        return;
    }

    $this->loading = true;

    $response = UmkmTransactionApiService::checkout([
        'product_id'     => $this->product['id'],
        'quantity'       => (int) $this->quantity,
        'payment_method' => $this->selectedMethod,
        'customer_email' => $this->customerEmail ?: null,
    ]);

    $this->loading = false;

    if (!$response->successful()) {
        $this->error = $response->json('error') ?? $response->json('message') ?? 'Checkout gagal, coba lagi.';
        return;
    }

    $data = $response->json();

    // Simpan ke session untuk halaman checkout
    session([
        'umkm_checkout_invoice'      => $data['invoice'],
        'umkm_checkout_payment_url'  => $data['payment_url'],
        'umkm_checkout_product_name' => $this->product['product_name'],
        'umkm_checkout_quantity'     => $this->quantity,
        'umkm_checkout_subtotal'     => $this->product['price'] * $this->quantity,
        'umkm_checkout_method'       => $this->selectedMethod,
    ]);

    $this->redirect(route('mobile.marketplace.checkout'), navigate: true);
};
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

                {{-- FOOTER CARD --}}
                <div class="border-t p-4 flex items-center space-x-3" x-data="{ open: false }">
                    <div class="flex-1">
                        <p class="text-xs text-gray-500">Harga</p>
                        <p class="text-green-600 font-bold">
                            Rp {{ number_format((float) $product['price'], 0, ',', '.') }}
                        </p>
                    </div>

                    <button @click="open = true"
                        class="bg-green-600 text-white px-5 py-2 rounded-full font-semibold text-sm">
                        Beli Sekarang
                    </button>

                    {{-- MODAL CHECKOUT --}}
                    <div x-show="open" x-cloak
                        class="fixed inset-0 z-50 flex items-end justify-center"
                        @click.self="open = false">
                        <div class="absolute inset-0 bg-black/40"></div>
                        <div class="relative w-full max-w-md bg-white rounded-t-2xl p-6 space-y-4 z-10">
                            <h3 class="text-base font-bold text-gray-800">Order Produk</h3>

                            {{-- ERROR --}}
                            @if($error)
                                <div class="bg-red-50 border border-red-200 text-red-700 text-sm px-3 py-2 rounded-xl">
                                    {{ $error }}
                                </div>
                            @endif

                            {{-- QTY --}}
                            <div>
                                <label class="text-sm font-medium text-gray-700 mb-1 block">Jumlah</label>
                                <input type="number" wire:model="quantity" min="1"
                                    class="w-full border border-gray-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-300">
                            </div>

                            {{-- CUSTOMER INFO --}}
                            <div class="bg-gray-50 rounded-xl px-4 py-3 space-y-2.5 text-sm">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-500 shrink-0">Nama</span>
                                    <span class="font-medium text-gray-800 text-right">{{ $user['name'] ?? '-' }}</span>
                                </div>
                                <div class="space-y-1">
                                    <label class="text-gray-500">Email</label>
                                    <input type="email" wire:model="customerEmail"
                                        placeholder="Email untuk pembayaran"
                                        class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-300">
                                </div>
                            </div>

                            {{-- SUBTOTAL --}}
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Subtotal</span>
                                <span class="font-bold text-green-600" x-text="'Rp ' + ({{ (float)$product['price'] }} * $wire.quantity).toLocaleString('id-ID')"></span>
                            </div>

                            {{-- PAYMENT METHOD --}}
                            <div>
                                <label class="text-sm font-medium text-gray-700 mb-1 block">Metode Pembayaran</label>
                                <select wire:model="selectedMethod"
                                    class="w-full border border-gray-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-300 bg-white">
                                    <option value="">Pilih metode...</option>
                                    @foreach ($paymentMethods as $pm)
                                        <option value="{{ $pm['code'] }}">{{ $pm['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- BUTTONS --}}
                            <div class="flex gap-3 pt-2">
                                <button @click="open = false"
                                    class="flex-1 border border-gray-300 text-gray-700 py-3 rounded-xl font-semibold text-sm">
                                    Batal
                                </button>
                                <button wire:click="checkout"
                                    :disabled="$wire.loading"
                                    class="flex-1 bg-green-600 text-white py-3 rounded-xl font-semibold text-sm disabled:opacity-60">
                                    <span x-show="!$wire.loading">Lanjutkan</span>
                                    <span x-show="$wire.loading">Memproses...</span>
                                </button>
                            </div>
                        </div>
                    </div>
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

                            {{-- DESKTOP CHECKOUT MODAL --}}
                            <div x-data="{ open: false }">
                                <button @click="open = true"
                                    class="flex items-center justify-center gap-2 w-full bg-green-600 hover:bg-green-700 text-white py-4 rounded-xl font-semibold text-base transition shadow-md shadow-green-200">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    Beli Sekarang
                                </button>

                                <div x-show="open" x-cloak
                                    class="fixed inset-0 z-50 flex items-center justify-center"
                                    @click.self="open = false">
                                    <div class="absolute inset-0 bg-black/40"></div>
                                    <div class="relative bg-white rounded-2xl shadow-xl p-8 w-full max-w-md z-10 space-y-5">
                                        <h3 class="text-lg font-bold text-gray-900">Order Produk</h3>

                                        @if($error)
                                            <div class="bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-xl">
                                                {{ $error }}
                                            </div>
                                        @endif

                                        <div>
                                            <label class="text-sm font-medium text-gray-700 block mb-1">Jumlah</label>
                                            <input type="number" wire:model="quantity" min="1"
                                                class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-green-300">
                                        </div>

                                        <div class="flex justify-between items-center py-2 px-4 bg-green-50 rounded-xl">
                                            <span class="text-sm text-gray-600">Subtotal</span>
                                            <span class="font-bold text-green-700" x-text="'Rp ' + ({{ (float)$product['price'] }} * $wire.quantity).toLocaleString('id-ID')"></span>
                                        </div>

                                        {{-- CUSTOMER INFO --}}
                                        <div class="bg-gray-50 rounded-xl px-4 py-3 space-y-3 text-sm">
                                            <div class="flex justify-between items-center">
                                                <span class="text-gray-500">Nama</span>
                                                <span class="font-medium text-gray-800">{{ $user['name'] ?? '-' }}</span>
                                            </div>
                                            <div class="space-y-1">
                                                <label class="text-gray-500">Email</label>
                                                <input type="email" wire:model="customerEmail"
                                                    placeholder="Email untuk pembayaran"
                                                    class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-300">
                                            </div>
                                        </div>

                                        <div>
                                            <label class="text-sm font-medium text-gray-700 block mb-1">Metode Pembayaran</label>
                                            <select wire:model="selectedMethod"
                                                class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-green-300 bg-white">
                                                <option value="">Pilih metode...</option>
                                                @foreach ($paymentMethods as $pm)
                                                    <option value="{{ $pm['code'] }}">{{ $pm['name'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="flex gap-3 pt-2">
                                            <button @click="open = false"
                                                class="flex-1 border border-gray-200 text-gray-600 py-3 rounded-xl font-semibold hover:bg-gray-50 transition">
                                                Batal
                                            </button>
                                            <button wire:click="checkout" :disabled="$wire.loading"
                                                class="flex-1 bg-green-600 hover:bg-green-700 text-white py-3 rounded-xl font-semibold transition shadow-md shadow-green-200 disabled:opacity-60">
                                                <span x-show="!$wire.loading">Lanjutkan Pembayaran</span>
                                                <span x-show="$wire.loading">Memproses...</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </x-desktop.layout>
    </x-slot:desktop>

</x-layouts.mobile>