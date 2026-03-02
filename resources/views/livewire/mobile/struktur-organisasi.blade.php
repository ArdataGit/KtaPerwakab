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

    {{-- ==================== DESKTOP VIEW ==================== --}}
    <x-slot:desktop>
        <x-desktop.layout title="Struktur Organisasi">
            <div class="max-w-5xl mx-auto">

                {{-- PAGE HEADER --}}
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900">Struktur Organisasi</h1>
                    <p class="text-gray-500 mt-1">Susunan kepengurusan organisasi Perwakab Batam.</p>
                </div>

                @if($loading)
                    <div class="flex items-center justify-center py-20">
                        <div class="flex flex-col items-center gap-4">
                            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-600"></div>
                            <p class="text-sm text-gray-500">Memuat data...</p>
                        </div>
                    </div>

                @elseif($error)
                    <div class="bg-red-50 border border-red-200 rounded-2xl p-8 text-center">
                        <svg class="w-12 h-12 text-red-400 mx-auto mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                        <p class="text-red-600 font-medium">{{ $error }}</p>
                    </div>

                @elseif($struktur && isset($struktur['file_url']))
                    @php
                        $fileUrl = $struktur['file_url'];
                        $extension = strtolower(pathinfo($fileUrl, PATHINFO_EXTENSION));
                        $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                        $isPdf = $extension === 'pdf';
                    @endphp

                    {{-- IMAGE --}}
                    @if($isImage)
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                            <img src="{{ $fileUrl }}" alt="Struktur Organisasi" class="w-full h-auto">
                        </div>

                    {{-- PDF --}}
                    @elseif($isPdf)
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden p-4">
                            <div id="desktop-pdf-wrapper" class="w-full">
                                <div id="desktop-pdf-loading" class="flex items-center justify-center py-20">
                                    <div class="flex flex-col items-center gap-4">
                                        <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-green-600"></div>
                                        <p class="text-sm text-gray-500">Memuat PDF...</p>
                                    </div>
                                </div>
                                <canvas id="desktop-pdf-canvas" class="w-full hidden"></canvas>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-center gap-4">
                            <a href="{{ $fileUrl }}"
                               target="_blank"
                               class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-8 py-3 rounded-xl font-semibold transition-all duration-200 hover:-translate-y-0.5 shadow-md shadow-green-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                Buka PDF
                            </a>
                        </div>

                        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
                        <script>
                        (function() {
                            var rendered = false;
                            function renderDesktopPdf() {
                                if (rendered) return;
                                var canvas = document.getElementById('desktop-pdf-canvas');
                                var loading = document.getElementById('desktop-pdf-loading');
                                var wrapper = document.getElementById('desktop-pdf-wrapper');
                                if (!canvas || !wrapper) return;

                                rendered = true;
                                var pdfUrl = "{{ url('/proxy-pdf?url=' . urlencode($fileUrl)) }}";

                                pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

                                pdfjsLib.getDocument(pdfUrl).promise.then(function(pdf) {
                                    pdf.getPage(1).then(function(page) {
                                        var viewport = page.getViewport({ scale: 1 });
                                        var containerWidth = wrapper.clientWidth - 32;
                                        var scale = containerWidth / viewport.width;
                                        var outputScale = window.devicePixelRatio || 1;
                                        var scaledViewport = page.getViewport({ scale: scale });

                                        canvas.width = scaledViewport.width * outputScale;
                                        canvas.height = scaledViewport.height * outputScale;
                                        canvas.style.width = scaledViewport.width + 'px';
                                        canvas.style.height = scaledViewport.height + 'px';

                                        var context = canvas.getContext('2d');
                                        context.setTransform(outputScale, 0, 0, outputScale, 0, 0);

                                        page.render({
                                            canvasContext: context,
                                            viewport: scaledViewport
                                        }).promise.then(function() {
                                            if (loading) loading.style.display = 'none';
                                            canvas.classList.remove('hidden');
                                        });
                                    });
                                }).catch(function(err) {
                                    if (loading) loading.innerHTML = '<p class="text-red-500 text-sm">Gagal memuat PDF. Gunakan tombol di bawah untuk membuka file.</p>';
                                    console.error('Desktop PDF render error:', err);
                                });
                            }

                            // Try multiple timing strategies for Livewire compatibility
                            if (document.readyState === 'complete') {
                                setTimeout(renderDesktopPdf, 100);
                            } else {
                                document.addEventListener('DOMContentLoaded', function() {
                                    setTimeout(renderDesktopPdf, 100);
                                });
                            }
                            document.addEventListener('livewire:navigated', function() {
                                rendered = false;
                                setTimeout(renderDesktopPdf, 200);
                            });
                            // Ultimate fallback
                            setTimeout(renderDesktopPdf, 2000);
                        })();
                        </script>

                    {{-- OTHER --}}
                    @else
                        <div class="bg-yellow-50 border border-yellow-200 rounded-2xl p-8 text-center">
                            <svg class="w-12 h-12 text-yellow-500 mx-auto mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                            <p class="text-yellow-700 mb-4 font-medium">Format file tidak didukung untuk ditampilkan</p>
                            <a href="{{ $fileUrl }}" target="_blank"
                               class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-xl font-semibold transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                Download File
                            </a>
                        </div>
                    @endif

                @else
                    <div class="bg-gray-50 border border-gray-200 rounded-2xl p-8 text-center">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                        <p class="text-gray-600 font-medium">Data struktur organisasi tidak tersedia</p>
                    </div>
                @endif

            </div>
        </x-desktop.layout>
    </x-slot:desktop>

</x-layouts.mobile>