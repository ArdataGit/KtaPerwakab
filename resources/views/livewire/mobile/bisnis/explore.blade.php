<?php

use App\Services\BisnisApiService;
use function Livewire\Volt\{state, mount, updated};

state([
    'bisnis' => [],
    'search' => '',
    'category' => '',
]);

mount(function () {
    $response = BisnisApiService::list([
        'per_page' => 12,
        'search' => $this->search ?: null,
        'category' => $this->category ?: null,
    ]);

    if ($response->successful()) {
        // ✅ LANGSUNG data, bukan data.data
        $this->bisnis = $response->json('data') ?? [];
    } else {
        $this->bisnis = [];
    }
});

updated([
    'search' => function () {
        $response = BisnisApiService::list([
            'per_page' => 12,
            'search' => $this->search ?: null,
        	'category' => $this->category ?: null,
        ]);

        if ($response->successful()) {
            $this->bisnis = $response->json('data') ?? [];
        } else {
            $this->bisnis = [];
        }
    },    
  'category' => function () {
        $response = BisnisApiService::list([
            'per_page' => 12,
            'search' => $this->search ?: null,
        	'category' => $this->category ?: null,
        ]);

        if ($response->successful()) {
            $this->bisnis = $response->json('data') ?? [];
        } else {
            $this->bisnis = [];
        }
    },
]);

?>
@php
    $kategoriBisnis = [
        'Kuliner',
        'Fashion',
        'Kerajinan',
        'Jasa',
        'Pertanian',
        'Perikanan',
        'Peternakan',
        'Perdagangan',
        'Industri Rumah Tangga',
        'Lainnya',
    ];
@endphp


<x-layouts.mobile title="Explore Bisnis">

    {{-- HEADER --}}
    <div class="bg-green-600 px-4 py-4 flex items-center gap-3 rounded-b-2xl">
        <button onclick="window.history.back()">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2"
                 viewBox="0 0 24 24">
                <path d="M15 19l-7-7 7-7" />
            </svg>
        </button>
        <p class="text-white font-semibold text-base">Explore di Sekitarmu</p>
    </div>

{{-- SEARCH & FILTER --}}
<div class="px-4 mt-4">
    <div class="flex items-center gap-2">

        <!-- SEARCH -->
        <div class="flex-1">
            <div class="relative">
                <svg class="w-4 h-4 absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"
                     fill="none" stroke="currentColor" stroke-width="2"
                     viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8"/>
                    <path d="M21 21l-4.35-4.35"/>
                </svg>

                <input
                    type="text"
                    wire:model.live.debounce.500ms="search"
                    placeholder="Cari bisnis di sekitarmu"
                    class="w-full h-11 pl-10 pr-4 rounded-full border text-sm
                           focus:outline-none focus:ring focus:ring-green-200"
                >
            </div>
        </div>

        <!-- FILTER CATEGORY -->
        <div class="shrink-0">
            <select
                wire:model.live="category"
                class="h-11 pl-3 pr-8 rounded-full border text-sm bg-white
                       {{ $category ? 'border-green-500 ring-1 ring-green-300' : '' }}
                       focus:outline-none focus:ring focus:ring-green-200">
                <option value="">Semua</option>
                @foreach ($kategoriBisnis as $kat)
                    <option value="{{ $kat }}">{{ $kat }}</option>
                @endforeach
            </select>
        </div>

    </div>
</div>



    {{-- TITLE --}}
    <div class="px-4 mt-4">
        <h3 class="font-semibold text-sm">Explore Bisnis di Sekitarmu</h3>
    </div>

    {{-- GRID BISNIS --}}
    <div class="px-4 mt-3 grid grid-cols-2 gap-4">

        @forelse ($bisnis as $item)
            @php
    $image = data_get($item, 'media.0.file_path')
        ? api_product_url(data_get($item, 'media.0.file_path'))
        : '/images/assets/placeholder.png';
