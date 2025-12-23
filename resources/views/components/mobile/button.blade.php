@props([
    'variant' => 'primary',   // primary | secondary | outline | ghost
    'hex' => null,            // background HEX, ex: #1ABC9C
    'text' => null,           // text HEX, ex: #ffffff
])
@php
    $base = "w-full py-3 rounded-lg font-medium text-sm text-center transition";

    // Jika HEX diberikan → override semua warna default
    if ($hex) {
        $bgStyle = "background-color: {$hex};";

        // Jika text diberikan pakai HEX custom
        $textColor = $text ? "color: {$text};" : "color: white;";

        $style = $bgStyle . $textColor;
        $classes = ""; // warna dari style inline, bukan Tailwind
    } else {
        // Default Tailwind variant
        $style = null;

        $classes = match ($variant) {
            'primary' => 'bg-green-600 text-white hover:bg-green-700',
            'secondary' => 'bg-gray-200 text-gray-700 hover:bg-gray-300',
            'outline' => 'border border-gray-400 text-gray-700 bg-white hover:bg-gray-100',
            'ghost' => 'bg-transparent text-gray-700 hover:bg-gray-100',
            default => 'bg-green-600 text-white',
        };
    }
@endphp

<button 
    {{ $attributes->merge(['class' => "$base $classes"]) }}
    style="{{ $style }}"
>
    {{ $slot }}
</button>
