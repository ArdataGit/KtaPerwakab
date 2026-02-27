<x-layouts.mobile title="Kartu Tanda Anggota">
    @php
        use App\Services\AuthApiService;

        $token = session('token');
        if ($token) {
            $response = AuthApiService::me($token);
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
    @endphp
    <div class="px-6 mt-6 flex justify-center">
        <div x-data="{ side: 'front', flip(){ this.side = this.side === 'front' ? 'back' : 'front' } }"
            class="w-full flex flex-col items-center">
            {{-- KTA CONTAINER --}}
            <div class="relative perspective w-[280px] max-w-full h-[440px] md:h-[480px]">
                <div :class="side === 'back' ? 'flipper is-flipped' : 'flipper'">
                    {{-- ============= FRONT SIDE ============= --}}
                    <div class="face face-front bg-white rounded-2xl shadow-lg overflow-hidden ">
                        <img src="/images/assets/kta/kta_depan.png" class="w-full object-contain">
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
                        <img src="/images/assets/kta/kta_belakang.png" class="w-full object-contain">
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
</x-layouts.mobile>