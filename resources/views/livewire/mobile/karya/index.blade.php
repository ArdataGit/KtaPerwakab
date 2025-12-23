<?php

use App\Services\PublikasiApiService;
use function Livewire\Volt\state;
use function Livewire\Volt\mount;

state([
    'publikasi' => [],
]);

mount(function () {
    $response = PublikasiApiService::list([
        'per_page' => 10
    ]);

    if ($response->successful()) {
        $this->publikasi = $response->json('data.data') ?? [];
    } else {
        $this->publikasi = [];
    }
});
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
            <input type="text" placeholder="Cari karya"
                class="flex-1 px-4 py-2 rounded-full border text-sm focus:ring focus:ring-green-200">

            <button class="bg-green-600 text-white px-4 py-2 rounded-full text-sm font-semibold">
                Tampilkan
            </button>
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

</x-layouts.mobile>