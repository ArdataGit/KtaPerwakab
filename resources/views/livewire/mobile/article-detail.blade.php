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

    {{-- ==================== DESKTOP VIEW ==================== --}}
    <x-slot:desktop>
        <x-desktop.layout title="Detail Berita">
            <div class="max-w-4xl mx-auto">

                @if ($article)
                    {{-- BREADCRUMB --}}
                    <div class="flex items-center gap-2 text-sm text-gray-400 mb-6">
                        <a href="{{ route('mobile.articles') }}" class="hover:text-green-600 transition">&larr; Kembali ke Berita</a>
                    </div>

                    {{-- COVER IMAGE --}}
                    @if($article['cover_image'])
                        <div class="rounded-2xl overflow-hidden shadow-lg mb-8">
                            <img src="{{ $article['cover_image'] }}" class="w-full h-80 object-cover">
                        </div>
                    @endif

                    {{-- TITLE --}}
                    <h1 class="text-3xl font-bold text-gray-900 leading-snug mb-4">
                        {{ $article['title'] }}
                    </h1>

                    {{-- META --}}
                    <div class="flex items-center gap-4 text-sm text-gray-500 mb-8 pb-6 border-b border-gray-100">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center text-green-700 font-bold text-xs">
                                {{ strtoupper(substr($article['author'] ?? 'A', 0, 1)) }}
                            </div>
                            <span class="font-medium text-gray-700">{{ $article['author'] ?? 'Admin' }}</span>
                        </div>
                        <span class="text-gray-300">•</span>
                        <span>{{ $article['published_at'] }}</span>
                    </div>

                    {{-- CONTENT --}}
                    <div class="prose prose-lg max-w-none text-gray-700 leading-relaxed mb-10">
                        {!! $article['content'] !!}
                    </div>

                    {{-- VIDEO --}}
                    @if ($embedUrl)
                        <div class="mb-10">
                            <h3 class="text-lg font-bold text-gray-800 mb-3">Video Terkait</h3>
                            <div class="rounded-2xl overflow-hidden shadow-lg">
                                <iframe src="{{ $embedUrl }}" class="w-full h-96" frameborder="0"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                    allowfullscreen>
                                </iframe>
                            </div>
                        </div>
                    @endif

                @else
                    <div class="flex flex-col items-center justify-center py-20 text-gray-400">
                        <svg class="animate-spin h-10 w-10 text-green-500 mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>
                        <p class="text-sm font-medium">Memuat detail berita...</p>
                    </div>
                @endif

            </div>
        </x-desktop.layout>
    </x-slot:desktop>

</x-layouts.mobile>