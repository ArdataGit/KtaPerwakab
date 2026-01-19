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
updated([
    'category' => function () {
        $response = NewsArticleApiService::list([
            'category' => $this->category !== 'all' ? $this->category : null,
            'search' => $this->search ?: null,
        ]);
        if ($response->successful()) {
            $this->featured = $response->json('data.featured') ?? [];
            $this->articles = $response->json('data.articles.data') ?? [];
            $this->dispatch(
                'articles-fetched',
                'category',
                $this->category,
                $this->search,
                count($this->featured),
                count($this->articles),
                $this->articles
            );
        }
    },
    'search' => function () {
        $response = NewsArticleApiService::list([
            'category' => $this->category !== 'all' ? $this->category : null,
            'search' => $this->search ?: null,
        ]);
        if ($response->successful()) {
            // UX umum: saat search tidak kosong, featured disembunyikan
            if ($this->search) {
                $this->featured = [];
            } else {
                $this->featured = $response->json('data.featured') ?? [];
            }
            $this->articles = $response->json('data.articles.data') ?? [];
            $this->dispatch(
                'articles-fetched',
                'search',
                $this->category,
                $this->search,
                count($this->featured),
                count($this->articles),
                $this->articles
            );
        }
    }
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
                <input type="text" wire:model.live="search" placeholder="Cari berita" class="w-full pl-10 pr-4 py-2 rounded-full border text-sm
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
        <div wire:key="featured-{{ $category }}" class="space-y-4">
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
        <div wire:key="articles-{{ $category }}" class="space-y-4">
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
    <!-- <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('articles-fetched', (payload) => {
                // payload adalah array: [source, category, featuredCount, articlesCount, articles]
                const [
                    source,
                    category,
                    featuredCount,
                    articlesCount,
                    articles
                ] = payload;
                console.group('📦 Articles Fetched');
                console.log('Source:', source);
                console.log('Category:', category);
                console.log('Featured Count:', featuredCount);
                console.log('Articles Count:', articlesCount);
                console.log('Articles Data:', articles);
                console.groupEnd();
            });
        });
    </script> -->
</x-layouts.mobile>