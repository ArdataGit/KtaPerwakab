<?php

use App\Services\MarketplaceApiService;
use function Livewire\Volt\state;
use function Livewire\Volt\mount;

state([
    'products' => [],
    'status' => '',
    'snackbar' => ['type' => '', 'message' => ''],
]);

mount(function () {
    $response = MarketplaceApiService::myProducts();

    if ($response->successful()) {
        $this->products = $response->json('data.data') ?? [];
    }
});

$deleteProduct = function ($id) {
    try {
        $response = MarketplaceApiService::delete($id);

        if ($response->failed()) {
            $this->snackbar = ['type' => 'error', 'message' => $response->json('message') ?? 'Gagal menghapus produk'];
            return;
        }

        $this->snackbar = ['type' => 'success', 'message' => 'Produk berhasil dihapus'];
        
        // Refresh data
        $response = MarketplaceApiService::myProducts();
        if ($response->successful()) {
            $this->products = $response->json('data.data') ?? [];
        }
        
    } catch (\Exception $e) {
        $this->snackbar = ['type' => 'error', 'message' => 'Error: ' . $e->getMessage()];
    }
};
?>
<x-layouts.mobile title="Produk Saya">

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

    <div class="w-full bg-green-600 px-4 py-4 flex items-center justify-between rounded-b-2xl">
        <div class="flex items-center space-x-3">
            <button onclick="window.history.back()">
                <img src="/images/assets/icon/back.svg" class="w-5 h-5">
            </button>
            <p class="text-white font-semibold text-base">Produk Saya</p>
        </div>
        <a href="{{ route('mobile.my-products.create') }}" class="bg-white text-green-600 rounded-full p-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M12 5v14M5 12h14" stroke-linecap="round"/>
            </svg>
        </a>
    </div>

    <div class="px-4 mt-4 space-y-4">

        <div class="flex space-x-2">
            <select wire:model="status"
                class="w-full px-4 py-2 rounded-full border text-sm focus:outline-none">
                <option value="">Semua Status</option>
                <option value="pending">Pending</option>
                <option value="approved">Disetujui</option>
                <option value="rejected">Ditolak</option>
            </select>
        </div>

        <div class="space-y-3">
            @forelse ($products as $item)
                @php
                    $photo = $item['photos'][0]['file_path'] ?? null;
                    $image = api_product_url($photo);
                @endphp

                <div class="bg-white rounded-xl p-3 shadow-sm">
                    <div class="flex space-x-3">
                        <img src="{{ $image }}" class="w-24 h-20 rounded-lg object-cover flex-shrink-0">

                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-sm text-gray-800">
                                {{ $item['product_name'] }}
                            </p>

                            <p class="text-xs text-gray-500 mt-1">
                                Rp{{ number_format($item['price'], 0, ',', '.') }}
                            </p>

                            <span class="inline-block mt-1 px-2 py-0.5 text-xs rounded-full
                                @if($item['status'] === 'approved') bg-green-100 text-green-700
                                @elseif($item['status'] === 'pending') bg-yellow-100 text-yellow-700
                                @else bg-red-100 text-red-700 @endif">
                                {{ ucfirst($item['status']) }}
                            </span>
                        </div>
                    </div>

                    <!-- BUTTONS DI BAWAH -->
                    <div class="flex justify-end space-x-2 mt-3">
                        @if(strtolower($item['status']) === 'approved')
                            <a href="/my-product/{{ $item['id'] }}/edit"
                                style="background-color: #2563eb !important; color: white !important;"
                                class="px-3 py-1 text-xs rounded-full font-medium">
                                Edit
                            </a>
                        @endif

                        <button 
                            onclick="if(confirm('Apakah Anda yakin ingin menghapus produk ini?')) { @this.call('deleteProduct', {{ $item['id'] }}) }"
                            style="background-color: #dc2626 !important; color: white !important;"
                            class="px-3 py-1 text-xs rounded-full font-medium">
                            Hapus
                        </button>

                        <a href="{{ route('mobile.marketplace.show', $item['id']) }}"
                            class="px-3 py-1 text-xs bg-gray-200 text-gray-700 rounded-full">
                            Detail
                        </a>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-xl p-4 text-center text-sm text-gray-500">
                    Kamu belum punya produk.
                </div>
            @endforelse
        </div>

    </div>

    <div class="h-20"></div>

    <x-mobile.navbar active="produk" />

</x-layouts.mobile>
