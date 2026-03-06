<?php

use App\Services\UmkmTransactionApiService;
use function Livewire\Volt\state;
use function Livewire\Volt\mount;

state([
    'sales'       => [],
    'totalRevenue'=> 0,
    'totalItems'  => 0,
    'noUmkm'      => false,
]);

mount(function () {
    $response = UmkmTransactionApiService::umkmSales();

    if ($response->status() === 403) {
        $this->noUmkm = true;
        return;
    }

    if ($response->successful()) {
        $items = $response->json('data') ?? [];
        $this->sales        = $items;
        $this->totalRevenue = collect($items)->sum('subtotal');
        $this->totalItems   = collect($items)->sum('quantity');
    }
});
?>

<x-layouts.mobile title="Penjualan UMKM Saya">

    {{-- HEADER --}}
    <div class="w-full bg-green-600 px-4 py-4 flex items-center space-x-3 rounded-b-2xl">
        <button onclick="window.history.back()">
            <img src="/images/assets/icon/back.svg" class="w-5 h-5" alt="Back">
        </button>
        <p class="text-white font-semibold text-base">Penjualan UMKM Saya</p>
    </div>

    @if ($noUmkm)
        <div class="px-4 mt-8 text-center text-sm text-gray-500">
            <svg class="w-12 h-12 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 2.189a3 3 0 01-.621 4.72m-13.5 8.65h3.75a.75.75 0 00.75-.75V13.5a.75.75 0 00-.75-.75H6.75a.75.75 0 00-.75.75v3.75c0 .415.336.75.75.75z"/>
            </svg>
            <p class="font-medium">Anda belum memiliki UMKM.</p>
            <p class="text-xs text-gray-400 mt-1">Daftarkan UMKM Anda untuk melihat statistik penjualan.</p>
        </div>
    @else

        <div class="px-4 mt-4 space-y-4">

            {{-- SUMMARY CARDS --}}
            <div class="grid grid-cols-2 gap-3">
                <div class="bg-green-600 rounded-2xl p-4 text-white">
                    <p class="text-xs opacity-80">Total Pendapatan</p>
                    <p class="text-lg font-bold mt-1">Rp {{ number_format((float) $totalRevenue, 0, ',', '.') }}</p>
                </div>
                <div class="bg-white rounded-2xl shadow-sm p-4">
                    <p class="text-xs text-gray-500">Item Terjual</p>
                    <p class="text-lg font-bold text-gray-900 mt-1">{{ $totalItems }} pcs</p>
                </div>
            </div>

            {{-- SALES LIST --}}
            <p class="text-sm font-semibold text-gray-700">Riwayat Penjualan</p>

            @forelse ($sales as $item)
                @php
                    $photo  = data_get($item, 'product.photos.0.file_path');
                    $imgUrl = $photo
                        ? rtrim(env('STORAGE_BASE_URL'), '/') . '/storage/' . $photo
                        : asset('images/no-image.png');
                @endphp
                <div class="bg-white rounded-2xl shadow-sm p-4 flex gap-3 items-center">
                    <img src="{{ $imgUrl }}" class="w-14 h-14 rounded-xl object-cover shrink-0">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-800 leading-snug line-clamp-1">{{ $item['product_name'] }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">{{ $item['quantity'] }} pcs terjual</p>
                        <p class="text-xs text-gray-400">Invoice: {{ data_get($item, 'transaction.invoice_number', '-') }}</p>
                    </div>
                    <div class="text-right shrink-0">
                        <p class="text-sm font-bold text-green-600">Rp {{ number_format((float) $item['subtotal'], 0, ',', '.') }}</p>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl p-8 text-center text-sm text-gray-400">
                    Belum ada penjualan.
                </div>
            @endforelse

        </div>

        <div class="h-24"></div>
        <x-mobile.navbar active="marketplace" />
    @endif

    {{-- ==================== DESKTOP VIEW ==================== --}}
    <x-slot:desktop>
        <x-desktop.layout title="Penjualan UMKM Saya">
            <div class="max-w-5xl mx-auto">
                <div class="flex items-center gap-2 text-sm text-gray-400 mb-6">
                    <a href="javascript:history.back()" class="hover:text-green-600 transition">&larr; Kembali</a>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Penjualan UMKM Saya</h1>
                <p class="text-gray-500 mb-8">Statistik dan riwayat penjualan produk UMKM Anda.</p>

                @if ($noUmkm)
                    <div class="bg-white rounded-2xl border border-gray-100 p-12 text-center">
                        <p class="text-gray-500 font-medium">Anda belum memiliki UMKM terdaftar.</p>
                    </div>
                @else
                    {{-- SUMMARY --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl p-6 text-white shadow-lg shadow-green-200 md:col-span-2">
                            <p class="text-sm opacity-80">Total Pendapatan</p>
                            <p class="text-4xl font-extrabold mt-2">Rp {{ number_format((float) $totalRevenue, 0, ',', '.') }}</p>
                        </div>
                        <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
                            <p class="text-sm text-gray-500">Total Item Terjual</p>
                            <p class="text-4xl font-extrabold text-gray-900 mt-2">{{ $totalItems }}</p>
                            <p class="text-sm text-gray-400 mt-1">pcs</p>
                        </div>
                    </div>

                    {{-- TABLE --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <h3 class="font-semibold text-gray-800">Riwayat Penjualan</h3>
                        </div>

                        @forelse ($sales as $item)
                            @php
                                $photo  = data_get($item, 'product.photos.0.file_path');
                                $imgUrl = $photo
                                    ? rtrim(env('STORAGE_BASE_URL'), '/') . '/storage/' . $photo
                                    : asset('images/no-image.png');
                            @endphp
                            <div class="px-6 py-4 flex items-center gap-4 hover:bg-gray-50 transition border-b border-gray-50 last:border-0">
                                <img src="{{ $imgUrl }}" class="w-14 h-14 rounded-xl object-cover shrink-0">
                                <div class="flex-1 min-w-0">
                                    <p class="font-semibold text-gray-900">{{ $item['product_name'] }}</p>
                                    <p class="text-sm text-gray-500 mt-0.5">
                                        {{ $item['quantity'] }} pcs ·
                                        Rp {{ number_format((float) $item['price'], 0, ',', '.') }}/pcs
                                    </p>
                                    <p class="text-xs text-gray-400">Invoice: {{ data_get($item, 'transaction.invoice_number', '-') }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-green-600">Rp {{ number_format((float) $item['subtotal'], 0, ',', '.') }}</p>
                                </div>
                            </div>
                        @empty
                            <div class="px-6 py-12 text-center text-sm text-gray-400">
                                Belum ada penjualan.
                            </div>
                        @endforelse
                    </div>
                @endif
            </div>
        </x-desktop.layout>
    </x-slot:desktop>

</x-layouts.mobile>
