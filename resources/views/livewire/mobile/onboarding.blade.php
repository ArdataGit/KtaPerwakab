<?php

use function Livewire\Volt\{state};

?>

<x-layouts.mobile title="Selamat Datang">

    <div class="flex flex-col h-screen bg-white">

        {{-- SWIPER AREA --}}
        <div class="flex-1 flex flex-col justify-center px-6">
            <div class="swiper mySwiper h-full">
                <div class="swiper-wrapper" style="padding-top: 26vh;">

                    <!-- Slide 1 -->
                    <div class="swiper-slide flex flex-col items-center justify-center text-center">
                        <img src="/images/assets/logo.png" alt="Logo" class="w-28 mb-9 mx-auto">
                        <h1 class="text-lg font-bold text-gray-900 mx-auto mb-2">Selamat Datang</h1>
                        <p class="text-gray-600 text-sm leading-relaxed mx-auto">
                            Di Aplikasi Organisasi Nasional<br>
                            <span class="font-semibold text-green-600">KTA Perwakab Digital</span>
                        </p>
                    </div>

                    <!-- Slide 2 -->
                    <div class="swiper-slide flex flex-col items-center justify-center text-center">
                        <img src="/images/assets/bell.png" alt="Info" class="w-24 mb-12 mx-auto">
                        <h1 class="text-lg font-bold text-gray-900 mb-2 mx-auto">Informasi Lengkap</h1>
                        <p class="text-gray-600 text-sm leading-relaxed mx-auto">
                            Dapatkan layanan informasi organisasi dan fitur<br>
                            pendukung aktivitas komunitas Anda
                        </p>
                    </div>

                    <!-- Tambah slide lagi kalau perlu -->

                </div>

                <!-- Pagination -->
                <div class="swiper-pagination mt-4"></div>
            </div>
        </div>

        {{-- BUTTON AREA (fixed di bawah) --}}
        <div class="px-6 pb-8 pt-4 space-y-3">
            <button id="btn-next"
                class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-3 rounded-lg text-base transition">
                Lanjut
            </button>

            <button id="btn-skip"
                class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-3 rounded-lg text-base transition">
                Lewati
            </button>
        </div>

    </div>

    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

    <script>
        let mySwiperInstance = null;

        function initMySwiper() {
            const swiperEl = document.querySelector('.mySwiper');
            if (!swiperEl || swiperEl.swiper) return;

            mySwiperInstance = new Swiper(swiperEl, {
                pagination: {
                    el: swiperEl.querySelector('.swiper-pagination'),
                    clickable: true,
                },
                observer: true,
                observeParents: true,
            });

            const btnNext = document.getElementById('btn-next');
            const btnSkip = document.getElementById('btn-skip');

            if (btnNext) {
                btnNext.onclick = function () {
                    if (mySwiperInstance.isEnd) {
                        // Redirect ke /auth saat di slide terakhir
                        window.location.href = '/auth';
                    } else {
                        mySwiperInstance.slideNext();
                    }
                    updateButtonText();
                };
            }

            if (btnSkip) {
                btnSkip.onclick = function () {
                    // Redirect ke /auth saat skip
                    window.location.href = '/auth';
                };
            }

            // Fungsi untuk update teks button next jika di slide terakhir
            function updateButtonText() {
                if (btnNext) {
                    if (mySwiperInstance.isEnd) {
                        btnNext.textContent = 'Mulai';
                    } else {
                        btnNext.textContent = 'Lanjut';
                    }
                }
            }

            // Panggil update awal
            updateButtonText();

            // Listener untuk perubahan slide
            if(mySwiperInstance) {
                mySwiperInstance.on('slideChange', updateButtonText);
            }
        }

        document.addEventListener('DOMContentLoaded', initMySwiper);
        document.addEventListener('livewire:navigated', initMySwiper);
    </script>
    
    <x-slot:desktop>
        <div class="min-h-screen bg-green-700 flex items-center justify-center relative overflow-hidden">
            <!-- Decorative Elements -->
            <div class="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 bg-green-600 rounded-full mix-blend-multiply opacity-50 blur-3xl"></div>
            <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-80 h-80 bg-emerald-500 rounded-full mix-blend-multiply opacity-50 blur-3xl"></div>
            
            <div class="relative z-10 text-center animate-pulse">
                <img src="/images/assets/logo.png" class="w-48 mx-auto drop-shadow-2xl" onerror="this.src='/images/assets/iuran.png'">
                <p class="mt-8 text-green-100 text-lg font-medium tracking-widest">Platform KTA Digital</p>
            </div>
            
            <!-- Immediate auto-redirect for desktop users directly to login -->
            <script>
                setTimeout(() => {
                    window.location.href = '/login';
                }, 1500);
            </script>
        </div>
    </x-slot:desktop>

</x-layouts.mobile>