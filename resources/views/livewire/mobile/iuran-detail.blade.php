<?php

use App\Services\MembershipFeeApiService;
use function Livewire\Volt\state;
use function Livewire\Volt\mount;

state([
    'token' => session('token'),
    'fee' => null,
    'snackbar' => ['type' => '', 'message' => ''],
]);

mount(function ($id) {
    if (!$this->token)
        return;

    $response = MembershipFeeApiService::detail($this->token, $id);

    if ($response->successful()) {
        $this->fee = $response->json('data');
    } else {
        $this->snackbar = [
            'type' => 'error',
            'message' => 'Data iuran tidak ditemukan'
        ];
    }
});
?>
@php
    $proof_image = api_proof_url($fee['proof_image'] ?? null);
@endphp

<x-layouts.mobile title="Detail Iuran">

    {{-- HEADER --}}
    <div class="w-full bg-green-600 px-4 py-4 flex items-center space-x-3 rounded-b-2xl">
        <button onclick="window.history.back()">
            <img src="/images/assets/icon/back.svg" class="w-5 h-5">
        </button>
        <p class="text-white font-semibold text-base">Detail Iuran</p>
    </div>

    @if($fee)
        <div class="px-4 mt-4 space-y-4">

            {{-- INFO --}}
            <div class="bg-white rounded-xl p-4 shadow-sm space-y-2 text-sm">
                <div class="flex justify-between">
                    <span>Jenis</span>
                    <span class="font-medium capitalize">{{ $fee['type'] }}</span>
                </div>

                <div class="flex justify-between">
                    <span>Nominal</span>
                    <span class="font-semibold text-green-600">
                        Rp{{ number_format($fee['amount'], 0, ',', '.') }}
                    </span>
                </div>

                <div class="flex justify-between">
                    <span>Status</span>
                    <span class="font-medium">
                        {{ ucfirst($fee['payment_status']) }}
                    </span>
                </div>

                <div class="flex justify-between">
                    <span>Tanggal</span>
                    <span>
                        {{ \Carbon\Carbon::parse($fee['created_at'])->format('d M Y') }}
                    </span>
                </div>
            </div>

            {{-- BUKTI --}}
            @if(!empty($fee['proof_image']))
                <div class="bg-white rounded-xl p-4 shadow-sm">
                    <p class="text-sm font-semibold mb-2">Bukti Pembayaran</p>
                    <img src="{{$proof_image }}" class="w-full rounded-lg border">
                </div>
            @endif

            {{-- ACTION --}}
            @if($fee['payment_status'] === 'pending' && empty($fee['proof_image']))
                <a href="{{ route('mobile.iuran.upload', ['id' => $fee['id']]) }}"
                    class="block w-full bg-green-600 text-white py-3 rounded-xl font-semibold text-center">
                    Upload Bukti Pembayaran
                </a>
            @endif

        </div>
    @endif

    <x-mobile.navbar active="home" />

    {{-- ==================== DESKTOP VIEW ==================== --}}
    <x-slot:desktop>
        <x-desktop.layout title="Detail Iuran">
            <div class="max-w-xl mx-auto">
                <div class="flex items-center gap-2 text-sm text-gray-400 mb-6">
                    <a href="{{ route('mobile.iuran.saya') }}" class="hover:text-green-600 transition">&larr; Kembali ke Iuran</a>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 mb-8">Detail Iuran</h1>

                @if($fee)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-3 text-sm mb-6">
                        <div class="flex justify-between"><span class="text-gray-500">Jenis</span><span class="font-medium capitalize">{{ $fee['type'] }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">Nominal</span><span class="font-semibold text-green-600">Rp{{ number_format($fee['amount'], 0, ',', '.') }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">Status</span><span class="font-medium">{{ ucfirst($fee['payment_status']) }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">Tanggal</span><span>{{ \Carbon\Carbon::parse($fee['created_at'])->format('d M Y') }}</span></div>
                    </div>

                    @if(!empty($fee['proof_image']))
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
                            <p class="text-sm font-semibold mb-3">Bukti Pembayaran</p>
                            <img src="{{ $proof_image }}" class="w-full rounded-xl border border-gray-200">
                        </div>
                    @endif

                    @if($fee['payment_status'] === 'pending' && empty($fee['proof_image']))
                        <a href="{{ route('mobile.iuran.upload', ['id' => $fee['id']]) }}"
                            class="block w-full bg-green-600 hover:bg-green-700 text-white py-4 rounded-xl font-semibold text-center transition shadow-md shadow-green-200">
                            Upload Bukti Pembayaran
                        </a>
                    @endif
                @endif
            </div>
        </x-desktop.layout>
    </x-slot:desktop>

</x-layouts.mobile>