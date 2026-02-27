@props([
    'active' => 'home'   // menu yang sedang aktif
])

<div class="fixed bottom-0 left-1/2 -translate-x-1/2 bg-white border-t shadow-lg py-3 z-50"
     style="width: min(100vw, 420px);">
  <div class="flex justify-around items-center text-center">

        {{-- HOME --}}
        <a href="{{ route('mobile.home') }}" class="flex flex-col items-center 
            {{ $active === 'home' ? 'text-green-600' : 'text-gray-500' }}">
            <img src="/images/assets/icon/home.svg" class="w-5 h-5 mb-1">
            <span class="text-[10px]">Home</span>
        </a>

        {{-- KTA --}}
        <a href="{{ route('mobile.articles') }}" class="flex flex-col items-center 
            {{ $active === 'artikel' ? 'text-green-600' : 'text-gray-500' }}">
            <img src="/images/assets/icon/artikel-menu.svg" class="w-5 h-5 mb-1">
            <span class="text-[10px]">Artikel</span>
        </a>

        {{-- MENU / FITUR --}}
        <a href="/poin-saya" class="flex flex-col items-center 
            {{ $active === 'menu' ? 'text-green-600' : 'text-gray-500' }}">
            <img src="/images/assets/icon/point.svg" class="w-5 h-5 mb-1">
            <span class="text-[10px]">Point</span>
        </a>

        {{-- PROFILE --}}
        <a href="{{ route('mobile.profile') }}" class="flex flex-col items-center 
            {{ $active === 'profile' ? 'text-green-600' : 'text-gray-500' }}">
            <img src="/images/assets/icon/profile.svg" class="w-5 h-5 mb-1">
            <span class="text-[10px]">Profil</span>
        </a>

    </div>
</div>
