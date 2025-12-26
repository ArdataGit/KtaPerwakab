@props([
    'name' => 'Pengguna',
    'fullname' => null,
    'city' => 'Kota Anda',
    'role' => 'User',
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
        <a href="{{ url('/profile/edit') }}"
        class="w-12 h-12 rounded-full bg-white shadow overflow-hidden flex items-center justify-center
                hover:ring-2 hover:ring-white transition"
        title="Edit Profil">

            <img 
                src="{{ $avatar }}" 
                class="w-full h-full object-cover cursor-pointer"
                alt="Foto Profil"
            >

        </a>


    </div>
    {{-- User Status --}}
    <div class="mt-5 bg-white rounded-xl px-4 py-3 flex items-center justify-between shadow">

        <div class="flex items-center space-x-2">
            <span class="text-green-600">
                <i class="bi bi-shield-check text-green-600 text-lg"></i>
            </span>

            <p class="text-gray-700 text-sm font-medium">
                Status Anda: <span class="font-semibold">{{ ucfirst($role) }}</span>
            </p>
        </div>

    </div>


</div>
