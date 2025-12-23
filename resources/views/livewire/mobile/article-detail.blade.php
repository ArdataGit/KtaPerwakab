<?php

use App\Services\NewsArticleApiService;
use function Livewire\Volt\state;
use function Livewire\Volt\mount;

state([
    'article' => null,
]);

mount(function ($id) {

    if (!session('token')) {
        return redirect()->route('mobile.login');
    }

    $response = NewsArticleApiService::detail($id);

    if ($response->successful()) {
        $this->article = $response->json('data');
    }
});
?>

@php
    $embedUrl = youtube_embed_url($article['video_url'] ?? null);
@endphp

<x-layouts.mobile title="Detail Berita">

    {{-- HEADER --}}
    <div class="w-full bg-green-600 px-4 py-4 flex items-center space-x-3 rounded-b-2xl">
        <button onclick="window.history.back()">
            <img src="/images/assets/icon/back.svg" class="w-5 h-5">
        </button>
        <p class="text-white font-semibold text-base">
            Detail Berita
        </p>
    </div>

    @if ($article)

        <div class="px-4 mt-4 space-y-4">

            {{-- COVER --}}
            @if($article['cover_image'])
                <img src="{{ $article['cover_image'] }}" class="w-full h-48 rounded-xl object-cover shadow">
            @endif

            {{-- TITLE --}}
            <h1 class="text-lg font-bold text-gray-900 leading-snug">
                {{ $article['title'] }}
            </h1>

            {{-- META --}}
            <div class="text-xs text-gray-500 flex items-center space-x-2">
                <span>Penulis: {{ $article['author'] ?? 'Admin' }}</span>
                <span>•</span>
                <span>{{ $article['published_at'] }}</span>
            </div>

            {{-- CONTENT --}}
            <div class="prose prose-sm max-w-none text-gray-700 leading-relaxed">
                {!! $article['content'] !!}
            </div>

            {{-- VIDEO (OPTIONAL) --}}

            @if ($embedUrl)
                <iframe src="{{ $embedUrl }}" class="w-full h-48 rounded-xl" frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen>
                </iframe>
            @endif

        </div>

    @else
        {{-- LOADING / EMPTY --}}
        <div class="px-4 mt-10 text-center text-sm text-gray-500">
            Memuat detail berita...
        </div>
    @endif

    <div class="h-20"></div>

    <x-mobile.navbar active="artikel" />

</x-layouts.mobile>