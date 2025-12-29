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
]);
$formatAmount = function ($value) {
    return number_format((int) $value, 0, ',', '.');
};

mount(function ($id) {

    // DETAIL CAMPAIGN
    $campaignRes = DonationCampaignApiService::detail($id);
    if ($campaignRes->successful()) {
        $this->campaign = $campaignRes->json('data');
    }

    // PAYMENT METHODS (Tripay)
     $paymentRes = TripayApiService::paymentMethods();
  
  //dd($paymentRes);
     if ($paymentRes->successful()) {
         $this->paymentMethods = $paymentRes->json('data') ?? [];
     }
});

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
  dd([
    'status' => $response->status(),
    'body'   => $response->body(),
    'json'   => $response->json(),
]);
};

?>

<x-layouts.mobile title="Donasi">

    {{-- HEADER --}}
    <div class="w-full bg-green-600 px-4 py-4 flex items-center space-x-3">
        <button onclick="window.history.back()">
            <img src="/images/assets/icon/back.svg" class="w-5 h-5">
        </button>
        <p class="text-white font-semibold text-base">Donasi</p>
    </div>

    @if ($campaign)

        {{-- HERO IMAGE --}}
        <div class="relative">
            <img src="{{ $campaign['thumbnail'] }}" class="w-full h-72 object-cover" alt="{{ $campaign['title'] }}">
        </div>

        {{-- FORM CARD --}}
        <div class="relative -mt-14 bg-white rounded-t-[28px] px-4 pt-6 pb-8 space-y-6">


            {{-- TITLE --}}
            <p class="font-bold text-base text-gray-900 leading-snug">
                {{ $campaign['title'] }}
            </p>

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
                    <!--<option value="bcava">bca</option>-->
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

            {{-- MESSAGE --}}
            <div>
                <p class="text-sm font-semibold text-gray-800 mb-2">
                    Tulis doa dan berikan dukungan (Opsional)
                </p>

                <textarea wire:model="donor_message" rows="3"
                    class="w-full border border-gray-300 rounded-2xl px-4 py-3 text-base"></textarea>
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

        </div>

    @endif

    <div class="h-24"></div>

    <x-mobile.navbar active="donasi" />

</x-layouts.mobile>