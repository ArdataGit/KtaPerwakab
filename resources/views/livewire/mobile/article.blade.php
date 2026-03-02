<?php
use App\Services\NewsArticleApiService;
use function Livewire\Volt\state;
use function Livewire\Volt\mount;
use function Livewire\Volt\updated;
state([
    'featured' => [],
    'articles' => [],
    'category' => 'all',
    'search' => '',
]);
mount(function () {
    $response = NewsArticleApiService::list([
        'category' => $this->category !== 'all' ? $this->category : null,
        'search' => $this->search ?: null,
    ]);
    if ($response->successful()) {
        $this->featured = $response->json('data.featured') ?? [];
        $this->articles = $response->json('data.articles.data') ?? [];
        $this->dispatch(
            'articles-fetched',
            'mount',
            $this->category,
            $this->search,
            count($this->featured),
            count($this->articles),
            $this->articles
        );
    }
});
$fetchArticles = function () {
    $response = NewsArticleApiService::list([
        'category' => $this->category !== 'all' ? $this->category : null,
        'search' => $this->search ?: null,
    ]);
    if ($response->successful()) {
        if ($this->search) {
            $this->featured = [];
        } else {
            $this->featured = $response->json('data.featured') ?? [];
        }
        $this->articles = $response->json('data.articles.data') ?? [];
        $this->dispatch('articles-fetched');
    }
};

