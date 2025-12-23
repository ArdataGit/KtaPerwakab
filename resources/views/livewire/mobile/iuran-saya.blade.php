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
</x-layouts.mobile>