@props([
    'image' => '/images/assets/default-article.png',
    'title' => "Judul artikel belum tersedia",
    'link' => '#',
])

<div class="px-6 mt-10 pb-20">

    <div class="bg-white rounded-xl shadow overflow-hidden">

        {{-- Thumbnail --}}
        <img 
            src="{{ $image }}" 
            class="w-full h-36 object-cover" 
            alt="Article Thumbnail"
        >

        {{-- Content --}}
        <div class="p-4">

            <p class="text-gray-900 font-bold text-lg leading-tight whitespace-pre-line">
                {{ $title }}
            </p>

            <a href="{{ $link }}" class="text-green-600 text-sm font-semibold mt-2 inline-block">
                Baca Selengkapnya →
            </a>

        </div>

    </div>

</div>
