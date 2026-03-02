<?php

use App\Services\DonationCampaignApiService;
use function Livewire\Volt\{state, mount, updated};

state([
    'campaigns' => [],
    'keyword' => '',
]);

mount(function () {
    $response = DonationCampaignApiService::list([
        'search' => $this->keyword ?: null,
    ]);

    if ($response->successful()) {
        $this->campaigns = $response->json('data.campaigns') ?? [];
    } else {
        $this->campaigns = [];
    }
});

updated([
    'keyword' => function () {
        $response = DonationCampaignApiService::list([
            'search' => $this->keyword ?: null,
        ]);

        if ($response->successful()) {
            $this->campaigns = $response->json('data.campaigns') ?? [];
        } else {
            $this->campaigns = [];
        }
    },
]);
?>


<x-layouts.mobile title="Donasi">

    {{-- HEADER --}}
    <div class="w-full bg-green-600 px-4 py-4 flex items-center space-x-3 rounded-b-2xl">
        <button onclick="window.history.back()">
            <img src="/images/assets/icon/back.svg" class="w-5 h-5">
        </button>
        <p class="text-white font-semibold text-base">Donasi</p>
    </div>

    <div class="px-4 mt-4 space-y-4">

        {{-- SEARCH --}}
        <div class="flex items-center space-x-2">
            <div class="flex-1 relative">
                <input
    type="text"
    placeholder="Cari"
    wire:model.live="keyword"
    class="w-full pl-10 pr-4 py-2 rounded-full border text-sm
           focus:outline-none focus:ring focus:ring-green-200"
/>

                <span class="absolute left-3 top-1/2 -translate-y-1/2">
                    <img src="/images/assets/icon/search.svg" class="w-4 h-4 opacity-60">
                </span>
            </div>

        </div>

        {{-- CAMPAIGN LIST --}}
        <div class="space-y-3">
            @forelse ($campaigns as $item)
                @php
                    $image = api_product_url($item['thumbnail'] ?? null);
                @endphp

                <div wire:key="campaign-{{ $item['id'] }}" class="bg-white rounded-xl p-2 shadow-sm flex space-x-3">
                    {{-- THUMBNAIL --}}
                    <img src="{{ $image }}" class="w-24 h-20 rounded-lg object-cover" alt="{{ $item['title'] }}">

                    {{-- CONTENT --}}
                    <div class="flex-1 flex flex-col justify-between">
                        <div>
                            <p class="font-semibold text-sm text-gray-800 leading-snug">
                                {{ $item['title'] }}
                            </p>

                            <p class="text-xs text-gray-500 mt-1">
                                Terkumpul
                            </p>

                            <p class="text-sm font-semibold text-gray-800">
                                Rp{{ number_format($item['total_collected'], 0, ',', '.') }}
                            </p>
                        </div>

                        <div class="flex justify-end">
                            <a href="{{ route('mobile.donation.detail', $item['id']) }}" class="px-4 py-1.5 text-xs font-semibold
                                                       bg-green-600 text-white rounded-full">
                                Donasi
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-xl p-4 text-center text-sm text-gray-500">
                    Belum ada campaign donasi.
                </div>
            @endforelse

        </div>

    </div>

    <div class="h-20"></div>

    {{-- BOTTOM NAV --}}
    <x-mobile.navbar active="donasi" />

    {{-- ==================== DESKTOP VIEW ==================== --}}
    <x-slot:desktop>
        <x-desktop.layout title="Donasi Peduli">
            <div class="max-w-7xl mx-auto">
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900">Donasi & Peduli</h1>
                    <p class="text-gray-500 mt-1">Bantu sesama melalui donasi untuk campaign yang tersedia.</p>
                </div>

                {{-- SEARCH --}}
                <div class="relative max-w-xl mb-8">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z"/></svg>
                    </span>
                    <input type="text" wire:model.live="keyword" placeholder="Cari campaign donasi..."
                        class="w-full pl-12 pr-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-green-200 focus:border-green-500 transition">
                </div>

                {{-- CAMPAIGN GRID --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse ($campaigns as $item)
                        @php $image = api_product_url($item['thumbnail'] ?? null); @endphp

                        <div wire:key="desktop-campaign-{{ $item['id'] }}"
                            class="group bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
                            <div class="relative h-48 overflow-hidden">
                                <img src="{{ $image }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" alt="{{ $item['title'] }}">
                            </div>
                            <div class="p-5 space-y-3">
                                <h3 class="font-bold text-gray-900 leading-snug line-clamp-2">{{ $item['title'] }}</h3>

                                {{-- Progress --}}
                                <div class="space-y-1">
                                    <div class="bg-gray-200 rounded-full h-2">
                                        <div class="bg-green-500 h-2 rounded-full" style="width: {{ min(($item['total_collected'] / max($item['target_amount'] ?? 1000000, 1)) * 100, 100) }}%"></div>
                                    </div>
                                    <div class="flex justify-between text-xs text-gray-500">
                                        <span>Terkumpul</span>
                                        <span class="font-semibold text-green-600">Rp{{ number_format($item['total_collected'], 0, ',', '.') }}</span>
                                    </div>
                                </div>

                                <a href="{{ route('mobile.donation.detail', $item['id']) }}"
                                    class="block w-full text-center bg-green-600 hover:bg-green-700 text-white py-2.5 rounded-xl font-semibold text-sm transition">
                                    Donasi Sekarang
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full flex flex-col items-center justify-center py-16 text-gray-400">
                            <svg class="w-16 h-16 mb-4 opacity-40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/></svg>
                            <p class="text-sm font-medium">Belum ada campaign donasi</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </x-desktop.layout>
    </x-slot:desktop>

</x-layouts.mobile>