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
            <div class="swiper myHomeSwiper rounded-xl overflow-hidden">
                <div class="swiper-wrapper">
                   @foreach ($banners as $banner)
                      @php
                          $image = api_product_url($banner['image'] ?? null);
                      @endphp

                      <div class="swiper-slide">
                          <a href="{{ $banner['link'] ?? '#' }}" class="block">
                              <img
                                  src="{{ $image }}"
                                  class="w-full h-40 object-cover"
                                  alt="{{ $banner['title'] ?? 'Banner' }}"
                              >
                          </a>
                      </div>
                  @endforeach
                </div>
                <div class="swiper-pagination"></div>
            </div>
        </div>
    @endif

    @once
    <script>
        document.addEventListener("livewire:init", () => {
            setTimeout(() => {
                if (document.querySelector(".myHomeSwiper")) {
                    new Swiper(".myHomeSwiper", {
                        loop: true,
                        autoplay: { delay: 3500 },
                        pagination: {
                            el: ".swiper-pagination",
                            clickable: true,
                        },
                    });
                }
            }, 300);
        });
    </script>
    @endonce
</div>