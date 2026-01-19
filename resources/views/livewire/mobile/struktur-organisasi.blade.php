<?php

use App\Services\StrukturOrganisasiApiService;
use function Livewire\Volt\state;
use function Livewire\Volt\mount;

state([
    'struktur' => null,
    'loading' => true,
    'error' => null,
]);

mount(function () {
    $token = session('token');

    if (!$token) {
        $this->error = 'Token tidak ditemukan';
        $this->loading = false;
        return;
    }

    try {
        $response = StrukturOrganisasiApiService::get($token);

        if ($response->successful()) {
            $this->struktur = $response->json('data');
        } else {
            $this->error = 'Gagal memuat data struktur organisasi';
        }
    } catch (\Exception $e) {
        $this->error = 'Terjadi kesalahan: ' . $e->getMessage();
    }

    $this->loading = false;
});

?>

<x-layouts.mobile title="Struktur Organisasi">
    {{-- HEADER --}}
    <div class="w-full bg-green-600 px-4 py-4 flex items-center space-x-3 rounded-b-2xl">
        <button onclick="window.history.back()">
            <img src="/images/assets/icon/back.svg" class="w-5 h-5">
        </button>
        <p class="text-white font-semibold text-base">Struktur Organisasi</p>
    </div>

    <div class="px-4 py-6" x-data="{ showFullscreen: false }">
        @if($loading)
            <div class="flex items-center justify-center py-20">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-600"></div>
            </div>
        @elseif($error)
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-center">
                <p class="text-red-600">{{ $error }}</p>
            </div>
        @elseif($struktur && isset($struktur['file_url']))
            @php
                $fileUrl = $struktur['file_url'];
                $extension = strtolower(pathinfo($fileUrl, PATHINFO_EXTENSION));
                $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                $isPdf = $extension === 'pdf';
            @endphp

            @if($isImage)
                {{-- Tampilan untuk gambar --}}
                <div class="bg-white rounded-lg shadow-sm p-2">
                    <img src="{{ $fileUrl }}" alt="Struktur Organisasi"
                        class="w-full h-auto rounded-lg">
                </div>

                {{-- Fullscreen Modal untuk Image --}}
                <div x-show="showFullscreen" 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     @click="showFullscreen = false"
                     class="fixed inset-0 z-[9999] bg-black/90 overflow-auto"
                     style="display: none;">
                    
                    {{-- Header dengan tombol close --}}
                    <div class="sticky top-0 right-0 flex justify-end p-4 z-10">
                        <button @click="showFullscreen = false" 
                                class="bg-white/20 hover:bg-white/30 text-white rounded-full p-2 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    {{-- Container gambar dengan ukuran penuh --}}
                    <div class="min-h-screen flex items-center justify-center p-4" @click.stop>
                        <img src="{{ $fileUrl }}" 
                             alt="Struktur Organisasi" 
                             class="w-auto h-auto max-w-full">
                    </div>
                </div>

            @elseif($isPdf)
                {{-- Tampilan untuk PDF --}}
                <div class="space-y-4">
                    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                        <iframe 
                            src="/pdf-proxy?url={{ urlencode($fileUrl) }}" 
                            class="w-full border-0" style="min-height:30rem;">
                        </iframe>
                    </div>

                    <a href="{{ $fileUrl }}" target="_blank"
                        class="block w-full bg-green-600 text-white text-center py-3 rounded-xl font-semibold">
                        Buka PDF di Tab Baru
                    </a>
                </div>

            @else
                {{-- File tidak didukung --}}
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
                    <p class="text-yellow-700 mb-4">Format file tidak didukung untuk ditampilkan</p>
                    <a href="{{ $fileUrl }}" target="_blank"
                        class="inline-block bg-green-600 text-white px-6 py-3 rounded-xl font-semibold">
                        Download File
                    </a>
                </div>
            @endif
        @else
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 text-center">
                <p class="text-gray-600">Data struktur organisasi tidak tersedia</p>
            </div>
        @endif
    </div>

    <div class="h-20"></div>

    <x-mobile.navbar active="home" />
</x-layouts.mobile>
