<?php
use function Livewire\Volt\{state, uses};
use Livewire\WithFileUploads;
use App\Services\UserApiService;

uses(WithFileUploads::class);

state([
    'user' => session('user') ?? [],
    'name' => session('user.name') ?? '',
    'email' => session('user.email') ?? '',
    'phone' => session('user.phone') ?? '',
    'address' => session('user.address') ?? '',
    'city' => session('user.city') ?? '',
    'occupation' => session('user.occupation') ?? '',
    'gender' => session('user.gender') ?? '',
    'birth_date' => session('user.birth_date')
        ? substr(session('user.birth_date'), 0, 10)
        : '',
    'photo' => null,
    'photo_preview' => null,



    'snackbar' => ['type' => '', 'message' => ''],
]);



$submit = function () {

    if (!$this->name) {
        $this->snackbar = ['type' => 'error', 'message' => 'Nama lengkap wajib diisi'];
        return;
    }

    $token = session('token');
    if (!$token) {
        $this->snackbar = ['type' => 'error', 'message' => 'Sesi login tidak valid'];
        return;
    }

    $payload = [
        'name'      => $this->name,
        'phone'     => $this->phone,
        'address'   => $this->address,
        'city'      => $this->city,
        'occupation'=> $this->occupation,
        'gender'    => $this->gender ?: null,
        'birth_date'=> $this->birth_date ?: null,
    ];

    $response = UserApiService::updateProfileWithPhoto(
        $token,
        $payload,
        $this->photo
    );

    if ($response->failed()) {
        $this->snackbar = ['type' => 'error', 'message' => 'Gagal menyimpan perubahan profil'];
        return;
    }

    $user = $response->json('data');
    session(['user' => $user]);
    $this->user = $user;

    $this->snackbar = ['type' => 'success', 'message' => 'Profil berhasil diperbarui'];

    $this->redirect(route('mobile.profile'), navigate: true);
};
?>

@php
    $avatar = api_profile_url($user['profile_photo'] ?? null);
@endphp

<x-layouts.mobile title="Edit Profile">

    {{-- SNACKBAR --}}
    @if($snackbar['message'])
        <div class="fixed top-0 left-1/2 -translate-x-1/2 w-[390px]
                {{ $snackbar['type'] === 'error' ? 'bg-red-500' : 'bg-green-600' }}
                text-white px-4 py-3 text-sm font-medium shadow-lg rounded-b-lg z-[9999]">
            {{ $snackbar['message'] }}
        </div>
    @endif

    {{-- HEADER --}}
    <div class="w-full bg-green-600 px-4 py-4 flex items-center space-x-3 rounded-b-2xl">
        <button onclick="window.history.back()">
            <img src="/images/assets/icon/back.svg" class="w-5 h-5">
        </button>
        <p class="text-white font-semibold text-base">Edit Profil</p>
    </div>
    <div class="px-4 pb-20 mt-4 space-y-6">

        {{-- AVATAR --}}
        <div class="rounded-2xl p-6 text-white" style="background: linear-gradient(135deg, #4CAF50, #66BB6A)">
            <div class="flex justify-center">
                <div class="relative inline-block">
                    <div class="w-24 h-24 rounded-full bg-white overflow-hidden shadow border-2 border-white">
                        <template x-if="$wire.photo_preview">
                            <img :src="$wire.photo_preview" class="w-full h-full object-cover">
                        </template>
                        <template x-if="!$wire.photo_preview">
                            <img src="{{ $avatar ?: '/images/assets/default-avatar.png' }}" class="w-full h-full object-cover">
                        </template>
                    </div>

                    <input type="file" wire:model.defer="photo" accept="image/*" class="hidden" id="photoInput"
                           x-on:change="
                               $wire.photo_preview = '';
                               if ($event.target.files[0]) {
                                   let reader = new FileReader();
                                   reader.onload = (e) => $wire.set('photo_preview', e.target.result);
                                   reader.readAsDataURL($event.target.files[0]);
                               }
                           ">

                    <button type="button" onclick="document.getElementById('photoInput').click()"
                        class="absolute bottom-0 right-0 translate-x-1/4 translate-y-1/4
                               w-10 h-10 rounded-full bg-green-600
                               flex items-center justify-center shadow-md border-2 border-white">
                        <img src="/images/assets/icon/camera.svg" class="w-5 h-5">
                    </button>
                </div>
            </div>

            <div wire:loading wire:target="photo" class="text-xs text-white text-center mt-3">
                Mengunggah foto...
            </div>
        </div>

        {{-- FORM --}}
        <form wire:submit.prevent="submit" class="bg-gray-100 rounded-2xl p-6 space-y-6">

            <!-- Data Identitas -->
            <div>
                <p class="text-base font-semibold text-gray-800 mb-4">Data Identitas</p>
                <div class="space-y-4">

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input type="text" wire:model.defer="name" placeholder="Masukkan nama lengkap"
                            class="w-full rounded-lg px-4 py-3 text-sm bg-white border border-gray-300 focus:border-green-500 focus:ring-1 focus:ring-green-500 outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" value="{{ $email }}"
                            class="w-full rounded-lg px-4 py-3 text-sm bg-white border border-gray-300 focus:border-green-500 focus:ring-1 focus:ring-green-500 outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">No. Telepon</label>
                        <input type="tel" wire:model.defer="phone" placeholder="Contoh: 08123456789"
                            class="w-full rounded-lg px-4 py-3 text-sm bg-white border border-gray-300 focus:border-green-500 focus:ring-1 focus:ring-green-500 outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kota</label>
                        <input type="text" wire:model.defer="city" placeholder="Kota tempat tinggal"
                            class="w-full rounded-lg px-4 py-3 text-sm bg-white border border-gray-300 focus:border-green-500 focus:ring-1 focus:ring-green-500 outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pekerjaan</label>
                        <input type="text" wire:model.defer="occupation" placeholder="Pekerjaan saat ini"
                            class="w-full rounded-lg px-4 py-3 text-sm bg-white border border-gray-300 focus:border-green-500 focus:ring-1 focus:ring-green-500 outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Lengkap</label>
                        <textarea wire:model.defer="address" rows="3" placeholder="Jalan, RT/RW, Kelurahan, Kecamatan..."
                            class="w-full rounded-lg px-4 py-3 text-sm bg-white border border-gray-300 focus:border-green-500 focus:ring-1 focus:ring-green-500 outline-none resize-none"></textarea>
                    </div>

                </div>
            </div>

            <!-- Data Pribadi -->
            <div>
                <p class="text-base font-semibold text-gray-800 mb-4">Data Pribadi</p>
                <div class="space-y-4">

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin</label>
                        <select wire:model.defer="gender"
                            class="w-full rounded-lg px-4 py-3 text-sm bg-white border border-gray-300 focus:border-green-500 focus:ring-1 focus:ring-green-500 outline-none">
                            <option value="">Pilih jenis kelamin</option>
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir</label>
                        <input type="date" wire:model.defer="birth_date"
                            class="w-full rounded-lg px-4 py-3 text-sm bg-white border border-gray-300 focus:border-green-500 focus:ring-1 focus:ring-green-500 outline-none">
                    </div>

                </div>
            </div>
          


            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white py-4 rounded-xl font-semibold text-base transition duration-200">
                Simpan Perubahan
            </button>

        </form>
    </div>

    <x-mobile.navbar active="profile" />
</x-layouts.mobile>