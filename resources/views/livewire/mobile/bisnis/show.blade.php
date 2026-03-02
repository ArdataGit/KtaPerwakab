<?php

use App\Services\BisnisApiService;
use function Livewire\Volt\{state, mount};

state([
    'bisnis' => null,
    'error' => null,
]);

mount(function ($slug) {
    $response = BisnisApiService::show($slug);

    if ($response->successful()) {
        $this->bisnis = $response->json('data');
    } else {
        $this->error = 'Data bisnis tidak ditemukan';
    }
});
?>
<x-layouts.mobile title="Explore di Sekitarmu">

    {{-- HEADER IMAGE --}}
    <div class="relative">
        <button onclick="window.history.back()"
                class="absolute top-4 left-4 z-10 bg-black/40 rounded-full p-2">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2"
                 viewBox="0 0 24 24">
                <path d="M15 19l-7-7 7-7" />
            </svg>
        </button>

        @php
            $cover = collect($bisnis['media'] ?? [])
                ->firstWhere('type', 'image');
        @endphp

        <img
            src="{{ $cover ? api_product_url($cover['file_path']) : '/images/assets/placeholder.png' }}"
            class="w-full h-56 object-cover"
        />
    </div>

    {{-- CONTENT --}}
    <div class="px-4 -mt-6 relative z-10">

        <div class="bg-white rounded-2xl p-4 shadow space-y-4">

            {{-- TITLE --}}
            <div>
                <h2 class="font-bold text-lg">{{ $bisnis['nama'] ?? '-' }}</h2>
                <p class="text-sm text-gray-500">{{ $bisnis['kategori'] ?? 'Bisnis' }}</p>
            </div>

            {{-- ALAMAT --}}
            @if(!empty($bisnis['alamat']))
                <div class="flex items-start gap-3 bg-gray-100 rounded-xl p-3">
                    <svg class="w-5 h-5 text-green-600 mt-0.5" fill="none" stroke="currentColor" stroke-width="2"
                         viewBox="0 0 24 24">
                        <path d="M12 21s8-4.5 8-11a8 8 0 10-16 0c0 6.5 8 11 8 11z" />
                        <circle cx="12" cy="10" r="3" />
                    </svg>
                    <p class="text-sm text-gray-700">
                        {{ $bisnis['alamat'] }}
                    </p>
                </div>
            @endif

            {{-- TENTANG --}}
            <div>
                <h3 class="font-semibold text-sm mb-1">Tentang</h3>
                <div class="text-sm text-gray-700 leading-relaxed">
                    {!! $bisnis['deskripsi'] ?? '-' !!}
                </div>
            </div>

            {{-- GALERI --}}
            @php
                $images = collect($bisnis['media'] ?? [])
                    ->where('type', 'image')
                    ->values();
            @endphp

            @if($images->count())
                <div>
                    <h3 class="font-semibold text-sm mb-2">Galeri</h3>

                    <div class="grid grid-cols-3 gap-2">
                        @foreach($images->take(5) as $img)
                            <img
                                src="{{ api_product_url($img['file_path']) }}"
                                onerror="this.src='/images/assets/placeholder.png'"
                                class="w-full h-24 object-cover rounded-lg"
                            />
                        @endforeach

                        @if($images->count() > 5)
                            <div class="flex items-center justify-center bg-gray-100 rounded-lg text-xs text-gray-600">
                                +{{ $images->count() - 5 }} lainnya
                            </div>
                        @endif
                    </div>
                </div>
            @endif
            @php
            function youtubeId($url) {
                preg_match(
                    '/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/))([\w-]+)/',
                    $url,
                    $matches
                );
                return $matches[1] ?? null;
            }
            @endphp
          	{{-- VIDEO --}}
            @php
                $videos = collect($bisnis['media'] ?? [])
                    ->where('type', 'youtube')
                    ->values();
            @endphp

            @if($videos->count())
                <div>
                    <h3 class="font-semibold text-sm mb-2">Video</h3>

                    @foreach($videos as $vid)
                        @php
                            $ytId = youtubeId($vid['url']);
                        @endphp

                        @if($ytId)
                            <a href="{{ $vid['url'] }}"
                               target="_blank"
                               class="block mb-3 rounded-xl overflow-hidden relative bg-black">

                                <img
                                    src="https://img.youtube.com/vi/{{ $ytId }}/hqdefault.jpg"
                                    class="w-full aspect-video object-cover opacity-90">

                                {{-- PLAY ICON --}}
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <div class="bg-black/60 text-white rounded-full p-4 text-xl">
                                        ▶
                                    </div>
                                </div>

                                <div class="absolute bottom-0 w-full bg-black/60
                                            text-white text-xs px-3 py-2">
                                    Tonton di YouTube
                                </div>
                            </a>
                        @endif
                    @endforeach
                </div>
            @endif


            {{-- WHATSAPP --}}
            @if(!empty($bisnis['telepon']))
                @php
                    $wa = preg_replace('/[^0-9]/', '', $bisnis['telepon']);
                    if (substr($wa, 0, 1) === '0') {
                        $wa = '62' . substr($wa, 1);
                    }
                @endphp

                <a href="https://wa.me/{{ $wa }}"
                   target="_blank"
                   class="flex items-center justify-center gap-2 bg-green-600
                          text-white py-3 rounded-xl font-semibold">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M20.52 3.48A11.91 11.91 0 0012.04 0C5.43 0 .16 5.27.16 11.87c0 2.09.55 4.14 1.6 5.94L0 24l6.38-1.67a11.84 11.84 0 005.66 1.45h.01c6.6 0 11.87-5.27 11.87-11.87a11.8 11.8 0 00-3.4-8.43z"/>
                    </svg>
                    Hubungi via WhatsApp
                </a>
            @endif

        </div>
    </div>

    <div class="h-24"></div>

    <x-mobile.navbar active="explore" />

    {{-- ==================== DESKTOP VIEW ==================== --}}
    <x-slot:desktop>
        <x-desktop.layout title="Detail Bisnis">
            <div class="max-w-5xl mx-auto">
                <div class="flex items-center gap-2 text-sm text-gray-400 mb-6">
                    <a href="{{ route('mobile.bisnis.explore') }}" class="hover:text-green-600 transition">&larr; Kembali ke Jejaring Bisnis</a>
                </div>

                @php
                    $cover = collect($bisnis['media'] ?? [])->firstWhere('type', 'image');
                @endphp

                {{-- HERO --}}
                <div class="rounded-2xl overflow-hidden shadow-lg mb-8">
                    <img src="{{ $cover ? api_product_url($cover['file_path']) : '/images/assets/placeholder.png' }}"
                         class="w-full h-80 object-cover">
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    {{-- LEFT: Info --}}
                    <div class="lg:col-span-2 space-y-6">
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                            <h1 class="text-2xl font-bold text-gray-900 mb-1">{{ $bisnis['nama'] ?? '-' }}</h1>
                            <p class="text-sm text-gray-500 mb-6">{{ $bisnis['kategori'] ?? 'Bisnis' }}</p>

                            @if(!empty($bisnis['alamat']))
                                <div class="flex items-start gap-3 bg-gray-50 rounded-xl p-4 mb-6">
                                    <svg class="w-5 h-5 text-green-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 21s8-4.5 8-11a8 8 0 10-16 0c0 6.5 8 11 8 11z"/><circle cx="12" cy="10" r="3"/></svg>
                                    <p class="text-sm text-gray-700">{{ $bisnis['alamat'] }}</p>
                                </div>
                            @endif

                            <h3 class="font-semibold text-gray-800 mb-2">Tentang</h3>
                            <div class="prose max-w-none text-sm text-gray-700 leading-relaxed">
                                {!! $bisnis['deskripsi'] ?? '-' !!}
                            </div>
                        </div>

                        {{-- VIDEOS --}}
                        @php
                            $videos = collect($bisnis['media'] ?? [])->where('type', 'youtube')->values();
                        @endphp
                        @if($videos->count())
                            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                                <h3 class="font-semibold text-gray-800 mb-4">Video</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    @foreach($videos as $vid)
                                        @php $ytId = youtubeId($vid['url']); @endphp
                                        @if($ytId)
                                            <a href="{{ $vid['url'] }}" target="_blank"
                                                class="block rounded-xl overflow-hidden relative bg-black group">
                                                <img src="https://img.youtube.com/vi/{{ $ytId }}/hqdefault.jpg"
                                                    class="w-full aspect-video object-cover opacity-90 group-hover:opacity-75 transition">
                                                <div class="absolute inset-0 flex items-center justify-center">
                                                    <div class="bg-black/60 text-white rounded-full p-4">▶</div>
                                                </div>
                                            </a>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- RIGHT: Gallery + CTA --}}
                    <div class="space-y-6">
                        @php
                            $images = collect($bisnis['media'] ?? [])->where('type', 'image')->values();
                        @endphp
                        @if($images->count())
                            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                                <h3 class="font-semibold text-gray-800 mb-3">Galeri</h3>
                                <div class="grid grid-cols-2 gap-2">
                                    @foreach($images->take(6) as $img)
                                        <img src="{{ api_product_url($img['file_path']) }}"
                                            onerror="this.src='/images/assets/placeholder.png'"
                                            class="w-full h-28 object-cover rounded-lg">
                                    @endforeach
                                </div>
                                @if($images->count() > 6)
                                    <p class="text-xs text-gray-400 mt-2 text-center">+{{ $images->count() - 6 }} lainnya</p>
                                @endif
                            </div>
                        @endif

                        @if(!empty($bisnis['telepon']))
                            @php
                                $wa = preg_replace('/[^0-9]/', '', $bisnis['telepon']);
                                if (substr($wa, 0, 1) === '0') { $wa = '62' . substr($wa, 1); }
                            @endphp
                            <a href="https://wa.me/{{ $wa }}" target="_blank"
                                class="flex items-center justify-center gap-2 w-full bg-green-600 hover:bg-green-700 text-white py-4 rounded-xl font-semibold transition shadow-md shadow-green-200">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M20.52 3.48A11.91 11.91 0 0012.04 0C5.43 0 .16 5.27.16 11.87c0 2.09.55 4.14 1.6 5.94L0 24l6.38-1.67a11.84 11.84 0 005.66 1.45h.01c6.6 0 11.87-5.27 11.87-11.87a11.8 11.8 0 00-3.4-8.43z"/></svg>
                                Hubungi via WhatsApp
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </x-desktop.layout>
    </x-slot:desktop>

</x-layouts.mobile>
