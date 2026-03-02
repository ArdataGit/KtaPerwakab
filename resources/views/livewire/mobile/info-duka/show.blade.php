<?php

use App\Services\InfoDukaApiService;
use function Livewire\Volt\state;
use function Livewire\Volt\mount;

state([
    'item' => null,
]);

mount(function ($id) {
    $response = InfoDukaApiService::detail($id);

    $this->item = $response->successful()
        ? $response->json('data')
        : null;
});
?>
<x-layouts.mobile title="Info Duka">

    @if (!$item)
        <div class="p-6 text-center text-sm text-gray-500">
            Memuat info duka...
        </div>
    @else

        <!-- HEADER -->
        <div class="bg-green-600 px-4 py-4 flex items-center space-x-3 rounded-b-2xl">
            <button onclick="window.history.back()">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M15 19l-7-7 7-7" />
                </svg>
            </button>
            <p class="text-white font-semibold text-base">Info Duka</p>
        </div>

        <div class="px-4 mt-4  space-y-4">

            <!-- HEADER INFO -->
            <div class="flex items-center space-x-3">

                <div>
                    <p class="text-sm font-semibold text-gray-800">
                        Alm. {{ $item['nama_almarhum'] }}
                    </p>
                    <p class="text-xs text-gray-500">
                        {{ \Carbon\Carbon::parse($item['tanggal_wafat'])->format('d F Y') }}
                    </p>
                </div>
            </div>
            <div class="rounded-xl shadow p-6 min-h-screen" style="background-color: #80808029;">

                <!-- FOTO UTAMA -->
                <img src="{{ api_product_url($item['foto'] ?? null) }}" onerror="this.src='/images/assets/placeholder.png'"
                    class="w-full mb-3 h-56 object-cover rounded-xl ">

                <!-- CARD DETAIL -->
                <div class="bg-white rounded-xl shadow p-4 space-y-3">

                    <p class="text-base font-semibold text-gray-900">
                        {{ $item['judul'] }}
                    </p>

                    <div class="text-sm text-gray-700 leading-relaxed space-y-2">

                        {{-- ISI DARI EDITOR --}}
                        {!! $item['isi'] !!}

                    </div>

                    <!-- META -->
                    <div class="pt-3 border-t text-xs text-gray-600 space-y-1">
                        <p><strong>Usia:</strong> {{ $item['usia'] ? $item['usia'] . ' Tahun' : '-' }}</p>
                        <p><strong>Asal:</strong> {{ $item['asal'] ?? '-' }}</p>
                    </div>

                </div>
            </div>
        </div>

        <div class="h-24"></div>

        <!-- BOTTOM NAV -->
        <x-mobile.navbar active="info-duka" />

    @endif

    {{-- ==================== DESKTOP VIEW ==================== --}}
    <x-slot:desktop>
        <x-desktop.layout title="Info Duka">
            <div class="max-w-4xl mx-auto">

                @if (!$item)
                    <div class="flex flex-col items-center justify-center py-20 text-gray-400">
                        <svg class="animate-spin h-10 w-10 text-green-500 mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>
                        <p class="text-sm font-medium">Memuat info duka...</p>
                    </div>
                @else
                    {{-- BREADCRUMB --}}
                    <div class="flex items-center gap-2 text-sm text-gray-400 mb-6">
                        <a href="{{ route('mobile.info-duka.index') }}" class="hover:text-green-600 transition">&larr; Kembali ke Info Duka</a>
                    </div>

                    {{-- HEADER WITH PHOTO --}}
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        {{-- LEFT: Photo --}}
                        <div class="lg:col-span-1">
                            <div class="rounded-2xl overflow-hidden shadow-lg sticky top-8">
                                <img src="{{ api_product_url($item['foto'] ?? null) }}" onerror="this.src='/images/assets/placeholder.png'"
                                    class="w-full h-72 lg:h-auto object-cover">
                                {{-- Meta Card --}}
                                <div class="bg-white p-5 space-y-3">
                                    <h2 class="text-lg font-bold text-gray-900">Alm. {{ $item['nama_almarhum'] }}</h2>
                                    <div class="space-y-2 text-sm text-gray-600">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            <span>{{ \Carbon\Carbon::parse($item['tanggal_wafat'])->format('d F Y') }}</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                            <span>Usia: {{ $item['usia'] ? $item['usia'] . ' Tahun' : '-' }}</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                            <span>Asal: {{ $item['asal'] ?? '-' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- RIGHT: Content --}}
                        <div class="lg:col-span-2">
                            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                                <h1 class="text-2xl font-bold text-gray-900 mb-6">{{ $item['judul'] }}</h1>
                                <div class="prose prose-lg max-w-none text-gray-700 leading-relaxed">
                                    {!! $item['isi'] !!}
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

            </div>
        </x-desktop.layout>
    </x-slot:desktop>

</x-layouts.mobile>