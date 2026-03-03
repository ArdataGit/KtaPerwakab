@props(['items' => []])

@php
    $user = session('user') ?? [];
    $role = $user['role'] ?? 'publik';
@endphp

<div class="px-6 mt-6" x-data="{
        showSnackbar: false,
        message: ''
    }">

    {{-- SNACKBAR --}}
    <div x-show="showSnackbar" x-transition x-cloak
        class="fixed bottom-24 left-1/2 -translate-x-1/2 bg-red-600 text-white text-sm px-4 py-3 rounded-xl shadow-lg z-50">
        <span x-text="message"></span>
    </div>

    <p class="text-base font-bold text-gray-800 mb-4">Menu</p>

    <div class="grid grid-cols-4 card gap-4">

        @foreach ($items as $item)

            @php
                $href = $item['href'] ?? ($item['route'] ?? null);
                $icon = $item['icon'] ?? '';
                $label = $item['label'] ?? '';

                // Menu yang dibatasi untuk publik
                $restricted = in_array($icon, ['kta', 'poin','struktur']);
            @endphp

            <div class="cursor-pointer flex flex-col items-center text-center space-y-2" @if($href && !($restricted && $role === 'publik')) onclick="window.location='{{ $href }}'" @else @click="
                message = 'Menu ini hanya tersedia untuk anggota';
                showSnackbar = true;
                setTimeout(() => showSnackbar = false, 2500);
            " @endif>

                {{-- ICON --}}
                <div class="bg-green-50 w-16 h-16 rounded-xl flex items-center justify-center shadow-sm">
                    <img src="/images/assets/icon/{{ $icon }}.svg" class="w-7 h-7" alt="{{ $label }} icon">
                </div>

                {{-- LABEL --}}
                <p class="text-[10px] font-semibold text-gray-700 leading-tight">
                    {{ $label }}
                </p>

            </div>

        @endforeach

    </div>
</div>