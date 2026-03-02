<?php
use App\Services\DonationCampaignApiService;
use App\Services\DonationApiService;
use App\Services\TripayApiService;
use function Livewire\Volt\state;
use function Livewire\Volt\mount;
state([
    'campaign' => null,
    // form
    'amount' => 0,
    'amount_display' => '',
    'payment_method' => '',
    'donor_name' => '',
    'donor_email' => '',
    'donor_message' => '',
    'paymentMethods' => [],
    'showForm' => false,
    'donationHistories' => [],
  
  
    'userId' => null,
]);
$formatAmount = function ($value) {
    return number_format((int) $value, 0, ',', '.');
};
mount(function ($id) {
    // DETAIL CAMPAIGN
    // ambil user dari session
    $user = session('user');
    $this->userId = $user['id'] ?? null;
  
    $campaignRes = DonationCampaignApiService::detail($id);
    if ($campaignRes->successful()) {
        $this->campaign = $campaignRes->json('data');
        // Filter hanya donasi dengan status PAID
        $this->donationHistories = array_filter($this->campaign['donations'] ?? [], function($donation) {
            return $donation['status'] === 'PAID';
        });
      
      //dd($this->donationHistories);
    }
    // PAYMENT METHODS (Tripay)
     $paymentRes = TripayApiService::paymentMethods();
  //dd($paymentRes);
     if ($paymentRes->successful()) {
         $this->paymentMethods = $paymentRes->json('data') ?? [];
     }
});
/**
 * TOGGLE FORM
 */
$toggleForm = function () {
    $this->showForm = true;
};
/**
 * SUBMIT DONASI
 */
$submit = function () {
  $this->validate([
      'amount' => 'required|numeric|min:20000',
      'payment_method' => 'required',
      'donor_name' => 'nullable|string|max:191',
      'donor_email' => 'nullable|email|max:191',
  ]);
    $response = DonationApiService::donate([
        'campaign_id' => $this->campaign['id'],
    	'user_id' => $this->userId,
        'amount' => $this->amount,
        'payment_method' => $this->payment_method,
        'donor_name' => $this->donor_name,
        'donor_email' => $this->donor_email,
    ]);
    if ($response->successful()) {
        $donationId = $response->json('data.donation_id');
        return redirect()->route(
            'mobile.donation.checkout',
            ['id' => $donationId]
        );
    }
};
?>
<x-layouts.mobile title="Donasi">
    {{-- HEADER --}}
