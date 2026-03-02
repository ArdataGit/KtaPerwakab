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

    {{-- ==================== DESKTOP VIEW ==================== --}}
    <x-slot:desktop>
        <x-desktop.layout title="Edit Profil">
            <div class="max-w-5xl mx-auto">

                {{-- SNACKBAR --}}
                @if($snackbar['message'])
                    <div class="fixed top-4 right-4 z-[9999]
                        {{ $snackbar['type'] === 'error' ? 'bg-red-500' : 'bg-green-600' }}
                        text-white px-6 py-3 text-sm font-medium shadow-lg rounded-xl">
                        {{ $snackbar['message'] }}
                    </div>
                @endif

                <div class="flex items-center gap-2 text-sm text-gray-400 mb-6">
                    <a href="{{ route('mobile.profile') }}" class="hover:text-green-600 transition">&larr; Kembali ke Profil</a>
                </div>

                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900">Edit Profil</h1>
                    <p class="text-gray-500 mt-1">Perbarui informasi profil dan data keluarga Anda.</p>
                </div>

                <form wire:submit.prevent="submit" class="space-y-8">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        {{-- LEFT: Avatar --}}
                        <div>
                            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl p-8 text-white text-center shadow-lg sticky top-6">
                                <div class="relative inline-block">
                                    <div class="w-28 h-28 rounded-full bg-white overflow-hidden shadow-md border-4 border-white/30 mx-auto">
                                        <template x-if="$wire.photo_preview">
                                            <img :src="$wire.photo_preview" class="w-full h-full object-cover">
                                        </template>
                                        <template x-if="!$wire.photo_preview">
                                            <img src="{{ $avatar ?: '/images/assets/default-avatar.png' }}" class="w-full h-full object-cover">
                                        </template>
                                    </div>
                                    <input type="file" wire:model.defer="photo" accept="image/*" class="hidden" id="desktopPhotoInput"
                                        x-on:change="
                                            $wire.photo_preview = '';
                                            if ($event.target.files[0]) {
                                                let reader = new FileReader();
                                                reader.onload = (e) => $wire.set('photo_preview', e.target.result);
                                                reader.readAsDataURL($event.target.files[0]);
                                            }
                                        ">
                                    <button type="button" onclick="document.getElementById('desktopPhotoInput').click()"
                                        class="absolute bottom-0 right-0 translate-x-1/4 translate-y-1/4
                                            w-10 h-10 rounded-full bg-green-700 hover:bg-green-800
                                            flex items-center justify-center shadow-md border-2 border-white transition">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><circle cx="12" cy="13" r="3"/></svg>
                                    </button>
                                </div>
                                <div wire:loading wire:target="photo" class="text-xs text-white/80 mt-3">Mengunggah foto...</div>
                                <p class="mt-4 text-sm font-semibold">{{ $user['name'] ?? 'Pengguna' }}</p>
                                <p class="text-xs text-white/70">{{ $user['email'] ?? '-' }}</p>
                            </div>
                        </div>

                        {{-- RIGHT: Form --}}
                        <div class="lg:col-span-2 space-y-6">
                            {{-- Data Identitas --}}
                            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                                <h3 class="text-lg font-bold text-gray-900 mb-6">Data Identitas</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                                        <input type="text" wire:model.defer="name" placeholder="Masukkan nama lengkap"
                                            class="w-full rounded-xl px-4 py-3 text-sm bg-gray-50 border border-gray-200 focus:border-green-500 focus:ring-1 focus:ring-green-500 outline-none transition">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                        <input type="email" value="{{ $email }}" disabled
                                            class="w-full rounded-xl px-4 py-3 text-sm bg-gray-100 border border-gray-200 text-gray-500 cursor-not-allowed">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">No. Telepon</label>
                                        <input type="tel" wire:model.defer="phone" placeholder="08123456789"
                                            class="w-full rounded-xl px-4 py-3 text-sm bg-gray-50 border border-gray-200 focus:border-green-500 focus:ring-1 focus:ring-green-500 outline-none transition">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Kota</label>
                                        <input type="text" wire:model.defer="city" placeholder="Kota tempat tinggal"
                                            class="w-full rounded-xl px-4 py-3 text-sm bg-gray-50 border border-gray-200 focus:border-green-500 focus:ring-1 focus:ring-green-500 outline-none transition">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Pekerjaan</label>
                                        <input type="text" wire:model.defer="occupation" placeholder="Pekerjaan saat ini"
                                            class="w-full rounded-xl px-4 py-3 text-sm bg-gray-50 border border-gray-200 focus:border-green-500 focus:ring-1 focus:ring-green-500 outline-none transition">
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Lengkap</label>
                                        <textarea wire:model.defer="address" rows="3" placeholder="Jalan, RT/RW, Kelurahan, Kecamatan..."
                                            class="w-full rounded-xl px-4 py-3 text-sm bg-gray-50 border border-gray-200 focus:border-green-500 focus:ring-1 focus:ring-green-500 outline-none resize-none transition"></textarea>
                                    </div>
                                </div>
                            </div>

                            {{-- Data Pribadi --}}
                            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                                <h3 class="text-lg font-bold text-gray-900 mb-6">Data Pribadi</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin</label>
                                        <select wire:model.defer="gender"
                                            class="w-full rounded-xl px-4 py-3 text-sm bg-gray-50 border border-gray-200 focus:border-green-500 focus:ring-1 focus:ring-green-500 outline-none transition">
                                            <option value="">Pilih jenis kelamin</option>
                                            <option value="L">Laki-laki</option>
                                            <option value="P">Perempuan</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir</label>
                                        <input type="date" wire:model.defer="birth_date"
                                            class="w-full rounded-xl px-4 py-3 text-sm bg-gray-50 border border-gray-200 focus:border-green-500 focus:ring-1 focus:ring-green-500 outline-none transition">
                                    </div>
                                </div>
                            </div>

                            {{-- Data Keluarga --}}
                            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                                <h3 class="text-lg font-bold text-gray-900 mb-6">Data Keluarga Pengikut / Tertanggung</h3>
                                <div class="space-y-5">
                                    @foreach($family_members as $index => $member)
                                        <div class="bg-gray-50 rounded-xl p-5 border border-gray-200 space-y-4">
                                            <div class="flex justify-between items-center">
                                                <span class="text-sm font-semibold text-gray-700">Anggota #{{ $index + 1 }}</span>
                                                @if(count($family_members) > 1)
                                                    <button type="button" wire:click="removeFamilyMember({{ $index }})"
                                                        class="text-red-500 hover:text-red-700 text-xs font-semibold transition">Hapus</button>
                                                @endif
                                            </div>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <select wire:model.defer="family_members.{{ $index }}.relationship"
                                                    class="w-full rounded-xl px-4 py-3 text-sm bg-white border border-gray-200 focus:border-green-500 outline-none transition">
                                                    <option value="">Pilih Hubungan</option>
                                                    <option value="Ayah">Ayah</option>
                                                    <option value="Ibu">Ibu</option>
                                                    <option value="Suami">Suami</option>
                                                    <option value="Istri">Istri</option>
                                                    <option value="Anak">Anak</option>
                                                    <option value="Ayah Mertua">Ayah Mertua</option>
                                                    <option value="Ibu Mertua">Ibu Mertua</option>
                                                </select>
                                                <input type="number" wire:model.defer="family_members.{{ $index }}.age" placeholder="Umur"
                                                    class="w-full rounded-xl px-4 py-3 text-sm bg-white border border-gray-200 focus:border-green-500 outline-none transition">
                                                <input type="text" wire:model.defer="family_members.{{ $index }}.name_ktp" placeholder="Nama sesuai KTP"
                                                    class="w-full rounded-xl px-4 py-3 text-sm bg-white border border-gray-200 focus:border-green-500 outline-none transition">
                                                <input type="text" wire:model.defer="family_members.{{ $index }}.nickname" placeholder="Nama Panggilan"
                                                    class="w-full rounded-xl px-4 py-3 text-sm bg-white border border-gray-200 focus:border-green-500 outline-none transition">
                                                <div class="md:col-span-2">
                                                    <textarea wire:model.defer="family_members.{{ $index }}.address" placeholder="Alamat" rows="2"
                                                        class="w-full rounded-xl px-4 py-3 text-sm bg-white border border-gray-200 focus:border-green-500 outline-none resize-none transition"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach

                                    <button type="button" wire:click="addFamilyMember"
                                        class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 py-3 rounded-xl text-sm font-semibold transition">
                                        + Tambah Anggota Keluarga
                                    </button>
                                </div>
                            </div>

                            {{-- Submit --}}
                            <button type="submit"
                                class="w-full bg-green-600 hover:bg-green-700 text-white py-4 rounded-xl font-semibold text-base transition shadow-md shadow-green-200">
                                Simpan Perubahan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </x-desktop.layout>
    </x-slot:desktop>

</x-layouts.mobile>