updated([
    'category' => $fetchArticles,
    'search' => $fetchArticles,
]);
?>
<x-layouts.mobile title="Berita">
    {{-- HEADER --}}
    <div class="w-full bg-green-600 py-4 px-4 flex items-center space-x-3 rounded-b-2xl">
        <button onclick="window.history.back()">
            <img src="/images/assets/icon/back.svg" class="w-5 h-5">
        </button>
        <p class="text-white font-semibold text-base">Berita</p>
    </div>
    <div class="px-4 mt-4 space-y-4">
        {{-- SEARCH --}}
        <div class="flex items-center space-x-2">
            <div class="flex-1 relative">
                <input type="text" wire:model.live.debounce.500ms="search" placeholder="Cari berita" class="w-full pl-10 pr-4 py-2 rounded-full border text-sm
                           focus:outline-none focus:ring focus:ring-green-200">
                {{-- ICON SEARCH --}}
                <span class="absolute left-3 top-1/2 -translate-y-1/2">
                    <img src="/images/assets/icon/search.svg" class="w-4 h-4 opacity-60">
                </span>
            </div>
            {{-- ICON FILTER --}}
            {{--<button class="w-10 h-10 rounded-full border flex items-center justify-center">
                <img src="/images/assets/icon/filter.svg" class="w-4 h-4">
            </button>--}}
        </div>
        {{-- FILTER --}}
        <div class="flex space-x-2 overflow-x-auto">
            @php
                $filters = [
                    'all' => 'Semua',
                    'berita' => 'Berita',
                    'pengumuman' => 'Pengumuman',
                    'lainnya' => 'Lainnya',
                ];
            @endphp
            @foreach ($filters as $key => $label)
                    <button wire:click="$set('category','{{ $key }}')" class="px-4 py-1.5 rounded-full text-xs font-semibold
                                                                                                        {{ $category === $key
                ? 'bg-green-600 text-white'
                : 'border border-green-600 text-green-600' }}">
                        {{ $label }}
                    </button>
            @endforeach
        </div>
        {{-- FEATURED ARTICLES --}}
        <div wire:key="featured-{{ $category }}-{{ $search }}" class="space-y-4">
            @if (count($featured) > 0)
                <p class="text-sm font-semibold text-gray-700">Featured</p>
                @foreach ($featured as $item)
                    <a wire:key="featured-{{ $item['id'] }}" href="{{ route('mobile.article.detail', $item['id']) }}"
                        class="flex space-x-3 bg-white rounded-xl p-2 shadow-sm active:bg-gray-100">
                        <img src="{{ $item['cover_image'] }}" class="w-24 h-20 rounded-lg object-cover">
                        <div class="flex-1">
                            <p class="font-semibold text-sm text-gray-800 leading-snug">
                                {{ $item['title'] }}
                            </p>
                            <p class="text-xs text-gray-600 mt-1 line-clamp-2">
                                {{ $item['excerpt'] ?? Illuminate\Support\Str::limit(strip_tags($item['content'] ?? ''), 100) }}
                            </p>
                            <p class="text-xs text-gray-500 mt-1">
                                {{ $item['published_at'] }}
                            </p>
                        </div>
                    </a>
                @endforeach
            @endif
        </div>
        {{-- ARTICLES --}}
        <div wire:key="articles-{{ $category }}-{{ $search }}" class="space-y-4">
            @forelse ($articles as $item)
                <a wire:key="article-{{ $item['id'] }}" href="{{ route('mobile.article.detail', $item['id']) }}"
                    class="flex space-x-3 bg-white rounded-xl p-2 shadow-sm active:bg-gray-100">
                    <img src="{{ $item['cover_image'] }}" class="w-24 h-20 rounded-lg object-cover">
                    <div class="flex-1">
                        <p class="font-semibold text-sm text-gray-800 leading-snug">
                            {{ $item['title'] }}
                        </p>
                        <p class="text-xs text-gray-600 mt-1 line-clamp-2">
                            {{ $item['excerpt'] ?? Illuminate\Support\Str::limit(strip_tags($item['content'] ?? ''), 100) }}
                        </p>
                        <p class="text-xs text-gray-500 mt-1">
                            {{ $item['published_at'] }}
                        </p>
                    </div>
                </a>
            @empty
                @if (count($featured) === 0)
                    <div class="bg-white rounded-xl p-4 text-center text-sm text-gray-500">
                        Tidak ada artikel untuk kategori ini.
                    </div>
                @endif
            @endforelse
        </div>
    </div>
    <div class="h-20"></div>
    {{-- BOTTOM NAV --}}
    <x-mobile.navbar active="artikel" />

    {{-- ==================== DESKTOP VIEW ==================== --}}
    <x-slot:desktop>
        <x-desktop.layout title="Berita & Artikel">
            <div class="max-w-7xl mx-auto">

                {{-- PAGE HEADER --}}
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900">Berita & Artikel</h1>
                    <p class="text-gray-500 mt-1">Temukan informasi terbaru seputar organisasi dan kegiatan Perwakab.</p>
                </div>

                {{-- SEARCH + FILTER ROW --}}
                <div class="flex flex-col md:flex-row items-start md:items-center gap-4 mb-8">
                    {{-- Search --}}
                    <div class="relative flex-1 w-full">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z"/></svg>
                        </span>
                        <input type="text" wire:model.live.debounce.500ms="search" placeholder="Cari berita atau artikel..."
                            class="w-full pl-12 pr-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-green-200 focus:border-green-500 transition">
                    </div>
                    {{-- Category Filters --}}
                    <div class="flex gap-2 flex-wrap">
                        @php
                            $filters = [
                                'all' => 'Semua',
                                'berita' => 'Berita',
                                'pengumuman' => 'Pengumuman',
                                'lainnya' => 'Lainnya',
                            ];
                        @endphp
                        @foreach ($filters as $key => $label)
                            <button wire:click="$set('category','{{ $key }}')"
                                class="px-5 py-2 rounded-full text-sm font-semibold transition-all duration-200
                                {{ $category === $key
                                    ? 'bg-green-600 text-white shadow-md shadow-green-200'
                                    : 'bg-white border border-gray-200 text-gray-600 hover:border-green-400 hover:text-green-600' }}">
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- FEATURED ARTICLES --}}
                <div wire:key="desktop-featured-{{ $category }}-{{ $search }}">
                    @if (count($featured) > 0)
                        <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            Featured
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
                            @foreach ($featured as $item)
                                <a wire:key="desktop-feat-{{ $item['id'] }}" href="{{ route('mobile.article.detail', $item['id']) }}"
                                    class="group bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
                                    <div class="relative h-52 overflow-hidden">
                                        <img src="{{ $item['cover_image'] }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                        <div class="absolute top-3 left-3 bg-yellow-400 text-yellow-900 text-xs font-bold px-3 py-1 rounded-full">Featured</div>
                                    </div>
                                    <div class="p-5">
                                        <h3 class="font-bold text-gray-900 text-lg leading-snug group-hover:text-green-700 transition-colors">{{ $item['title'] }}</h3>
                                        <p class="text-sm text-gray-500 mt-2 line-clamp-2">{{ $item['excerpt'] ?? Illuminate\Support\Str::limit(strip_tags($item['content'] ?? ''), 120) }}</p>
                                        <p class="text-xs text-gray-400 mt-3">{{ $item['published_at'] }}</p>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- ALL ARTICLES --}}
                <div wire:key="desktop-articles-{{ $category }}-{{ $search }}">
                    @if (count($featured) > 0 && count($articles) > 0)
                        <h2 class="text-lg font-bold text-gray-800 mb-4">Semua Artikel</h2>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @forelse ($articles as $item)
                            <a wire:key="desktop-art-{{ $item['id'] }}" href="{{ route('mobile.article.detail', $item['id']) }}"
                                class="group bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
                                <div class="relative h-44 overflow-hidden">
                                    <img src="{{ $item['cover_image'] }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                </div>
                                <div class="p-5">
                                    <h3 class="font-semibold text-gray-900 leading-snug group-hover:text-green-700 transition-colors">{{ $item['title'] }}</h3>
                                    <p class="text-sm text-gray-500 mt-2 line-clamp-2">{{ $item['excerpt'] ?? Illuminate\Support\Str::limit(strip_tags($item['content'] ?? ''), 100) }}</p>
                                    <p class="text-xs text-gray-400 mt-3">{{ $item['published_at'] }}</p>
                                </div>
                            </a>
                        @empty
                            @if (count($featured) === 0)
                                <div class="col-span-full flex flex-col items-center justify-center py-16 text-gray-400">
                                    <svg class="w-16 h-16 mb-4 opacity-40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                                    <p class="text-sm font-medium">Tidak ada artikel untuk kategori ini.</p>
                                </div>
                            @endif
                        @endforelse
                    </div>
                </div>

            </div>
        </x-desktop.layout>
    </x-slot:desktop>

</x-layouts.mobile>