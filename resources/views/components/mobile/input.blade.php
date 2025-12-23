@props([
    'label' => null,
    'type' => 'text',
    'invalid' => false,        // boolean → untuk error styling
    'errorMessage' => null,    // string → pesan error
    'icon' => null,            // icon bootstrap (bi-eye, bi-lock, dll)
])

<div class="w-full space-y-1">

    {{-- LABEL --}}
    @if ($label)
        <label class="text-sm font-medium text-gray-700">{{ $label }}</label>
    @endif

    {{-- INPUT WRAPPER --}}
    <div class="relative">

        <input 
            type="{{ $type }}"
            {{ $attributes->merge([
                'class' => 
                    'w-full px-4 py-3 my-3 rounded-xl border text-sm transition focus:outline-none ' .
                    ($invalid
                        ? 'border-red-500 bg-red-50'
                        : 'border-gray-300 bg-white focus:border-green-500')
            ])}}
        >

        {{-- ICON (Bootstrap Icons) --}}
        @if ($icon)
            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500">
                <i class="bi {{ $icon }} text-lg"></i>
            </span>
        @endif
    </div>

    {{-- ERROR MESSAGE --}}
    @if ($errorMessage)
        <p class="text-xs text-red-600">{{ $errorMessage }}</p>
    @endif
</div>
