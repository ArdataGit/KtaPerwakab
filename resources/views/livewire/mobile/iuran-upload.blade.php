<?php

use App\Services\MembershipFeeApiService;
use Livewire\WithFileUploads;
use function Livewire\Volt\state;
use function Livewire\Volt\uses;

uses(WithFileUploads::class);

state([
    'token' => session('token'),
    'fee_id' => session('membership_fee_id'),
    'proof_image' => null,
    'snackbar' => ['type' => '', 'message' => ''],
]);

$submit = function () {
    if (!$this->token || !$this->fee_id) {
        $this->snackbar = [
            'type' => 'error',
            'message' => 'Data iuran tidak ditemukan'
        ];
        return;
    }

    if (!$this->proof_image) {
        $this->snackbar = [
            'type' => 'error',
            'message' => 'Silakan unggah bukti pembayaran'
        ];
        return;
    }

    $response = MembershipFeeApiService::uploadProof(
        $this->fee_id,
        $this->proof_image
    );

    if ($response->failed()) {
        $this->snackbar = [
            'type' => 'error',
            'message' => 'Upload bukti pembayaran gagal'
        ];
        return;
    }

    $this->redirect('/iuran/saya', navigate: true);
};
?>


<x-layouts.mobile title="Upload Bukti Pembayaran">

    {{-- Snackbar --}}
    @if($snackbar['message'])
        <div class="fixed top-0 left-1/2 -translate-x-1/2 w-[390px]
                                    {{ $snackbar['type'] === 'error' ? 'bg-red-500' : 'bg-green-600' }}
                                    text-white px-4 py-3 text-sm font-medium shadow-lg rounded-b-lg z-[9999]">
            {{ $snackbar['message'] }}
        </div>
    @endif

    {{-- Header --}}
    <div class="w-full bg-green-600 px-4 py-4 flex items-center space-x-3 rounded-b-2xl">
        <button onclick="window.history.back()">
            <img src="/images/assets/icon/back.svg" class="w-5 h-5">
        </button>
        <p class="text-white font-semibold text-base">
            Upload Bukti Pembayaran
        </p>
    </div>

    <div class="px-4 mt-4">
        <form wire:submit.prevent="submit" class="bg-white rounded-xl p-4 shadow-sm space-y-4">

            {{-- PREVIEW IMAGE --}}
            @if ($proof_image)
                <div class="w-full">
                    <p class="text-sm text-gray-600 mb-2">Preview Bukti Pembayaran</p>
                    <img src="{{ $proof_image->temporaryUrl() }}" class="w-full max-h-64 object-contain rounded-xl border">
                </div>
            @endif

            {{-- UPLOAD --}}
            <label class="border-2 border-dashed border-green-400 rounded-xl p-6 text-center block cursor-pointer">
                <input type="file" wire:model="proof_image" accept="image/*" class="hidden">
                <p class="text-green-700 font-semibold">
                    {{ $proof_image ? 'Ganti Foto' : 'Unggah Foto Bukti Pembayaran' }}
                </p>
                <p class="text-xs text-gray-500 mt-1">
                    JPG / PNG, max 2MB
                </p>
            </label>

            {{-- LOADING --}}
            <div wire:loading wire:target="proof_image" class="text-sm text-gray-500 text-center">
                Memuat gambar...
            </div>

            {{-- SUBMIT --}}
            <button type="submit" class="mt-4 w-full bg-green-600 text-white py-3 rounded-xl font-semibold">
                Kirim Bukti Pembayaran
            </button>

        </form>
    </div>

    <x-mobile.navbar active="home" />
</x-layouts.mobile>