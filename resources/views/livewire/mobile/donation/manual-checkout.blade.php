<?php

use function Livewire\Volt\state;
use function Livewire\Volt\mount;

state([
    'donationId'    => null,
    'campaignTitle' => '',
    'amount'        => 0,
    'bankName'      => '',
    'bankAccount'   => '',
    'accountName'   => '',
]);

mount(function ($id) {
    $this->donationId = $id;

    // Data bank diambil dari session flash (disimpan oleh detail.blade.php saat submit)
    $data = session('manual_checkout');

    if ($data && isset($data['bank'])) {
        $this->campaignTitle = $data['campaign'] ?? '-';
        $this->amount        = (int) ($data['amount'] ?? 0);
        $this->bankName      = $data['bank']['bank_name'] ?? '-';
        $this->bankAccount   = $data['bank']['account_number'] ?? '-';
        $this->accountName   = $data['bank']['account_name'] ?? '-';
    }
});
?>

<x-layouts.mobile title="Transfer Manual">

    {{-- HEADER --}}
    <div class="w-full bg-green-600 px-4 py-4 flex items-center space-x-3 rounded-b-2xl">
        <button onclick="window.history.back()">
            <img src="/images/assets/icon/back.svg" class="w-5 h-5">
        </button>
        <p class="text-white font-semibold text-base">Transfer Manual</p>
    </div>

    <div class="px-4 mt-6 space-y-5">

        {{-- INFO NOMINAL --}}
        @if ($campaignTitle)
        <div class="bg-white rounded-2xl shadow-sm p-4 space-y-2 text-sm text-gray-700">
            <div class="flex justify-between">
                <span>Nama Campaign</span>
                <span class="font-semibold text-gray-900">{{ $campaignTitle }}</span>
            </div>
            <div class="flex justify-between">
                <span>Nominal Donasi</span>
                <span class="font-semibold text-gray-900">Rp {{ number_format($amount, 0, ',', '.') }}</span>
            </div>
            <hr>
            <div class="flex justify-between text-base font-bold text-gray-900">
                <span>Total Transfer</span>
                <span>Rp {{ number_format($amount, 0, ',', '.') }}</span>
            </div>
        </div>
        @endif

        {{-- INFO BANK --}}
        <div class="bg-green-50 border border-green-200 rounded-2xl p-4 space-y-3 text-sm">
            <p class="font-bold text-green-800 text-base">Rekening Tujuan</p>

            @if ($bankName)
            <div>
                <p class="text-gray-500">Bank</p>
                <p class="font-semibold text-gray-900">{{ $bankName }}</p>
            </div>

            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500">Nomor Rekening</p>
                    <p class="font-bold text-gray-900 text-base">{{ $bankAccount }}</p>
                </div>
                <button
                    onclick="navigator.clipboard.writeText('{{ $bankAccount }}'); this.textContent='✓ Disalin'; setTimeout(() => this.textContent='Salin', 2000);"
                    class="text-xs px-3 py-1.5 border border-green-600 text-green-600 rounded-full font-medium hover:bg-green-600 hover:text-white transition">
                    Salin
                </button>
            </div>

            <div>
                <p class="text-gray-500">Atas Nama</p>
                <p class="font-semibold text-gray-900">{{ $accountName }}</p>
            </div>
            @else
            <p class="text-sm text-amber-700">
                ⚠️ Silakan cek email konfirmasi Anda untuk detail rekening tujuan, atau hubungi admin.
            </p>
            @endif
        </div>

        {{-- INSTRUKSI --}}
        <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 text-sm text-amber-800 space-y-1">
            <p class="font-semibold">⚠️ Penting</p>
            <p>Pastikan nominal transfer sesuai dengan jumlah di atas. Setelah transfer, upload bukti pembayaran.</p>
        </div>

        {{-- BUTTON UPLOAD BUKTI --}}
        <a href="{{ route('mobile.donation.upload-proof', $donationId) }}"
           class="block w-full text-center bg-green-600 hover:bg-green-700 text-white font-semibold py-4 rounded-2xl text-base transition shadow-md shadow-green-200">
            📤 Upload Bukti Transfer
        </a>

        <a href="{{ route('mobile.donation.my') }}"
           class="block w-full text-center bg-white border border-gray-300 text-gray-700 font-semibold py-3 rounded-2xl text-sm transition hover:bg-gray-50">
            Upload Nanti
        </a>

    </div>

    <div class="h-24"></div>

    <x-mobile.navbar active="donasi" />

    {{-- ==================== DESKTOP VIEW ==================== --}}
    <x-slot:desktop>
        <x-desktop.layout title="Transfer Manual">
            <div class="max-w-xl mx-auto">
                <div class="flex items-center gap-2 text-sm text-gray-400 mb-6">
                    <a href="javascript:history.back()" class="hover:text-green-600 transition">&larr; Kembali</a>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 mb-8">Transfer Manual</h1>

                @if ($campaignTitle)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-3 text-sm mb-6">
                    <div class="flex justify-between"><span class="text-gray-500">Nama Campaign</span><span class="font-semibold text-gray-900">{{ $campaignTitle }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Nominal Donasi</span><span>Rp {{ number_format($amount, 0, ',', '.') }}</span></div>
                    <hr>
                    <div class="flex justify-between text-base font-bold text-gray-900"><span>Total Transfer</span><span>Rp {{ number_format($amount, 0, ',', '.') }}</span></div>
                </div>
                @endif

                <div class="bg-green-50 border border-green-200 rounded-2xl p-6 space-y-4 text-sm mb-6">
                    <p class="font-bold text-green-800 text-base">Rekening Tujuan</p>
                    @if ($bankName)
                    <div><p class="text-gray-500">Bank</p><p class="font-semibold text-gray-900">{{ $bankName }}</p></div>
                    <div class="flex items-center justify-between">
                        <div><p class="text-gray-500">Nomor Rekening</p><p class="font-bold text-gray-900 text-lg">{{ $bankAccount }}</p></div>
                        <button onclick="navigator.clipboard.writeText('{{ $bankAccount }}'); this.textContent='✓ Disalin'; setTimeout(() => this.textContent='Salin', 2000);"
                            class="text-xs px-4 py-2 border border-green-600 text-green-600 rounded-full hover:bg-green-600 hover:text-white transition font-medium">Salin</button>
                    </div>
                    <div><p class="text-gray-500">Atas Nama</p><p class="font-semibold text-gray-900">{{ $accountName }}</p></div>
                    @else
                    <p class="text-sm text-amber-700">⚠️ Silakan cek email konfirmasi untuk detail rekening, atau hubungi admin.</p>
                    @endif
                </div>

                <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 text-sm text-amber-800 mb-6">
                    <p class="font-semibold mb-1">⚠️ Penting</p>
                    <p>Pastikan nominal transfer sesuai. Setelah transfer, upload bukti pembayaran.</p>
                </div>

                <a href="{{ route('mobile.donation.upload-proof', $donationId) }}"
                   class="block w-full text-center bg-green-600 hover:bg-green-700 text-white font-semibold py-4 rounded-xl text-base transition shadow-md shadow-green-200 mb-3">
                    📤 Upload Bukti Transfer
                </a>
                <a href="{{ route('mobile.donation.my') }}"
                   class="block w-full text-center bg-white border border-gray-300 text-gray-600 font-semibold py-3 rounded-xl text-sm hover:bg-gray-50 transition">
                    Upload Nanti
                </a>
            </div>
        </x-desktop.layout>
    </x-slot:desktop>

</x-layouts.mobile>
