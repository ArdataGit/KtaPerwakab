<div class="bg-gray-100 min-h-screen w-full font-sans">
    <style>
        @media (max-width: 767px) {
            .desktop-view-container { display: none !important; }
        }
        @media (min-width: 768px) {
            .mobile-view-container { display: none !important; }
        }
    </style>

    <!-- TAMPILAN MOBILE -->
    <div class="md:hidden mobile-view-container bg-white min-h-screen flex flex-col shadow-2xl overflow-y-auto mx-auto relative"
         style="width: min(100vw, 420px);">
        <div class="flex-1 shrink-0">
            {{ $slot }}
        </div>
        <div class="pb-20 shrink-0">
            <x-footer />
        </div>
    </div>

    <!-- TAMPILAN DESKTOP -->
    <div class="hidden md:block desktop-view-container w-full min-h-screen bg-gray-50">
        @if(isset($desktop))
            {{ $desktop }}
        @else
            <div class="min-h-screen flex items-center justify-center">
                <div class="text-center bg-white p-10 rounded-2xl shadow-sm border border-gray-100">
                    <img src="/images/assets/iuran.png" class="mx-auto w-24 mb-4 grayscale opacity-50">
                    <h2 class="text-xl font-bold text-gray-700 mb-2">Versi Desktop Belum Tersedia</h2>
                    <p class="text-gray-500">Halaman ini baru mendukung akses melalui mobile/smartphone.</p>
                </div>
            </div>
        @endif
    </div>
</div>