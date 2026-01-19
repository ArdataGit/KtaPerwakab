<?php

use App\Services\BannerApiService;
use function Livewire\Volt\state;
use function Livewire\Volt\mount;

state([
    'banner' => null,
]);

mount(function ($id) {
    try {
        $response = BannerApiService::getBanners();
        
        if ($response->successful()) {
            $data = $response->json('data') ?? [];
            $this->banner = collect($data)->firstWhere('id', (int)$id);
        }
    } catch (\Exception $e) {
        $this->banner = null;
    }
});
?>
@php
    $cover = api_product_url(data_get($banner, 'image'));
@endphp
<x-layouts.mobile title="Detail Banner">
    <div>
        <div class="w-full bg-green-600 px-4 py-4 flex items-center space-x-3 rounded-b-2xl">
            <button onclick="window.history.back()">
                <img src="/images/assets/icon/back.svg" class="w-5 h-5">
            </button>
            <p class="text-white font-semibold text-base">Detail Banner</p>
        </div>

        @if($banner)
            <div class="px-4 mt-4 space-y-4">
                <img 
                    src="{{ $cover }}" 
                    class="w-full rounded-xl object-cover" 
                    alt="{{ $banner['title'] ?? 'Banner' }}"
                >                
                <div class="bg-white rounded-xl p-4 shadow-sm">
                    <h1 class="text-xl font-bold text-gray-800 mb-2">{{ $banner['title'] }}</h1>
                    
                    @if(!empty($banner['subtitle']))
                        <p class="text-sm font-semibold text-gray-600 mb-3">{{ $banner['subtitle'] }}</p>
                    @endif
                    
                    @if(!empty($banner['description']))
                        <p class="text-sm text-gray-700 leading-relaxed">{{ $banner['description'] }}</p>
                    @endif
                </div>
            </div>
        @else
            <div class="px-4 mt-4">
                <div class="bg-white rounded-xl p-4 text-center text-gray-500">
                    Banner tidak ditemukan
                </div>
            </div>
        @endif

        <div class="h-20"></div>

        <x-mobile.navbar active="home" />
    </div>
</x-layouts.mobile>