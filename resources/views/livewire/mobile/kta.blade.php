<x-layouts.mobile title="Kartu Tanda Anggota">
    @php
        $token = session('token');
        if ($token) {
            $response = \App\Services\AuthApiService::me($token);
            if ($response->successful()) {
                $user = $response->json('data');
                session(['user' => $user]);
            }
        }
        $user = session('user') ?? [];
        $name = $user['name'] ?? '-';
        $email = $user['email'] ?? '-';
        $phone = $user['phone'] ?? '-';
        $id = str_pad($user['id'] ?? 0, 6, '0', STR_PAD_LEFT);
        $expired = !empty($user['expired_at'])
            ? date('d M Y', strtotime($user['expired_at']))
            : '-';
    @endphp
    {{-- HEADER --}}
    <div class="w-full bg-green-600 px-4 py-4 flex items-center space-x-3 rounded-b-2xl">
        <button onclick="window.history.back()">
            <img src="/images/assets/icon/back.svg" class="w-5 h-5">
        </button>
        <p class="text-white font-semibold text-base">Kartu Tanda Anggota</p>
    </div>
    @php
        $u = session('user') ?? [];
        $name = $u['name'] ?? '-';
        $email = $u['email'] ?? '-';
        $phone = $u['phone'] ?? '-';
        $id = $u['kta_id'] ?? '-';
        $expired = $u['expired_at'] ? date('d M Y', strtotime($u['expired_at'])) : '-';
        $photo = api_profile_url($u['profile_photo'] ?? null);

        $ktaTemplate = null;
        $ktaResponse = \App\Services\KtaTemplateApiService::getActive();
        if ($ktaResponse->successful()) {
            $ktaTemplate = $ktaResponse->json('data');
        }
        $frontImage = $ktaTemplate['front_image'] ?? '/images/assets/kta/kta_depan.png';
        $backImage  = $ktaTemplate['back_image']  ?? '/images/assets/kta/kta_belakang.png';
    @endphp
    <div class="px-6 mt-6 flex justify-center">
        <div x-data="{ side: 'front', flip(){ this.side = this.side === 'front' ? 'back' : 'front' } }"
            class="w-full flex flex-col items-center">
            {{-- KTA CONTAINER --}}
            <div class="relative perspective w-[280px] max-w-full h-[440px] md:h-[480px]">
                <div :class="side === 'back' ? 'flipper is-flipped' : 'flipper'">
                    {{-- ============= FRONT SIDE ============= --}}
                    <div class="face face-front bg-white rounded-2xl shadow-lg overflow-hidden ">
                        <img src="{{ $frontImage }}" class="w-full object-contain">
                        {{-- FOTO USER --}}
                        <img src="{{ $photo }}"
                            class="absolute top-[105px] left-1/2 -translate-x-1/2 w-24 h-28 object-cover rounded-md border shadow">
                        {{-- NAMA --}}
                        <p class="absolute top-[250px] w-full text-center font-bold text-gray-900 text-[15px]">
                            {{ $name }}
                        </p>
                        {{-- JABATAN (opsional) --}}
                        <p class="absolute top-[270px] w-full text-center text-gray-600 text-xs">
                            Anggota
                        </p>
                        {{-- DETAIL --}}
                        <div class="absolute top-[305px] left-8 text-[11px] text-gray-700 leading-4">
                            <p><strong>ID No</strong> : {{ $id }}</p>
                            <p><strong>Email</strong> : {{ $email }}</p>
                            <p><strong>Phone</strong> : {{ $phone }}</p>
                            <p><strong>Expired</strong> : {{ $expired }}</p>
                        </div>
                    </div>
                    {{-- ============= BACK SIDE ============= --}}
                    <div class="face face-back bg-white rounded-2xl shadow-lg overflow-hidden ">
                        {{-- TEMPLATE BACKGROUND --}}
                        <img src="{{ $backImage }}" class="w-full object-contain">
                        {{-- HEADER VISI --}}
                        <div class="absolute top-[100px] left-0 w-full text-center">
                            <p class="bg-[#F59E0B] text-white font-bold text-[14px] py-1 w-[80%] mx-auto rounded">
                                VISI
                            </p>
                        </div>
                        {{-- TEKS VISI --}}
                        <div class="absolute top-[140px] left-0 w-full px-6 text-center">
                            <p class="text-gray-700 text-[12px] leading-[16px]">
                                "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor
                                incididunt ut labore et dolore magna aliqua. Lorem ipsum dolor sit amet,
                                consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore
                                magna aliqua."
                            </p>
                        </div>
                        {{-- JABATAN --}}
                        <p
                            class="absolute top-[255px] left-0 w-full text-center text-[12px] font-semibold text-gray-800">
                            Ketua Umum
                        </p>
                        {{-- TANDA TANGAN --}}
                        <img src="/images/assets/kta/ttd.png"
                            class="absolute top-[280px] left-1/2 -translate-x-1/2 w-28 opacity-90">
                        {{-- ID ANGGOTA --}}
                        <p class="absolute bottom-[95px] w-full text-center font-semibold text-gray-900 text-[14px]">
                            ID {{ $id }}
                        </p>
                    </div>
                </div>
            </div>
            {{-- ACTION BUTTONS --}}
            <div class="flex justify-center space-x-6 kta-actions">
                <button class="bg-green-600 w-12 h-12 rounded-xl flex items-center justify-center shadow-md">
                    <img src="/images/assets/icon/download.svg" class="w-6 h-6">
                </button>
                <button class="bg-green-600 w-12 h-12 rounded-xl flex items-center justify-center shadow-md"
                    @click="flip()">
                    <img src="/images/assets/icon/reverse.svg" class="w-6 h-6">
                </button>
            </div>
        </div>
    </div>
    <div class="h-20"></div>
    <style>
        .kta-actions {
            margin-top: 2.6rem; /* Konsisten margin untuk semua ukuran, adjust jika perlu */
        }
        /* Tambahkan responsif untuk button size jika diperlukan */
        @media (min-width: 640px) {
            .kta-actions button {
                width: 3.5rem;
                height: 3.5rem;
            }
            .kta-actions img {
                width: 1.75rem;
                height: 1.75rem;
            }
        }
        @media (min-width: 1024px) {
            .kta-actions button {
                width: 4rem;
                height: 4rem;
            }
            .kta-actions img {
                width: 2rem;
                height: 2rem;
            }
        }
    </style>
    <x-mobile.navbar active="home" />

    <!-- ================== DESKTOP VIEW ================== -->
    <x-slot:desktop>
        <x-desktop.layout title="Kartu Tanda Anggota (KTA) Digital">
            <div class="flex flex-col lg:flex-row gap-8 bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                <!-- KIRI: Informasi & Instruksi -->
                <div class="flex-1 space-y-6">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-2">KTA Digital Perwakab</h3>
                        <p class="text-gray-500">Berikut adalah identitas resmi Anda sebagai anggota. Anda dapat mengunduh kartu ini atau mencetaknya untuk keperluan administratif.</p>
                    </div>

                    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-r-xl">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800">Informasi Penting</h3>
                                <div class="mt-2 text-sm text-blue-700">
                                    <ul class="list-disc pl-5 space-y-1">
                                        <li>KTA ini berlaku sebagai bukti keanggotaan sah.</li>
                                        <li>Masa berlaku dihitung berdasarkan iuran tahunan yang Anda bayarkan.</li>
                                        <li>Tunjukkan KTA digital ini saat menghadiri acara resmi.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-100">
                        <h4 class="text-sm font-bold text-gray-800 mb-4 uppercase tracking-wider">Detail Anggota</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-gray-50 p-3 rounded-lg">
                                <span class="block text-xs text-gray-400 font-semibold mb-1">Nama Lengkap</span>
                                <span class="block text-sm font-bold text-gray-800">{{ $name }}</span>
                            </div>
                            <div class="bg-gray-50 p-3 rounded-lg">
                                <span class="block text-xs text-gray-400 font-semibold mb-1">ID Anggota</span>
                                <span class="block text-sm font-bold text-gray-800">{{ $id }}</span>
                            </div>
                            <div class="bg-gray-50 p-3 rounded-lg">
                                <span class="block text-xs text-gray-400 font-semibold mb-1">Email</span>
                                <span class="block text-sm font-bold text-gray-800">{{ $email }}</span>
                            </div>
                            <div class="bg-gray-50 p-3 rounded-lg">
                                <span class="block text-xs text-gray-400 font-semibold mb-1">Status Masa Berlaku</span>
                                <span class="block text-sm font-bold text-green-600">{{ $expired }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- KANAN: Tampilan KTA -->
                <div class="flex-shrink-0 w-full lg:w-[400px] bg-gray-50 rounded-2xl p-6 flex flex-col items-center justify-center border border-gray-100 border-dashed">
                    
                    <div x-data="{ side: 'front', flip(){ this.side = this.side === 'front' ? 'back' : 'front' } }"
                        class="w-full flex flex-col items-center">
                        {{-- KTA CONTAINER --}}
                        <div class="relative perspective w-[280px] max-w-full h-[440px]">
                            <div :class="side === 'back' ? 'flipper is-flipped' : 'flipper'">
                                {{-- ============= FRONT SIDE ============= --}}
                                <div class="face face-front bg-white rounded-2xl shadow-xl overflow-hidden ring-1 ring-black/5">
                                    <img src="{{ $frontImage }}" class="w-full object-contain">
                                    {{-- FOTO USER --}}
                                    <img src="{{ $photo }}"
                                        class="absolute top-[105px] left-1/2 -translate-x-1/2 w-24 h-28 object-cover rounded-md border shadow">
                                    {{-- NAMA --}}
                                    <p class="absolute top-[250px] w-full text-center font-bold text-gray-900 text-[15px]">
                                        {{ $name }}
                                    </p>
                                    {{-- JABATAN (opsional) --}}
                                    <p class="absolute top-[270px] w-full text-center text-gray-600 text-xs">
                                        Anggota
                                    </p>
                                    {{-- DETAIL --}}
                                    <div class="absolute top-[305px] left-8 text-[11px] text-gray-700 leading-4">
                                        <p><strong>ID No</strong> : {{ $id }}</p>
                                        <p><strong>Email</strong> : {{ $email }}</p>
                                        <p><strong>Phone</strong> : {{ $phone }}</p>
                                        <p><strong>Expired</strong> : {{ $expired }}</p>
                                    </div>
                                </div>
                                {{-- ============= BACK SIDE ============= --}}
                                <div class="face face-back bg-white rounded-2xl shadow-xl overflow-hidden ring-1 ring-black/5">
                                    {{-- TEMPLATE BACKGROUND --}}
                                    <img src="{{ $backImage }}" class="w-full object-contain">
                                    {{-- HEADER VISI --}}
                                    <div class="absolute top-[100px] left-0 w-full text-center">
                                        <p class="bg-[#F59E0B] text-white font-bold text-[14px] py-1 w-[80%] mx-auto rounded">
                                            VISI
                                        </p>
                                    </div>
                                    {{-- TEKS VISI --}}
                                    <div class="absolute top-[140px] left-0 w-full px-6 text-center">
                                        <p class="text-gray-700 text-[12px] leading-[16px]">
                                            "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor
                                            incididunt ut labore et dolore magna aliqua. Lorem ipsum dolor sit amet,
                                            consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore
                                            magna aliqua."
                                        </p>
                                    </div>
                                    {{-- JABATAN --}}
                                    <p
                                        class="absolute top-[255px] left-0 w-full text-center text-[12px] font-semibold text-gray-800">
                                        Ketua Umum
                                    </p>
                                    {{-- TANDA TANGAN --}}
                                    <img src="/images/assets/kta/ttd.png"
                                        class="absolute top-[280px] left-1/2 -translate-x-1/2 w-28 opacity-90">
                                    {{-- ID ANGGOTA --}}
                                    <p class="absolute bottom-[95px] w-full text-center font-semibold text-gray-900 text-[14px]">
                                        ID {{ $id }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        {{-- ACTION BUTTONS DESKTOP --}}
                        <div class="flex justify-center space-x-4 mt-10">
                            <button class="flex items-center space-x-2 bg-green-600 hover:bg-green-700 text-white px-5 py-3 rounded-xl shadow-lg transition duration-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                <span class="font-semibold text-sm">Unduh KTA</span>
                            </button>
                            <button class="flex items-center space-x-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 px-5 py-3 rounded-xl shadow-sm transition duration-200"
                                @click="flip()">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                <span class="font-semibold text-sm">Balik Kartu</span>
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </x-desktop.layout>
    </x-slot:desktop>

</x-layouts.mobile>