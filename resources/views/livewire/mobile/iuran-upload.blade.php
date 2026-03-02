<?php

use App\Services\MembershipFeeApiService;
use Livewire\WithFileUploads;
use function Livewire\Volt\state;
use function Livewire\Volt\uses;

uses(WithFileUploads::class);

state([
    'token'         => session('token'),
    'fee_id'        => session('membership_fee_id'),
    'proof_image'   => null,
    'proof_preview' => null,
    'snackbar'      => ['type' => '', 'message' => ''],
    'isSubmitting'  => false,
]);

$submit = function () {
    // DEBUG 1: Cek apakah method dipanggil
     //dd('Submit method dipanggil. Data saat ini:', [
     //    'fee_id' => $this->fee_id,
     //    'token'  => $this->token,
     //    'proof_image' => $this->proof_image ? 'Ada file' : 'Tidak ada file',
     //]);

    $this->isSubmitting = true;

    if (!$this->token || !$this->fee_id) {
        $this->snackbar = ['type' => 'error', 'message' => 'Data iuran tidak ditemukan'];
        $this->isSubmitting = false;
        return;
    }

    if (!$this->proof_image) {
        $this->snackbar = ['type' => 'error', 'message' => 'Silakan unggah bukti pembayaran'];
        $this->isSubmitting = false;
        return;
    }

    // DEBUG 2: Cek file sebelum kirim ke service
    // dd('File akan diupload:', [
    //     'nama file' => $this->proof_image->getClientOriginalName(),
    //     'ukuran'    => $this->proof_image->getSize() . ' bytes',
    //     'mime'      => $this->proof_image->getMimeType(),
    // ]);

    $response = MembershipFeeApiService::uploadProof(
        $this->fee_id,
        $this->proof_image
    );

    // DEBUG 3: Cek response dari service (ini yang paling penting saat gagal)
    //if ($response->failed()) {
    //    dd('Upload gagal dari MembershipFeeApiService', [
    //        'status'   => $response->status(),
    //        'body'     => $response->json(),
    //        'headers'  => $response->headers(),
    //        'fee_id'   => $this->fee_id,
    //        'file'     => $this->proof_image ? $this->proof_image->getClientOriginalName() : 'tidak ada',
    //    ]);
    //}

    $this->snackbar = [
        'type'    => 'success',
        'message' => 'Bukti pembayaran berhasil diunggah!'
    ];

    $this->redirect('/iuran/saya', navigate: true);

    $this->isSubmitting = false;
};

?>

