@props([
    'label' => null,
    'error' => null,      // boolean atau string
])

<div class="w-full space-y-1">

    {{-- LABEL --}}
    @if ($label)
        <label class="text-sm font-semibold text-black mb-1 block">
            {{ $label }}
        </label>
    @endif

    <div class="relative">

        {{-- SELECT --}}
        <select
            {{ $attributes->merge([
                'class' => 
                    'w-full px-4 py-3 rounded-2xl border text-sm appearance-none 
                    bg-white shadow-sm transition
                    ' . ($error
                        ? 'border-red-500 bg-red-50'
                        : 'border-gray-300 focus:border-green-600 focus:ring focus:ring-green-200')
            ]) }}
        >
            {{ $slot }}
        </select>

        {{-- ICON DROPDOWN --}}
        <span class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-gray-600 text-sm">
            ▼
        </span>

    </div>

    {{-- ERROR MESSAGE --}}
    @if ($error)
        <p class="text-xs text-red-600">{{ $error }}</p>
    @endif

</div>
