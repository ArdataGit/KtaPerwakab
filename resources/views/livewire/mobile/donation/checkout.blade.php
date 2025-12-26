<?php
use function Livewire\Volt\state;
use function Livewire\Volt\mount;

state([
    'campaignName' => '',
    'amount' => 300000,
    'uniqueCode' => 102,
    'bank' => 'Bank Central Asia',
    'accountNumber' => '1234 567 890',
    'accountName' => 'Joko Adiwinnansa',
    'expiredAt' => null,
]);

mount(function ($id) {
    // DUMMY DATA
    $this->campaignName = 'Bantuan Banjir Sumatera';
    $this->expiredAt = now()->addMinutes(80);
});
?>
<x-layouts.mobile title="Detail Donasi">

    {{-- HEADER --}}
    <div class="w-full bg-green-600 px-4 py-4 flex items-center space-x-3 rounded-b-2xl">
        <button onclick="window.history.back()">
            <img src="/images/assets/icon/back.svg" class="w-5 h-5">
        </button>
        <p class="text-white font-semibold text-base">Detail Donasi</p>
    </div>

    <div class="px-4 mt-6 space-y-6">

        {{-- INFO CARD --}}
        <div class="bg-white rounded-2xl shadow-sm p-4 space-y-2 text-sm text-gray-700">
            <div class="flex justify-between">
                <span>Nama Campaign</span>
                <span class="font-semibold text-gray-900">
                    {{ $campaignName }}
                </span>
            </div>

            <div class="flex justify-between">
                <span>Nominal Donasi</span>
                <span>Rp {{ number_format($amount, 0, ',', '.') }}</span>
            </div>

            <div class="flex justify-between">
                <span>Kode Unik</span>
                <span>Rp {{ number_format($uniqueCode, 0, ',', '.') }}</span>
            </div>

            <hr>

            <div class="flex justify-between text-base font-bold text-gray-900">
                <span>Total Transfer</span>
                <span>
                    Rp {{ number_format($amount + $uniqueCode, 0, ',', '.') }}
                </span>
            </div>
        </div>

        {{-- TRANSFER INFO --}}
        <div class="space-y-3">
            <p class="font-bold text-gray-900">
                Transfer ke Rekening Donasi
            </p>

            <div class="bg-gray-50 rounded-2xl p-4 space-y-3 text-sm">
                <div>
                    <p class="text-gray-500">Nama Bank</p>
                    <p class="font-semibold text-gray-900">{{ $bank }}</p>
                </div>

                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500">Nomor Rekening</p>
                        <p class="font-semibold text-gray-900">{{ $accountNumber }}</p>
                    </div>

                    <button onclick="navigator.clipboard.writeText('{{ $accountNumber }}')"
                        class="text-xs px-3 py-1 border border-green-600 text-green-600 rounded-full">
                        Salin
                    </button>
                </div>

                <div>
                    <p class="text-gray-500">Atas Nama</p>
                    <p class="font-semibold text-gray-900">{{ $accountName }}</p>
                </div>
            </div>
        </div>

        {{-- COUNTDOWN --}}
        <div class="flex justify-center">
            <div class="px-4 py-2 border rounded-full text-xs text-gray-700">
                Batas Pembayaran {{ $expiredAt->format('H:i') }}
            </div>
        </div>

        {{-- BUTTON --}}
        <div class="flex justify-center pt-4">
            <button class="bg-green-600 text-white font-semibold px-10 py-3 rounded-full">
                Selesai
            </button>
        </div>

    </div>

    <div class="h-24"></div>

    <x-mobile.navbar active="donasi" />

</x-layouts.mobile>