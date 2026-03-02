<?php

use App\Services\PublikasiApiService;
use function Livewire\Volt\{state, mount, updated};

state([
    'publikasi' => [],
    'search' => '',
]);

mount(function () {
    $response = PublikasiApiService::list([
        'per_page' => 10,
        'search' => $this->search ?: null,
    ]);

    if ($response->successful()) {
        $this->publikasi = $response->json('data.data') ?? [];
    }
});


updated([
    'search' => function () {

        $response = PublikasiApiService::list([
            'per_page' => 10,
            'search' => $this->search ?: null,
        ]);

        if ($response->successful()) {
            $this->publikasi = $response->json('data.data') ?? [];
        } else {
            $this->publikasi = [];
        }
    },
]);

?>


<x-layouts.mobile title="Publikasi">

    <!-- HEADER -->
    <div class="bg-green-600 px-4 py-4 flex items-center space-x-3 rounded-b-2xl">
        <button onclick="window.history.back()">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M15 19l-7-7 7-7" />
            </svg>
        </button>
        <p class="text-white font-semibold text-base">Karya Publikasi</p>
    </div>

    <div class="px-4 mt-4 space-y-4">

        <!-- BANNER -->
        <div class="rounded-xl overflow-hidden shadow">
            <img src="/images/assets/banner.png" class="w-full h-36 object-cover">
        </div>

        <!-- SEARCH (UI ONLY) -->
        <div class="flex items-center space-x-2">
            <input
    type="text"
    wire:model.live="search"
    placeholder="Cari karya"
    class="flex-1 px-4 py-2 rounded-full border text-sm
           focus:outline-none focus:ring focus:ring-green-200"
/>
        </div>

        <!-- PUBLIKASI GRID -->
        <div class="grid grid-cols-2 gap-4">

            @forelse ($publikasi as $item)

                @php
                    $image = api_product_url(
                        data_get($item, 'photos.0.file_path')
                    );
                @endphp

                <a href="{{ route('mobile.karya.show', $item['id']) }}"
                    class="rounded-2xl overflow-hidden shadow bg-green-600 text-white active:scale-[0.98] transition">

                    {{-- HEADER AUTHOR --}}
                    <div class="flex items-center space-x-2 px-3 pt-3 pb-2">
                        <div
                            class="w-8 h-8 rounded-full bg-white/90 flex items-center justify-center text-green-600 font-bold text-xs">
                            {{ strtoupper(substr($item['creator'], 0, 1)) }}
                        </div>

                        <div class="leading-tight">
                            <p class="text-xs font-semibold">
                                {{ $item['creator'] }}
                            </p>
                        </div>
                    </div>

                    {{-- IMAGE --}}
                    @php
                        $image = api_product_url(data_get($item, 'photos.0.file_path'));
                    @endphp

                    <div class="px-3">
                        <img src="{{ $image }}" onerror="this.src='/images/assets/placeholder.png'"
                            class="w-full h-40 object-cover rounded-xl bg-white">
                    </div>

                    {{-- CONTENT --}}
                    <div class="px-3 pt-2 pb-3 space-y-1">
                        <p class="text-sm font-semibold line-clamp-1">
                            {{ $item['title'] }}
                        </p>

                        <p class="text-xs text-white/90 line-clamp-2">
                            {{ Str::limit(strip_tags($item['description']), 70) }}
                        </p>

                        <p class="text-[10px] text-white/70 mt-1">
                            {{ \Carbon\Carbon::parse($item['created_at'])->translatedFormat('d F Y') }}
                        </p>
                    </div>

                </a>


            @empty
                <div class="col-span-2 text-center text-sm text-gray-500 py-10">
                    Publikasi Karya belum tersedia
                </div>
            @endforelse

        </div>

    </div>

    <div class="h-24"></div>

    <!-- BOTTOM NAV -->
    <x-mobile.navbar active="publikasi" />

    {{-- ==================== DESKTOP VIEW ==================== --}}
    <x-slot:desktop>
        <x-desktop.layout title="Karya Anggota">
            <div class="max-w-7xl mx-auto">
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900">Karya & Publikasi Anggota</h1>
                    <p class="text-gray-500 mt-1">Jelajahi karya-karya terbaik dari anggota Perwakab Batam.</p>
                </div>

                {{-- SEARCH --}}
                <div class="relative max-w-xl mb-8">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z"/></svg>
                    </span>
                    <input type="text" wire:model.live="search" placeholder="Cari karya publikasi..."
                        class="w-full pl-12 pr-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-green-200 focus:border-green-500 transition">
                </div>

                {{-- GRID --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse ($publikasi as $item)
                        @php
                            $image = api_product_url(data_get($item, 'photos.0.file_path'));
                        @endphp
                        <a href="{{ route('mobile.karya.show', $item['id']) }}"
                            class="group bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg hover:-translate-y-1 transition-all duration-300">

                            {{-- Author Header --}}
                            <div class="bg-green-600 px-4 py-3 flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-white/90 flex items-center justify-center text-green-600 font-bold text-sm">
                                    {{ strtoupper(substr($item['creator'], 0, 1)) }}
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-white">{{ $item['creator'] }}</p>
                                    <p class="text-xs text-white/70">{{ \Carbon\Carbon::parse($item['created_at'])->translatedFormat('d F Y') }}</p>
                                </div>
                            </div>

                            {{-- Image --}}
                            <div class="relative h-52 overflow-hidden">
                                <img src="{{ $image }}" onerror="this.src='/images/assets/placeholder.png'"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            </div>

                            {{-- Content --}}
                            <div class="p-5">
                                <h3 class="font-bold text-gray-900 leading-snug group-hover:text-green-700 transition-colors">{{ $item['title'] }}</h3>
                                <p class="text-sm text-gray-500 mt-2 line-clamp-2">{{ Str::limit(strip_tags($item['description']), 100) }}</p>
                            </div>
                        </a>
                    @empty
                        <div class="col-span-full flex flex-col items-center justify-center py-16 text-gray-400">
                            <svg class="w-16 h-16 mb-4 opacity-40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>
                            <p class="text-sm font-medium">Publikasi karya belum tersedia</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </x-desktop.layout>
    </x-slot:desktop>

</x-layouts.mobile>