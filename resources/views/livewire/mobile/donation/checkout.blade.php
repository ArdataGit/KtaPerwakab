<?php

use App\Services\DonationApiService;
use Carbon\Carbon;
use function Livewire\Volt\state;
use function Livewire\Volt\mount;

state([
    'campaignName'   => '',
    'amount'         => 0,
    'paymentName'    => '',
    'accountNumber'  => '',
    'accountName'    => '',
    'checkoutUrl'    => '',
    'expiredAt'      => null,
]);

mount(function ($id) {

    $res = DonationApiService::detail($id);

    if (!$res->successful()) {
        abort(404);
    }

    $data = $res->json('data');

    $this->campaignName  = $data['campaign_name'];
    $this->amount        = (int) $data['amount'];
    $this->paymentName   = $data['payment_name'];
    $this->accountNumber = $data['pay_code'];
    $this->accountName   = $data['account_name'] ?? 'TRIPAY';
    $this->checkoutUrl   = $data['checkout_url'];
    $this->expiredAt     = Carbon::parse($data['expired_at']);
});
?>

<x-layouts.mobile title="Detail Donasi">

    {{-- HEADER --}}
    <div class="w-full bg-green-600 px-4 py-4 flex items-center space-x-3 rounded-b-2xl">
        <button onclick="window.history.back()">
            <img src="/images/assets/icon/back.svg" class="w-5 h-5">
        </button>
        <p class="text-white font-semibold text-base">Detail Donasi</p>
    </div>

    <div class="px-4 mt-6 space-y-6">

        {{-- INFO CARD --}}
        <div class="bg-white rounded-2xl shadow-sm p-4 space-y-2 text-sm text-gray-700">
            <div class="flex justify-between">
                <span>Nama Campaign</span>
                <span class="font-semibold text-gray-900">
                    {{ $campaignName }}
                </span>
            </div>

            <div class="flex justify-between">
                <span>Nominal Donasi</span>
                <span>Rp {{ number_format($amount, 0, ',', '.') }}</span>
            </div>

            <hr>

            <div class="flex justify-between text-base font-bold text-gray-900">
                <span>Total Transfer</span>
                <span>
                    Rp {{ number_format($amount, 0, ',', '.') }}
                </span>
            </div>
        </div>


        {{-- TRANSFER INFO --}}
        <div class="bg-gray-50 rounded-2xl p-4 space-y-3 text-sm">
          <div>
              <p class="text-gray-500">Metode Pembayaran</p>
              <p class="font-semibold text-gray-900">{{ $paymentName }}</p>
          </div>

          <div class="flex items-center justify-between">
              <div>
                  <p class="text-gray-500">Nomor VA / Kode Bayar</p>
                  <p class="font-semibold text-gray-900">{{ $accountNumber }}</p>
              </div>

              <button
                  onclick="navigator.clipboard.writeText('{{ $accountNumber }}')"
                  class="text-xs px-3 py-1 border border-green-600 text-green-600 rounded-full">
                  Salin
              </button>
          </div>

          <div>
              <p class="text-gray-500">Atas Nama</p>
              <p class="font-semibold text-gray-900">{{ $accountName }}</p>
          </div>
      </div>


        {{-- COUNTDOWN --}}
      <div class="flex justify-center">
          <div class="px-4 py-2 border rounded-full text-xs text-gray-700">
              Batas Pembayaran {{ $expiredAt->format('d M Y • H:i') }}
          </div>
      </div>


        {{-- BUTTON --}}
<div class="flex justify-center pt-4">
    <button
        onclick="window.location.href='{{ $checkoutUrl }}'"
        class="bg-green-600 text-white font-semibold px-10 py-3 rounded-full">
        Bayar Sekarang
    </button>
</div>

    </div>

    <div class="h-24"></div>

    <x-mobile.navbar active="donasi" />

</x-layouts.mobile>