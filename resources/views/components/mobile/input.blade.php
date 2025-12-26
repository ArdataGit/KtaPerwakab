@props([
    'label' => null,
    'type' => 'text',
    'invalid' => false,
    'errorMessage' => null,
])

@php
    $isPassword = $type === 'password';
@endphp

<div
    class="w-full space-y-1"
    x-data="{
        show: false,
        toggle() {
            if (!{{ $isPassword ? 'true' : 'false' }}) return;
            this.show = !this.show;
        }
    }"
>

    {{-- LABEL --}}
    @if ($label)
        <label class="text-sm font-medium text-gray-700">
            {{ $label }}
        </label>
    @endif

    {{-- INPUT WRAPPER --}}
    <div class="relative">

        <input
            :type="{{ $isPassword ? 'show ? \'text\' : \'password\'' : '\'' . $type . '\'' }}"
            {{ $attributes->merge([
                'class' =>
                    'w-full px-4 py-3 my-3 rounded-xl border text-sm transition focus:outline-none pr-12 ' .
                    ($invalid
                        ? 'border-red-500 bg-red-50'
                        : 'border-gray-300 bg-white focus:border-green-500')
            ]) }}
        >

        {{-- TOGGLE PASSWORD (SVG) --}}
        @if ($isPassword)
            <button
                type="button"
                @click="toggle"
                class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 focus:outline-none"
            >

                {{-- EYE --}}
                <svg
                    x-show="!show"
                    xmlns="http://www.w3.org/2000/svg"
                    class="w-5 h-5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>

                {{-- EYE OFF --}}
                <svg
                    x-show="show"
                    x-cloak
                    xmlns="http://www.w3.org/2000/svg"
                    class="w-5 h-5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.27-2.943-9.543-7a9.956 9.956 0 012.143-3.592M6.18 6.18A9.956 9.956 0 0112 5c4.478 0 8.27 2.943 9.543 7a9.958 9.958 0 01-4.132 5.411M15 12a3 3 0 00-3-3m0 0a3 3 0 013 3m-3-3L3 21" />
                </svg>

            </button>
        @endif

    </div>

    {{-- ERROR MESSAGE --}}
    @if ($errorMessage)
        <p class="text-xs text-red-600">
            {{ $errorMessage }}
        </p>
    @endif

</div>
