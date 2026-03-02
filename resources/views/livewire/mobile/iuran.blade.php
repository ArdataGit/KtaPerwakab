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
                {{-- INPUT CUSTOM (RUPIAH FORMAT) --}}
                <div x-show="type === 'custom'" x-transition x-data="{
        raw: @entangle('nominal_custom'),
        format(val) {
            if (!val) return '';
            val = val.toString().replace(/[^0-9]/g, '');
            return val.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        },
        onInput(e) {
            let numbers = e.target.value.replace(/[^0-9]/g, '');
            this.raw = numbers;
            e.target.value = this.format(numbers);
        }
    }">
                    <input type="text" inputmode="numeric" placeholder="Minimal Rp240.001" class="w-full border rounded-lg px-3 py-2 text-sm
               focus:outline-none focus:ring focus:ring-green-200" @input="onInput($event)" :value="format(raw)" />
                </div>

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

    {{-- ==================== DESKTOP VIEW ==================== --}}
    <x-slot:desktop>
        <x-desktop.layout title="Iuran Tahunan">
            <div class="max-w-xl mx-auto">
                @if($snackbar['message'])
                    <div class="fixed top-4 right-4 z-[9999] {{ $snackbar['type'] === 'error' ? 'bg-red-500' : 'bg-green-600' }} text-white px-6 py-3 text-sm font-medium shadow-lg rounded-xl">{{ $snackbar['message'] }}</div>
                @endif

                <div class="flex items-center gap-2 text-sm text-gray-400 mb-6">
                    <a href="javascript:history.back()" class="hover:text-green-600 transition">&larr; Kembali</a>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 mb-8">Iuran Tahunan Periode 2025</h1>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8" x-data="{ type: @entangle('type') }">
                    <p class="font-semibold text-gray-800 mb-1">Pembayaran Iuran</p>
                    <p class="text-sm text-gray-500 mb-6">{{ $user['name'] ?? 'Pengguna' }} {{ isset($user['member_id']) ? '| ' . $user['member_id'] : '' }}</p>

                    <p class="text-sm font-medium text-gray-700 mb-3">Jumlah Kirim</p>
                    <div class="space-y-3 mb-6">
                        <label class="flex items-center gap-3 cursor-pointer p-3 rounded-xl border border-gray-200 hover:bg-gray-50 transition" :class="type === 'fixed' ? 'border-green-500 bg-green-50' : ''">
                            <input type="radio" value="fixed" wire:model="type" class="accent-green-600">
                            <span class="text-gray-700 font-medium">Rp240.000</span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer p-3 rounded-xl border border-gray-200 hover:bg-gray-50 transition" :class="type === 'custom' ? 'border-green-500 bg-green-50' : ''">
                            <input type="radio" value="custom" wire:model="type" class="accent-green-600">
                            <span class="text-gray-700">Masukkan jumlah lain (di atas Rp240.000)</span>
                        </label>
                        <div x-show="type === 'custom'" x-transition x-data="{ raw: @entangle('nominal_custom'), format(val) { if (!val) return ''; val = val.toString().replace(/[^0-9]/g, ''); return val.replace(/\B(?=(\d{3})+(?!\d))/g, '.'); }, onInput(e) { let numbers = e.target.value.replace(/[^0-9]/g, ''); this.raw = numbers; e.target.value = this.format(numbers); } }">
                            <input type="text" inputmode="numeric" placeholder="Minimal Rp240.001" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-1 focus:ring-green-500 focus:border-green-500 bg-gray-50" @input="onInput($event)" :value="format(raw)"/>
                        </div>
                    </div>

                    <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-6">
                        <p class="text-sm font-semibold text-green-700 mb-2">Petunjuk Pembayaran</p>
                        <ol class="text-xs text-green-700 space-y-1 list-decimal list-inside">
                            <li>Transfer sesuai nominal yang dipilih</li>
                            <li>Gunakan rekening tujuan yang tersedia</li>
                            <li>Simpan bukti transfer</li>
                            <li>Klik tombol konfirmasi untuk melanjutkan</li>
                        </ol>
                    </div>

                    <button type="button" wire:click="submit" class="w-full bg-green-600 hover:bg-green-700 text-white py-4 rounded-xl font-semibold transition shadow-md shadow-green-200">KONFIRMASI</button>
                </div>
            </div>
        </x-desktop.layout>
    </x-slot:desktop>

</x-layouts.mobile>