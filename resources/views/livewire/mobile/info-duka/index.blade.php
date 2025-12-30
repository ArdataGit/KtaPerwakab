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

</x-layouts.mobile>