<?php

use function Livewire\Volt\{state, uses};
use Livewire\WithFileUploads;
use App\Services\UserApiService;

uses(WithFileUploads::class);

state([
    'user' => session('user') ?? [],

    /*
    |--------------------------------------------------------------------------
    | DATA IDENTITAS
    |--------------------------------------------------------------------------
    */
    'name' => session('user.name') ?? '',
    'email' => session('user.email') ?? '',
    'phone' => session('user.phone') ?? '',
    'address' => session('user.address') ?? '',
    'city' => session('user.city') ?? '',
    'occupation' => session('user.occupation') ?? '',

    /*
    |--------------------------------------------------------------------------
    | DATA PRIBADI
    |--------------------------------------------------------------------------
    */
    'gender' => session('user.gender') ?? '',
    'birth_date' => session('user.birth_date') ?? '',

    /*
    |--------------------------------------------------------------------------
    | PHOTO
    |--------------------------------------------------------------------------
    */
    'photo' => null,

    /*
    |--------------------------------------------------------------------------
    | UI STATE
    |--------------------------------------------------------------------------
    */
    'snackbar' => ['type' => '', 'message' => ''],
]);

$submit = function () {

    if (!$this->name) {
        $this->snackbar = [
            'type' => 'error',
            'message' => 'Nama lengkap wajib diisi',
        ];
        return;
    }

    $token = session('token');

    if (!$token) {
        $this->snackbar = [
            'type' => 'error',
            'message' => 'Sesi login tidak valid',
        ];
        return;
    }

    /*
    |--------------------------------------------------------------------------
    | PAYLOAD
    |--------------------------------------------------------------------------
    */
    $payload = [
        'name' => $this->name,
        'phone' => $this->phone,
        'address' => $this->address,
        'city' => $this->city,
        'occupation' => $this->occupation,
        'gender' => $this->gender ?: null,
        'birth_date' => $this->birth_date ?: null,
    ];

    /*
    |--------------------------------------------------------------------------
    | API CALL
    |--------------------------------------------------------------------------
    */
    $response = UserApiService::updateProfileWithPhoto(
        $token,
        $payload,
        $this->photo
    );

    if ($response->failed()) {
        $this->snackbar = [
            'type' => 'error',
            'message' => 'Gagal menyimpan perubahan profil',
        ];
        return;
    }

    /*
    |--------------------------------------------------------------------------
    | SYNC SESSION
    |--------------------------------------------------------------------------
    */
    $user = $response->json('data');
    session(['user' => $user]);
    $this->user = $user;

    $this->snackbar = [
        'type' => 'success',
        'message' => 'Profil berhasil diperbarui',
    ];

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

    <div class="px-4 pb-20 mt-4 space-y-4">

        {{-- AVATAR --}}
        <div class="rounded-2xl p-6 text-white" style="background: linear-gradient(135deg, #4CAF50, #66BB6A)">
            <div class="flex justify-center">
                <div class="relative inline-block">
                    <div class="w-24 h-24 rounded-full bg-white overflow-hidden shadow">
                        @if($photo)
                            <img src="{{ $photo->temporaryUrl() }}" class="w-full h-full object-cover">
                        @else
                            <img src="{{ $avatar ?: '/images/assets/default-avatar.png' }}"
                                class="w-full h-full object-cover">
                        @endif
                    </div>

                    <input type="file" wire:model="photo" accept="image/*" class="hidden" id="photoInput">

                    <button type="button" onclick="document.getElementById('photoInput').click()" class="absolute bottom-0 right-0 translate-x-1/4 translate-y-1/4
                               w-10 h-10 rounded-full bg-green-600
                               flex items-center justify-center
                               shadow-md border-2 border-white">
                        <img src="/images/assets/icon/camera.svg" class="w-5 h-5">
                    </button>
                </div>
            </div>

            <div wire:loading wire:target="photo" class="text-xs text-white text-center mt-2">
                Mengunggah foto...
            </div>
        </div>

        {{-- FORM --}}
        <form wire:submit.prevent="submit" class="bg-gray-100 rounded-2xl p-4 space-y-6">

            {{-- DATA IDENTITAS --}}
            <div>
                <p class="text-sm font-semibold text-gray-700 mb-2">Data Identitas</p>

                <div class="space-y-3">
                    <input type="text" wire:model.defer="name" placeholder="Nama Lengkap"
                        class="w-full rounded-lg px-3 py-2 text-sm bg-gray-200 focus:outline-none">

                    <input type="email" value="{{ $email }}" readonly
                        class="w-full rounded-lg px-3 py-2 text-sm bg-gray-200">

                    <input type="text" wire:model.defer="phone" placeholder="No Telepon"
                        class="w-full rounded-lg px-3 py-2 text-sm bg-gray-200 focus:outline-none">

                    <input type="text" wire:model.defer="city" placeholder="Kota"
                        class="w-full rounded-lg px-3 py-2 text-sm bg-gray-200 focus:outline-none">

                    <input type="text" wire:model.defer="occupation" placeholder="Pekerjaan"
                        class="w-full rounded-lg px-3 py-2 text-sm bg-gray-200 focus:outline-none">

                    <textarea wire:model.defer="address" rows="3" placeholder="Alamat"
                        class="w-full rounded-lg px-3 py-2 text-sm bg-gray-200 resize-none focus:outline-none"></textarea>
                </div>
            </div>

            {{-- DATA PRIBADI --}}
            <div>
                <p class="text-sm font-semibold text-gray-700 mb-2">Data Pribadi</p>

                <div class="space-y-3">
                    <select wire:model.defer="gender"
                        class="w-full rounded-lg px-3 py-2 text-sm bg-gray-200 focus:outline-none">
                        <option value="">Jenis Kelamin</option>
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                    </select>

                    <input type="date" wire:model.defer="birth_date"
                        class="w-full rounded-lg px-3 py-2 text-sm bg-gray-200 focus:outline-none">
                </div>
            </div>

            <button type="submit" class="w-full bg-green-600 text-white py-3 rounded-xl font-semibold">
                Simpan Perubahan
            </button>

        </form>
    </div>

    <x-mobile.navbar active="profile" />
</x-layouts.mobile>