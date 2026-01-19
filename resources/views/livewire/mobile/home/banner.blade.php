<?php
use App\Services\HomeBannerApiService;
use function Livewire\Volt\{state, mount};

state([
    'banners' => [],
]);

mount(function () {

    $response = HomeBannerApiService::list();


    if ($response->successful()) {
        $data = $response->json('data') ?? [];


        $this->banners = $data;
    } else {
        // DEBUG 4: Jika gagal
        $this->banners = [];

    }
});
?>

<div> 
    @if(!empty($banners) && count($banners) > 0)
        <div class="px-4 mt-4">
            <div class="swiper myHomeSwiper rounded-xl overflow-hidden relative">
    <div class="swiper-wrapper">
        @foreach ($banners as $banner)
            @php
                $image = api_product_url($banner['image'] ?? null);
            @endphp

            <div class="swiper-slide">
                <a href="/banner/{{$banner['id']}}" class="block">
                    <img
                        src="{{ $image }}"
                        class="w-full h-40 object-cover"
                        alt="{{ $banner['title'] ?? 'Banner' }}"
                    >
                </a>
            </div>
        @endforeach
    </div>

    <!-- BUTTON PREV -->
    <button type="button"
        class="home-swiper-prev absolute left-2 top-1/2 -translate-y-1/2 z-10 bg-black/40 hover:bg-black/60 text-white w-8 h-8 rounded-full flex items-center justify-center">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
            <path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </button>

    <!-- BUTTON NEXT -->
    <button type="button"
        class="home-swiper-next absolute right-2 top-1/2 -translate-y-1/2 z-10 bg-black/40 hover:bg-black/60 text-white w-8 h-8 rounded-full flex items-center justify-center">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
            <path d="M9 6l6 6-6 6" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </button>

    <div class="swiper-pagination"></div>
</div>

        </div>
    @endif

    @once
    <script>
    document.addEventListener("livewire:init", () => {
        setTimeout(() => {
            const swiperEl = document.querySelector(".myHomeSwiper");
            if (!swiperEl) return;

            const swiper = new Swiper(swiperEl, {
                loop: true,
                autoplay: {
                    delay: 3500,
                    disableOnInteraction: false,
                },
                pagination: {
                    el: ".swiper-pagination",
                    clickable: true,
                },
            });

            // Manual navigation (lebih stabil di Livewire)
            document.querySelector(".home-swiper-prev")
                ?.addEventListener("click", () => swiper.slidePrev());

            document.querySelector(".home-swiper-next")
                ?.addEventListener("click", () => swiper.slideNext());

        }, 300);
    });
    </script>
    @endonce

</div>