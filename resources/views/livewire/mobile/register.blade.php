<?php
// resources/views/livewire/registration-form.blade.php

use App\Services\AuthApiService;
use function Livewire\Volt\usesFileUploads;
use Illuminate\Support\Facades\Http;
use function Livewire\Volt\{state, mount, updated};

usesFileUploads();

state([
    'step' => 1,
    'nama' => '',
    'username' => '',
    'email' => '',
    'telp' => '',
    'alamat' => '',
    'password' => '',
    'gender' => '',
    'birth_date' => '',
    'role' => '',
    'province_code' => '',
    'city_code' => '',
    'district_code' => '',
    'village_code' => '',
    'provinces' => [],
    'cities' => [],
    'districts' => [],
    'villages' => [],
    'city' => '',
    'kecamatan' => '',
    'kelurahan' => '',
    'occupation' => '',
    'occupation_other' => '',
    'profile_photo' => null,
    'photo_preview' => null,
    'has_kta' => null, // null / 'yes' / 'no'
    'kta_id' => '',
    'loadingCities' => false,
    'loadingDistricts' => false,
    'loadingVillages' => false,
    'isSubmitting' => false,
    'formErrors' => [],
    'snackbar' => ['type' => '', 'message' => ''],
]);

mount(function () {
    $resProv = Http::get('https://adminperwakb.ktadigital.id/api/regions/provinces');
    if ($resProv->successful()) {
        $this->provinces = $resProv->json()['data'] ?? [];
    }
    $this->province_code = 21;
    $this->loadingCities = true;
    $resCity = Http::get('https://adminperwakb.ktadigital.id/api/regions/cities', ['province_id' => 21]);
    if ($resCity->successful()) {
        $this->cities = $resCity->json()['data'] ?? [];
        $this->city_code = 2171;
        $city = collect($this->cities)->firstWhere('code', 2171);
        $this->city = $city['name'] ?? '';
    }
    $this->loadingCities = false;
    $this->loadingDistricts = true;
    $resDistrict = Http::get('https://adminperwakb.ktadigital.id/api/regions/districts', ['city_id' => 2171]);
    if ($resDistrict->successful()) {
        $this->districts = $resDistrict->json()['data'] ?? [];
    }
    $this->loadingDistricts = false;
});

