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

    {{-- ==================== DESKTOP VIEW ==================== --}}
    <x-slot:desktop>
        <div class="min-h-screen bg-gray-50 flex flex-col">
            <x-desktop.topbar />
            
            <div class="flex flex-1">
                <x-desktop.sidebar />
                
                <div class="flex-1 p-8 overflow-y-auto">
                    <div class="max-w-4xl mx-auto">
                        
                        {{-- Header & Back Button --}}
                        <div class="flex items-center gap-4 mb-8">
                            <a href="javascript:history.back()" class="p-2 bg-white rounded-xl border border-gray-200 hover:bg-gray-50 transition shadow-sm">
                                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                            </a>
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900">Detail Banner</h1>
                                <p class="text-sm text-gray-500 mt-1">Informasi lengkap terkait banner/pengumuman ini.</p>
                            </div>
                        </div>

                        @if($banner)
                            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                                {{-- Image --}}
                                <div class="w-full h-80 bg-gray-100 relative">
                                    <img 
                                        src="{{ $cover }}" 
                                        class="w-full h-full object-cover" 
                                        alt="{{ $banner['title'] ?? 'Banner' }}"
                                        onerror="this.src='/images/assets/placeholder.jpg'"
                                    >
                                </div>
                                
                                {{-- Content --}}
                                <div class="p-8">
                                    <div class="mb-6 pb-6 border-b border-gray-100">
                                        <h2 class="text-3xl font-extrabold text-gray-900 mb-3">{{ $banner['title'] }}</h2>
                                        @if(!empty($banner['subtitle']))
                                            <p class="text-lg font-medium text-green-700 bg-green-50 px-3 py-1 rounded-lg inline-block">{{ $banner['subtitle'] }}</p>
                                        @endif
                                    </div>
                                    
                                    @if(!empty($banner['description']))
                                        <div class="prose max-w-none text-gray-700 leading-relaxed text-lg">
                                            {!! nl2br(e($banner['description'])) !!}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="bg-white rounded-2xl p-12 text-center border border-gray-100 shadow-sm flex flex-col items-center justify-center">
                                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                                <h3 class="text-xl font-bold text-gray-800 mb-2">Banner tidak ditemukan</h3>
                                <p class="text-gray-500">Banner yang Anda cari mungkin telah dihapus atau link tidak valid.</p>
                                <a href="javascript:history.back()" class="mt-6 px-6 py-2 bg-green-600 text-white rounded-lg font-semibold shadow-sm hover:bg-green-700 transition">Kembali</a>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </x-slot:desktop>
</x-layouts.mobile>