<?php

use App\Services\MembershipFeeApiService;
use function Livewire\Volt\state;

/*
|--------------------------------------------------------------------------
| CONSTANT BUSINESS RULE
|--------------------------------------------------------------------------
*/
const MIN_MEMBERSHIP_FEE = 240000;

state([
    'user' => session('user') ?? [],
    'token' => session('token'),

    // fixed | custom
    'type' => 'fixed',

    // custom nominal
    'nominal_custom' => '',

    // snackbar notification
    'snackbar' => [
        'type' => '',
        'message' => '',
    ],
]);

$submit = function () {

    // 🔐 Validasi token
    if (!$this->token) {
        $this->snackbar = [
            'type' => 'error',
            'message' => 'Token tidak ditemukan, silakan login ulang',
        ];
        return;
    }

    // 💰 Tentukan nominal
    $amount = $this->type === 'fixed'
        ? MIN_MEMBERSHIP_FEE
        : (int) $this->nominal_custom;

    // ❌ Validasi custom nominal
    if ($this->type === 'custom' && $amount <= MIN_MEMBERSHIP_FEE) {
        $this->snackbar = [
            'type' => 'error',
            'message' => 'Nominal iuran harus lebih dari Rp240.000',
        ];
        return;
    }

    // 🚀 Call API
    $response = MembershipFeeApiService::create($this->token, $amount);

    if ($response->failed()) {
        $this->snackbar = [
            'type' => 'error',
            'message' => 'Gagal memproses iuran, silakan coba kembali',
        ];
        return;
    }

    $data = $response->json('data');

    // 💾 Simpan ke session
    session([
        'membership_fee_id' => $data['id'],
        'membership_fee_amount' => $amount,
    ]);

    // ➡️ Redirect
    $this->redirect('/iuran/metode', navigate: true);
};
?>

<x-layouts.mobile title="Iuran Tahunan Periode 2025">

    {{-- 🔔 SNACKBAR --}}
    @if($snackbar['message'])
        <div class="fixed top-0 left-1/2 -translate-x-1/2 w-[390px] z-[9999]
                       {{ $snackbar['type'] === 'error' ? 'bg-red-500' : 'bg-green-600' }}
                       text-white px-4 py-3 text-sm font-medium shadow-lg rounded-b-lg">
            {{ $snackbar['message'] }}
        </div>
    @endif

    {{-- HEADER --}}
    <div class="w-full bg-green-600 px-4 py-4 flex items-center space-x-3 rounded-b-2xl">
        <button onclick="window.history.back()">
            <img src="/images/assets/icon/back.svg" class="w-5 h-5" alt="Back">
        </button>
        <p class="text-white font-semibold text-base">
            Iuran Tahunan Periode 2025
        </p>
    </div>

    <div class="px-4 mt-4">

        {{-- CARD --}}
        <div class="bg-white rounded-xl p-4 shadow-sm" x-data="{ type: @entangle('type') }">

            <p class="font-semibold text-gray-800 mb-1">
                Pembayaran Iuran
            </p>

            <p class="text-sm text-gray-500 mb-4">
                {{ $user['name'] ?? 'Pengguna' }}
                {{ isset($user['member_id']) ? '| ' . $user['member_id'] : '' }}
            </p>

            {{-- JUMLAH KIRIM --}}
            <p class="text-sm font-medium text-gray-700 mb-2">
                Jumlah Kirim
            </p>

            <div class="space-y-3">

                {{-- FIXED --}}
                <label class="flex items-center space-x-3 cursor-pointer">
                    <input type="radio" value="fixed" wire:model="type" class="accent-green-600">
                    <span class="text-gray-700 font-medium">
                        Rp240.000
                    </span>
                </label>

                {{-- CUSTOM --}}
                <label class="flex items-center space-x-3 cursor-pointer">
                    <input type="radio" value="custom" wire:model="type" class="accent-green-600">
                    <span class="text-gray-700">
                        Masukkan jumlah lain (di atas Rp240.000)
                    </span>
                </label>

                {{-- INPUT CUSTOM --}}
                <input x-show="type === 'custom'" x-transition type="number" min="240001"
                    wire:model.defer="nominal_custom" placeholder="Minimal Rp240.001" class="w-full border rounded-lg px-3 py-2 text-sm
                           focus:outline-none focus:ring focus:ring-green-200">
            </div>

            {{-- PETUNJUK --}}
            <div class="mt-4 bg-green-50 border border-green-200 rounded-xl p-3">
                <p class="text-sm font-semibold text-green-700 mb-2">
                    Petunjuk Pembayaran
                </p>
                <ol class="text-xs text-green-700 space-y-1 list-decimal list-inside">
                    <li>Transfer sesuai nominal yang dipilih</li>
                    <li>Gunakan rekening tujuan yang tersedia</li>
                    <li>Simpan bukti transfer</li>
                    <li>Klik tombol konfirmasi untuk melanjutkan</li>
                </ol>
            </div>

            {{-- BUTTON --}}
            <div class="mt-6">
                <button type="button" wire:click="submit"
                    class="w-full bg-green-600 text-white py-3 rounded-xl font-semibold">
                    KONFIRMASI
                </button>
            </div>

        </div>
    </div>

    <x-mobile.navbar active="home" />
</x-layouts.mobile>