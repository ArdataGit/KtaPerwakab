<?php

use App\Services\PublikasiApiService;
use function Livewire\Volt\state;
use function Livewire\Volt\mount;

state([
    'publikasi' => null,
]);

mount(function ($id) {
    $response = PublikasiApiService::detail($id);

    $this->publikasi = $response->successful()
        ? $response->json('data')
        : null;
});
    ?>

<x-layouts.mobile title="Detail Publikasi">

    @if (!$publikasi)
        <div class="p-6 text-center text-sm text-gray-500">
            Memuat detail publikasi...
        </div>
    @else

        @php
            $photoUrls = collect($publikasi['photos'] ?? [])
                ->map(fn($p) => api_product_url($p['file_path']))
                ->filter()
                ->values();
        @endphp

        {{-- HEADER --}}
        <div class="bg-green-600 px-4 py-4 flex items-center space-x-3 rounded-b-2xl">
            <button onclick="window.history.back()">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M15 19l-7-7 7-7" />
                </svg>
            </button>
            <p class="text-white font-semibold text-base">Detail Publikasi</p>
        </div>

        <div class="px-4 mt-4 space-y-6">

            {{-- IMAGE CAROUSEL --}}
            <div class="relative">

                <div class="swiper publikasiSwiper">
                    <div class="swiper-wrapper">
                        @forelse ($photoUrls as $url)
                            <div class="swiper-slide">
                                <img src="{{ $url }}" class="w-full h-64 object-cover rounded-xl">
                            </div>
                        @empty
                            <div class="swiper-slide">
                                <img src="{{ asset('images/no-image.png') }}" class="w-full h-64 object-cover rounded-xl">
                            </div>
                        @endforelse
                    </div>


                    <div class="swiper-pagination"></div>
                </div>

                {{-- OVERLAY BUTTON --}}
                <button type="button" class="publikasi-prev absolute left-2 top-1/2 -translate-y-1/2 z-50">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M15 19l-7-7 7-7" />
                    </svg>
                </button>

                <button type="button" class="publikasi-next absolute right-2 top-1/2 -translate-y-1/2 z-50">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M9 5l7 7-7 7" />
                    </svg>

                </button>
            </div>

            {{-- CARD UTAMA --}}
            <div class="bg-white rounded-2xl shadow overflow-hidden">
                <div class="p-4 space-y-4">

                    <p class="text-base font-semibold text-gray-800">
                        {{ $publikasi['title'] }}
                    </p>

                    <p class="text-xs text-gray-500">
                        Oleh <strong>{{ $publikasi['creator'] }}</strong>
                    </p>

                    <hr>

                    <div>
                        <p class="font-semibold text-sm text-gray-800 mb-1">
                            Deskripsi Karya
                        </p>
                        <p class="text-sm text-gray-600 leading-relaxed">
                            {!! nl2br(e($publikasi['description'] ?: 'Tidak ada deskripsi.')) !!}
                        </p>
                    </div>

                    @if (!empty($publikasi['videos']))
                        <div class="space-y-2">
                            <p class="font-semibold text-sm text-gray-800">
                                Video Terkait
                            </p>

                            @foreach ($publikasi['videos'] as $video)
                                <a href="{{ $video['link'] }}" target="_blank"
                                    class="flex items-center space-x-2 text-green-600 text-sm font-semibold">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M8 5v14l11-7z" />
                                    </svg>
                                    <span>Lihat Video</span>
                                </a>
                            @endforeach
                        </div>
                    @endif

                </div>
            </div>

        </div>

        <div class="h-10"></div>

        {{-- SCRIPT (POLA ONBOARDING, AMAN LIVEWIRE) --}}
        <script>
            let publikasiSwiper = null;

            function initPublikasiSwiper() {
                const el = document.querySelector('.publikasiSwiper');
                if (!el || el.swiper) return;

                publikasiSwiper = new Swiper(el, {
                    pagination: {
                        el: '.swiper-pagination',
                    },
                });

                const btnNext = document.querySelector('.publikasi-next');
                const btnPrev = document.querySelector('.publikasi-prev');

                if (btnNext) btnNext.onclick = () => publikasiSwiper.slideNext();
                if (btnPrev) btnPrev.onclick = () => publikasiSwiper.slidePrev();
            }

            document.addEventListener('DOMContentLoaded', initPublikasiSwiper);
            document.addEventListener('livewire:navigated', initPublikasiSwiper);
        </script>

    @endif

    {{-- ==================== DESKTOP VIEW ==================== --}}
    <x-slot:desktop>
        <x-desktop.layout title="Detail Karya">
            <div class="max-w-4xl mx-auto">
                @if (!$publikasi)
                    <div class="flex flex-col items-center justify-center py-20 text-gray-400">
                        <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-green-600 mb-4"></div>
                        <p class="text-sm">Memuat detail publikasi...</p>
                    </div>
                @else
                    @php
                        $photoUrls = collect($publikasi['photos'] ?? [])
                            ->map(fn($p) => api_product_url($p['file_path']))
                            ->filter()
                            ->values();
                    @endphp

                    <div class="flex items-center gap-2 text-sm text-gray-400 mb-6">
                        <a href="{{ route('mobile.karya.index') }}" class="hover:text-green-600 transition">&larr; Kembali ke Karya</a>
                    </div>

                    {{-- IMAGE GALLERY --}}
                    @if($photoUrls->count())
                        <div class="grid grid-cols-{{ min($photoUrls->count(), 3) }} gap-3 mb-8">
                            @foreach($photoUrls->take(6) as $url)
                                <div class="rounded-2xl overflow-hidden shadow-lg">
                                    <img src="{{ $url }}" class="w-full h-64 object-cover hover:scale-105 transition-transform duration-300">
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- CONTENT --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                        <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ $publikasi['title'] }}</h1>
                        <p class="text-sm text-gray-500 mb-6">Oleh <strong>{{ $publikasi['creator'] }}</strong></p>

                        <hr class="mb-6">

                        <h3 class="font-semibold text-gray-800 mb-2">Deskripsi Karya</h3>
                        <div class="prose max-w-none text-gray-600 leading-relaxed">
                            {!! nl2br(e($publikasi['description'] ?: 'Tidak ada deskripsi.')) !!}
                        </div>

                        @if (!empty($publikasi['videos']))
                            <div class="mt-8 space-y-3">
                                <h3 class="font-semibold text-gray-800">Video Terkait</h3>
                                @foreach ($publikasi['videos'] as $video)
                                    <a href="{{ $video['link'] }}" target="_blank"
                                        class="inline-flex items-center gap-2 text-green-600 font-semibold hover:text-green-700 transition">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                        Lihat Video
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </x-desktop.layout>
    </x-slot:desktop>

</x-layouts.mobile>