@props([
    'image' => '/images/assets/default-article.png',
    'title' => 'Judul artikel belum tersedia',
    'link' => '#',
])

<div class="px-4 mb-4">
    <a href="{{ $link }}" class="block bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-md transition-shadow">
        {{-- Thumbnail --}}
        <div class="relative">
            <img
                src="{{ $image }}"
                class="w-full h-32 object-cover"
                alt="Article Thumbnail"
            >
            <!-- Optional overlay untuk efek lebih premium -->
            <div class="absolute inset-0 bg-black/10"></div>
        </div>

        {{-- Content --}}
        <div class="p-4">
            <h3 class="text-gray-900 font-semibold text-base leading-snug line-clamp-2">
                {{ $title }}
            </h3>

            <span class="text-green-600 text-sm font-medium mt-3 inline-flex items-center">
                Baca Selengkapnya
                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </span>
        </div>
    </a>
</div>