<?php
use App\Services\MarketplaceApiService;
use Livewire\WithFileUploads;
use function Livewire\Volt\state;
use function Livewire\Volt\rules;
use function Livewire\Volt\uses;
use function Livewire\Volt\mount;

uses([WithFileUploads::class]);

state([
    'productId'       => null,
    'product_name'    => '',
    'category'        => '',                  // ← BARU: kategori
    'description'     => '',
    'price'           => '',
    'youtube_link'    => '',
    'photos'          => [],
    'existing_photos' => [],
    'snackbar'        => ['type' => '', 'message' => ''],
]);

mount(function ($id) {
    $this->productId = $id;

    $response = MarketplaceApiService::productDetail($id);

    if ($response->successful()) {
        $product = $response->json('data');
        $this->product_name   = $product['product_name'] ?? '';
        $this->category       = $product['category'] ?? '';           // ← BARU: load kategori
        $this->description    = $product['description'] ?? '';
        $this->price          = $product['price'] ?? '';
        $this->youtube_link   = $product['youtube_link'] ?? '';
        $this->existing_photos = $product['photos'] ?? [];
    } else {
        $this->snackbar = ['type' => 'error', 'message' => 'Produk tidak ditemukan'];
    }
});

$deletePhoto = function ($photoId) {
    try {
        \Log::info('Attempting to delete photo', ['photo_id' => $photoId]);

        $response = MarketplaceApiService::deletePhoto($photoId);

        \Log::info('Delete photo response', [
            'status' => $response->status(),
            'body'   => $response->body()
        ]);

        if ($response->failed()) {
            $errorMessage = $response->json('message') ?? 'Gagal menghapus foto';
            \Log::error('Failed to delete photo', ['message' => $errorMessage]);
            $this->snackbar = ['type' => 'error', 'message' => $errorMessage];
            return;
        }

        // Refresh data produk setelah hapus
        $response = MarketplaceApiService::productDetail($this->productId);
        if ($response->successful()) {
            $product = $response->json('data');
            $this->existing_photos = $product['photos'] ?? [];
        }

        $this->snackbar = ['type' => 'success', 'message' => 'Foto berhasil dihapus'];

    } catch (\Exception $e) {
        \Log::error('Exception deleting photo', ['error' => $e->getMessage()]);
        $this->snackbar = ['type' => 'error', 'message' => 'Error: ' . $e->getMessage()];
    }
};

rules([
    'product_name'   => 'required|string|max:255',
    'category'       => 'required|string|in:Makanan,Minuman,Kerajinan,Fashion,Jasa,Pertanian,Perikanan,Lainnya', // ← BARU
    'description'    => 'nullable|string',
    'price'          => 'required|numeric|min:0',
    'youtube_link'   => 'nullable|url',
    'photos.*'       => 'image|mimes:jpeg,jpg,png|max:2048',
]);

$submit = function () {
    $this->validate();

    try {
        $data = [
            'product_name'   => $this->product_name,
            'category'       => $this->category,              // ← BARU: kirim kategori
            'description'    => $this->description,
            'price'          => $this->price,
            'youtube_link'   => $this->youtube_link,
        ];

        // Hanya kirim photos jika ada upload baru
        if (!empty($this->photos)) {
            $data['photos'] = $this->photos;
        }

        $response = MarketplaceApiService::update($this->productId, $data);

        if ($response->failed()) {
            $this->snackbar = ['type' => 'error', 'message' => $response->json('message') ?? 'Gagal mengupdate produk'];
            return;
        }

        $this->snackbar = ['type' => 'success', 'message' => 'Produk berhasil diupdate'];
        $this->js('setTimeout(() => window.location.href = "/my-product", 1500)');

    } catch (\Exception $e) {
        $this->snackbar = ['type' => 'error', 'message' => 'Error: ' . $e->getMessage()];
    }
};
?>

<x-layouts.mobile title="Edit Produk">

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
        <p class="text-white font-semibold text-base">Edit Produk</p>
    </div>

    <div class="px-4 mt-4 space-y-5 pb-24">

        <!-- FOTO EXISTING -->
        @if(!empty($existing_photos))
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Foto Saat Ini</label>
                <div class="flex gap-3 overflow-x-auto pb-2">
                    @foreach($existing_photos as $photo)
                        <div class="relative flex-shrink-0">
                            <img src="{{ api_product_url($photo['file_path']) }}"
                                 class="w-24 h-24 rounded-xl object-cover border border-gray-200 shadow-sm">
                            <button
                                type="button"
                                onclick="if(confirm('Yakin ingin menghapus foto ini?')) { @this.call('deletePhoto', {{ $photo['id'] }}) }"
                                class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-7 h-7 flex items-center justify-center text-base font-bold shadow-lg hover:bg-red-600 transition">
                                ×
                            </button>
                        </div>
                    @endforeach
                </div>
                <p class="text-xs text-gray-500 mt-1">Klik tombol × untuk menghapus foto</p>
            </div>
        @endif

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

        <!-- FOTO PRODUK BARU -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Upload Foto Baru (Opsional)</label>
            <input type="file" wire:model.live="photos" multiple accept="image/jpeg,image/jpg,image/png"
                   class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-green-500 focus:ring-2 focus:ring-green-200 focus:outline-none text-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
            <p class="text-xs text-gray-500 mt-2">Kosongkan jika tidak ingin menambah foto baru (jpeg, jpg, png - max 2MB per foto)</p>
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
            Update Produk
        </button>

    </div>

    <x-mobile.navbar active="produk" />
</x-layouts.mobile>