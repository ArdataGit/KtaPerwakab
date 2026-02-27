<?php

use App\Services\StrukturOrganisasiApiService;
use function Livewire\Volt\state;
use function Livewire\Volt\mount;

state([
    'struktur' => null,
    'loading' => true,
    'error' => null,
]);

mount(function () {
    $token = session('token');

    if (!$token) {
        $this->error = 'Token tidak ditemukan';
        $this->loading = false;
        return;
    }

    try {
        $response = StrukturOrganisasiApiService::get($token);

        if ($response->successful()) {
            $this->struktur = $response->json('data');
        } else {
            $this->error = 'Gagal memuat data struktur organisasi';
        }
    } catch (\Exception $e) {
        $this->error = 'Terjadi kesalahan: ' . $e->getMessage();
    }

    $this->loading = false;
});
?>

<x-layouts.mobile title="Struktur Organisasi">

    {{-- HEADER --}}
    <div class="w-full bg-green-600 px-4 py-4 flex items-center space-x-3 rounded-b-2xl">
        <button onclick="window.history.back()">
            <img src="/images/assets/icon/back.svg" class="w-5 h-5">
        </button>
        <p class="text-white font-semibold text-base">Struktur Organisasi</p>
    </div>

    <div class="px-4 py-6">

        @if($loading)
            <div class="flex items-center justify-center py-20">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-600"></div>
            </div>

        @elseif($error)
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-center">
                <p class="text-red-600">{{ $error }}</p>
            </div>

        @elseif($struktur && isset($struktur['file_url']))
            @php
                $fileUrl = $struktur['file_url'];
                $extension = strtolower(pathinfo($fileUrl, PATHINFO_EXTENSION));
                $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                $isPdf = $extension === 'pdf';
            @endphp

            {{-- ================= IMAGE ================= --}}
            @if($isImage)
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <img src="{{ $fileUrl }}"
                         alt="Struktur Organisasi"
                         class="w-full h-auto">
                </div>

            {{-- ================= PDF ================= --}}
            @elseif($isPdf)
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div id="pdf-container" class="w-full flex justify-center">
                    <canvas id="pdf-render" class="w-full"></canvas>
                </div>
            </div>

            <div class="mt-4">
                <a href="{{ url('/proxy-pdf?url=' . urlencode($fileUrl)) }}"
                   target="_blank"
                   class="block w-full bg-green-600 text-white text-center py-3 rounded-xl font-semibold">
                    Buka di PDF Viewer
                </a>
            </div>

            <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>

            <script>
            document.addEventListener("DOMContentLoaded", function () {

                const url = "{{ url('/proxy-pdf?url=' . urlencode($fileUrl)) }}";
                const container = document.getElementById('pdf-container');
                const canvas = document.getElementById('pdf-render');
                const context = canvas.getContext('2d');

                pdfjsLib.getDocument(url).promise.then(function(pdf) {
                    pdf.getPage(1).then(function(page) {

                        const viewport = page.getViewport({ scale: 1 });

                        const containerWidth = container.clientWidth;
                        const scale = containerWidth / viewport.width;

                        const outputScale = window.devicePixelRatio || 1;

                        const scaledViewport = page.getViewport({ scale: scale });

                        // Set actual canvas size (high resolution)
                        canvas.width = scaledViewport.width * outputScale;
                        canvas.height = scaledViewport.height * outputScale;

                        // Set CSS size (normal size)
                        canvas.style.width = scaledViewport.width + "px";
                        canvas.style.height = scaledViewport.height + "px";

                        context.setTransform(outputScale, 0, 0, outputScale, 0, 0);

                        page.render({
                            canvasContext: context,
                            viewport: scaledViewport
                        });
                    });
                });

            });
            </script>

            {{-- ================= FILE LAIN ================= --}}
            @else
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
                    <p class="text-yellow-700 mb-4">
                        Format file tidak didukung untuk ditampilkan
                    </p>
                    <a href="{{ $fileUrl }}"
                       target="_blank"
                       class="inline-block bg-green-600 text-white px-6 py-3 rounded-xl font-semibold">
                        Download File
                    </a>
                </div>
            @endif

        @else
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 text-center">
                <p class="text-gray-600">
                    Data struktur organisasi tidak tersedia
                </p>
            </div>
        @endif

    </div>

    <div class="h-20"></div>

    <x-mobile.navbar active="home" />

</x-layouts.mobile>