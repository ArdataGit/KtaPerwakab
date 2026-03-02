<?php
use function Livewire\Volt\{state, mount};
use App\Services\OrganizationHistoryApiService;

state([
    'history' => null,
    'isLoading' => true,
    'error' => null,
]);

mount(function () {
    $token = session('token');
    
    if (!$token) {
        $this->error = 'Sesi tidak valid. Silakan login kembali.';
        $this->isLoading = false;
        return;
    }

    $response = OrganizationHistoryApiService::get($token);

    if ($response->successful()) {
        $this->history = $response->json('data');
    } else {
        $this->error = 'Gagal memuat data sejarah organisasi.';
    }

    $this->isLoading = false;
});
?>

<x-layouts.mobile title="Tentang Kami">
    {{-- HEADER --}}
    <div class="w-full bg-green-600 px-4 py-4 flex items-center space-x-3 rounded-b-2xl">
        <button onclick="window.history.back()">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
        </button>
        <p class="text-white font-semibold text-lg tracking-wide">Tentang Kami</p>
    </div>

    {{-- CONTENT --}}
    <div class="pt-20 px-4 pb-24 mt-2">
        @if($isLoading)
            <div class="flex flex-col items-center justify-center py-20">
                <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-green-600 mb-4"></div>
                <p class="text-gray-500 text-sm">Memuat data...</p>
            </div>
        @elseif($error)
            <div class="bg-red-50 p-4 rounded-xl border border-red-100 flex items-start space-x-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-500 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-red-700 text-sm">{{ $error }}</p>
            </div>
        @elseif(!$history || empty($history))
            <div class="bg-gray-50 p-6 rounded-xl border border-gray-100 text-center py-16">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                <p class="text-gray-500 font-medium">Belum ada konten tentang kami.</p>
            </div>
        @else
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden group">
                @if(!empty($history['featured_image']))
                    <div class="w-full aspect-video overflow-hidden">
                        <img src="{{ api_profile_url($history['featured_image']) }}" 
                             alt="{{ $history['title'] ?? 'Tentang Kami' }}" 
                             class="w-full h-full object-cover transition duration-300 group-hover:scale-105"
                             onerror="this.src='/images/assets/default-article.png'">
                    </div>
                @endif
                
                <div class="p-5">
                    <h1 class="text-xl font-bold text-gray-900 mb-3 leading-snug">{{ $history['title'] ?? 'Tentang Kami' }}</h1>
                    
                    <div class="text-gray-700 text-sm leading-relaxed prose prose-sm max-w-none prose-img:rounded-xl prose-img:shadow-sm prose-a:text-green-600 prose-headings:text-gray-900 space-y-4">
                        {!! $history['content'] ?? '' !!}
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- NAVBAR --}}
    <x-mobile.navbar active="home" />

    {{-- ================== DESKTOP VIEW ================== --}}
    <x-slot:desktop>
        <x-desktop.layout title="Tentang Kami">

            <div class="max-w-4xl mx-auto space-y-6">

                {{-- Hero Header --}}
                <div class="bg-gradient-to-br from-green-700 to-green-900 rounded-2xl p-8 text-white relative overflow-hidden shadow-lg">
                    <div class="relative z-10">
                        <h1 class="text-3xl font-bold mb-2">Tentang Kami</h1>
                        <p class="text-green-100 text-lg opacity-90">Mengenal perwakilan organisasi dan perjalanan panjang kami.</p>
                    </div>
                    <div class="absolute right-0 top-0 w-64 h-64 bg-green-500 rounded-full mix-blend-multiply filter blur-3xl opacity-50 translate-x-1/2 -translate-y-1/2"></div>
                    <div class="absolute right-20 bottom-0 w-48 h-48 bg-emerald-400 rounded-full mix-blend-multiply filter blur-3xl opacity-50 translate-x-1/2 translate-y-1/2"></div>
                </div>

                {{-- Content Card --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    @if($isLoading)
                        <div class="flex flex-col items-center justify-center py-24">
                            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-600 mb-4"></div>
                            <p class="text-gray-500">Memuat data...</p>
                        </div>
                    @elseif($error)
                        <div class="p-8">
                            <div class="bg-red-50 p-5 rounded-xl border border-red-100 flex items-start space-x-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-red-500 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="text-red-700">{{ $error }}</p>
                            </div>
                        </div>
                    @elseif(!$history || empty($history))
                        <div class="p-8 text-center py-24">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                            <p class="text-gray-500 font-medium text-lg">Belum ada konten tentang kami.</p>
                        </div>
                    @else
                        @if(!empty($history['featured_image']))
                            <div class="w-full aspect-[21/9] overflow-hidden">
                                <img src="{{ api_profile_url($history['featured_image']) }}"
                                     alt="{{ $history['title'] ?? 'Tentang Kami' }}"
                                     class="w-full h-full object-cover"
                                     onerror="this.src='/images/assets/default-article.png'">
                            </div>
                        @endif

                        <div class="p-8 lg:p-10">
                            <h2 class="text-2xl font-bold text-gray-900 mb-6 leading-snug">{{ $history['title'] ?? 'Tentang Kami' }}</h2>

                            <div class="text-gray-700 leading-relaxed prose prose-lg max-w-none prose-img:rounded-xl prose-img:shadow-sm prose-a:text-green-600 prose-headings:text-gray-900 space-y-4">
                                {!! $history['content'] ?? '' !!}
                            </div>
                        </div>
                    @endif
                </div>

            </div>

        </x-desktop.layout>
    </x-slot:desktop>

</x-layouts.mobile>
