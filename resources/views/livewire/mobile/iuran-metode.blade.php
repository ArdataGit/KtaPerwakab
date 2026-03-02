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

    {{-- ==================== DESKTOP VIEW ==================== --}}
    <x-slot:desktop>
        <div class="min-h-screen bg-gray-50 flex flex-col">
            <x-desktop.topbar />
            
            <div class="flex flex-1">
                <x-desktop.sidebar />
                
                <div class="flex-1 p-8 overflow-y-auto">
                    <div class="max-w-3xl mx-auto">
                        
                        {{-- Header & Back Button --}}
                        <div class="flex items-center gap-4 mb-8">
                            <a href="javascript:history.back()" class="p-2 bg-white rounded-xl border border-gray-200 hover:bg-gray-50 transition">
                                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                            </a>
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900">Metode Pembayaran Tunai</h1>
                                <p class="text-sm text-gray-500 mt-1">Selesaikan pembayaran iuran tahunan Anda melalui transfer bank</p>
                            </div>
                        </div>

                        {{-- CARD INFORMASI REKENING --}}
                        <div class="bg-white rounded-2xl p-8 border border-gray-100 shadow-sm">
                            <h2 class="font-bold text-lg text-gray-800 mb-6 pb-4 border-b border-gray-100">Informasi Rekening Tujuan</h2>

                            <div class="space-y-6">
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-white rounded-lg shadow-sm flex items-center justify-center p-2">
                                            <span class="font-bold text-blue-800 text-xs">BCA</span>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-500">Bank Tujuan</p>
                                            <p class="font-bold text-gray-900">Bank Central Asia</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-6">
                                    <div class="p-4 border border-gray-100 rounded-xl bg-white">
                                        <p class="text-sm text-gray-500 mb-1">Nomor Rekening</p>
                                        <p class="text-lg font-bold text-gray-900 tracking-wide">1234 567 890</p>
                                    </div>
                                    
                                    <div class="p-4 border border-gray-100 rounded-xl bg-white">
                                        <p class="text-sm text-gray-500 mb-1">Atas Nama</p>
                                        <p class="text-base font-bold text-gray-900">Joko Adiwinansa</p>
                                    </div>
                                </div>

                                <div class="p-5 bg-green-50 border border-green-200 rounded-xl flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-green-800 mb-1">Total Yang Harus Dibayar</p>
                                        <p class="text-3xl font-extrabold text-green-700">Rp{{ number_format(session('membership_fee_amount', 0), 0, ',', '.') }}</p>
                                    </div>
                                </div>
                            </div>

                            {{-- INFO NOTE --}}
                            <div class="mt-6 flex gap-3 p-4 bg-yellow-50 text-yellow-800 rounded-xl text-sm leading-relaxed border border-yellow-200">
                                <svg class="w-6 h-6 flex-shrink-0 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <div>
                                    <strong>Penting:</strong> Pastikan nominal transfer sesuai persis dengan jumlah di atas (tanpa dibulatkan) agar sistem atau admin dapat memverifikasi pembayaran Anda dengan lebih cepat.
                                </div>
                            </div>

                            <div class="mt-10 pt-6 border-t border-gray-100 flex items-center justify-between">
                                <p class="text-sm text-gray-500">Sudah melakukan transfer?</p>
                                <a href="{{ route('mobile.iuran.upload') }}" class="px-8 py-3.5 bg-green-600 hover:bg-green-700 text-white rounded-xl font-bold transition shadow-lg shadow-green-200">
                                    Lanjut Upload Bukti Bayar
                                </a>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </x-slot:desktop>

</x-layouts.mobile>