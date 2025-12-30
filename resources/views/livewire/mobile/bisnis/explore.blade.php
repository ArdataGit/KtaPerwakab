<?php

use App\Services\BisnisApiService;
use function Livewire\Volt\{state, mount, updated};

state([
    'bisnis' => [],
    'search' => '',
]);

mount(function () {
    $response = BisnisApiService::list([
        'per_page' => 12,
        'search' => $this->search ?: null,
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
        ]);

        if ($response->successful()) {
            $this->bisnis = $response->json('data') ?? [];
        } else {
            $this->bisnis = [];
        }
    },
]);

?>

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

    {{-- SEARCH --}}
    <div class="px-4 mt-4">
        <div class="flex items-center gap-2">
            <div class="flex-1 relative">
                <input
                    type="text"
                    wire:model.live="search"
                    placeholder="Cari apapun disini"
                    class="w-full pl-10 pr-4 py-2 rounded-full border text-sm
                           focus:outline-none focus:ring focus:ring-green-200"
                >
                <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"
                     fill="none" stroke="currentColor" stroke-width="2"
                     viewBox="0 0 24 24">
                    <path d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z" />
                </svg>
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

</x-layouts.mobile>
