<?php

use App\Services\DonationCampaignApiService;
use function Livewire\Volt\{state, mount};

state([
    'campaign' => null,
    'donationHistories' => [],
]);

mount(function ($id) {
    $res = DonationCampaignApiService::detail($id);

    if ($res->successful()) {
        $campaign = $res->json('data');

        $this->campaign = $campaign;

        // hanya ambil donasi PAID
        $this->donationHistories = array_values(array_filter(
            $campaign['donations'] ?? [],
            fn ($donation) => $donation['status'] === 'PAID'
        ));
    }
});
?>
<x-layouts.mobile title="Riwayat Donasi">

    {{-- HEADER --}}
    <div class="w-full bg-green-600 px-4 py-4 flex items-center space-x-3 rounded-b-2xl">
        <button onclick="window.history.back()">
            <img src="/images/assets/icon/back.svg" class="w-5 h-5">
        </button>
        <p class="text-white font-semibold text-base">Riwayat Donasi</p>
    </div>

    <div class="px-4 mt-4 space-y-4">

        {{-- INFO CAMPAIGN --}}
        @if ($campaign)
            <div class="bg-white rounded-xl p-4 shadow-sm">
                <p class="text-sm font-semibold text-gray-800">
                    {{ $campaign['title'] }}
                </p>
                <p class="text-xs text-gray-500 mt-1">
                    Total Donasi Terkumpul
                </p>
                <p class="text-lg font-bold text-green-600">
                    Rp{{ number_format($campaign['total_collected'], 0, ',', '.') }}
                </p>
            </div>
        @endif

        {{-- LIST DONASI --}}
        <div class="space-y-2">

            @forelse ($donationHistories as $donation)
                @php
                    $name = $donation['donor_name'] ?? 'Hamba Allah';
                    $initial = strtoupper(substr($name, 0, 1));
                @endphp

                <div class="bg-white rounded-xl p-4 shadow-sm flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        {{-- AVATAR --}}
                        <div class="w-10 h-10 rounded-full bg-green-100
                                    text-green-700 flex items-center justify-center font-bold">
                            {{ $initial }}
                        </div>

                        {{-- INFO --}}
                        <div>
                            <p class="text-sm font-medium text-gray-800">
                                {{ $name }}
                            </p>
                            <p class="text-xs text-gray-500">
                                {{ \Carbon\Carbon::parse($donation['created_at'])->diffForHumans() }}
                            </p>
                        </div>
                    </div>

                    {{-- NOMINAL --}}
                    <div class="text-right">
                        <p class="text-sm font-semibold text-green-600">
                            Rp{{ number_format($donation['amount'], 0, ',', '.') }}
                        </p>
                        <span class="text-[10px] text-gray-400">
                            Donasi
                        </span>
                    </div>
                </div>

            @empty
                <div class="bg-white rounded-xl p-4 text-center text-sm text-gray-500">
                    Belum ada riwayat donasi.
                </div>
            @endforelse

        </div>

    </div>

    <div class="h-24"></div>

    <x-mobile.navbar active="donasi" />

    {{-- ==================== DESKTOP VIEW ==================== --}}
    <x-slot:desktop>
        <x-desktop.layout title="Riwayat Donasi">
            <div class="max-w-4xl mx-auto">
                <div class="flex items-center gap-2 text-sm text-gray-400 mb-6">
                    <a href="javascript:history.back()" class="hover:text-green-600 transition">&larr; Kembali</a>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 mb-8">Riwayat Donasi</h1>

                @if ($campaign)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
                        <p class="font-semibold text-gray-800">{{ $campaign['title'] }}</p>
                        <p class="text-xs text-gray-500 mt-1">Total Donasi Terkumpul</p>
                        <p class="text-2xl font-bold text-green-600 mt-1">Rp{{ number_format($campaign['total_collected'], 0, ',', '.') }}</p>
                    </div>
                @endif

                <div class="space-y-3">
                    @forelse ($donationHistories as $donation)
                        @php $name = $donation['donor_name'] ?? 'Hamba Allah'; $initial = strtoupper(substr($name, 0, 1)); @endphp
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex items-center justify-between hover:shadow-md transition">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-full bg-green-100 text-green-700 flex items-center justify-center font-bold">{{ $initial }}</div>
                                <div>
                                    <p class="text-sm font-medium text-gray-800">{{ $name }}</p>
                                    <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($donation['created_at'])->diffForHumans() }}</p>
                                </div>
                            </div>
                            <p class="text-sm font-semibold text-green-600">Rp{{ number_format($donation['amount'], 0, ',', '.') }}</p>
                        </div>
                    @empty
                        <div class="bg-white rounded-xl p-8 text-center text-sm text-gray-500 border border-gray-100">Belum ada riwayat donasi.</div>
                    @endforelse
                </div>
            </div>
        </x-desktop.layout>
    </x-slot:desktop>

</x-layouts.mobile>
