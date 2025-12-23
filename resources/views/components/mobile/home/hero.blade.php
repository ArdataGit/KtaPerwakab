@props([
    'name' => 'Pengguna',
    'fullname' => null,
    'city' => 'Kota Anda',
    'region' => 'Provinsi Anda',
    'photo' => null,   // URL foto profil, null = default avatar
])

@php
    $avatar = api_profile_url($photo);
@endphp

<div class="w-full bg-gradient-to-br from-green-500 to-green-700 p-6 rounded-b-3xl">
    <!-- {{ api_profile_url($photo) }} -->
    {{-- User Greeting --}}
    <div class="flex items-center justify-between">

        <div>
            <p class="text-white text-sm opacity-80">
                Hallo, {{ $name }}
            </p>

            <p class="text-white font-semibold text-lg -mt-1">
                Selamat datang, {{ $fullname ?? $name }}
            </p>
        </div>

        {{-- Profile Picture --}}
        <div class="w-12 h-12 rounded-full bg-white shadow overflow-hidden flex items-center justify-center">
            <img 
                src="{{ $avatar }}" 
                class="w-full h-full object-cover"
                alt="Foto Profil"
            >
        </div>

    </div>

    {{-- Location Selector --}}
    <div class="mt-5 bg-white rounded-xl px-4 py-3 flex items-center justify-between shadow">

        <div class="flex items-center space-x-2">
            <span class="text-red-500">
                <i class="bi bi-geo-alt-fill text-red-500 text-lg"></i>
             </span>

            <p class="text-gray-700 text-sm font-medium">
                {{ $city }}, {{ $region }}
            </p>
        </div>

        <button class="text-green-700 font-semibold text-sm">
            Ganti
        </button>
    </div>

</div>
