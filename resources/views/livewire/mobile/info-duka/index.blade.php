<?php

use App\Services\InfoDukaApiService;
use function Livewire\Volt\{state, mount};

state([
    'items' => [],
    'q' => '',
    'year' => '',
]);

$load = function () {
    $res = InfoDukaApiService::list([
        'search' => $this->q,
        'tahun' => $this->year,
        'per_page' => 10,
    ]);

    $this->items = $res->successful()
        ? $res->json('data.data') ?? []
        : [];
};

mount(fn() => $this->load());
?>

<x-layouts.mobile title="Info Duka">

    {{-- HEADER --}}
    <div class="bg-green-600 px-4 py-4 flex items-center gap-3 rounded-b-2xl">
        <button onclick="history.back()">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M15 19l-7-7 7-7" />
            </svg>
        </button>
        <p class="text-white font-semibold text-base">Info Duka</p>
    </div>

    <div class="px-4 mt-4 space-y-4">

        {{-- SEARCH + FILTER --}}
        <div class="flex gap-2 w-full max-w-full overflow-x-hidden">

          <input
              wire:model.defer="q"
              placeholder="Cari nama"
              class="flex-1 min-w-0 px-4 py-2 rounded-full border text-sm"
          >

          <select
              wire:model.defer="year"
              class="w-[90px] max-w-[90px] px-3 py-2 rounded-full border text-sm"
          >
              <option value="">Tahun</option>
              @for ($y = now()->year; $y >= 2018; $y--)
                  <option value="{{ $y }}">{{ $y }}</option>
              @endfor
          </select>

          <button
              wire:click="load"
              class="shrink-0 bg-green-600 text-white px-4 py-2 rounded-full text-sm"
          >
              Cari
          </button>
      </div>


        {{-- GRID --}}
        <div class="grid grid-cols-2 gap-4">

            @forelse ($items as $item)
                @php $img = api_product_url($item['foto'] ?? null); @endphp

                <a href="{{ route('mobile.info-duka.show', $item['id']) }}"
                    class="bg-white rounded-xl shadow-sm overflow-hidden">

                    <img src="{{ $img }}" onerror="this.src='/images/assets/placeholder.png'"
                        class="w-full h-32 object-cover">

                    <div class="p-2 text-xs space-y-1">
                        <p class="font-semibold text-gray-800 line-clamp-2">
                            {{ $item['nama_almarhum'] }}
                        </p>

                        <p class="text-gray-500">
                            {{ $item['usia'] ? $item['usia'] . ' th' : '-' }}
                            • {{ $item['asal'] ?? '-' }}
                        </p>

                        <p class="text-gray-600">
                            {{ \Carbon\Carbon::parse($item['tanggal_wafat'])->format('d M Y') }}
                        </p>
                    </div>
                </a>
            @empty
                <div class="col-span-2 text-center text-sm text-gray-500 py-8">
                    Data tidak ditemukan
                </div>
            @endforelse

        </div>

    </div>

    <div class="h-24"></div>
    <x-mobile.navbar active="info-duka" />

    {{-- ==================== DESKTOP VIEW ==================== --}}
    <x-slot:desktop>
        <x-desktop.layout title="Info Duka">
            <div class="max-w-7xl mx-auto">

                {{-- PAGE HEADER --}}
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900">Info Duka Cita</h1>
                    <p class="text-gray-500 mt-1">Informasi duka cita anggota Perwakab Batam.</p>
                </div>

                {{-- SEARCH + FILTER --}}
                <div class="flex flex-col md:flex-row gap-4 mb-8">
                    <div class="relative flex-1">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z"/></svg>
                        </span>
                        <input wire:model.defer="q" placeholder="Cari nama almarhum..."
                            class="w-full pl-12 pr-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-green-200 focus:border-green-500 transition">
                    </div>
                    <select wire:model.defer="year"
                        class="w-full md:w-40 px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-green-200 focus:border-green-500 transition bg-white">
                        <option value="">Semua Tahun</option>
                        @for ($y = now()->year; $y >= 2018; $y--)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endfor
                    </select>
                    <button wire:click="load"
                        class="shrink-0 bg-green-600 hover:bg-green-700 text-white px-8 py-3 rounded-xl text-sm font-semibold transition-all duration-200 hover:-translate-y-0.5 shadow-md shadow-green-200">
                        Cari
                    </button>
                </div>

                {{-- GRID --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @forelse ($items as $item)
                        @php $img = api_product_url($item['foto'] ?? null); @endphp

                        <a href="{{ route('mobile.info-duka.show', $item['id']) }}"
                            class="group bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg hover:-translate-y-1 transition-all duration-300">

                            <div class="relative h-48 overflow-hidden">
                                <img src="{{ $img }}" onerror="this.src='/images/assets/placeholder.png'"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent"></div>
                                <div class="absolute bottom-3 left-3 right-3">
                                    <p class="text-white font-bold text-sm leading-snug line-clamp-2">{{ $item['nama_almarhum'] }}</p>
                                </div>
                            </div>

                            <div class="p-4 space-y-1">
                                <div class="flex items-center gap-2 text-xs text-gray-500">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    <span>{{ $item['usia'] ? $item['usia'] . ' tahun' : '-' }}</span>
                                    <span class="text-gray-300">•</span>
                                    <span>{{ $item['asal'] ?? '-' }}</span>
                                </div>
                                <div class="flex items-center gap-2 text-xs text-gray-500">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <span>{{ \Carbon\Carbon::parse($item['tanggal_wafat'])->format('d M Y') }}</span>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="col-span-full flex flex-col items-center justify-center py-16 text-gray-400">
                            <svg class="w-16 h-16 mb-4 opacity-40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M15.182 15.182a4.5 4.5 0 01-6.364 0M21 12a9 9 0 11-18 0 9 9 0 0118 0zM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75zm-.375 0h.008v.015h-.008V9.75zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75zm-.375 0h.008v.015h-.008V9.75z"/></svg>
                            <p class="text-sm font-medium">Data tidak ditemukan</p>
                        </div>
                    @endforelse
                </div>

            </div>
        </x-desktop.layout>
    </x-slot:desktop>

</x-layouts.mobile>