<?php

use App\Services\DonationApiService;
use function Livewire\Volt\state;
use function Livewire\Volt\mount;

state([
    'totalPaid' => 0,
    'donations' => [],
]);

mount(function () {
    $res = DonationApiService::myDonations();
    if ($res->successful()) {
        $this->totalPaid = (int) $res->json('data.total_paid');
        $this->donations = $res->json('data.donations') ?? [];
    }
//dd($this->donations);
});
?>
<x-layouts.mobile title="Donasi Saya">

    {{-- HEADER --}}
    <div class="w-full bg-green-600 px-4 py-4 flex items-center space-x-3 rounded-b-2xl">
        <button onclick="window.history.back()">
            <img src="/images/assets/icon/back.svg" class="w-5 h-5">
        </button>
        <p class="text-white font-semibold text-base">Donasi Saya</p>
    </div>

    <div class="px-4 mt-4 space-y-4">

        {{-- TOTAL --}}
        <div class="flex justify-between text-sm text-gray-700">
            <span>Total Donasi Terbayar</span>
            <span class="font-semibold">
                Rp {{ number_format($totalPaid, 0, ',', '.') }}
            </span>
        </div>

        {{-- LIST --}}
        <div class="space-y-3">

            @forelse ($donations as $item)
              <div class="bg-white rounded-xl shadow-sm px-4 py-3 space-y-2">

                  <div class="flex items-center justify-between">
                      <div>
                          <p class="text-sm font-semibold text-gray-900">
                              {{ $item['campaign_title'] }}
                          </p>
                          <p class="text-xs text-green-600">
                              Rp {{ number_format($item['amount'], 0, ',', '.') }}
                          </p>
                      </div>

                      {{-- STATUS BADGE --}}
                      @if ($item['status'] === 'PAID')
                          <span class="text-xs px-3 py-1 rounded-full bg-green-100 text-green-700">
                              Sudah dibayar
                          </span>
                      @else
                          <span class="text-xs px-3 py-1 rounded-full bg-orange-100 text-orange-700">
                              Pending
                          </span>
                      @endif
                  </div>

                  {{-- CTA CHECKOUT --}}
                  @if (
                      $item['status'] === 'PENDING'
                      && isset($item['checkout_url']['data']['checkout_url'])
                  )
                      <a href="{{ $item['checkout_url']['data']['checkout_url'] }}"
                         class="block text-center text-sm font-medium bg-green-600 text-white py-2 rounded-lg"
                         target="_blank" rel="noopener">
                          Lanjutkan Pembayaran
                      </a>
                  @endif

              </div>
          @empty
              <div class="bg-white rounded-xl p-4 text-center text-sm text-gray-500">
                  Belum ada donasi.
              </div>
          @endforelse


        </div>
    </div>

    <div class="h-24"></div>

    <x-mobile.navbar active="donasi" />

    {{-- ==================== DESKTOP VIEW ==================== --}}
    <x-slot:desktop>
        <x-desktop.layout title="Donasi Saya">
            <div class="max-w-4xl mx-auto">
                <div class="flex items-center gap-2 text-sm text-gray-400 mb-6">
                    <a href="{{ route('mobile.profile') }}" class="hover:text-green-600 transition">&larr; Kembali ke Profil</a>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Donasi Saya</h1>
                <p class="text-gray-500 mb-8">Riwayat dan status donasi Anda.</p>

                <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-2xl p-6 text-white flex items-center justify-between mb-8 shadow-lg">
                    <div>
                        <p class="text-sm opacity-80">Total Donasi Terbayar</p>
                        <p class="text-3xl font-bold mt-1">Rp {{ number_format($totalPaid, 0, ',', '.') }}</p>
                    </div>
                    <div class="text-5xl opacity-30">💚</div>
                </div>

                <div class="space-y-3">
                    @forelse ($donations as $item)
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 px-6 py-4 flex items-center justify-between hover:shadow-md transition">
                            <div>
                                <p class="font-semibold text-gray-900">{{ $item['campaign_title'] }}</p>
                                <p class="text-sm text-green-600 mt-1">Rp {{ number_format($item['amount'], 0, ',', '.') }}</p>
                            </div>
                            <div class="flex items-center gap-3">
                                @if ($item['status'] === 'PAID')
                                    <span class="text-xs px-3 py-1.5 rounded-full bg-green-100 text-green-700 font-medium">Sudah dibayar</span>
                                @else
                                    <span class="text-xs px-3 py-1.5 rounded-full bg-orange-100 text-orange-700 font-medium">Pending</span>
                                @endif
                                @if ($item['status'] === 'PENDING' && isset($item['checkout_url']['data']['checkout_url']))
                                    <a href="{{ $item['checkout_url']['data']['checkout_url'] }}" target="_blank"
                                        class="text-xs px-4 py-1.5 bg-green-600 text-white rounded-full font-semibold hover:bg-green-700 transition">
                                        Lanjutkan Pembayaran
                                    </a>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="bg-white rounded-xl p-8 text-center text-sm text-gray-500 border border-gray-100">Belum ada donasi.</div>
                    @endforelse
                </div>
            </div>
        </x-desktop.layout>
    </x-slot:desktop>

</x-layouts.mobile>