<x-layouts.mobile title="Upload Bukti Pembayaran">
    {{-- Snackbar --}}
    @if($snackbar['message'])
        <div class="fixed top-0 left-1/2 -translate-x-1/2 w-[390px] max-w-[95vw]
                    {{ $snackbar['type'] === 'error' ? 'bg-red-600' : 'bg-green-600' }}
                    text-white px-5 py-3 text-sm font-medium shadow-xl rounded-b-xl z-[9999] transition-all duration-300">
            {{ $snackbar['message'] }}
        </div>
    @endif

    {{-- Header --}}
    <div class="w-full bg-green-600 px-4 py-4 flex items-center space-x-3 rounded-b-2xl shadow-md">
        <button onclick="window.history.back()" class="text-white">
            <img src="/images/assets/icon/back.svg" class="w-6 h-6">
        </button>
        <p class="text-white font-semibold text-lg">Upload Bukti Pembayaran</p>
    </div>

    <div class="px-4 mt-6">
        <div x-data="{ hasFile: false }">
            <form wire:submit.prevent="submit" class="bg-white rounded-2xl p-6 shadow-lg space-y-6 border border-gray-100">

                <!-- PREVIEW – client-side cepat -->
                <template x-if="$wire.proof_preview">
                    <div class="mt-5 flex justify-center">
                        <img
                            :src="$wire.proof_preview"
                            alt="Preview Bukti Pembayaran"
                            class="max-w-[360px] h-32 object-contain rounded-lg border-2 border-green-500 shadow-lg bg-gray-50"
                        >
                    </div>
                </template>

                <!-- UPLOAD BOX -->
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-800 mb-2">Bukti Pembayaran</label>
                    <label class="flex flex-col items-center justify-center w-full h-40
                                  border-2 border-dashed border-gray-300 rounded-xl
                                  cursor-pointer bg-gray-50 hover:bg-gray-100 transition">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6 text-center">
                            <svg class="w-10 h-10 mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M7 16V4a1 1 0 011-1h8a1 1 0 011 1v12m-5 4h.01M12 20h.01"/>
                            </svg>
                            <p class="text-sm text-gray-600">
                                Klik untuk upload bukti pembayaran
                            </p>
                            <p class="text-xs text-gray-400 mt-1">
                                JPG / PNG / JPEG (maks. 2MB)
                            </p>
                        </div>
                        <input type="file" class="hidden" accept="image/*" wire:model.defer="proof_image"
                               x-on:change="
                                   $wire.set('proof_preview', '');
                                   hasFile = false;
                                   if ($event.target.files[0]) {
                                       hasFile = true;
                                       let reader = new FileReader();
                                       reader.onload = (e) => $wire.set('proof_preview', e.target.result);
                                       reader.readAsDataURL($event.target.files[0]);
                                   }
                               "/>
                    </label>
                </div>

                <!-- Loading khusus saat memilih/upload file (background) -->
                <div wire:loading wire:target="proof_image" class="text-center text-sm text-gray-600 flex items-center justify-center gap-2">
                    <svg class="animate-spin h-5 w-5 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Memproses gambar...
                </div>

                <!-- Loading saat submit (dengan delay agar cepat muncul) -->
                <div wire:loading.delay.shortest wire:target="submit" class="text-center text-sm text-gray-600 flex items-center justify-center gap-2 mt-4">
                    <svg class="animate-spin h-5 w-5 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Mengunggah bukti pembayaran...
                </div>

                <!-- Tombol Submit -->
                <button type="submit"
                        wire:click="submit"
                        class="mt-6 w-full bg-green-600 text-white py-3.5 rounded-xl font-semibold shadow-md
                             disabled:opacity-60 disabled:cursor-not-allowed transition"
                        :disabled="$wire.isSubmitting || !hasFile"
                        wire:loading.attr="disabled"
                        wire:target="submit">
                    <span x-show="!$wire.isSubmitting">Kirim Bukti Pembayaran</span>
                    <span x-show="$wire.isSubmitting" class="flex items-center justify-center">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Mengirim...
                    </span>
                </button>
            </form>
        </div>
    </div>

    <x-mobile.navbar active="home" />

    {{-- ==================== DESKTOP VIEW ==================== --}}
    <x-slot:desktop>
        <x-desktop.layout title="Upload Bukti Pembayaran">
            <div class="max-w-xl mx-auto">
                @if($snackbar['message'])
                    <div class="fixed top-4 right-4 z-[9999] {{ $snackbar['type'] === 'error' ? 'bg-red-500' : 'bg-green-600' }} text-white px-6 py-3 text-sm font-medium shadow-lg rounded-xl">{{ $snackbar['message'] }}</div>
                @endif

                <div class="flex items-center gap-2 text-sm text-gray-400 mb-6">
                    <a href="javascript:history.back()" class="hover:text-green-600 transition">&larr; Kembali</a>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 mb-8">Upload Bukti Pembayaran</h1>

                <div x-data="{ hasFile: false }" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                    <form wire:submit.prevent="submit" class="space-y-6">
                        <template x-if="$wire.proof_preview">
                            <div class="flex justify-center">
                                <img :src="$wire.proof_preview" class="max-w-full h-48 object-contain rounded-xl border-2 border-green-500 shadow-lg bg-gray-50">
                            </div>
                        </template>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Bukti Pembayaran</label>
                            <label class="flex flex-col items-center justify-center w-full h-40 border-2 border-dashed border-gray-300 rounded-xl cursor-pointer bg-gray-50 hover:bg-gray-100 transition">
                                <svg class="w-10 h-10 mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4a1 1 0 011-1h8a1 1 0 011 1v12m-5 4h.01M12 20h.01"/></svg>
                                <p class="text-sm text-gray-600">Klik untuk upload bukti pembayaran</p>
                                <p class="text-xs text-gray-400 mt-1">JPG / PNG / JPEG (maks. 2MB)</p>
                                <input type="file" class="hidden" accept="image/*" wire:model.defer="proof_image"
                                    x-on:change="$wire.set('proof_preview', ''); hasFile = false; if ($event.target.files[0]) { hasFile = true; let reader = new FileReader(); reader.onload = (e) => $wire.set('proof_preview', e.target.result); reader.readAsDataURL($event.target.files[0]); }"/>
                            </label>
                        </div>

                        <div wire:loading wire:target="proof_image" class="text-center text-sm text-gray-600 flex items-center justify-center gap-2">Memproses gambar...</div>

                        <button type="submit" wire:click="submit" class="w-full bg-green-600 hover:bg-green-700 text-white py-4 rounded-xl font-semibold transition shadow-md shadow-green-200 disabled:opacity-60 disabled:cursor-not-allowed" :disabled="$wire.isSubmitting || !hasFile">
                            <span x-show="!$wire.isSubmitting">Kirim Bukti Pembayaran</span>
                            <span x-show="$wire.isSubmitting">Mengirim...</span>
                        </button>
                    </form>
                </div>
            </div>
        </x-desktop.layout>
    </x-slot:desktop>

</x-layouts.mobile>