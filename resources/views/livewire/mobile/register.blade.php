<?php

use App\Services\AuthApiService;
use function Livewire\Volt\state;

state([
    'nama' => '',
    'email' => '',
    'telp' => '',
    'alamat' => '',
    'password' => '',
    'gender' => '',
    'birth_date' => '',
    'city' => '',
    'occupation' => '',
    'role' => '',
    // tambahkan property snackbar untuk binding
    'snackbar' => ['type' => '', 'message' => ''],
]);


$submit = function () {

    if (!$this->nama || !$this->email || !$this->password) {
        // Set state snackbar (Volt-friendly)
        $this->snackbar = ['type' => 'error', 'message' => 'Semua field wajib diisi'];
        return;
    }

    $payload = [
        'name' => $this->nama,
        'email' => $this->email,
        'phone' => $this->telp,
        'address' => $this->alamat,
        'password' => $this->password,
        'gender' => $this->gender,
        'birth_date' => $this->birth_date,
        'city' => $this->city,
        'occupation' => $this->occupation,
        'role' => $this->role,
    ];

    $response = AuthApiService::register($payload);

    if ($response->failed()) {

        $body = $response->json();
        $error = 'Registrasi gagal';

        // VALIDASI GAGAL → TANGKAP PESAN VALIDASI PERTAMA
        if (isset($body['errors'])) {
            $error = collect($body['errors'])->flatten()->first();
        } elseif (isset($body['message'])) {
            $error = $body['message'];
        }

        // Set state snackbar dengan pesan error
        $this->snackbar = ['type' => 'error', 'message' => $error];

        return;
    }

    // SUCCESS -> tampilkan snackbar sukses via state
    $this->snackbar = ['type' => 'success', 'message' => 'Registrasi berhasil!'];

    // jeda singkat supaya user sempat lihat pesan
    sleep(1);
    $this->redirect('/login', navigate: true);
};
?>

<x-layouts.mobile title="Daftar Akun">

    {{-- SNACKBAR (always render, no props; handled via events) --}}
    <x-mobile.snackbar />

    <div class="relative w-full min-h-full flex flex-col">
        {{-- Background --}}
        <img src="/images/assets/bg-pattern.png"
            class="absolute inset-0 w-full h-full object-cover pointer-events-none" />

        <div class="relative z-10 px-6 pt-10">
            {{-- Back button --}}
            <a href="/auth" class="text-white text-xl">&larr;</a>

            {{-- Logo --}}
            <div class="flex justify-center mt-4 mb-3">
                <img src="/images/assets/logo.png" class="w-20">
            </div>

            {{-- Form --}}
            <div class="bg-white/80 backdrop-blur-sm rounded-xl p-6 shadow-lg" wire:key="register-form">

                <h1 class="text-center font-bold text-xl mb-4">Daftar Akun</h1>

                <x-mobile.select wire:model="role" label="Daftar Sebagai">
                    <option value="">Pilih jenis pendaftaran</option>
                    <option value="anggota">Anggota</option>
                    <option value="publik">Publik</option>
                </x-mobile.select>
                {{-- Nama --}}
                <x-mobile.input wire:model="nama" placeholder="Nama lengkap" />

                {{-- Email --}}
                <x-mobile.input wire:model="email" placeholder="Email" type="email" />

                {{-- Telepon --}}
                <x-mobile.input wire:model="telp" placeholder="No Telepon" />

                {{-- Alamat --}}
                <x-mobile.input wire:model="alamat" placeholder="Alamat" />

                {{-- Gender --}}
                <x-mobile.select wire:model="gender" label="Jenis Kelamin">
                    <option value="">Pilih opsi</option>
                    <option value="L">Laki-laki</option>
                    <option value="P">Perempuan</option>
                </x-mobile.select>

                {{-- Birth Date --}}
                <x-mobile.input wire:model="birth_date" class="mt-2" placeholder="Tanggal Lahir" label="Tanggal Lahir" type="date" />

                {{-- City --}}
                <x-mobile.input wire:model="city" placeholder="Kota" />

                {{-- Occupation --}}
                <x-mobile.input wire:model="occupation" placeholder="Pekerjaan" />


                {{-- Password --}}
                <x-mobile.input wire:model="password" placeholder="Password" type="password" icon="eye" />

                {{-- Role --}}
                {{-- <x-mobile.select wire:model="kategori" label="Daftar Sebagai">
                    <option value="">Pilih opsi</option>
                    <option value="anggota">Keanggotaan</option>
                    <option value="umum">Umum (Akses informasi & marketplace)</option>
                </x-mobile.select> --}}


                {{-- Button --}}
                <x-mobile.button class="mt-4" wire:click="submit">
                    Daftar Akun
                </x-mobile.button>

                {{-- Footer link --}}
                <p class="text-center text-sm mt-3">
                    Sudah punya akun?
                    <a href="/login" class="text-green-700 font-semibold">Login</a>
                </p>

            </div>
        </div>
    </div>

</x-layouts.mobile>