@endphp


            <a href="{{ route('mobile.bisnis.show', $item['slug']) }}"
               class="rounded-xl overflow-hidden shadow bg-white
                      active:scale-[0.97] transition">

                {{-- IMAGE --}}
                <img
                    src="{{ $image }}"
                    onerror="this.src='/images/assets/placeholder.png'"
                    class="w-full h-32 object-cover"
                >

                {{-- INFO --}}
                <div class="p-2">
                    <p class="text-xs font-semibold line-clamp-1">
                        {{ $item['nama'] }}
                    </p>
                    <p class="text-[11px] text-gray-500 line-clamp-1">
                        {{ $item['kategori'] ?? 'Bisnis' }}
                    </p>
                </div>
            </a>

        @empty
            <div class="col-span-2 text-center text-sm text-gray-500 py-10">
                Bisnis belum tersedia
            </div>
        @endforelse

    </div>

    <div class="h-24"></div>

    {{-- BOTTOM NAV --}}
    <x-mobile.navbar active="explore" />

    {{-- ==================== DESKTOP VIEW ==================== --}}
    <x-slot:desktop>
        <x-desktop.layout title="Jejaring Bisnis">
            <div class="max-w-7xl mx-auto">
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900">Jejaring Bisnis</h1>
                    <p class="text-gray-500 mt-1">Explore dan temukan bisnis anggota Perwakab di sekitarmu.</p>
                </div>

                {{-- SEARCH + FILTER --}}
                <div class="flex flex-col md:flex-row gap-4 mb-8">
                    <div class="relative flex-1">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z"/></svg>
                        </span>
                        <input type="text" wire:model.live.debounce.500ms="search" placeholder="Cari bisnis di sekitarmu..."
                            class="w-full pl-12 pr-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-green-200 focus:border-green-500 transition">
                    </div>
                    <select wire:model.live="category"
                        class="w-full md:w-52 px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-green-200 focus:border-green-500 transition bg-white {{ $category ? 'border-green-500 ring-1 ring-green-300' : '' }}">
                        <option value="">Semua Kategori</option>
                        @foreach ($kategoriBisnis as $kat)
                            <option value="{{ $kat }}">{{ $kat }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- BUSINESS GRID --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @forelse ($bisnis as $item)
                        @php
                            $image = data_get($item, 'media.0.file_path')
                                ? api_product_url(data_get($item, 'media.0.file_path'))
                                : '/images/assets/placeholder.png';
                        @endphp
                        <a href="{{ route('mobile.bisnis.show', $item['slug']) }}"
                            class="group bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
                            <div class="relative h-44 overflow-hidden">
                                <img src="{{ $image }}" onerror="this.src='/images/assets/placeholder.png'"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                <div class="absolute top-3 right-3 bg-green-600 text-white text-[10px] font-bold px-3 py-1 rounded-full">
                                    {{ $item['kategori'] ?? 'Bisnis' }}
                                </div>
                            </div>
                            <div class="p-4">
                                <h3 class="font-semibold text-gray-900 leading-snug line-clamp-1 group-hover:text-green-700 transition-colors">{{ $item['nama'] }}</h3>
                                <p class="text-xs text-gray-500 mt-1 line-clamp-1">{{ $item['kategori'] ?? 'Bisnis' }}</p>
                            </div>
                        </a>
                    @empty
                        <div class="col-span-full flex flex-col items-center justify-center py-16 text-gray-400">
                            <svg class="w-16 h-16 mb-4 opacity-40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72m-13.5 8.65h3.75a.75.75 0 00.75-.75V13.5a.75.75 0 00-.75-.75H6.75a.75.75 0 00-.75.75v3.75c0 .415.336.75.75.75z"/></svg>
                            <p class="text-sm font-medium">Bisnis belum tersedia</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </x-desktop.layout>
    </x-slot:desktop>

</x-layouts.mobile>