<div class="w-full bg-green-600 px-4 py-4 flex items-center space-x-3 rounded-b-[24px] shadow-sm">
        <button onclick="window.history.back()">
            <img src="/images/assets/icon/back.svg" class="w-5 h-5">
        </button>
        <p class="text-white font-semibold text-base">Donasi</p>
    </div>
    @if ($campaign)
        {{-- HERO IMAGE --}}
        <div class="relative">
            <img src="{{ $campaign['thumbnail'] }}"
     class="w-full h-72 object-cover rounded-b-[32px]">
        </div>
        {{-- CARD --}}
        <div class="relative -mt-14 bg-white rounded-t-[28px] px-4 pt-4 pb-8 space-y-6">
            @if (!$showForm)
                {{-- DETAIL CAMPAIGN --}}
                <div class="space-y-4">
                    {{-- TITLE --}}
                    <p class="font-bold text-base text-gray-900 leading-snug">
                        {{ $campaign['title'] }}
                    </p>
                    {{-- DESCRIPTION --}}
                   <span class="text-gray-700 text-xs mb-4">-Deskripsi-</span>
                    <div class="prose prose-sm mt-3 text-gray-700">
                        {!! $campaign['description'] ?? 'Deskripsi campaign tidak tersedia.' !!}
                    </div>
                    {{-- OTHER DETAILS (opsional, tambahkan jika ada data) --}}
                    {{-- Contoh: Progress donasi --}}
                    @if (isset($campaign['total_collected']) && isset($campaign['total_collected']))
                        <div class="space-y-2">
                            <p class="text-sm font-semibold text-gray-800">Donasi Terkumpul</p>
                            <div class="bg-gray-200 rounded-full h-2.5">
                                <div class="bg-green-600 h-2.5 rounded-full" style="width: {{ ($campaign['total_collected'] / 1000000) * 100 }}%; max-width:100%;"></div>
                            </div>
                            <p class="text-xs text-gray-600">
                                Terkumpul Rp{{ number_format($campaign['total_collected'], 0, ',', '.') }}
                            </p>
                        </div>
                    @endif
                    {{-- RIWAYAT DONASI --}}
                    @if (!empty($donationHistories))
                        <div class="mt-6 space-y-3">
                            <p class="text-sm font-semibold text-gray-800">
                                Riwayat Donasi
                            </p>
                          <a href="{{ route('mobile.donation.histories', $campaign['id']) }}"
                             class="text-xs text-green-600 font-semibold block text-right mt-2">
                              Lihat semua donasi →
                          </a>
                            <div class="space-y-2">
                                @foreach ($donationHistories as $donation)
                                    <div class="flex justify-between items-center bg-gray-50 rounded-xl px-4 py-3">
                                        <div>
                                            <p class="text-sm font-medium text-gray-800">
                                                {{ $donation['donor_name'] ?? 'Hamba Allah' }}
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                {{ \Carbon\Carbon::parse($donation['created_at'])->diffForHumans() }}
                                            </p>
                                        </div>
                                        <p class="text-sm font-semibold text-green-600">
                                            Rp{{ number_format($donation['amount'], 0, ',', '.') }}
                                        </p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    {{-- TOMBOL DONASI --}}
                    <button wire:click="toggleForm" class="w-full bg-green-600 text-white font-semibold py-4 rounded-2xl text-base">
                        Donasi Sekarang
                    </button>
                </div>
            @else
                {{-- FORM DONASI --}}
                {{-- AMOUNT --}}
                <div>
                    <p class="text-sm font-semibold text-gray-800 mb-2">
                        Masukkan Nominal Donasi
                    </p>
                    <div class="flex items-center border-2 border-gray-300 rounded-2xl px-4 py-3 focus-within:border-green-500">
                        <span class="text-base font-semibold mr-2">Rp</span>
                        <input
                            type="text"
                            inputmode="numeric"
                            placeholder="Minimal Rp20.000"
                            value="{{ $amount_display }}"
                            class="w-full focus:outline-none text-base"
                            wire:ignore
                            oninput="
                                let raw = this.value.replace(/\D/g,'');
                                this.value = raw.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                                @this.set('amount', raw);
                                @this.set('amount_display', this.value);
                            "
                        >
                    </div>
                    <p class="text-xs text-gray-500 mt-1">
                        Minimal donasi Rp20.000
                    </p>
                    @error('amount')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <hr class="border-gray-100">
                {{-- PAYMENT METHOD --}}
                <div>
                    <p class="text-sm font-semibold text-gray-800 mb-2">
                        Pilih Metode Pembayaran
                    </p>
                    <select
                      wire:model="payment_method"
                      class="w-full border-2 border-gray-300 rounded-2xl px-4 py-3 text-base focus:border-green-500 focus:ring-0">
                        <option value="">-- Pilih --</option>
                        @foreach ($paymentMethods as $method)
                            <option value="{{ $method['code'] }}">
                                {{ $method['name'] }}
                            </option>
                        @endforeach
                    </select>
                    @error('payment_method')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                {{-- DONOR PROFILE --}}
                <div class="bg-gray-50 rounded-2xl p-4 space-y-3">
                    <p class="text-sm font-semibold text-gray-800">
                        Profil Donatur
                    </p>
                    <input type="text" wire:model="donor_name" placeholder="Atas Nama"
                        class="w-full border border-gray-300 rounded-xl px-4 py-3 text-base">
                    <input type="email" wire:model="donor_email" placeholder="Alamat Email"
                        class="w-full border border-gray-300 rounded-xl px-4 py-3 text-base">
                </div>
                {{-- SUBMIT --}}
                <button wire:click="submit"
                    class="w-full bg-green-600 text-white font-semibold py-4 rounded-2xl text-base">
                    Lanjutkan Donasi
                </button>
                @error('submit')
                    <p class="text-xs text-red-500 text-center mt-2">
                        {{ $message }}
                    </p>
                @enderror
            @endif
        </div>
    @endif
    <div class="h-24"></div>
    <x-mobile.navbar active="donasi" />

    {{-- ==================== DESKTOP VIEW ==================== --}}
    <x-slot:desktop>
        <x-desktop.layout title="Donasi">
            <div class="max-w-5xl mx-auto">
                <div class="flex items-center gap-2 text-sm text-gray-400 mb-6">
                    <a href="{{ route('mobile.donation.index') }}" class="hover:text-green-600 transition">&larr; Kembali ke Donasi</a>
                </div>

                @if ($campaign)
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        {{-- LEFT: Campaign Info --}}
                        <div class="lg:col-span-2 space-y-6">
                            {{-- HERO --}}
                            <div class="rounded-2xl overflow-hidden shadow-lg">
                                <img src="{{ $campaign['thumbnail'] }}" class="w-full h-80 object-cover">
                            </div>

                            {{-- CONTENT --}}
                            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                                <h1 class="text-2xl font-bold text-gray-900 mb-4">{{ $campaign['title'] }}</h1>

                                <span class="text-gray-700 text-xs mb-4 block">- Deskripsi -</span>
                                <div class="prose max-w-none text-gray-700 text-sm leading-relaxed">
                                    {!! $campaign['description'] ?? 'Deskripsi campaign tidak tersedia.' !!}
                                </div>

                                @if (isset($campaign['total_collected']))
                                    <div class="mt-6 space-y-2 bg-gray-50 rounded-xl p-4">
                                        <p class="text-sm font-semibold text-gray-800">Donasi Terkumpul</p>
                                        <div class="bg-gray-200 rounded-full h-3">
                                            <div class="bg-green-500 h-3 rounded-full transition-all" style="width: {{ min(($campaign['total_collected'] / max($campaign['target_amount'] ?? 1000000, 1)) * 100, 100) }}%"></div>
                                        </div>
                                        <p class="text-sm text-gray-600">Terkumpul <strong class="text-green-600">Rp{{ number_format($campaign['total_collected'], 0, ',', '.') }}</strong></p>
                                    </div>
                                @endif
                            </div>

                            {{-- RIWAYAT --}}
                            @if (!empty($donationHistories))
                                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                                    <div class="flex items-center justify-between mb-4">
                                        <h3 class="font-semibold text-gray-800">Riwayat Donasi</h3>
                                        <a href="{{ route('mobile.donation.histories', $campaign['id']) }}"
                                            class="text-xs text-green-600 font-semibold hover:text-green-700 transition">Lihat Semua →</a>
                                    </div>
                                    <div class="space-y-3">
                                        @foreach ($donationHistories as $donation)
                                            <div class="flex justify-between items-center bg-gray-50 rounded-xl px-5 py-3">
                                                <div>
                                                    <p class="text-sm font-medium text-gray-800">{{ $donation['donor_name'] ?? 'Hamba Allah' }}</p>
                                                    <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($donation['created_at'])->diffForHumans() }}</p>
                                                </div>
                                                <p class="text-sm font-semibold text-green-600">Rp{{ number_format($donation['amount'], 0, ',', '.') }}</p>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- RIGHT: Form Donasi --}}
                        <div class="space-y-6">
                            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sticky top-6">
                                @if (!$showForm)
                                    <div class="text-center">
                                        <h3 class="font-bold text-lg text-gray-900 mb-2">Berikan Donasi Anda</h3>
                                        <p class="text-sm text-gray-500 mb-6">Setiap donasi sangat berarti bagi mereka yang membutuhkan.</p>
                                        <button wire:click="toggleForm"
                                            class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-4 rounded-xl text-base transition shadow-md shadow-green-200">
                                            Donasi Sekarang
                                        </button>
                                    </div>
                                @else
                                    <div class="space-y-5">
                                        {{-- AMOUNT --}}
                                        <div>
                                            <p class="text-sm font-semibold text-gray-800 mb-2">Nominal Donasi</p>
                                            <div class="flex items-center border-2 border-gray-200 rounded-xl px-4 py-3 focus-within:border-green-500 transition">
                                                <span class="text-base font-semibold mr-2">Rp</span>
                                                <input type="text" inputmode="numeric" placeholder="Minimal Rp20.000"
                                                    value="{{ $amount_display }}"
                                                    class="w-full focus:outline-none text-base" wire:ignore
                                                    oninput="let raw = this.value.replace(/\D/g,''); this.value = raw.replace(/\B(?=(\d{3})+(?!\d))/g, '.'); @this.set('amount', raw); @this.set('amount_display', this.value);">
                                            </div>
                                            <p class="text-xs text-gray-500 mt-1">Minimal donasi Rp20.000</p>
                                            @error('amount') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                                        </div>

                                        {{-- PAYMENT METHOD --}}
                                        <div>
                                            <p class="text-sm font-semibold text-gray-800 mb-2">Metode Pembayaran</p>
                                            <select wire:model="payment_method"
                                                class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 text-sm focus:border-green-500 focus:ring-0 transition">
                                                <option value="">-- Pilih --</option>
                                                @foreach ($paymentMethods as $method)
                                                    <option value="{{ $method['code'] }}">{{ $method['name'] }}</option>
                                                @endforeach
                                            </select>
                                            @error('payment_method') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                                        </div>

                                        {{-- DONOR INFO --}}
                                        <div class="bg-gray-50 rounded-xl p-4 space-y-3">
                                            <p class="text-sm font-semibold text-gray-800">Profil Donatur</p>
                                            <input type="text" wire:model="donor_name" placeholder="Atas Nama"
                                                class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm">
                                            <input type="email" wire:model="donor_email" placeholder="Alamat Email"
                                                class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm">
                                        </div>

                                        <button wire:click="submit"
                                            class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-4 rounded-xl text-base transition shadow-md shadow-green-200">
                                            Lanjutkan Donasi
                                        </button>
                                        @error('submit') <p class="text-xs text-red-500 text-center mt-2">{{ $message }}</p> @enderror
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </x-desktop.layout>
    </x-slot:desktop>

</x-layouts.mobile>