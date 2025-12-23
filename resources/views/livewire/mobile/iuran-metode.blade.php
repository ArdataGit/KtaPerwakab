<x-layouts.mobile title="Metode Pembayaran Tunai">

    {{-- HEADER --}}
    <div class="w-full bg-green-600 px-4 py-4 flex items-center space-x-3 rounded-b-2xl">
        <button onclick="window.history.back()">
            <img src="/images/assets/icon/back.svg" class="w-5 h-5">
        </button>
        <p class="text-white font-semibold text-base">
            Metode Pembayaran Tunai
        </p>
    </div>

    <div class="px-4 mt-4">

        {{-- CARD INFORMASI REKENING --}}
        <div class="bg-white rounded-xl p-4 shadow-sm">

            <p class="font-semibold text-gray-800 mb-3">
                Informasi Rekening
            </p>

            <div class="space-y-3 text-sm text-gray-700">

                <div class="flex justify-between">
                    <span>Bank Tujuan</span>
                    <span class="font-medium">Bank Central Asia</span>
                </div>

                <div class="flex justify-between">
                    <span>Nomor Rekening</span>
                    <span class="font-medium">1234 567 890</span>
                </div>

                <div class="flex justify-between">
                    <span>Atas Nama</span>
                    <span class="font-medium">Joko Adiwinansa</span>
                </div>

                <div class="flex justify-between">
                    <span>Jumlah Transfer</span>
                    <span class="font-semibold text-green-600">
                        Rp{{ number_format(session('membership_fee_amount'), 0, ',', '.') }}
                    </span>
                </div>

            </div>

            {{-- INFO NOTE --}}
            <div class="mt-4 bg-green-50 border border-green-200 rounded-xl p-3">
                <p class="text-xs text-green-700">
                    Pastikan nominal transfer sesuai dengan jumlah di atas
                    agar pembayaran dapat diverifikasi dengan cepat.
                </p>
            </div>

            {{-- BUTTON --}}
            <a href="{{ route('mobile.iuran.upload') }}"
                class="mt-6 block w-full text-center bg-green-600 text-white py-3 rounded-xl font-semibold">
                Selesaikan
            </a>

        </div>
    </div>

    <x-mobile.navbar active="home" />

</x-layouts.mobile>