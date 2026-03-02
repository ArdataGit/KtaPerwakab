<div class="flex h-screen bg-gray-50 font-sans overflow-hidden">
    <!-- Desktop Sidebar -->
    <x-desktop.sidebar :active="$active ?? ''" />

    <div class="flex-1 flex flex-col h-screen overflow-hidden">
        <!-- Desktop Topbar -->
        <x-desktop.topbar :title="$title ?? 'Dashboard'" />

        <!-- Main Content (Scrollable) -->
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 pb-10">
            <!-- Content Wrapper -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                {{ $slot }}
            </div>
        </main>
    </div>

    <!-- Floating WhatsApp Button Desktop -->
    <div class="fixed z-[9999] flex justify-end" style="right: 4rem; top: 39rem;">
        <a href="https://wa.me/628567895905" target="_blank"
            class="bg-white text-[#25D366] px-5 py-3 rounded-full shadow-xl hover:bg-gray-50 transition-transform hover:scale-105 flex items-center justify-center border border-gray-100 gap-3 relative group cursor-pointer">
            <span class="font-bold text-gray-800 text-sm">Hubungi Kami!</span>
            <img src="/images/assets/icon/whatsapp.svg" 
                alt="WhatsApp" 
                class="w-8 h-8 object-contain">
        </a>
    </div>

</div>
