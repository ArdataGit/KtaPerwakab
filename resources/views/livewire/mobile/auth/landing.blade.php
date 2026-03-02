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
        <div class="relative z-10 w-full px-12 pb-10 space-y-3" style="margin-top:13rem;">

            <x-mobile.button wire:navigate href="/login">
                Login
            </x-mobile.button>

            <x-mobile.button hex="#D9D9D9" text="#50555C" wire:navigate href=" /register">
                Daftar Akun
            </x-mobile.button>

        </div>

    </div>

    <!-- Redirect desktop users straight to the premium login page -->
    <x-slot:desktop>
        <div class="min-h-screen bg-green-700 flex items-center justify-center relative overflow-hidden">
            <!-- Decorative Elements -->
            <div class="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 bg-green-600 rounded-full mix-blend-multiply opacity-50 blur-3xl"></div>
            <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-80 h-80 bg-emerald-500 rounded-full mix-blend-multiply opacity-50 blur-3xl"></div>
            
            <div class="relative z-10 text-center animate-pulse">
                <img src="/images/assets/logo.png" class="w-48 mx-auto drop-shadow-2xl" onerror="this.src='/images/assets/iuran.png'">
            </div>
            
            <script>
                setTimeout(() => {
                    window.location.href = '/login';
                }, 500);
            </script>
        </div>
    </x-slot:desktop>

</x-layouts.mobile>