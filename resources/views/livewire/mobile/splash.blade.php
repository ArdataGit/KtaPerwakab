<?php
use function Livewire\Volt\state;

state([
    'token' => session('token'),
]);
?>

@php
    // Tentukan tujuan redirect
    $redirectTo = $token
        ? route('mobile.home')
        : route('mobile.onboarding');
@endphp

<x-layouts.mobile title="Splash">
    <div class="relative h-screen w-full">

        {{-- Background --}}
        <img src="/images/assets/backgroundsplash.png" class="absolute inset-0 w-full h-full object-cover z-0">

        {{-- Logo Tengah --}}
        <div class="absolute inset-0 flex items-center justify-center z-10">
            <img src="/images/assets/logo.png" class="w-48 opacity-0 animate-fadein">
        </div>

    </div>

    <script>
        setTimeout(() => {
            window.location.href = "{{ $redirectTo }}";
        }, 2000);
    </script>
</x-layouts.mobile>