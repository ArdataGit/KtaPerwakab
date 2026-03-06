<?php

use App\Services\UmkmTransactionApiService;
use function Livewire\Volt\state;
use function Livewire\Volt\mount;

state([
    'transactions' => [],
    'meta'         => [],
]);

mount(function () {
    $response = UmkmTransactionApiService::myTransactions();

    if ($response->successful()) {
        $this->transactions = $response->json('data') ?? [];
        $this->meta         = $response->json('meta') ?? [];
    }
});
?>

<x-layouts.mobile title="Pesanan Saya">

    {{-- HEADER --}}
    <div class="w-full bg-green-600 px-4 py-4 flex items-center space-x-3 rounded-b-2xl">
        <button onclick="window.history.back()">
            <img src="/images/assets/icon/back.svg" class="w-5 h-5" alt="Back">
        </button>
        <p class="text-white font-semibold text-base">Pesanan Saya</p>
    </div>

    <div class="px-4 mt-4 space-y-4">

        @forelse ($transactions as $trx)
            @php
                $statusColor = match($trx['status']) {
                    'paid'    => 'bg-green-100 text-green-700',
                    'pending' => 'bg-orange-100 text-orange-700',
                    'failed'  => 'bg-red-100 text-red-700',
                    default   => 'bg-gray-100 text-gray-600',
                };
                $statusLabel = match($trx['status']) {
                    'paid'    => '✅ Lunas',
                    'pending' => '⏳ Menunggu Pembayaran',
                    'failed'  => '❌ Gagal',
                    default   => ucfirst($trx['status']),
                };
                $firstProduct = data_get($trx, 'items.0.product_name', '-');
                $itemCount    = count($trx['items'] ?? []);
            @endphp

            <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                <div class="p-4 space-y-2">
                    {{-- INVOICE + DATE --}}
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs text-gray-500">{{ $trx['invoice_number'] }}</p>
                            <p class="text-sm font-semibold text-gray-800 mt-0.5">
                                {{ $firstProduct }}
                                @if($itemCount > 1)
                                    <span class="text-gray-400 font-normal">+{{ $itemCount - 1 }} lainnya</span>
                                @endif
                            </p>
                        </div>
                        <span class="text-xs px-3 py-1 rounded-full font-medium {{ $statusColor }}">
                            {{ $statusLabel }}
                        </span>
                    </div>

                    {{-- TOTAL --}}
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500">Total</span>
                        <span class="font-bold text-green-600">
                            Rp {{ number_format((float) $trx['total_amount'], 0, ',', '.') }}
                        </span>
                    </div>
                </div>

                {{-- ACTIONS --}}
                <div class="border-t px-4 py-3 flex gap-2">
                    <a href="{{ route('mobile.marketplace.order-detail', $trx['id']) }}"
                        class="flex-1 text-center text-sm font-medium border border-gray-300 text-gray-700 py-2 rounded-xl">
                        Lihat Detail
                    </a>

                    @if ($trx['status'] === 'pending' && !empty($trx['payment_url']))
                        <a href="{{ $trx['payment_url'] }}" target="_blank"
                            class="flex-1 text-center text-sm font-medium bg-green-600 text-white py-2 rounded-xl">
                            Bayar Sekarang
                        </a>
                    @endif
                </div>
            </div>

        @empty
            <div class="bg-white rounded-2xl p-8 text-center text-sm text-gray-400">
                <svg class="w-12 h-12 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007z"/>
                </svg>
                <p>Belum ada pesanan.</p>
                <a href="{{ route('mobile.marketplace.index') }}" class="mt-3 inline-block text-green-600 font-semibold text-sm">
                    Mulai Belanja
                </a>
            </div>
        @endforelse

    </div>

    <div class="h-24"></div>
    <x-mobile.navbar active="marketplace" />

    {{-- ==================== DESKTOP VIEW ==================== --}}
    <x-slot:desktop>
        <x-desktop.layout title="Pesanan Saya">
            <div class="max-w-4xl mx-auto">
                <div class="flex items-center gap-2 text-sm text-gray-400 mb-6">
                    <a href="{{ route('mobile.marketplace.index') }}" class="hover:text-green-600 transition">&larr; Kembali ke Marketplace</a>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Pesanan Saya</h1>
                <p class="text-gray-500 mb-8">Riwayat dan status pembelian produk UMKM.</p>

                <div class="space-y-4">
                    @forelse ($transactions as $trx)
                        @php
                            $statusColor = match($trx['status']) {
                                'paid'    => 'bg-green-100 text-green-700',
                                'pending' => 'bg-orange-100 text-orange-700',
                                'failed'  => 'bg-red-100 text-red-700',
                                default   => 'bg-gray-100 text-gray-600',
                            };
                            $statusLabel = match($trx['status']) {
                                'paid'    => '✅ Lunas',
                                'pending' => '⏳ Menunggu Pembayaran',
                                'failed'  => '❌ Gagal',
                                default   => ucfirst($trx['status']),
                            };
                            $firstProduct = data_get($trx, 'items.0.product_name', '-');
                            $itemCount    = count($trx['items'] ?? []);
                        @endphp

                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition">
                            <div class="p-6 flex items-center justify-between">
                                <div class="space-y-1">
                                    <p class="text-xs text-gray-400">{{ $trx['invoice_number'] }}</p>
                                    <p class="font-semibold text-gray-900">
                                        {{ $firstProduct }}
                                        @if($itemCount > 1)
                                            <span class="text-gray-400 font-normal text-sm">+{{ $itemCount - 1 }} lainnya</span>
                                        @endif
                                    </p>
                                    <p class="text-green-600 font-bold">
                                        Rp {{ number_format((float) $trx['total_amount'], 0, ',', '.') }}
                                    </p>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="text-xs px-3 py-1.5 rounded-full font-medium {{ $statusColor }}">
                                        {{ $statusLabel }}
                                    </span>

                                    @if ($trx['status'] === 'pending' && !empty($trx['payment_url']))
                                        <a href="{{ $trx['payment_url'] }}" target="_blank"
                                            class="text-xs px-4 py-1.5 bg-green-600 hover:bg-green-700 text-white rounded-full font-semibold transition">
                                            Bayar Sekarang
                                        </a>
                                    @endif

                                    <a href="{{ route('mobile.marketplace.order-detail', $trx['id']) }}"
                                        class="text-xs px-4 py-1.5 border border-gray-300 text-gray-700 rounded-full font-semibold hover:bg-gray-50 transition">
                                        Lihat Detail
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="bg-white rounded-2xl p-12 text-center border border-gray-100">
                            <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007z"/>
                            </svg>
                            <p class="text-gray-500 mb-4">Belum ada pesanan.</p>
                            <a href="{{ route('mobile.marketplace.index') }}"
                                class="inline-block bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-xl font-semibold transition">
                                Mulai Belanja
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>
        </x-desktop.layout>
    </x-slot:desktop>

</x-layouts.mobile>
