<?php

use App\Services\DonationCampaignApiService;
use function Livewire\Volt\state;
use function Livewire\Volt\mount;

state([
    'campaigns' => [],
    'keyword' => '',
]);

mount(function () {
    $response = DonationCampaignApiService::list();
    if ($response->successful()) {
        $this->campaigns = $response->json('data.campaigns') ?? [];
    }
});
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
                <input type="text" placeholder="Cari" wire:model.debounce.500ms="keyword" class="w-full pl-10 pr-4 py-2 rounded-full border text-sm
                           focus:outline-none focus:ring focus:ring-green-200">

                <span class="absolute left-3 top-1/2 -translate-y-1/2">
                    <img src="/images/assets/icon/search.svg" class="w-4 h-4 opacity-60">
                </span>
            </div>

            <button class="w-10 h-10 rounded-full border flex items-center justify-center">
                <img src="/images/assets/icon/filter.svg" class="w-4 h-4">
            </button>
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

</x-layouts.mobile>