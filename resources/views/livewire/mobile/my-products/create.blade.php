<?php

use App\Services\MarketplaceApiService;
use Livewire\WithFileUploads;
use function Livewire\Volt\state;
use function Livewire\Volt\rules;
use function Livewire\Volt\uses;

uses([WithFileUploads::class]);

state([
    'product_name' => '',
    'description' => '',
    'price' => '',
    'youtube_link' => '',
    'photos' => [],
    'snackbar' => ['type' => '', 'message' => ''],
]);

rules([
    'product_name' => 'required|string|max:255',
    'description' => 'nullable|string',
    'price' => 'required|numeric|min:0',
    'youtube_link' => 'nullable|url',
    'photos.*' => 'image|mimes:jpeg,jpg,png|max:2048',
]);

$submit = function () {
    // Validasi manual untuk photos
    if (empty($this->photos)) {
        $this->snackbar = ['type' => 'error', 'message' => 'Minimal upload 1 foto produk'];
        return;
    }

    $this->validate();

    try {
        $response = MarketplaceApiService::store([
            'product_name' => $this->product_name,
            'description' => $this->description,
            'price' => $this->price,
            'youtube_link' => $this->youtube_link,
            'photos' => $this->photos,
        ]);

        if ($response->failed()) {
            $this->snackbar = ['type' => 'error', 'message' => $response->json('message') ?? 'Gagal menambahkan produk'];
            return;
        }

        $this->snackbar = ['type' => 'success', 'message' => 'Produk berhasil ditambahkan dan menunggu persetujuan admin'];
        $this->js('setTimeout(() => window.location.href = "/my-product", 1500)');
        
    } catch (\Exception $e) {
        $this->snackbar = ['type' => 'error', 'message' => 'Error: ' . $e->getMessage()];
    }
};
?>

<x-layouts.mobile title="Tambah Produk">

    <!-- SNACKBAR -->
    <div x-data="{
        snackbar: @entangle('snackbar'),
        show: false,
        icons: { error: '⚠', success: '✔' },
        styles: {
            error: 'bg-red-500 text-white',
            success: 'bg-green-600 text-white'
        }
    }" x-init="
        $watch('snackbar', value => {
            if (value && value.message) {
                show = true;
                setTimeout(() => show = false, 2500);
            }
        });
    " x-show="show" x-transition.opacity
        class="fixed top-0 left-1/2 -translate-x-1/2 w-[390px] z-[9999] flex items-center gap-2 px-4 py-3 text-sm font-medium shadow-lg rounded-b-lg"
        :class="styles[snackbar?.type ?? 'success']">
        <span class="text-lg" x-text="icons[snackbar?.type ?? 'success']"></span>
        <span x-text="snackbar?.message ?? ''"></span>
    </div>

    <!-- HEADER -->
    <div class="w-full bg-green-600 px-4 py-4 flex items-center space-x-3 rounded-b-2xl">
        <button onclick="window.history.back()">
            <img src="/images/assets/icon/back.svg" class="w-5 h-5">
        </button>
        <p class="text-white font-semibold text-base">Tambah Produk</p>
    </div>

    <div class="px-4 mt-4 space-y-4 pb-24">

        <!-- NAMA PRODUK -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Produk</label>
            <input type="text" wire:model="product_name"
                class="w-full px-4 py-2 rounded-lg border focus:ring-2 focus:ring-green-200 focus:outline-none"
                placeholder="Masukkan nama produk">
            @error('product_name')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- DESKRIPSI -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi (Opsional)</label>
            <textarea wire:model="description" rows="4"
                class="w-full px-4 py-2 rounded-lg border focus:ring-2 focus:ring-green-200 focus:outline-none"
                placeholder="Masukkan deskripsi produk"></textarea>
            @error('description')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- HARGA -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Harga</label>
            <input type="number" wire:model="price"
                class="w-full px-4 py-2 rounded-lg border focus:ring-2 focus:ring-green-200 focus:outline-none"
                placeholder="Masukkan harga">
            @error('price')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- YOUTUBE LINK -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Link YouTube (Opsional)</label>
            <input type="url" wire:model="youtube_link"
                class="w-full px-4 py-2 rounded-lg border focus:ring-2 focus:ring-green-200 focus:outline-none"
                placeholder="https://youtube.com/...">
            @error('youtube_link')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- FOTO PRODUK -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Foto Produk</label>
            <input type="file" wire:model="photos" multiple accept="image/jpeg,image/jpg,image/png"
                class="w-full px-4 py-2 rounded-lg border focus:ring-2 focus:ring-green-200 focus:outline-none">
            <p class="text-xs text-gray-500 mt-1">Upload minimal 1 foto (jpeg, jpg, png - max 2MB per foto)</p>
            @error('photos')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- BUTTON SUBMIT -->
        <button wire:click="submit"
            class="w-full bg-green-600 text-white py-3 rounded-lg font-semibold hover:bg-green-700 active:bg-green-800">
            Simpan Produk
        </button>

    </div>

    <x-mobile.navbar active="produk" />

</x-layouts.mobile>
