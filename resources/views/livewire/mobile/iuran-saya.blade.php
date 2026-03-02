<?php

use App\Services\MembershipFeeApiService;
use function Livewire\Volt\state;
use function Livewire\Volt\mount;

state([
    'token' => session('token'),
    'fees' => [],
    'total' => 0,
]);

mount(function () {
    if (!$this->token) {
        return;
    }

    $response = MembershipFeeApiService::myFees($this->token);

    if ($response->successful()) {
        $data = $response->json('data') ?? [];

        $this->fees = $data;

        $this->total = collect($data)
            ->where('payment_status', 'success')
            ->sum('amount');
    }
});
?>


<x-layouts.mobile title="Iuran Saya">

    {{-- HEADER --}}
    <div class="w-full bg-green-600 px-4 py-4 flex items-center space-x-3 rounded-b-2xl">
        <button onclick="window.history.back()">
            <img src="/images/assets/icon/back.svg" class="w-5 h-5">
        </button>
        <p class="text-white font-semibold text-base">Iuran Saya</p>
    </div>

    <div class="px-4 mt-4 space-y-4">

        {{-- TOTAL --}}
        <div class="bg-white rounded-xl p-4 shadow-sm flex justify-between items-center">
            <p class="text-sm text-gray-600">Total Iuran Terbayar</p>
            <p class="font-bold text-green-600">
                Rp{{ number_format($total, 0, ',', '.') }}
            </p>
        </div>

        {{-- LIST IURAN --}}
        @forelse($fees as $fee)
            <a href="{{ route('mobile.iuran.detail', $fee['id']) }}" class="block bg-white rounded-xl p-4 shadow-sm">

                <div class="flex justify-between items-center">
                    <div>

                        <p class="text-sm font-semibold text-gray-800">
                            Periode: {{ $fee['year'] ?? '-' }}
                        </p>
                        <p class="text-xs text-gray-500">
                            Rp{{ number_format($fee['amount'], 0, ',', '.') }}
                        </p>
                    </div>

                    <span class="px-3 py-1 text-xs rounded-full
                                {{ $fee['payment_status'] === 'success'
            ? 'bg-green-100 text-green-700'
            : 'bg-yellow-100 text-yellow-700' }}">
                        {{ $fee['payment_status'] === 'success'
            ? 'Sudah dibayar'
            : 'Menunggu verifikasi' }}
                    </span>
                </div>
            </a>
        @empty

            <p class="text-center text-sm text-gray-500">
                Belum ada data iuran
            </p>
        @endforelse

    </div>

    <x-mobile.navbar active="home" />

    {{-- ==================== DESKTOP VIEW ==================== --}}
    <x-slot:desktop>
        <x-desktop.layout title="Iuran Saya">
            <div class="max-w-4xl mx-auto">
                <div class="flex items-center gap-2 text-sm text-gray-400 mb-6">
                    <a href="{{ route('mobile.profile') }}" class="hover:text-green-600 transition">&larr; Kembali ke Profil</a>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Iuran Saya</h1>
                <p class="text-gray-500 mb-8">Riwayat pembayaran iuran keanggotaan Anda.</p>

                <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-2xl p-6 text-white flex items-center justify-between mb-8 shadow-lg">
                    <div>
                        <p class="text-sm opacity-80">Total Iuran Terbayar</p>
                        <p class="text-3xl font-bold mt-1">Rp{{ number_format($total, 0, ',', '.') }}</p>
                    </div>
                    <div class="text-5xl opacity-30">💰</div>
                </div>

                <div class="space-y-3">
                    @forelse($fees as $fee)
                        <a href="{{ route('mobile.iuran.detail', $fee['id']) }}" class="block bg-white rounded-xl shadow-sm border border-gray-100 px-6 py-4 flex items-center justify-between hover:shadow-md transition group">
                            <div>
                                <p class="font-semibold text-gray-800 group-hover:text-green-700 transition">Periode: {{ $fee['year'] ?? '-' }}</p>
                                <p class="text-sm text-gray-500 mt-1">Rp{{ number_format($fee['amount'], 0, ',', '.') }}</p>
                            </div>
                            <span class="px-3 py-1.5 text-xs rounded-full font-medium {{ $fee['payment_status'] === 'success' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                {{ $fee['payment_status'] === 'success' ? 'Sudah dibayar' : 'Menunggu verifikasi' }}
                            </span>
                        </a>
                    @empty
                        <div class="bg-white rounded-xl p-8 text-center text-sm text-gray-500 border border-gray-100">Belum ada data iuran</div>
                    @endforelse
                </div>
            </div>
        </x-desktop.layout>
    </x-slot:desktop>

</x-layouts.mobile>