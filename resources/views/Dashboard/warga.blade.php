@extends('layouts.app')

@section('title', 'Dashboard Warga - SmartPath')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/smartpath-warga.css') }}">
    <style>
        /* Gaya untuk membuat tombol Screen Reader bisa dipindah-pindahkan (Draggable) */
        #homeScreenReaderButtonContainer {
            touch-action: none;
            user-select: none;
            cursor: move;
        }
    </style>
@endpush

@section('content')
    <div class="warga-shell flex min-h-screen bg-slate-50">
        @include('partials.sidebar-warga')

        <div class="warga-workspace flex-1">
            @include('partials.nav-public')
            @include('partials.bar-mobile-warga')

            <main
                id="warga-dashboard"
                class="warga-dashboard mx-auto w-full max-w-7xl px-6 py-8"
                data-laporan-url="{{ route('peta.data') }}"
                data-fasilitas-url="{{ route('peta.fasilitas') }}"
                data-detail-template="{{ route('laporan.show', ['laporan' => '__REPORT_ID__']) }}"
                data-pilot-bounds='@json(config("smartpath.pilot.bounds"))'
                data-pilot-center='@json(config("smartpath.pilot.center"))'
                data-gps-timeout="{{ config('smartpath.location.watch_timeout_ms', 15000) }}"
                data-warning-accuracy="{{ config('smartpath.location.warning_accuracy_meters', 100) }}"
                data-manual-accuracy="{{ config('smartpath.location.manual_recommended_accuracy_meters', 500) }}"
                data-user-name="{{ $user->nama_lengkap ?? 'Dewi Lestari' }}"
                data-report-count="{{ $jumlahLaporan ?? 0 }}"
            >
                @if(session('sukses'))
                    <div class="warga-dashboard__flash mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-3 text-sm font-medium text-emerald-800" role="status">
                        {{ session('sukses') }}
                    </div>
                @endif

                {{-- HERO BANNER UTAMA SMARTPATH --}}
                <header id="warga-intro-section" class="warga-hero-banner relative mb-8 min-h-[150px] overflow-hidden rounded-3xl border border-emerald-100 bg-gradient-to-r from-emerald-50 via-teal-50 to-emerald-100/70 shadow-sm transition-all duration-300">
                    <img
                        src="{{ asset('smartpath-banner.png') }}"
                        alt=""
                        aria-hidden="true"
                        class="pointer-events-none absolute right-[8%] top-0 hidden h-full w-[48%] object-contain object-center sm:block"
                        loading="eager"
                    >

                    <div class="pointer-events-none absolute inset-0 bg-gradient-to-r from-emerald-50 via-emerald-50/80 to-transparent" aria-hidden="true"></div>

                    <div class="relative z-10 flex min-h-[150px] flex-col justify-center gap-5 p-6 sm:w-[67%] sm:p-8 lg:w-[65%]">
                        <div class="max-w-2xl">
                            <span class="mb-1 inline-block text-xs font-bold uppercase tracking-wider text-emerald-700">
                                Selamat datang kembali,
                            </span>
                            <h1 class="flex items-center gap-2 text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">
                                Halo, {{ $user->nama_lengkap ?? 'Dewi Lestari' }}
                            </h1>
                            <p class="mt-2 max-w-xl text-sm leading-relaxed text-slate-600">
                                Kelola laporan aksesibilitas, lihat kondisi jalur pedestrian, dan temukan hambatan terverifikasi di sekitar lokasi Anda.
                            </p>
                        </div>
                    </div>
                </header>

                {{-- QUICK ACTIONS SECTION --}}
                <section id="warga-actions-section" class="mb-8" aria-labelledby="warga-actions-title">
                    <h2 id="warga-actions-title" class="sr-only">Akses cepat Dashboard Warga</h2>

                    <div class="warga-quick-actions grid gap-6 md:grid-cols-3">
                        <a href="{{ route('laporan.create') }}" class="warga-action group relative flex items-center justify-between rounded-2xl bg-emerald-800 p-6 text-white shadow-sm transition hover:bg-emerald-900 hover:shadow-md">
                            <div class="flex items-center gap-4">
                                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white/20">
                                    <i class="fa-solid fa-file-circle-plus text-xl" aria-hidden="true"></i>
                                </div>
                                <div>
                                    <h2 class="font-bold text-base">Buat Laporan</h2>
                                    <p class="text-xs text-emerald-100/90 mt-0.5">Laporkan hambatan aksesibilitas yang Anda temukan di ruang publik.</p>
                                </div>
                            </div>
                            <i class="fa-solid fa-chevron-right text-sm opacity-70 group-hover:translate-x-1 transition-transform"></i>
                        </a>

                        <a href="{{ route('laporan.index') }}" class="warga-action group relative flex items-center justify-between rounded-2xl border border-slate-200 bg-white p-6 shadow-xs transition hover:border-emerald-300 hover:shadow-md">
                            <div class="flex items-center gap-4">
                                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700">
                                    <i class="fa-solid fa-file-lines text-xl" aria-hidden="true"></i>
                                </div>
                                <div>
                                    <h2 class="font-bold text-base text-slate-800">Laporan Saya</h2>
                                    <p class="text-xs text-slate-500 mt-0.5">Lihat laporan yang pernah Anda kirim dan pantau statusnya.</p>
                                </div>
                            </div>
                            <i class="fa-solid fa-chevron-right text-sm text-slate-400 group-hover:translate-x-1 transition-transform"></i>
                        </a>

                        <a href="{{ route('peta.index') }}" class="warga-action group relative flex items-center justify-between rounded-2xl border border-slate-200 bg-white p-6 shadow-xs transition hover:border-emerald-300 hover:shadow-md">
                            <div class="flex items-center gap-4">
                                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700">
                                    <i class="fa-solid fa-map-location-dot text-xl" aria-hidden="true"></i>
                                </div>
                                <div>
                                    <h2 class="font-bold text-base text-slate-800">Peta Aksesibilitas</h2>
                                    <p class="text-xs text-slate-500 mt-0.5">Jelajahi titik hambatan terverifikasi dan fasilitas publik yang tersedia.</p>
                                </div>
                            </div>
                            <i class="fa-solid fa-chevron-right text-sm text-slate-400 group-hover:translate-x-1 transition-transform"></i>
                        </a>
                    </div>
                </section>

                {{-- MAP SECTION --}}
                <section id="warga-map-section" class="warga-map-card mb-8 overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-xs">
                    <div class="warga-map-card__header flex flex-col gap-3 border-b border-slate-100 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700">
                                <i class="fa-solid fa-map-location-dot" aria-hidden="true"></i>
                            </div>
                            <div>
                                <h2 id="warga-map-title" class="text-base font-bold text-slate-900">Peta Aksesibilitas</h2>
                                <p class="text-xs text-slate-500">Peta interaktif yang menampilkan lokasi hambatan aksesibilitas dan fasilitas publik di sekitar Anda.</p>
                            </div>
                        </div>
                    </div>

                    <div id="warga-map" class="warga-dashboard__map h-96 w-full" role="region" aria-label="Peta interaktif aksesibilitas"></div>
                </section>

                {{-- NEARBY SECTION --}}
                <section id="warga-nearby-section" class="warga-nearby mb-8 rounded-3xl border border-slate-200 bg-white p-6 shadow-xs">
                    <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700">
                                <i class="fa-solid fa-location-crosshairs text-lg"></i>
                            </div>
                            <div>
                                <h2 id="warga-nearby-title" class="text-base font-bold text-slate-900">Hambatan Terdekat</h2>
                                <p class="text-xs text-slate-500">Dapatkan informasi hambatan yang berada di sekitar lokasi Anda dalam radius 50 meter.</p>
                            </div>
                        </div>

                        <button type="button" id="btn-warga-location" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50 transition shadow-xs">
                            <i class="fa-solid fa-location-arrow text-emerald-600"></i>
                            <span>Gunakan Lokasi Saya</span>
                        </button>
                    </div>

                    <div id="warga-nearby-list" class="warga-nearby-list grid gap-3"></div>
                </section>

                {{-- SECONDARY TOOLS SECTION --}}
                <section id="warga-tools-section" class="grid gap-6 md:grid-cols-2 mb-8">
                    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xs flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700">
                                <i class="fa-solid fa-eye-low-vision text-xl"></i>
                            </div>
                            <div>
                                <h2 class="font-bold text-base text-slate-900">Mode Tunanetra (Nearby)</h2>
                                <p class="text-xs text-slate-500 mt-0.5">Buka Mode Tunanetra untuk mendengarkan hambatan terdekat lewat suara.</p>
                            </div>
                        </div>
                        <button type="button" id="btn-read-nearby" class="inline-flex items-center gap-2 rounded-xl bg-emerald-100/70 px-4 py-2.5 text-xs font-bold text-emerald-800 hover:bg-emerald-200 transition">
                            <i class="fa-solid fa-volume-high"></i>
                            <span>Mulai Nearby</span>
                        </button>
                    </div>

                    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xs flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700">
                                <i class="fa-solid fa-route text-xl"></i>
                            </div>
                            <div>
                                <h2 class="font-bold text-base text-slate-900">Navigasi Aktif</h2>
                                <p class="text-xs text-slate-500 mt-0.5">Tentukan tujuan perjalanan dan dapatkan panduan suara serta rute ramah disabilitas.</p>
                            </div>
                        </div>
                        <a href="{{ route('navigasi.index') }}" class="inline-flex items-center gap-2 rounded-xl bg-emerald-100/70 px-4 py-2.5 text-xs font-bold text-emerald-800 hover:bg-emerald-200 transition">
                            <span>Mulai Navigasi</span>
                            <i class="fa-solid fa-chevron-right text-xs"></i>
                        </a>
                    </div>
                </section>

                {{-- ACCESSIBILITY INFORMATION BOX CARD --}}
                <aside
                    id="warga-accessibility-section"
                    class="warga-accessibility-note mb-8 rounded-2xl border border-emerald-200 bg-white p-5 shadow-sm transition-all duration-300 sm:p-6"
                    role="note"
                    aria-label="Informasi aksesibilitas"
                >
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-start">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100">
                            <i class="fa-solid fa-universal-access text-2xl" aria-hidden="true"></i>
                        </div>

                        <div class="min-w-0 flex-1">
                            <h2 class="text-base font-bold text-slate-900">
                                Informasi Aksesibilitas
                            </h2>

                            <p class="mt-2 text-sm leading-relaxed text-slate-600">
                                Informasi hambatan disajikan dalam bentuk teks dan suara
                                agar mudah dipahami oleh pembaca layar.
                            </p>

                            <div class="mt-4 inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-800">
                                <i class="fa-solid fa-check-circle" aria-hidden="true"></i>
                                <span>Ramah Aksesibilitas</span>
                            </div>
                        </div>
                    </div>
                </aside>

            </main>
        </div>
    </div>

    {{-- ===== TOMBOL SCREEN READER MELAYANG ===== --}}
    <div id="homeScreenReaderButtonContainer" class="fixed bottom-5 right-5 z-[9998] cursor-grab active:cursor-grabbing flex flex-wrap justify-end gap-2" aria-label="Fitur aksesibilitas suara">
        <button id="homeScreenReaderButton" type="button"
            class="inline-flex items-center gap-2 rounded-full border border-emerald-700 bg-emerald-700 px-4 py-3 text-sm font-semibold text-white shadow-lg transition hover:bg-emerald-800 focus-visible:outline-white"
            aria-label="Mulai pembaca layar untuk konten halaman" aria-pressed="false">
            <i class="fa-solid fa-blind text-lg shrink-0" aria-hidden="true"></i>
            <span id="homeScreenReaderLabel">Dengar Panduan</span>
        </button>
        <p id="homeSpeechStatus" class="sr-only" role="status" aria-live="polite"></p>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/smartpath-warga.js') }}" defer></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const container = document.getElementById('homeScreenReaderButtonContainer');
            let isDragging = false;
            let startX = 0, startY = 0, initialX = 0, initialY = 0;
            let hasMoved = false;

            if (container) {
                container.addEventListener('mousedown', dragStart);
                document.addEventListener('mousemove', drag);
                document.addEventListener('mouseup', dragEnd);
                container.addEventListener('touchstart', dragStart, { passive: true });
                document.addEventListener('touchmove', drag, { passive: false });
                document.addEventListener('touchend', dragEnd);
            }

            function dragStart(e) {
                isDragging = true;
                hasMoved = false;
                const clientX = e.type === 'touchstart' ? e.touches[0].clientX : e.clientX;
                const clientY = e.type === 'touchstart' ? e.touches[0].clientY : e.clientY;
                startX = clientX;
                startY = clientY;
                const rect = container.getBoundingClientRect();
                initialX = rect.left;
                initialY = rect.top;
                container.style.bottom = 'auto';
                container.style.right = 'auto';
                container.style.left = `${initialX}px`;
                container.style.top = `${initialY}px`;
            }

            function drag(e) {
                if (!isDragging) return;
                const clientX = e.type === 'touchmove' ? e.touches[0].clientX : e.clientX;
                const clientY = e.type === 'touchmove' ? e.touches[0].clientY : e.clientY;
                const dx = clientX - startX;
                const dy = clientY - startY;
                if (Math.abs(dx) > 3 || Math.abs(dy) > 3) {
                    hasMoved = true;
                    if (e.type === 'touchmove') e.preventDefault();
                }
                container.style.left = `${initialX + dx}px`;
                container.style.top = `${initialY + dy}px`;
            }

            function dragEnd() {
                isDragging = false;
            }

            const readerButton = document.getElementById('homeScreenReaderButton');
            const readerLabel = document.getElementById('homeScreenReaderLabel');
            const speechStatus = document.getElementById('homeSpeechStatus');
            
            if (readerButton && 'speechSynthesis' in window) {
                let readingPage = false;
                let speechRunId = 0;

                function speakText(text, onEnd) {
                    window.speechSynthesis.cancel();
                    const currentRunId = ++speechRunId;

                    const sentences = text
                        .replace(/\s+/g, ' ')
                        .match(/[^.!?]+[.!?]+|[^.!?]+$/g);

                    const sentenceQueue = (sentences || [])
                        .map(sentence => sentence.trim())
                        .filter(Boolean);

                    let sentenceIndex = 0;

                    function speakNextSentence() {
                        if (currentRunId !== speechRunId) return;

                        if (sentenceIndex >= sentenceQueue.length) {
                            if (onEnd) onEnd();
                            return;
                        }

                        const utterance = new SpeechSynthesisUtterance(
                            sentenceQueue[sentenceIndex]
                        );

                        utterance.lang = 'id-ID';
                        utterance.rate = 0.75;
                        utterance.pitch = 1;

                        utterance.onend = () => {
                            if (currentRunId !== speechRunId) return;
                            sentenceIndex++;
                            window.setTimeout(speakNextSentence, 450);
                        };

                        utterance.onerror = () => {
                            if (currentRunId !== speechRunId) return;
                            if (speechStatus) {
                                speechStatus.textContent = 'Suara tidak dapat diputar.';
                            }
                            readingPage = false;
                            readerButton.setAttribute('aria-pressed', 'false');
                            readerLabel.textContent = 'Dengar Panduan';
                        };

                        window.speechSynthesis.speak(utterance);
                    }

                    speakNextSentence();
                }

                readerButton.addEventListener('click', (e) => {
                    if (hasMoved) return;

                    if (window.speechSynthesis.speaking || readingPage) {
                        speechRunId++;
                        window.speechSynthesis.cancel();
                        readingPage = false;
                        readerButton.setAttribute('aria-pressed', 'false');
                        readerLabel.textContent = 'Dengar Panduan';
                        if (speechStatus) speechStatus.textContent = 'Pembacaan dihentikan.';
                        return;
                    }

                    const btnRect = readerButton.getBoundingClientRect();
                    const sidebar = document.getElementById('warga-sidebar');
                    
                    let targetContent = '';
                    let statusMessage = '';

                    if (sidebar && btnRect.left < sidebar.getBoundingClientRect().right) {
                        targetContent = sidebar.innerText.replace(/\s+/g, ' ').trim();
                        statusMessage = 'Menu sidebar sedang dibacakan.';
                    } else {
                        const main = document.getElementById('warga-dashboard') || document.querySelector('.warga-workspace') || document.body;
                        targetContent = main ? main.innerText.replace(/\s+/g, ' ').trim() : '';
                        statusMessage = 'Konten dashboard sedang dibacakan.';
                    }

                    if (!targetContent) return;

                    readingPage = true;
                    readerButton.setAttribute('aria-pressed', 'true');
                    readerLabel.textContent = 'Hentikan Bacaan';
                    if (speechStatus) speechStatus.textContent = statusMessage;
                    
                    speakText(targetContent, () => {
                        readingPage = false;
                        readerButton.setAttribute('aria-pressed', 'false');
                        readerLabel.textContent = 'Dengar Panduan';
                        if (speechStatus) speechStatus.textContent = 'Pembacaan selesai.';
                    });
                });
            }
        });
    </script>
@endpush