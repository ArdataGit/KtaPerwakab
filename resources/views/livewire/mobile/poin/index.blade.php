<?php
use App\Services\PoinApiService;
use App\Services\AuthApiService;
use function Livewire\Volt\{state, mount};

state([
    'user' => null,
    'tab' => 'tambah', // tambah | tukar
    'saldo' => 0,
    'historyTambah' => [],
    'historyTukar' => [],
]);

$load = function () {

    $token = session('token');

    if (!$token) {
        redirect()->route('mobile.login');
        return;
    }

    /**
     * 🔄 AMBIL USER TERBARU (SUMBER UTAMA)
     */
    $userResponse = AuthApiService::me($token);

    if (!$userResponse->successful()) {
        session()->forget(['user', 'token']);
        redirect()->route('mobile.login');
        return;
    }

    $user = $userResponse->json('data');

    session(['user' => $user]);

    $this->user  = $user;
    $this->saldo = (int) ($user['point'] ?? 0);
  

    $userId = $user['id'];

    /**
     * 📥 RIWAYAT PENAMBAHAN POIN
     */
    $resTambah = PoinApiService::historyPenambahan($userId, [
        'per_page' => 20,
    ]);

    // Response langsung berupa array
        $data = $resTambah->json();
        
        if (is_array($data)) {
            $this->historyTambah = $data;
            
        }

    /**
     * 📤 RIWAYAT PENUKARAN POIN
     */
    $resTukar = PoinApiService::historyPenukaran($userId, [
        'per_page' => 20,
    ]);

    $this->historyTukar = $resTukar->successful()
        ? $resTukar->json('data.data') ?? []
        : [];
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
                    <div class="flex justify-between items-start text-sm bg-white p-3 rounded-xl border border-gray-100 shadow-sm">
                        <div class="flex-1 pr-3">
                            <p class="font-semibold text-gray-800">
                                {{ $item['keterangan'] ?? 'Penukaran Poin' }}
                            </p>
                            <p class="text-xs text-gray-500 mt-1">
                                {{ \Carbon\Carbon::parse($item['tanggal'])->format('d M Y • H:i') }}
                            </p>
                        </div>

                        <div class="text-right">
                            <p class="font-bold text-red-600 mb-1">
                                {{ $item['point'] }}
                            </p>
                            
                            @php
                                $status = $item['status'] ?? 'pending';
                                $statusClass = 'bg-gray-100 text-gray-600';
                                $statusLabel = ucfirst($status);
                                
                                if($status === 'pending') {
                                    $statusClass = 'bg-yellow-100 text-yellow-700';
                                    $statusLabel = 'Menunggu';
                                } elseif($status === 'approved' || $status === 'selesai' || $status === 'success') {
                                    $statusClass = 'bg-green-100 text-green-700';
                                    $statusLabel = 'Disetujui';
                                } elseif($status === 'rejected' || $status === 'failed' || $status === 'batal') {
                                    $statusClass = 'bg-red-100 text-red-700';
                                    $statusLabel = 'Ditolak';
                                }
                            @endphp
                            
                            <span class="inline-block px-2 py-0.5 rounded text-[10px] font-semibold {{ $statusClass }}">
                                {{ $statusLabel }}
                            </span>
                        </div>
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

    {{-- ==================== DESKTOP VIEW ==================== --}}
    <x-slot:desktop>
        <x-desktop.layout title="Poin">
            <div class="max-w-4xl mx-auto">
                <div class="flex items-center gap-2 text-sm text-gray-400 mb-6">
                    <a href="{{ route('mobile.profile') }}" class="hover:text-green-600 transition">&larr; Kembali ke Profil</a>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Poin Saya</h1>
                <p class="text-gray-500 mb-8">Kelola dan tukarkan poin reward Anda.</p>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    {{-- LEFT: Saldo --}}
                    <div>
                        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl p-6 text-white shadow-lg sticky top-6">
                            <p class="text-sm opacity-80">Hallo, {{ $user['name'] ?? '-' }}</p>
                            <div class="flex items-center justify-between mt-3">
                                <div>
                                    <p class="text-xs opacity-70">Total Poin</p>
                                    <p class="text-3xl font-bold">{{ number_format($saldo) }} <span class="text-base font-medium">Poin</span></p>
                                </div>
                                <div class="text-yellow-300 text-4xl">🪙</div>
                            </div>
                            <a href="{{ route('mobile.poin.tukar') }}" class="block mt-4 bg-white/20 hover:bg-white/30 text-white text-center py-2.5 rounded-full font-semibold text-sm transition">Tukar Poin</a>
                        </div>
                    </div>

                    {{-- RIGHT: History --}}
                    <div class="lg:col-span-2">
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                            <div class="flex border-b text-sm">
                                <button wire:click="$set('tab','tambah')" class="flex-1 py-4 text-center font-medium transition {{ $tab === 'tambah' ? 'text-green-600 border-b-2 border-green-600' : 'text-gray-500 hover:text-gray-700' }}">Riwayat Poin</button>
                                <button wire:click="$set('tab','tukar')" class="flex-1 py-4 text-center font-medium transition {{ $tab === 'tukar' ? 'text-green-600 border-b-2 border-green-600' : 'text-gray-500 hover:text-gray-700' }}">Riwayat Penukaran</button>
                            </div>
                            <div class="p-6 space-y-4">
                                @if ($tab === 'tambah')
                                    @forelse ($historyTambah as $item)
                                        <div class="flex justify-between items-start text-sm border-b border-gray-50 pb-3">
                                            <div>
                                                <p class="font-semibold text-gray-800">{{ $item['kategori_name'] ?? 'Penambahan Poin' }}</p>
                                                <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($item['created_at'])->format('d M Y • H:i') }}</p>
                                                @if(isset($item['added_by']))<p class="text-xs text-gray-400 mt-0.5">oleh {{ $item['added_by'] }}</p>@endif
                                            </div>
                                            <p class="font-semibold text-green-600">+{{ $item['point'] ?? 0 }}</p>
                                        </div>
                                    @empty
                                        <p class="text-center text-sm text-gray-500 py-8">Belum ada riwayat poin</p>
                                    @endforelse
                                @endif
                                @if ($tab === 'tukar')
                                    @forelse ($historyTukar as $item)
                                        <div class="flex justify-between items-start text-sm border-b border-gray-50 pb-3">
                                            <div>
                                                <p class="font-semibold text-gray-800">{{ $item['keterangan'] ?? 'Penukaran Poin' }}</p>
                                                <p class="text-xs text-gray-500 mt-1">{{ \Carbon\Carbon::parse($item['tanggal'])->format('d M Y • H:i') }}</p>
                                            </div>
                                            <div class="text-right">
                                                <p class="font-bold text-red-600 mb-1">{{ $item['point'] }}</p>
                                                
                                                @php
                                                    $status = $item['status'] ?? 'pending';
                                                    $statusClass = 'bg-gray-100 text-gray-600';
                                                    $statusLabel = ucfirst($status);
                                                    
                                                    if($status === 'pending') {
                                                        $statusClass = 'bg-yellow-100 text-yellow-700';
                                                        $statusLabel = 'Menunggu';
                                                    } elseif($status === 'approved' || $status === 'selesai' || $status === 'success') {
                                                        $statusClass = 'bg-green-100 text-green-700';
                                                        $statusLabel = 'Disetujui';
                                                    } elseif($status === 'rejected' || $status === 'failed' || $status === 'batal') {
                                                        $statusClass = 'bg-red-100 text-red-700';
                                                        $statusLabel = 'Ditolak';
                                                    }
                                                @endphp
                                                
                                                <span class="inline-block px-2 py-0.5 rounded text-[10px] font-semibold {{ $statusClass }}">
                                                    {{ $statusLabel }}
                                                </span>
                                            </div>
                                        </div>
                                    @empty
                                        <p class="text-center text-sm text-gray-500 py-8">Belum ada penukaran poin</p>
                                    @endforelse
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </x-desktop.layout>
    </x-slot:desktop>

</x-layouts.mobile>