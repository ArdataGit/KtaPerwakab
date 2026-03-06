<?php

use function Livewire\Volt\state;
use function Livewire\Volt\mount;

state([
    'invoice'     => '',
    'paymentUrl'  => '',
    'productName' => '',
    'quantity'    => 0,
    'subtotal'    => 0,
    'method'      => '',
]);

mount(function () {
    if (!session('umkm_checkout_invoice')) {
        $this->redirect(route('mobile.marketplace.index'), navigate: true);
        return;
    }

    $this->invoice     = session('umkm_checkout_invoice');
    $this->paymentUrl  = session('umkm_checkout_payment_url');
    $this->productName = session('umkm_checkout_product_name');
    $this->quantity    = session('umkm_checkout_quantity');
    $this->subtotal    = session('umkm_checkout_subtotal');
    $this->method      = session('umkm_checkout_method');
});
?>

<x-layouts.mobile title="Konfirmasi Pesanan">

    {{-- HEADER --}}
    <div class="w-full bg-green-600 px-4 py-4 flex items-center space-x-3 rounded-b-2xl">
        <button onclick="window.history.back()">
            <img src="/images/assets/icon/back.svg" class="w-5 h-5" alt="Back">
        </button>
        <p class="text-white font-semibold text-base">Konfirmasi Pesanan</p>
    </div>

    <div class="px-4 mt-6 space-y-6">

        {{-- ORDER SUMMARY --}}
        <div class="bg-white rounded-2xl shadow-sm p-4 space-y-3 text-sm text-gray-700">
            <p class="font-semibold text-gray-800 text-base">Ringkasan Pesanan</p>

            <div class="flex justify-between">
                <span class="text-gray-500">Produk</span>
                <span class="font-medium text-right text-gray-900 max-w-[60%]">{{ $productName }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Jumlah</span>
                <span>{{ $quantity }} pcs</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Metode Bayar</span>
                <span>{{ $method }}</span>
            </div>

            <hr>

            <div class="flex justify-between text-base font-bold text-gray-900">
                <span>Total</span>
                <span class="text-green-600">Rp {{ number_format((float) $subtotal, 0, ',', '.') }}</span>
            </div>
        </div>

        {{-- INVOICE --}}
        <div class="bg-gray-50 rounded-2xl p-4 text-sm space-y-2">
            <p class="text-gray-500">No. Invoice</p>
            <div class="flex items-center justify-between">
                <p class="font-semibold text-gray-900 tracking-wide">{{ $invoice }}</p>
                <button onclick="navigator.clipboard.writeText('{{ $invoice }}')"
                    class="text-xs px-3 py-1 border border-green-600 text-green-600 rounded-full">
                    Salin
                </button>
            </div>
        </div>

        {{-- CTA --}}
        <div class="flex justify-center pt-2">
            <button onclick="window.location.href='{{ $paymentUrl }}'"
                class="w-full bg-green-600 text-white font-semibold py-4 rounded-xl shadow-md shadow-green-200">
                Bayar Sekarang
            </button>
        </div>

        {{-- Link ke riwayat --}}
        <div class="text-center">
            <a href="{{ route('mobile.marketplace.my-orders') }}"
                class="text-sm text-green-600 underline">
                Lihat riwayat pesanan saya
            </a>
        </div>

    </div>

    <div class="h-24"></div>
    <x-mobile.navbar active="marketplace" />

    {{-- ==================== DESKTOP VIEW ==================== --}}
    <x-slot:desktop>
        <x-desktop.layout title="Konfirmasi Pesanan">
            <div class="max-w-xl mx-auto">
                <div class="flex items-center gap-2 text-sm text-gray-400 mb-6">
                    <a href="javascript:history.back()" class="hover:text-green-600 transition">&larr; Kembali</a>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 mb-8">Konfirmasi Pesanan</h1>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-3 text-sm mb-6">
                    <p class="font-semibold text-gray-800 text-base mb-4">Ringkasan Pesanan</p>
                    <div class="flex justify-between"><span class="text-gray-500">Produk</span><span class="font-medium text-gray-900">{{ $productName }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Jumlah</span><span>{{ $quantity }} pcs</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Metode Bayar</span><span>{{ $method }}</span></div>
                    <hr>
                    <div class="flex justify-between text-base font-bold text-gray-900">
                        <span>Total</span>
                        <span class="text-green-600">Rp {{ number_format((float) $subtotal, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="bg-gray-50 border border-gray-100 rounded-2xl p-6 text-sm mb-6 space-y-2">
                    <p class="text-gray-500">No. Invoice</p>
                    <div class="flex items-center justify-between">
                        <p class="font-semibold text-gray-900 text-lg tracking-wide">{{ $invoice }}</p>
                        <button onclick="navigator.clipboard.writeText('{{ $invoice }}')"
                            class="text-xs px-4 py-2 border border-green-600 text-green-600 rounded-full hover:bg-green-50 transition">
                            Salin
                        </button>
                    </div>
                </div>

                <button onclick="window.location.href='{{ $paymentUrl }}'"
                    class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-4 rounded-xl transition shadow-md shadow-green-200 mb-4">
                    Bayar Sekarang
                </button>

                <p class="text-center text-sm text-gray-500">
                    <a href="{{ route('mobile.marketplace.my-orders') }}" class="text-green-600 underline">
                        Lihat riwayat pesanan saya
                    </a>
                </p>
            </div>
        </x-desktop.layout>
    </x-slot:desktop>

</x-layouts.mobile>
