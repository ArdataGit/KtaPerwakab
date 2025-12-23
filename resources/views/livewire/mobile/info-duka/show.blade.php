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

</x-layouts.mobile>