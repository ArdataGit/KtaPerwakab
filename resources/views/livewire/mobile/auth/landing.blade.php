<x-layouts.mobile title="Selamat Datang">

    <div class="relative w-full h-screen flex flex-col">

        {{-- Background Illustration --}}
        <img src="/images/assets/bg-pattern.png" class="absolute bottom-0 w-full pointer-events-none" />

        {{-- CONTENT AREA --}}
        <div class="relative z-10 px-12 pt-12">

            {{-- Logo --}}
            <img src="/images/assets/logo.png" class="mb-6" style="width: 5rem;">

            {{-- Title --}}
            <h1 class="text-black font-bold text-2xl tracking-wide">
                KTA Perwakab
            </h1>

            <p class="text-black/90 text-xl mt-2 leading-relaxed">
                Aplikasi siap digunakan. <br>
                Silakan login atau buat akun baru untuk mulai.
            </p>

        </div>

        {{-- BUTTON AREA selalu di bawah --}}
        <div class="relative z-10 w-full px-12 mt-86 pb-10 space-y-3">

            <x-mobile.button wire:navigate href="/login">
                Login
            </x-mobile.button>

            <x-mobile.button hex="#D9D9D9" text="#50555C" wire:navigate href=" /register">
                Daftar Akun
            </x-mobile.button>

        </div>

    </div>

</x-layouts.mobile>