updated([
    'profile_photo' => function () {
        try {
            $this->validateOnly('profile_photo', [
                'profile_photo' => 'image|mimes:jpg,jpeg,png|max:2048',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->reset('profile_photo', 'photo_preview');
            $message = collect($e->errors())->flatten()->first() ?? 'Ukuran foto terlalu besar';
            $this->snackbar = ['type' => 'error', 'message' => $message];
        }
    },
    'province_code' => function () {
        $this->reset(['city_code', 'district_code', 'village_code', 'city', 'kecamatan', 'kelurahan']);
        $this->cities = $this->districts = $this->villages = [];
        if (!$this->province_code) return;
        $this->loadingCities = true;
        $res = Http::timeout(60)->retry(2, 1000)
            ->get('https://adminperwakb.ktadigital.id/api/regions/cities', ['province_id' => $this->province_code]);
        if ($res->successful()) {
            $this->cities = $res->json()['data'] ?? [];
            if ($this->province_code == 21 && !$this->city_code) {
                $this->city_code = 2171;
            }
        }
        $this->loadingCities = false;
    },
    'city_code' => function () {
        $this->reset(['district_code', 'village_code', 'kecamatan', 'kelurahan']);
        $this->districts = $this->villages = [];
        if (!$this->city_code) return;
        $selected = collect($this->cities)->firstWhere('code', $this->city_code);
        $this->city = $selected['name'] ?? '';
        $this->loadingDistricts = true;
        $res = Http::timeout(60)->retry(2, 1000)
            ->get('https://adminperwakb.ktadigital.id/api/regions/districts', ['city_id' => $this->city_code]);
        if ($res->successful()) {
            $this->districts = $res->json()['data'] ?? [];
        } else {
            $this->snackbar = ['type' => 'error', 'message' => 'Gagal memuat kecamatan'];
        }
        $this->loadingDistricts = false;
    },
    'district_code' => function () {
        $this->reset(['village_code', 'kelurahan']);
        $this->villages = [];
        if (!$this->district_code) return;
        $selected = collect($this->districts)->firstWhere('code', $this->district_code);
        $this->kecamatan = $selected['name'] ?? '';
        $this->loadingVillages = true;
        $res = Http::timeout(60)->retry(2, 1000)
            ->get('https://adminperwakb.ktadigital.id/api/regions/villages', ['district_id' => $this->district_code]);
        if ($res->successful()) {
            $this->villages = $res->json()['data'] ?? [];
        } else {
            $this->snackbar = ['type' => 'error', 'message' => 'Gagal memuat kelurahan'];
        }
        $this->loadingVillages = false;
    },
    'village_code' => function () {
        if (!$this->village_code) return;
        $selected = collect($this->villages)->firstWhere('id', (int)$this->village_code);
        $this->kelurahan = $selected['name'] ?? '';
    },
]);

$nextStep = function () {
    $this->formErrors = [];
    $rules = $this->getValidationRulesForStep($this->step);
    try {
        $this->validate($rules);
        if ($this->step < 4) {
            $this->step++;
        }
    } catch (\Illuminate\Validation\ValidationException $e) {
        $this->formErrors = $e->errors();
        $firstError = collect($this->formErrors)->flatten()->first() ?? 'Harap isi data dengan benar';
        $this->snackbar = ['type' => 'error', 'message' => $firstError];
    }
};

$prevStep = function () {
    if ($this->step > 1) {
        $this->step--;
    }
};

$getValidationRulesForStep = function ($step) {
    $common = [
        'nama' => 'required|string|min:3|max:255',
        'username' => 'required|string|min:4|max:100|alpha_dash',
        'telp' => 'required|string|max:20',
        'password' => 'required|min:6',
        'role' => 'required|in:anggota,publik',
        'city' => 'required|string',
        'kecamatan' => 'required|string',
        'kelurahan' => 'required|string',
        'birth_date' => 'required|date',
        'occupation' => 'required|string',
        'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ];

    if ($step === 1) {
        return [
            'role' => $common['role'],
            'nama' => $common['nama'],
            'username' => $common['username'],
            'email' => 'nullable|email',
            'telp' => $common['telp'],
            'password' => $common['password'],
        ];
    }

    if ($step === 2) {
        return [
            'province_code' => 'required',
            'city_code' => 'required',
            'district_code' => 'required',
            'village_code' => 'required',
            'alamat' => 'nullable|string|max:500',
        ];
    }

    if ($step === 3) {
        return [
            'gender' => 'nullable|in:L,P',
            'birth_date' => $common['birth_date'],
            'occupation' => $common['occupation'],
            'profile_photo' => $common['profile_photo'],
        ];
    }

    if ($step === 4) {
        if ($this->has_kta === 'yes') {
            return ['kta_id' => 'required|string|min:5|max:50'];
        }
        return [];
    }

    return [];
};

$submit = function () {
    $this->isSubmitting = true;
    $this->formErrors = [];
    $this->snackbar = ['type' => '', 'message' => ''];

    try {
        $this->validate([
            'nama' => 'required|string|min:3|max:255',
            'username' => 'required|string|min:4|max:100|alpha_dash',
            'telp' => 'required|string|max:20',
            'password' => 'required|min:6',
            'role' => 'required|in:anggota,publik',
            'city' => 'required|string',
            'kecamatan' => 'required|string',
            'kelurahan' => 'required|string',
            'email' => 'nullable|email',
            'birth_date' => 'required|date',
            'occupation' => 'required|string',
            'profile_photo' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'kta_id' => $this->has_kta === 'yes' ? 'required|string|min:5|max:50' : 'nullable',
        ]);

        $payload = [
            'name' => $this->nama,
            'username' => $this->username,
            'email' => $this->email ?: null,
            'phone' => $this->telp,
            'address' => $this->alamat,
            'password' => $this->password,
            'gender' => $this->gender,
            'birth_date' => $this->birth_date,
            'city' => $this->city,
            'kecamatan' => $this->kecamatan,
            'kelurahan' => $this->kelurahan,
            'occupation' => $this->occupation === 'lain_lain' ? $this->occupation_other : $this->occupation,
            'role' => $this->role,
            'kta_id' => $this->has_kta === 'yes' ? $this->kta_id : null,
        ];

        $files = $this->profile_photo instanceof \Illuminate\Http\UploadedFile
            ? ['profile_photo' => $this->profile_photo]
            : [];

        $response = AuthApiService::register($payload, $files);
        $body = $response->json();

        if ($response->failed()) {
            if ($response->status() === 422 && isset($body['errors'])) {
                $this->formErrors = $body['errors'];
                $firstError = collect($body['errors'])->flatten()->first() ?? 'Validasi gagal';
                $this->snackbar = ['type' => 'error', 'message' => $firstError];
            } else {
                $this->snackbar = [
                    'type' => 'error',
                    'message' => $body['message'] ?? 'Registrasi gagal (status ' . $response->status() . ')'
                ];
            }
            return;
        }

        if (!isset($body['status']) || !$body['status']) {
            $this->snackbar = ['type' => 'error', 'message' => $body['message'] ?? 'Registrasi gagal'];
            return;
        }

        $this->snackbar = [
            'type' => 'success',
            'message' => 'Registrasi berhasil! Mengalihkan ke halaman login...'
        ];
        $this->dispatch('registered-success');

    } catch (\Exception $e) {
        $this->snackbar = ['type' => 'error', 'message' => $e->getMessage()];
    } finally {
        $this->isSubmitting = false;
    }
};
?>

<x-layouts.mobile title="Daftar Akun">
    <x-mobile.snackbar />

    <div class="relative w-full min-h-full p-4 flex flex-col">
        <img src="/images/assets/bg-pattern.png"
             class="absolute inset-0 w-full h-full object-cover pointer-events-none" />

        <div class="relative z-10 px-6 pt-10 pb-20">
            <a href="/auth" class="text-white text-2xl font-bold">&larr;</a>

            <div class="flex justify-center mt-6 mb-6">
                <img src="/images/assets/logo.png" class="w-24">
            </div>

            <div class="bg-white/90 backdrop-blur-md rounded-2xl p-6 shadow-xl border border-gray-100">

                <div class="flex justify-between items-center mb-6">
                    <h1 class="font-bold text-2xl text-gray-800">Daftar Akun</h1>
                    <span class="text-sm text-gray-600">Langkah {{ $step }} / 4</span>
                </div>

                @if($step === 1)
                    <x-mobile.select wire:model="role" label="Daftar Sebagai">
                        <option value="">Pilih jenis pendaftaran</option>
                        <option value="anggota">Anggota</option>
                        <option value="publik">Publik</option>
                    </x-mobile.select>
                    @error('role') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                    @if($formErrors['role'] ?? false)
                        <p class="text-red-600 text-xs mt-1">{{ $formErrors['role'][0] }}</p>
                    @endif

                    <x-mobile.input wire:model="nama" placeholder="Nama lengkap" />
                    @error('nama') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                    @if($formErrors['name'] ?? false)
                        <p class="text-red-600 text-xs mt-1">{{ $formErrors['name'][0] }}</p>
                    @endif

                    <x-mobile.input wire:model="username" placeholder="Username (tanpa spasi)" />
                    @error('username') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                    @if($formErrors['username'] ?? false)
                        <p class="text-red-600 text-xs mt-1">{{ $formErrors['username'][0] }}</p>
                    @endif

                    <x-mobile.input wire:model="email" placeholder="Email (opsional)" type="email" />
                    @error('email') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                    @if($formErrors['email'] ?? false)
                        <p class="text-red-600 text-xs mt-1">{{ $formErrors['email'][0] }}</p>
                    @endif

                    <x-mobile.input wire:model="telp" placeholder="No Telepon" />
                    @error('telp') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                    @if($formErrors['phone'] ?? false)
                        <p class="text-red-600 text-xs mt-1">{{ $formErrors['phone'][0] }}</p>
                    @endif

                    <x-mobile.input wire:model="password" placeholder="Password" type="password" icon="eye" />
                    @error('password') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                    @if($formErrors['password'] ?? false)
                        <p class="text-red-600 text-xs mt-1">{{ $formErrors['password'][0] }}</p>
                    @endif

                @elseif($step === 2)
                    <x-mobile.input wire:model="alamat" placeholder="Alamat lengkap" />
                    @error('alamat') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                    @if($formErrors['address'] ?? false)
                        <p class="text-red-600 text-xs mt-1">{{ $formErrors['address'][0] }}</p>
                    @endif

                    <x-mobile.select wire:model.live="province_code" label="Provinsi" disabled>
                        <option value="">Memuat provinsi...</option>
                        @foreach ($provinces as $prov)
                            <option value="{{ $prov['code'] ?? $prov['id'] }}">{{ $prov['name'] }}</option>
                        @endforeach
                    </x-mobile.select>
                    @error('province_code') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror

                    <x-mobile.select wire:model.live="city_code" label="Kota / Kabupaten" disabled>
                        <option value="">{{ $loadingCities ? 'Memuat kota...' : 'Pilih Kota / Kabupaten' }}</option>
                        @foreach ($cities as $ct)
                            <option value="{{ $ct['code'] ?? $ct['id'] }}">{{ $ct['name'] }}</option>
                        @endforeach
                    </x-mobile.select>
                    @error('city') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                    @if($formErrors['city'] ?? false)
                        <p class="text-red-600 text-xs mt-1">{{ $formErrors['city'][0] }}</p>
                    @endif

                    <x-mobile.select wire:model.live="district_code" label="Kecamatan" :disabled="$loadingDistricts">
                        <option value="">{{ $loadingDistricts ? 'Memuat kecamatan...' : 'Pilih Kecamatan' }}</option>
                        @foreach ($districts as $dist)
                            <option value="{{ $dist['code'] ?? $dist['id'] }}">{{ $dist['name'] }}</option>
                        @endforeach
                    </x-mobile.select>
                    @error('kecamatan') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                    @if($formErrors['kecamatan'] ?? false)
                        <p class="text-red-600 text-xs mt-1">{{ $formErrors['kecamatan'][0] }}</p>
                    @endif

                    <x-mobile.select wire:model.live="village_code" label="Kelurahan / Desa" :disabled="$loadingVillages || !$district_code">
                        <option value="">{{ $loadingVillages ? 'Memuat kelurahan...' : 'Pilih Kelurahan / Desa' }}</option>
                        @foreach ($villages as $vil)
                            <option value="{{ $vil['code'] ?? $vil['id'] }}">{{ $vil['name'] }}</option>
                        @endforeach
                    </x-mobile.select>
                    @error('kelurahan') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                    @if($formErrors['kelurahan'] ?? false)
                        <p class="text-red-600 text-xs mt-1">{{ $formErrors['kelurahan'][0] }}</p>
                    @endif

                @elseif($step === 3)
                    <x-mobile.select wire:model="gender" label="Jenis Kelamin">
                        <option value="">Pilih opsi</option>
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                    </x-mobile.select>
                    @error('gender') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror

                    <x-mobile.input wire:model="birth_date" type="date" label="Tanggal Lahir" />
                    @error('birth_date') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror

                    <x-mobile.select wire:model.live="occupation" label="Pekerjaan">
                        <option value="">Pilih pekerjaan</option>
                        <option value="wiraswasta">Wiraswasta</option>
                        <option value="karyawan_swasta">Karyawan Swasta</option>
                        <option value="asn">ASN</option>
                        <option value="tidak_bekerja">Tidak Bekerja</option>
                        <option value="lain_lain">Lain-lain</option>
                    </x-mobile.select>
                    @error('occupation') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                    @if ($occupation === 'lain_lain')
                        <x-mobile.input wire:model="occupation_other" placeholder="Tuliskan pekerjaan Anda" class="mt-2" />
                        @error('occupation_other') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                    @endif

                    <!-- Foto Profil -->
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-800 mb-2">Foto Profil</label>
                        <label class="flex flex-col items-center justify-center w-full h-36 border-2 border-dashed border-gray-300 rounded-xl cursor-pointer bg-gray-50 hover:bg-gray-100 transition">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6 text-center">
                                <svg class="w-10 h-10 mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4a1 1 0 011-1h8a1 1 0 011 1v12m-5 4h.01M12 20h.01"/>
                                </svg>
                                <p class="text-sm text-gray-600">Klik untuk upload foto</p>
                                <p class="text-xs text-gray-400 mt-1">JPG / PNG (maks. 2MB)</p>
                            </div>
                            <input type="file" class="hidden" accept="image/*" wire:model.defer="profile_photo"
                                   x-on:change="
                                       $wire.photo_preview = '';
                                       if ($event.target.files[0]) {
                                           let reader = new FileReader();
                                           reader.onload = (e) => $wire.set('photo_preview', e.target.result);
                                           reader.readAsDataURL($event.target.files[0]);
                                       }
                                   "/>
                        </label>
                        <template x-if="$wire.photo_preview">
                            <div class="mt-5 flex justify-center">
                                <img :src="$wire.photo_preview" alt="Preview" class="h-28 w-28 object-cover rounded-full border-4 border-green-500 shadow-md">
                            </div>
                        </template>
                    </div>
                    @error('profile_photo') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                    @if($formErrors['profile_photo'] ?? false)
                        <p class="text-red-600 text-xs mt-2">{{ $formErrors['profile_photo'][0] }}</p>
                    @endif

                @elseif($step === 4)

    <h2 class="text-xl font-bold text-center mb-2 text-gray-800">
        Sudah Punya KTA?
    </h2>

    <p class="text-center text-sm text-gray-500 mb-6">
        Pilih salah satu opsi berikut
    </p>

    <div class="space-y-4">

        <!-- OPSI SUDAH PUNYA -->
        <div
            wire:click="$set('has_kta', 'yes')"
            class="cursor-pointer border-2 rounded-xl p-4 transition
                   {{ $has_kta === 'yes'
                        ? 'border-green-600 bg-green-50'
                        : 'border-gray-200 bg-white hover:border-green-400' }}">

            <div class="flex items-start gap-3">
                <div class="mt-1">
                    <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center
                        {{ $has_kta === 'yes'
                            ? 'border-green-600'
                            : 'border-gray-400' }}">
                        @if($has_kta === 'yes')
                            <div class="w-2.5 h-2.5 bg-green-600 rounded-full"></div>
                        @endif
                    </div>
                </div>

                <div>
                    <h3 class="font-semibold text-gray-800">Saya sudah memiliki KTA</h3>
                    <p class="text-sm text-gray-500 mt-1">
                        Masukkan nomor KTA untuk diverifikasi sistem.
                    </p>
                </div>
            </div>
        </div>

        <!-- OPSI BELUM PUNYA -->
        <div
            wire:click="$set('has_kta', 'no')"
            class="cursor-pointer border-2 rounded-xl p-4 transition
                   {{ $has_kta === 'no'
                        ? 'border-green-600 bg-green-50'
                        : 'border-gray-200 bg-white hover:border-green-400' }}">

            <div class="flex items-start gap-3">
                <div class="mt-1">
                    <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center
                        {{ $has_kta === 'no'
                            ? 'border-green-600'
                            : 'border-gray-400' }}">
                        @if($has_kta === 'no')
                            <div class="w-2.5 h-2.5 bg-green-600 rounded-full"></div>
                        @endif
                    </div>
                </div>

                <div>
                    <h3 class="font-semibold text-gray-800">Saya belum memiliki KTA</h3>
                    <p class="text-sm text-gray-500 mt-1">
                        Nomor KTA akan dibuat otomatis setelah registrasi berhasil.
                    </p>
                </div>
            </div>
        </div>

    </div>

    <!-- INPUT KTA JIKA SUDAH PUNYA -->
    @if($has_kta === 'yes')
        <div class="mt-6">
            <x-mobile.input
                wire:model="kta_id"
                placeholder="Masukkan nomor KTA Anda"
                label="Nomor KTA"
            />
            @error('kta_id')
                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
            @enderror
            @if($formErrors['kta_id'] ?? false)
                <p class="text-red-600 text-xs mt-1">
                    {{ $formErrors['kta_id'][0] }}
                </p>
            @endif
        </div>
    @endif
@endif

                <div class="flex gap-4 mt-10">

    @if($step > 1)
        <button
            type="button"
            wire:click="prevStep"
            class="flex-1 h-12 bg-gray-300 hover:bg-gray-400 
                   text-gray-800 rounded-lg font-medium 
                   flex items-center justify-center transition"
        >
            Kembali
        </button>
    @endif

    @if($step < 4)
        <x-mobile.button
            class="flex-1 h-12"
            wire:click="nextStep">
            Selanjutnya
        </x-mobile.button>
    @else
        <x-mobile.button
            class="flex-1 h-12"
            wire:click="submit"
            :disabled="$isSubmitting"
        >
            <span x-show="!$wire.isSubmitting">
                Daftar
            </span>

            <span x-show="$wire.isSubmitting"
                  class="flex items-center justify-center gap-2">

                <svg class="animate-spin h-4 w-4 text-white"
                     xmlns="http://www.w3.org/2000/svg"
                     fill="none"
                     viewBox="0 0 24 24">
                    <circle class="opacity-25"
                            cx="12" cy="12" r="10"
                            stroke="currentColor"
                            stroke-width="4"></circle>
                    <path class="opacity-75"
                          fill="currentColor"
                          d="M4 12a8 8 0 018-8V0C5.373 0 0 
                             5.373 0 12h4zm2 5.291A7.962 
                             7.962 0 014 12H0c0 3.042 
                             1.135 5.824 3 7.938l3-2.647z"/>
                </svg>

                Memproses...
            </span>
        </x-mobile.button>
    @endif

</div>

                <p class="text-center text-sm mt-6 text-gray-600">
                    Sudah punya akun?
                    <a href="/login" class="text-green-700 font-semibold hover:underline">Login</a>
                </p>
            </div>
        </div>
    </div>
</x-layouts.mobile>

<script>
    document.addEventListener('registered-success', () => {
        setTimeout(() => {
            Livewire.navigate('/login');
        }, 1500);
    });
</script>