<?php
use App\Services\PoinApiService;
use function Livewire\Volt\{state, mount};

state([
    'user' => null,
    'tab' => 'tambah', // tambah | tukar
    'saldo' => 0,
    'historyTambah' => [],
    'historyTukar' => [],
]);
$load = function () {

    $this->user = session('user');

    if (!$this->user || !isset($this->user['id'])) {
        return;
    }

    $userId = $this->user['id'];

    $resTambah = PoinApiService::historyPenambahan($userId, [
        'per_page' => 20,
    ]);

    $resTukar = PoinApiService::historyPenukaran($userId, [
        'per_page' => 20,
    ]);

    if ($resTambah->successful()) {
        // Response langsung berupa array
        $data = $resTambah->json();
        
        if (is_array($data)) {
            $this->historyTambah = $data;
            
            // Ambil saldo dari user session atau item pertama jika ada
            if (!empty($data) && isset($data[0]['user_saldo'])) {
                $this->saldo = (int) $data[0]['user_saldo'];
            } else {
                // Fallback ke session user jika ada
                $this->saldo = (int) ($this->user['saldo_point'] ?? 0);
            }
        }
    }

    if ($resTukar->successful()) {
        $this->historyTukar = $resTukar->json('data.data') ?? [];
    }
};



mount(fn() => $this->load());

?>

<x-layouts.mobile title="Poin">

    {{-- HEADER --}}
    <div class="bg-green-600 px-4 py-4 flex items-center gap-3 rounded-b-2xl">
        <button onclick="history.back()">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M15 19l-7-7 7-7" />
            </svg>
        </button>
        <p class="text-white font-semibold text-base">POIN</p>
    </div>

    <div class="px-4 mt-4 space-y-4">

        {{-- SALDO CARD --}}
        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-2xl p-4 text-white">
            <p class="text-sm opacity-90">
                Hallo, {{ $user['name'] ?? '-' }}
            </p>

            <div class="flex items-center justify-between mt-2">
                <div>
                    <p class="text-xs opacity-80">Total Poin</p>
                    <p class="text-3xl font-bold">
                        {{ number_format($saldo) }}
                        <span class="text-base font-medium">Poin</span>
                    </p>
                </div>

                <div class="text-yellow-300 text-4xl">🪙</div>
            </div>

            <a href="{{ route('mobile.poin.tukar') }}"
                class="block mt-4 bg-green-200 text-green-800 text-center py-2 rounded-full font-semibold text-sm">
                Tukar Poin
            </a>
        </div>


        {{-- TAB --}}
        <div class="flex border-b text-sm">
            <button wire:click="$set('tab','tambah')" class="flex-1 py-2 text-center font-medium
                {{ $tab === 'tambah' ? 'text-green-600 border-b-2 border-green-600' : 'text-gray-500' }}">
                Riwayat Poin
            </button>

            <button wire:click="$set('tab','tukar')" class="flex-1 py-2 text-center font-medium
                {{ $tab === 'tukar' ? 'text-green-600 border-b-2 border-green-600' : 'text-gray-500' }}">
                Riwayat Penukaran
            </button>
        </div>

        {{-- LIST RIWAYAT --}}
        <div class="space-y-4">

            {{-- RIWAYAT PENAMBAHAN --}}
            @if ($tab === 'tambah')
                @forelse ($historyTambah as $item)
                    <div class="flex justify-between items-start text-sm">
                        <div class="flex-1">
                            <p class="font-semibold text-gray-800">
                                {{ $item['kategori_name'] ?? 'Penambahan Poin' }}
                            </p>
                            <p class="text-xs text-gray-500">
                                {{ \Carbon\Carbon::parse($item['created_at'])->format('d M Y • H:i') }}
                            </p>
                            @if(isset($item['added_by']))
                                <p class="text-xs text-gray-400 mt-0.5">
                                    oleh {{ $item['added_by'] }}
                                </p>
                            @endif
                        </div>

                        <p class="font-semibold text-green-600">
                            +{{ $item['point'] ?? 0 }}
                        </p>
                    </div>
                @empty
                    <p class="text-center text-sm text-gray-500 py-8">
                        Belum ada riwayat poin
                    </p>
                @endforelse
            @endif

            {{-- RIWAYAT PENUKARAN --}}
            @if ($tab === 'tukar')
                @forelse ($historyTukar as $item)
                    <div class="flex justify-between items-start text-sm">
                        <div>
                            <p class="font-semibold text-gray-800">
                                {{ $item['keterangan'] ?? 'Penukaran Poin' }}
                            </p>
                            <p class="text-xs text-gray-500">
                                {{ \Carbon\Carbon::parse($item['tanggal'])->format('d M • H:i') }}
                            </p>
                        </div>

                        <p class="font-semibold text-red-600">
                            {{ $item['point'] }}
                        </p>
                    </div>
                @empty
                    <p class="text-center text-sm text-gray-500 py-8">
                        Belum ada penukaran poin
                    </p>
                @endforelse
            @endif

        </div>

    </div>

    <div class="h-24"></div>
    <x-mobile.navbar active="poin" />

</x-layouts.mobile>