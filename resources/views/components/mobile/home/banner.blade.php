@props([
    'images' => [
        '/images/assets/default-banner-1.png',
        '/images/assets/default-banner-2.png',
    ],
])
<div class="px-6 mt-8">

    <div class="swiper myHomeSwiper rounded-2xl overflow-hidden shadow">

        <div class="swiper-wrapper">

            @foreach ($images as $img)
                <div class="swiper-slide">
                    <img src="{{ $img }}" class="w-full h-40 object-cover" alt="banner">
                </div>
            @endforeach

        </div>

        <div class="swiper-pagination mt-3"></div>
    </div>

</div>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        new Swiper(".myHomeSwiper", {
            pagination: { el: ".swiper-pagination", clickable: true },
            loop: true,
            autoplay: {
                delay: 3500,
            }
        });
    });
</script>
