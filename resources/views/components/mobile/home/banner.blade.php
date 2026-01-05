<div class="px-6 mt-8">

    <div class="swiper myHomeSwiper rounded-2xl overflow-hidden shadow">

        <div class="swiper-wrapper" id="bannerWrapper">
            <!-- Loading state -->
            <div class="swiper-slide">
                <div class="w-full h-40 bg-gray-200 animate-pulse flex items-center justify-center">
                    <span class="text-gray-400">Loading...</span>
                </div>
            </div>
        </div>

        <div class="swiper-pagination mt-3"></div>
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', async function() {
        const wrapper = document.getElementById('bannerWrapper');
        let swiper = null;

        try {
            // Fetch banners
            const response = await fetch('{{ route("api.banners") }}');
            const result = await response.json();

            console.log('Banner API Response:', result);

            // Clear loading state
            wrapper.innerHTML = '';

            if (result.success && result.data && result.data.length > 0) {
                // Add banner slides with click handler
                result.data.forEach(banner => {
                    const slide = document.createElement('div');
                    slide.className = 'swiper-slide cursor-pointer';
                    slide.innerHTML = `<img src="${banner.image}" class="w-full h-40 object-cover" alt="${banner.title || 'banner'}">`;
                    
                    // Add click event to navigate to detail page
                    slide.addEventListener('click', function() {
                        window.location.href = '{{ route("mobile.banner.show", ["id" => "__ID__"]) }}'.replace('__ID__', banner.id);
                    });
                    
                    wrapper.appendChild(slide);
                });
            } else {
                // Add default banner if no data
                const slide = document.createElement('div');
                slide.className = 'swiper-slide';
                slide.innerHTML = '<div class="w-full h-40 bg-gray-100 flex items-center justify-center"><span class="text-gray-500 text-sm">Saat ini banner belum tersedia</span></div>';
                wrapper.appendChild(slide);
            }

            // Initialize Swiper after content is loaded
            swiper = new Swiper(".myHomeSwiper", {
                pagination: { 
                    el: ".swiper-pagination", 
                    clickable: true 
                },
                loop: wrapper.children.length > 1,
                autoplay: wrapper.children.length > 1 ? {
                    delay: 3500,
                } : false,
            });

        } catch (error) {
            console.error('Error fetching banners:', error);
            
            // Show default banner on error
            wrapper.innerHTML = '<div class="swiper-slide"><div class="w-full h-40 bg-gray-100 flex items-center justify-center"><span class="text-gray-500 text-sm">Saat ini banner belum tersedia</span></div></div>';
            
            swiper = new Swiper(".myHomeSwiper", {
                pagination: { 
                    el: ".swiper-pagination", 
                    clickable: true 
                },
                loop: false,
                autoplay: false,
            });
        }
    });
</script>
