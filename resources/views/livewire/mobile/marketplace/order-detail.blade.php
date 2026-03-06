<?php

use App\Services\UmkmTransactionApiService;
use function Livewire\Volt\state;
use function Livewire\Volt\mount;

state([
    'transaction' => null,
]);

mount(function ($id) {
    $response = UmkmTransactionApiService::transactionDetail($id);

    if ($response->successful()) {
        // Response langsung di root, tidak ada wrapper 'data'
        $this->transaction = $response->json();
    } else {
        abort(404);
    }
});
?>

<x-layouts.mobile title="Detail Pesanan">

    {{-- HEADER --}}
    <div class="w-full bg-green-600 px-4 py-4 flex items-center space-x-3 rounded-b-2xl">
        <button onclick="window.history.back()">
            <img src="/images/assets/icon/back.svg" class="w-5 h-5" alt="Back">
        </button>
        <p class="text-white font-semibold text-base">Detail Pesanan</p>
    </div>

    @if (!$transaction)
        <div class="p-6 text-center text-sm text-gray-500">Memuat...</div>
    @else
        @php
            $statusColor = match($transaction['status']) {
                'paid'    => 'bg-green-100 text-green-700',
                'pending' => 'bg-orange-100 text-orange-700',
                'failed'  => 'bg-red-100 text-red-700',
                default   => 'bg-gray-100 text-gray-600',
            };
            $statusLabel = match($transaction['status']) {
                'paid'    => '✅ Lunas',
                'pending' => '⏳ Menunggu Pembayaran',
                'failed'  => '❌ Gagal',
                default   => ucfirst($transaction['status']),
            };
        @endphp

        <div class="px-4 mt-4 space-y-4">

            {{-- STATUS + INVOICE --}}
            <div class="bg-white rounded-2xl shadow-sm p-4">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-sm font-semibold text-gray-800">Status Pesanan</p>
                    <span class="text-xs px-3 py-1 rounded-full font-medium {{ $statusColor }}">
                        {{ $statusLabel }}
                    </span>
                </div>
                <p class="text-xs text-gray-500">No. Invoice</p>
                <p class="font-semibold text-gray-900">{{ $transaction['invoice_number'] }}</p>
                <p class="text-xs text-gray-400 mt-1">Metode: {{ $transaction['payment_method'] ?? '-' }}</p>
            </div>

            {{-- ITEM LIST --}}
            <div class="bg-white rounded-2xl shadow-sm p-4 space-y-4">
                <p class="text-sm font-semibold text-gray-800">Produk Dipesan</p>

                @foreach ($transaction['items'] ?? [] as $item)
                    @php
                        $photo = data_get($item, 'product.photos.0.file_path');
                        $imgUrl = $photo
                            ? rtrim(env('STORAGE_BASE_URL'), '/') . '/storage/' . $photo
                            : asset('images/no-image.png');
                    @endphp
                    <div class="flex gap-3">
                        <img src="{{ $imgUrl }}" class="w-16 h-16 rounded-xl object-cover shrink-0">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800 leading-snug">{{ $item['product_name'] }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">Toko: {{ data_get($item, 'product.umkm.user.name', '-') }}</p>
                            <div class="flex items-center justify-between mt-1">
                                <p class="text-xs text-gray-500">{{ $item['quantity'] }} × Rp {{ number_format((float) $item['price'], 0, ',', '.') }}</p>
                                <p class="text-sm font-bold text-green-600">Rp {{ number_format((float) $item['subtotal'], 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- TOTAL --}}
            <div class="bg-white rounded-2xl shadow-sm p-4">
                <div class="flex justify-between text-base font-bold text-gray-900">
                    <span>Total Pembayaran</span>
                    <span class="text-green-600">Rp {{ number_format((float) $transaction['total_amount'], 0, ',', '.') }}</span>
                </div>
            </div>

            {{-- CTA BAYAR --}}
            @if ($transaction['status'] === 'pending' && !empty($transaction['payment_url']))
                <button onclick="window.location.href='{{ $transaction['payment_url'] }}'"
                    class="w-full bg-green-600 text-white font-semibold py-4 rounded-xl shadow-md shadow-green-200">
                    Selesaikan Pembayaran
                </button>
            @endif

        </div>

        <div class="h-24"></div>
        <x-mobile.navbar active="marketplace" />
    @endif

    {{-- ==================== DESKTOP VIEW ==================== --}}
    <x-slot:desktop>
        <x-desktop.layout title="Detail Pesanan">
            <div class="max-w-3xl mx-auto">
                <div class="flex items-center gap-2 text-sm text-gray-400 mb-6">
                    <a href="{{ route('mobile.marketplace.my-orders') }}" class="hover:text-green-600 transition">&larr; Kembali ke Pesanan Saya</a>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 mb-8">Detail Pesanan</h1>

                @if (!$transaction)
                    <div class="text-center text-gray-400 py-12">Memuat...</div>
                @else
                    @php
                        $statusColor = match($transaction['status']) {
                            'paid'    => 'bg-green-100 text-green-700',
                            'pending' => 'bg-orange-100 text-orange-700',
                            'failed'  => 'bg-red-100 text-red-700',
                            default   => 'bg-gray-100 text-gray-600',
                        };
                        $statusLabel = match($transaction['status']) {
                            'paid'    => '✅ Lunas',
                            'pending' => '⏳ Menunggu Pembayaran',
                            'failed'  => '❌ Gagal',
                            default   => ucfirst($transaction['status']),
                        };
                    @endphp

                    <div class="grid gap-6">
                        {{-- STATUS + INVOICE --}}
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500 mb-1">No. Invoice</p>
                                <p class="font-bold text-gray-900 text-lg">{{ $transaction['invoice_number'] }}</p>
                                <p class="text-xs text-gray-400 mt-1">Metode: {{ $transaction['payment_method'] ?? '-' }}</p>
                            </div>
                            <span class="text-sm px-4 py-1.5 rounded-full font-semibold {{ $statusColor }}">
                                {{ $statusLabel }}
                            </span>
                        </div>

                        {{-- ITEMS --}}
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">
                            <h3 class="font-semibold text-gray-800 mb-4">Produk Dipesan</h3>
                            @foreach ($transaction['items'] ?? [] as $item)
                                @php
                                    $photo  = data_get($item, 'product.photos.0.file_path');
                                    $imgUrl = $photo
                                        ? rtrim(env('STORAGE_BASE_URL'), '/') . '/storage/' . $photo
                                        : asset('images/no-image.png');
                                @endphp
                                <div class="flex gap-4">
                                    <img src="{{ $imgUrl }}" class="w-20 h-20 rounded-xl object-cover shrink-0">
                                    <div class="flex-1">
                                        <p class="font-semibold text-gray-900">{{ $item['product_name'] }}</p>
                                        <p class="text-sm text-gray-500">Toko: {{ data_get($item, 'product.umkm.user.name', '-') }}</p>
                                        <div class="flex items-center justify-between mt-2">
                                            <span class="text-sm text-gray-500">{{ $item['quantity'] }} × Rp {{ number_format((float) $item['price'], 0, ',', '.') }}</span>
                                            <span class="font-bold text-green-600">Rp {{ number_format((float) $item['subtotal'], 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- TOTAL --}}
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center justify-between">
                            <span class="text-base font-semibold text-gray-800">Total Pembayaran</span>
                            <span class="text-xl font-bold text-green-600">Rp {{ number_format((float) $transaction['total_amount'], 0, ',', '.') }}</span>
                        </div>

                        {{-- CTA --}}
                        @if ($transaction['status'] === 'pending' && !empty($transaction['payment_url']))
                            <button onclick="window.location.href='{{ $transaction['payment_url'] }}'"
                                class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-4 rounded-xl transition shadow-md shadow-green-200 text-lg">
                                Selesaikan Pembayaran
                            </button>
                        @endif
                    </div>
                @endif
            </div>
        </x-desktop.layout>
    </x-slot:desktop>

</x-layouts.mobile>
