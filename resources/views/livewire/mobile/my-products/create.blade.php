<?php
use App\Services\MarketplaceApiService;
use Livewire\WithFileUploads;
use function Livewire\Volt\state;
use function Livewire\Volt\rules;
use function Livewire\Volt\uses;

uses([WithFileUploads::class]);

state([
    'product_name'   => '',
    'category'       => '',           // ← BARU: kategori
    'description'    => '',
    'price'          => '',
    'youtube_link'   => '',
    'photos'         => [],
    'snackbar'       => ['type' => '', 'message' => ''],
]);

rules([
    'product_name'   => 'required|string|max:255',
    'category'       => 'required|string|in:Makanan,Minuman,Kerajinan,Fashion,Jasa,Pertanian,Perikanan,Lainnya', // ← BARU
    'description'    => 'nullable|string',
    'price'          => 'required|numeric|min:0',
    'youtube_link'   => 'nullable|url',
    'photos.*'       => 'image|mimes:jpeg,jpg,png|max:2048',
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
            'product_name'   => $this->product_name,
            'category'       => $this->category,          // ← BARU: kirim ke API
            'description'    => $this->description,
            'price'          => $this->price,
            'youtube_link'   => $this->youtube_link,
            'photos'         => $this->photos,
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

    <div class="px-4 mt-4 space-y-5 pb-24">

        <!-- NAMA PRODUK -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Produk <span class="text-red-500">*</span></label>
            <input type="text" wire:model.live="product_name"
                   class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-green-500 focus:ring-2 focus:ring-green-200 focus:outline-none text-sm"
                   placeholder="Masukkan nama produk">
            @error('product_name')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- KATEGORI (BARU) -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Kategori Produk <span class="text-red-500">*</span></label>
            <select wire:model.live="category"
                    class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-green-500 focus:ring-2 focus:ring-green-200 focus:outline-none text-sm bg-white">
                <option value="">-- Pilih Kategori --</option>
                <option value="Makanan">Makanan</option>
                <option value="Minuman">Minuman</option>
                <option value="Kerajinan">Kerajinan</option>
                <option value="Fashion">Fashion</option>
                <option value="Jasa">Jasa</option>
                <option value="Pertanian">Pertanian</option>
                <option value="Perikanan">Perikanan</option>
                <option value="Lainnya">Lainnya</option>
            </select>
            @error('category')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- DESKRIPSI -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi (Opsional)</label>
            <textarea wire:model="description" rows="4"
                      class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-green-500 focus:ring-2 focus:ring-green-200 focus:outline-none text-sm resize-none"
                      placeholder="Masukkan deskripsi produk, bahan, ukuran, dll..."></textarea>
            @error('description')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- HARGA -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Harga (Rp) <span class="text-red-500">*</span></label>
            <input type="number" wire:model.live="price" min="0" step="1"
                   class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-green-500 focus:ring-2 focus:ring-green-200 focus:outline-none text-sm"
                   placeholder="Contoh: 50000">
            @error('price')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- YOUTUBE LINK -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Link YouTube (Opsional)</label>
            <input type="url" wire:model="youtube_link"
                   class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-green-500 focus:ring-2 focus:ring-green-200 focus:outline-none text-sm"
                   placeholder="https://www.youtube.com/watch?v=...">
            @error('youtube_link')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- FOTO PRODUK -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Foto Produk <span class="text-red-500">*</span></label>
            <input type="file" wire:model.live="photos" multiple accept="image/jpeg,image/jpg,image/png"
                   class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-green-500 focus:ring-2 focus:ring-green-200 focus:outline-none text-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
            <p class="text-xs text-gray-500 mt-2">Upload minimal 1 foto (jpeg, jpg, png - max 2MB per foto)</p>
            @error('photos.*')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
            @error('photos')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- BUTTON SUBMIT -->
        <button wire:click="submit"
                class="w-full bg-green-600 text-white py-4 rounded-xl font-semibold text-base hover:bg-green-700 active:bg-green-800 transition mt-6 shadow-md">
            Simpan Produk
        </button>

    </div>

    <x-mobile.navbar active="produk" />

    {{-- ==================== DESKTOP VIEW ==================== --}}
    <x-slot:desktop>
        <x-desktop.layout title="Tambah Produk">
            <div class="max-w-3xl mx-auto">
                <div x-data="{ snackbar: @entangle('snackbar'), show: false, icons: { error: '⚠', success: '✔' }, styles: { error: 'bg-red-500 text-white', success: 'bg-green-600 text-white' } }"
                    x-init="$watch('snackbar', v => { if (v && v.message) { show = true; setTimeout(() => show = false, 2500); } })"
                    x-show="show" x-transition.opacity class="fixed top-4 right-4 z-[9999] flex items-center gap-2 px-5 py-3 text-sm font-medium shadow-lg rounded-xl" :class="styles[snackbar?.type ?? 'success']">
                    <span class="text-lg" x-text="icons[snackbar?.type ?? 'success']"></span><span x-text="snackbar?.message ?? ''"></span>
                </div>

                <div class="flex items-center gap-2 text-sm text-gray-400 mb-6">
                    <a href="{{ route('mobile.my-products.index') }}" class="hover:text-green-600 transition">&larr; Kembali ke Produk Saya</a>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 mb-8">Tambah Produk Baru</h1>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Produk <span class="text-red-500">*</span></label>
                            <input type="text" wire:model.live="product_name" placeholder="Masukkan nama produk" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-green-500 focus:ring-1 focus:ring-green-500 outline-none text-sm bg-gray-50 transition">
                            @error('product_name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kategori Produk <span class="text-red-500">*</span></label>
                            <select wire:model.live="category" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-green-500 outline-none text-sm bg-gray-50 transition">
                                <option value="">-- Pilih Kategori --</option>
                                @foreach(['Makanan','Minuman','Kerajinan','Fashion','Jasa','Pertanian','Perikanan','Lainnya'] as $cat)
                                    <option value="{{ $cat }}">{{ $cat }}</option>
                                @endforeach
                            </select>
                            @error('category')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Harga (Rp) <span class="text-red-500">*</span></label>
                            <input type="number" wire:model.live="price" min="0" placeholder="50000" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-green-500 outline-none text-sm bg-gray-50 transition">
                            @error('price')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi (Opsional)</label>
                            <textarea wire:model="description" rows="4" placeholder="Deskripsi produk, bahan, ukuran..." class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-green-500 outline-none text-sm bg-gray-50 resize-none transition"></textarea>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Link YouTube (Opsional)</label>
                            <input type="url" wire:model="youtube_link" placeholder="https://www.youtube.com/watch?v=..." class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-green-500 outline-none text-sm bg-gray-50 transition">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Foto Produk <span class="text-red-500">*</span></label>
                            <input type="file" wire:model.live="photos" multiple accept="image/jpeg,image/jpg,image/png"
                                class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                            <p class="text-xs text-gray-500 mt-2">Upload minimal 1 foto (jpeg, jpg, png - max 2MB per foto)</p>
                        </div>
                    </div>
                    <button wire:click="submit" class="w-full bg-green-600 hover:bg-green-700 text-white py-4 rounded-xl font-semibold text-base transition shadow-md shadow-green-200">Simpan Produk</button>
                </div>
            </div>
        </x-desktop.layout>
    </x-slot:desktop>

</x-layouts.mobile>