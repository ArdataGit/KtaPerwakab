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

    // dd($res->json());   // cek isi response

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

        {{-- FLASH SUCCESS --}}
        @if (session('success'))
            <div class="bg-green-50 border border-green-200 rounded-xl p-3 text-sm text-green-700">
                ✅ {{ session('success') }}
            </div>
        @endif

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
                          <span class="text-xs px-3 py-1 rounded-full bg-green-100 text-green-700 font-medium">
                              ✅ Lunas
                          </span>
                      @elseif ($item['status'] === 'WAITING_VERIFICATION')
                          <span class="text-xs px-3 py-1 rounded-full bg-amber-100 text-amber-700 font-medium">
                              🕐 Menunggu Verifikasi
                          </span>
                      @elseif ($item['status'] === 'EXPIRED')
                          <span class="text-xs px-3 py-1 rounded-full bg-gray-100 text-gray-500 font-medium">
                              Kedaluwarsa
                          </span>
                      @else
                          <span class="text-xs px-3 py-1 rounded-full bg-orange-100 text-orange-700 font-medium">
                              Pending
                          </span>
                      @endif
                  </div>

                  {{-- CTA: Bayar Tripay --}}
                  @if ($item['status'] === 'PENDING' && isset($item['checkout_url']['data']['checkout_url']))
                      <a href="{{ $item['checkout_url']['data']['checkout_url'] }}"
                         class="block text-center text-sm font-medium bg-green-600 text-white py-2 rounded-lg"
                         target="_blank" rel="noopener">
                          Lanjutkan Pembayaran
                      </a>
                  @endif

                  {{-- CTA: Upload Bukti Manual --}}
                  @if (in_array($item['status'], ['PENDING', 'UNPAID']) && !isset($item['checkout_url']['data']['checkout_url']))
                      <a href="{{ route('mobile.donation.upload-proof', $item['id']) }}"
                         class="block text-center text-sm font-medium bg-amber-500 text-white py-2 rounded-lg">
                          📤 Upload Bukti Transfer
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
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 px-6 py-4 space-y-3 hover:shadow-md transition">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $item['campaign_title'] }}</p>
                                    <p class="text-sm text-green-600 mt-1">Rp {{ number_format($item['amount'], 0, ',', '.') }}</p>
                                </div>
                                <div class="flex items-center gap-2">
                                    @if ($item['status'] === 'PAID')
                                        <span class="text-xs px-3 py-1.5 rounded-full bg-green-100 text-green-700 font-medium">✅ Lunas</span>
                                    @elseif ($item['status'] === 'WAITING_VERIFICATION')
                                        <span class="text-xs px-3 py-1.5 rounded-full bg-amber-100 text-amber-700 font-medium">🕐 Menunggu Verifikasi</span>
                                    @elseif ($item['status'] === 'EXPIRED')
                                        <span class="text-xs px-3 py-1.5 rounded-full bg-gray-100 text-gray-500 font-medium">Kedaluwarsa</span>
                                    @else
                                        <span class="text-xs px-3 py-1.5 rounded-full bg-orange-100 text-orange-700 font-medium">Pending</span>
                                    @endif

                                    {{-- CTA Tripay --}}
                                    @if ($item['status'] === 'PENDING' && isset($item['checkout_url']['data']['checkout_url']))
                                        <a href="{{ $item['checkout_url']['data']['checkout_url'] }}" target="_blank"
                                            class="text-xs px-4 py-1.5 bg-green-600 text-white rounded-full font-semibold hover:bg-green-700 transition">
                                            Lanjutkan Pembayaran
                                        </a>
                                    @endif

                                    {{-- CTA Manual --}}
                                    @if (in_array($item['status'], ['PENDING', 'UNPAID']) && !isset($item['checkout_url']['data']['checkout_url']))
                                        <a href="{{ route('mobile.donation.upload-proof', $item['id']) }}"
                                            class="text-xs px-4 py-1.5 bg-amber-500 text-white rounded-full font-semibold hover:bg-amber-600 transition">
                                            📤 Upload Bukti
                                        </a>
                                    @endif
                                </div>
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
