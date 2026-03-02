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
</div>
