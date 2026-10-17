
@extends('layouts.app')

@section('title', 'Dashboard Warga - SmartPath')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/smartpath-warga.css') }}">

<style>
    /* ===== SCREEN READER ===== */
    #homeScreenReaderButtonContainer {
        position: fixed;
        right: 20px;
        bottom: 20px;
        z-index: 9998;
        max-width: calc(100vw - 24px);
        touch-action: none;
        user-select: none;
        -webkit-user-select: none;
        cursor: grab;
    }

    #homeScreenReaderButtonContainer.dragging {
        cursor: grabbing;
        opacity: .95;
        box-shadow: 0 12px 32px rgba(0,0,0,.25);
    }

    #homeScreenReaderButton {
        cursor: inherit;
        touch-action: none;
    }

    #homeSpeechStatus {
        position: absolute;
        width: 1px;
        height: 1px;
        padding: 0;
        margin: -1px;
        overflow: hidden;
        clip: rect(0,0,0,0);
        white-space: nowrap;
        border: 0;
    }

    /* ===== GARIS HIJAU PENANDA PILIHAN ===== */
    .screen-reader-selected {
        outline: 3px solid #10b981 !important;
        outline-offset: 4px !important;
        box-shadow: 0 0 0 5px rgba(16,185,129,.18) !important;
        border-radius: 6px;
    }

    body.screen-reader-click-mode button:focus,
    body.screen-reader-click-mode a:focus,
    body.screen-reader-click-mode [role="button"]:focus,
    body.screen-reader-click-mode [role="link"]:focus,
    body.screen-reader-click-mode input:focus,
    body.screen-reader-click-mode select:focus,
    body.screen-reader-click-mode textarea:focus {
        outline: 3px solid #10b981 !important;
        outline-offset: 4px !important;
    }

    #homeScreenReaderButton:focus-visible,
    a:focus-visible,
    button:focus-visible,
    [tabindex]:focus-visible {
        outline: 3px solid #059669;
        outline-offset: 3px;
    }

    /* ===== ACCESSIBILITY ===== */
    .skip-to-content {
        position: absolute;
        top: -100px;
        left: 1rem;
        z-index: 10000;
        padding: .75rem 1.25rem;
        background: #065f46;
        color: white;
        font-weight: 700;
        border-radius: .75rem;
    }

    .skip-to-content:focus {
        top: 1rem;
    }

    /* ===== HERO ===== */
    .warga-hero-banner {
        position: relative;
        min-height: 150px;
        background: linear-gradient(135deg,#ecfdf5,#d1fae5,#a7f3d0);
    }

    .warga-hero-banner__content {
        padding: clamp(1.5rem,4vw,2rem);
    }

    .warga-hero-banner__dot {
        animation: softPulse 2s ease-in-out 3;
    }

    @keyframes softPulse {
        0%,100% { opacity: 1; }
        50% { opacity: .6; }
    }

    @keyframes fadeSlideUp {
        from { opacity: 0; transform: translateY(12px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .warga-animate-in {
        animation: fadeSlideUp .4s ease both;
    }

    .warga-animate-in[data-delay="1"] { animation-delay: .08s; }
    .warga-animate-in[data-delay="2"] { animation-delay: .16s; }
    .warga-animate-in[data-delay="3"] { animation-delay: .24s; }
    .warga-animate-in[data-delay="4"] { animation-delay: .32s; }
    .warga-animate-in[data-delay="5"] { animation-delay: .4s; }

    .warga-action {
        position: relative;
        transition: transform .2s ease, box-shadow .2s ease;
    }

    .warga-action:hover {
        transform: translateY(-3px);
    }

    .warga-action__icon {
        transition: transform .2s ease;
    }

    .warga-action:hover .warga-action__icon {
        transform: scale(1.06);
    }

    /* ===== MAP ===== */
    .warga-map-card {
        transition: box-shadow .3s ease;
    }

    .warga-map-card:hover {
        box-shadow: 0 8px 30px rgba(16,185,129,.1);
    }

    #warga-map {
        z-index: 0;
        background: #f1f5f9;
    }

    .leaflet-container {
        font-family: inherit;
        border-radius: 0;
    }

    .warga-skeleton {
        background: linear-gradient(90deg,#e2e8f0 25%,#f1f5f9 50%,#e2e8f0 75%);
        background-size: 200% 100%;
        animation: skeletonShimmer 1.5s ease-in-out infinite;
        border-radius: .5rem;
    }

    @keyframes skeletonShimmer {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }

    /* ===== NEARBY ===== */
    .warga-nearby-item {
        display: flex;
        align-items: flex-start;
        gap: .75rem;
        padding: .875rem 1rem;
        border: 1px solid #e2e8f0;
        border-radius: .875rem;
        background: white;
        transition: border-color .2s ease, box-shadow .2s ease;
    }

    .warga-nearby-item:hover {
        border-color: #a7f3d0;
        box-shadow: 0 2px 12px rgba(16,185,129,.08);
    }

    @media (max-width: 900px) {
        .warga-topbar-desktop {
            display: none;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        *, *::before, *::after {
            animation-duration: .01ms !important;
            animation-iteration-count: 1 !important;
            transition-duration: .01ms !important;
        }
    }
</style>
@endpush

@section('content')
<a href="#warga-dashboard" class="skip-to-content">
    Lewati ke konten utama
</a>

<div class="warga-shell flex min-h-screen bg-slate-50">
    @include('partials.sidebar-warga')

    <div class="warga-workspace flex-1">
        <div class="warga-topbar-desktop">
            @include('partials.nav-public')
        </div>

        @include('partials.bar-mobile-warga')

        <main
            id="warga-dashboard"
            class="warga-dashboard mx-auto w-full max-w-7xl px-4 pt-6 pb-24 sm:px-6 sm:pt-8"
            tabindex="-1"
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
                <div
                    class="mb-6 flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-800"
                    role="status"
                    aria-live="polite"
                >
                    <i class="fa-solid fa-circle-check mt-0.5 text-lg text-emerald-600" aria-hidden="true"></i>
                    <span>{{ session('sukses') }}</span>
                </div>
            @endif

            {{-- HERO --}}
            <header
                id="warga-intro-section"
                class="warga-hero-banner warga-animate-in relative mb-6 overflow-hidden rounded-3xl border border-emerald-100 shadow-sm"
            >
                <img
                    src="{{ asset('banner-baru.png') }}"
                    alt=""
                    aria-hidden="true"
                    class="smartpath-hero-img pointer-events-none absolute right-0 top-1/2 hidden h-[140%] w-[52%] -translate-y-1/2 object-contain object-right opacity-100 sm:block"
                    loading="eager"
                >

                <div
                    class="smartpath-hero-fallback pointer-events-none absolute -right-8 top-1/2 hidden h-40 w-40 -translate-y-1/2 rounded-full bg-emerald-300/30 blur-2xl sm:block"
                    aria-hidden="true"
                ></div>

                <div class="pointer-events-none absolute inset-0 bg-gradient-to-r from-emerald-50 via-emerald-50/90 to-transparent sm:via-emerald-50/60" aria-hidden="true"></div>

                <div class="warga-hero-banner__content relative z-10 flex flex-col justify-center gap-4 sm:w-[68%] lg:w-[65%]">
                    <div class="max-w-2xl">
                        <span class="mb-2 inline-flex items-center gap-2 rounded-full bg-white/70 px-3 py-1 text-xs font-bold uppercase tracking-wider text-emerald-800">
                            <span class="warga-hero-banner__dot relative flex h-2 w-2" aria-hidden="true">
                                <span class="absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                <span class="relative inline-flex h-2 w-2 rounded-full bg-emerald-600"></span>
                            </span>
                            Selamat datang kembali
                        </span>

                        <h1 class="flex flex-wrap items-center gap-2 text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl lg:text-4xl">
                            Halo, {{ $user->nama_lengkap ?? 'Dewi Lestari' }}
                            <span aria-hidden="true"></span>
                        </h1>

                        <p class="mt-2 max-w-xl text-sm leading-relaxed text-slate-700 sm:text-base">
                            Kelola laporan aksesibilitas, lihat kondisi jalur pedestrian, dan temukan hambatan terverifikasi di sekitar lokasi Anda.
                        </p>

                        @if(($jumlahLaporan ?? 0) > 0)
                            <div class="mt-4 inline-flex items-center gap-2 rounded-full bg-white/80 px-4 py-2 text-xs font-semibold text-emerald-800 shadow-sm">
                                <i class="fa-solid fa-file-lines" aria-hidden="true"></i>
                                <span>{{ $jumlahLaporan }} laporan telah Anda kirimkan</span>
                            </div>
                        @endif
                    </div>
                </div>
            </header>

            {{-- AKSES CEPAT --}}
            <section
                id="warga-actions-section"
                class="warga-animate-in mb-6"
                data-delay="1"
                aria-labelledby="warga-actions-title"
            >
                <h2 id="warga-actions-title" class="sr-only">Akses cepat Dashboard Warga</h2>

                <div class="warga-quick-actions grid gap-4 sm:gap-5 md:grid-cols-3">
                    <a
                        href="{{ route('laporan.create') }}"
                        class="warga-action group flex items-center justify-between rounded-2xl bg-emerald-800 p-5 text-white shadow-md transition hover:bg-emerald-900 hover:shadow-md sm:p-6"
                    >
                        <div class="flex items-center gap-4">
                            <div class="warga-action__icon flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-white/20">
                                <i class="fa-solid fa-file-circle-plus text-xl" aria-hidden="true"></i>
                            </div>
                            <div>
                                <h3 class="text-base font-bold">Buat Laporan</h3>
                                <p class="mt-0.5 text-sm text-emerald-100">Laporkan hambatan aksesibilitas di ruang publik.</p>
                            </div>
                        </div>
                        <i class="fa-solid fa-chevron-right shrink-0 text-sm" aria-hidden="true"></i>
                    </a>

                    <a
                        href="{{ route('laporan.index') }}"
                        class="warga-action group flex items-center justify-between rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:border-emerald-300 hover:shadow-md sm:p-6"
                    >
                        <div class="flex items-center gap-4">
                            <div class="warga-action__icon flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700">
                                <i class="fa-solid fa-file-lines text-xl" aria-hidden="true"></i>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-slate-800">Laporan Saya</h3>
                                <p class="mt-0.5 text-sm text-slate-600">Pantau status laporan Anda.</p>
                            </div>
                        </div>
                        <i class="fa-solid fa-chevron-right shrink-0 text-sm text-slate-400" aria-hidden="true"></i>
                    </a>

                    <a
                        href="{{ route('peta.index') }}"
                        class="warga-action group flex items-center justify-between rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:border-emerald-300 hover:shadow-md sm:p-6"
                    >
                        <div class="flex items-center gap-4">
                            <div class="warga-action__icon flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700">
                                <i class="fa-solid fa-map-location-dot text-xl" aria-hidden="true"></i>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-slate-800">Peta Aksesibilitas</h3>
                                <p class="mt-0.5 text-sm text-slate-600">Jelajahi titik hambatan dan fasilitas publik.</p>
                            </div>
                        </div>
                        <i class="fa-solid fa-chevron-right shrink-0 text-sm text-slate-400" aria-hidden="true"></i>
                    </a>
                </div>
            </section>

            {{-- NAVIGASI AKTIF --}}
            <section
                id="warga-tools-section"
                class="warga-animate-in mb-6"
                data-delay="2"
                aria-label="Navigasi aktif"
            >
                <div class="flex flex-col items-start gap-4 rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:flex-row sm:items-center sm:justify-between sm:p-6">
                    <div class="flex items-center gap-4">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700">
                            <i class="fa-solid fa-route text-xl" aria-hidden="true"></i>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-slate-900">Navigasi Aktif</h2>
                            <p class="mt-0.5 text-sm text-slate-600">Tentukan tujuan dan dapatkan panduan suara serta rute ramah disabilitas.</p>
                        </div>
                    </div>

                    <a
                        href="{{ route('navigasi.index') }}"
                        class="inline-flex shrink-0 items-center gap-2 rounded-xl bg-emerald-100 px-5 py-3 text-sm font-bold text-emerald-800 transition hover:bg-emerald-200"
                    >
                        Mulai
                        <i class="fa-solid fa-chevron-right text-xs" aria-hidden="true"></i>
                    </a>
                </div>
            </section>

            {{-- PETA --}}
            <section
                id="warga-map-section"
                class="warga-map-card warga-animate-in mb-6 overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm"
                data-delay="3"
                aria-labelledby="warga-map-title"
            >
                <div class="flex flex-col gap-3 border-b border-slate-100 px-5 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700">
                            <i class="fa-solid fa-map-location-dot" aria-hidden="true"></i>
                        </div>
                        <div>
                            <h2 id="warga-map-title" class="text-base font-bold text-slate-900">Peta Aksesibilitas</h2>
                            <p class="text-sm text-slate-600">Lokasi hambatan aksesibilitas dan fasilitas publik di sekitar Anda.</p>
                        </div>
                    </div>

                    <button
                        type="button"
                        id="btn-map-refresh"
                        class="inline-flex items-center gap-2 self-start rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 sm:self-auto"
                        aria-label="Segarkan data peta"
                    >
                        <i class="fa-solid fa-rotate-right" aria-hidden="true"></i>
                        <span>Segarkan</span>
                    </button>
                </div>

                <ul class="flex flex-wrap items-center gap-x-5 gap-y-2 border-b border-slate-100 bg-slate-50/50 px-5 py-3 text-xs font-semibold text-slate-700 sm:px-6" aria-label="Legenda peta">
                    <li class="flex items-center gap-2">
                        <span class="inline-flex h-4 w-4 items-center justify-center rounded-full bg-red-600 text-[9px] font-black text-white" aria-hidden="true">▲</span>
                        Prioritas Tinggi (≥70)
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="inline-flex h-4 w-4 items-center justify-center rounded-full bg-orange-500 text-[9px] font-black text-white" aria-hidden="true">●</span>
                        Sedang (40–69)
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="inline-flex h-4 w-4 items-center justify-center rounded-full bg-green-600 text-[9px] font-black text-white" aria-hidden="true">▼</span>
                        Rendah (&lt;40)
                    </li>
                </ul>

                <div class="relative">
                    <div
                        id="warga-map"
                        class="warga-dashboard__map h-[24rem] w-full sm:h-[28rem]"
                        role="region"
                        aria-label="Peta interaktif aksesibilitas"
                    ></div>

                    <div
                        id="warga-map-skeleton"
                        class="warga-skeleton absolute inset-0 flex items-center justify-center"
                        aria-hidden="true"
                    >
                        <span class="text-sm font-semibold text-slate-500">Memuat peta…</span>
                    </div>
                </div>

                <div
                    id="warga-map-status"
                    class="border-t border-slate-100 bg-slate-50/50 px-5 py-3 text-sm text-slate-700 sm:px-6"
                    role="status"
                    aria-live="polite"
                >
                    Memuat peta…
                </div>
            </section>

            {{-- HAMBATAN TERDEKAT --}}
            <section
                id="warga-nearby-section"
                class="warga-nearby warga-animate-in mb-6 rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6"
                data-delay="4"
                aria-labelledby="warga-nearby-title"
            >
                <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700">
                            <i class="fa-solid fa-location-crosshairs text-lg" aria-hidden="true"></i>
                        </div>
                        <div>
                            <h2 id="warga-nearby-title" class="text-base font-bold text-slate-900">Hambatan Terdekat</h2>
                            <p class="text-sm text-slate-600">Hambatan di sekitar lokasi Anda dalam radius 50 meter.</p>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <button
                            type="button"
                            id="btn-warga-location"
                            class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 shadow-sm transition hover:border-emerald-300 hover:bg-emerald-50 disabled:cursor-wait disabled:opacity-60"
                            aria-describedby="warga-nearby-status"
                        >
                            <i class="fa-solid fa-location-arrow text-emerald-600" aria-hidden="true"></i>
                            <span class="btn-label">Gunakan Lokasi Saya</span>
                        </button>

                        <button
                            type="button"
                            id="btn-read-nearby"
                            class="inline-flex items-center gap-2 rounded-xl bg-emerald-100 px-4 py-2.5 text-sm font-bold text-emerald-800 transition hover:bg-emerald-200 disabled:cursor-not-allowed disabled:opacity-50"
                            aria-label="Dengarkan hasil hambatan terdekat"
                        >
                            <i class="fa-solid fa-volume-high" aria-hidden="true"></i>
                            <span>Dengarkan</span>
                        </button>
                    </div>
                </div>

                <div id="warga-nearby-list" class="warga-nearby-list grid gap-3">
                    <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 px-4 py-8 text-center">
                        <i class="fa-solid fa-location-dot mb-2 text-2xl text-slate-400" aria-hidden="true"></i>
                        <p class="text-sm text-slate-600">
                            Tekan <strong>Gunakan Lokasi Saya</strong> untuk melihat hambatan dalam radius 50 meter.
                        </p>
                    </div>
                </div>

                <p id="warga-nearby-summary" class="mt-4 text-sm font-semibold text-slate-700"></p>
                <p id="warga-nearby-status" class="sr-only" role="status" aria-live="polite"></p>
            </section>

            {{-- INFORMASI AKSESIBILITAS --}}
            <aside
                id="warga-accessibility-section"
                class="warga-animate-in mb-8 rounded-2xl border border-emerald-200 bg-gradient-to-br from-white to-emerald-50/60 p-5 shadow-sm sm:p-6"
                data-delay="5"
                role="note"
                aria-label="Informasi aksesibilitas"
            >
                <div class="flex flex-col gap-4 sm:flex-row sm:items-start">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700">
                        <i class="fa-solid fa-universal-access text-2xl" aria-hidden="true"></i>
                    </div>

                    <div class="min-w-0 flex-1">
                        <h2 class="text-base font-bold text-slate-900">Informasi Aksesibilitas</h2>

                        <p class="mt-2 text-sm leading-relaxed text-slate-700">
                            Informasi hambatan disajikan dalam bentuk teks dan suara agar mudah dipahami oleh pembaca layar.
                        </p>

                        <div class="mt-4 flex flex-wrap gap-2">
                            <span class="inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-800">
                                <i class="fa-solid fa-check-circle" aria-hidden="true"></i>
                                Ramah Aksesibilitas
                            </span>

                            <span class="inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-800">
                                <i class="fa-solid fa-keyboard" aria-hidden="true"></i>
                                Dukungan Keyboard
                            </span>

                            <span class="inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-800">
                                <i class="fa-solid fa-volume-high" aria-hidden="true"></i>
                                Narasi Suara
                            </span>
                        </div>
                    </div>
                </div>
            </aside>

        </main>
    </div>
</div>

{{-- TOMBOL SCREEN READER --}}
<div id="homeScreenReaderButtonContainer">
    <button
        id="homeScreenReaderButton"
        type="button"
        class="inline-flex items-center gap-2 rounded-full border-2 border-emerald-700 bg-emerald-700 px-4 py-3 text-sm font-semibold text-white shadow-lg transition hover:bg-emerald-800"
        aria-label="Aktifkan pembaca suara setiap klik"
        aria-pressed="false"
    >
        <i class="fa-solid fa-blind text-lg" aria-hidden="true"></i>
        <span id="homeScreenReaderLabel">Dengar Panduan</span>
    </button>

    <p id="homeSpeechStatus" role="status" aria-live="polite"></p>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/smartpath-warga.js') }}" defer></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('homeScreenReaderButtonContainer');
    const readerButton = document.getElementById('homeScreenReaderButton');
    const readerLabel = document.getElementById('homeScreenReaderLabel');
    const speechStatus = document.getElementById('homeSpeechStatus');

    const speechSupported =
        'speechSynthesis' in window &&
        typeof window.SpeechSynthesisUtterance !== 'undefined';

    let clickReaderEnabled = false;
    let lastHighlightedElement = null;

    /* DRAG BUTTON */
    let dragging = false;
    let dragMoved = false;
    let startX = 0;
    let startY = 0;
    let initialLeft = 0;
    let initialTop = 0;
    let activePointerId = null;
    let suppressNextReaderClick = false;

    /* CEGAH SUARA GANDA PADA KLIK DAN FOKUS */
    let keyboardNavigation = false;

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Tab' || event.key.startsWith('Arrow')) {
            keyboardNavigation = true;
        }

        if (event.key === 'Escape' && speechSupported) {
            window.speechSynthesis.cancel();

            if (speechStatus) {
                speechStatus.textContent = 'Pembacaan suara dihentikan.';
            }
        }
    });

    document.addEventListener('pointerdown', function () {
        keyboardNavigation = false;
    });

    /* GAMBAR HERO */
    document.querySelectorAll('img.smartpath-hero-img').forEach(function (img) {
        const fallback = img.parentElement.querySelector('.smartpath-hero-fallback');

        function showFallback() {
            img.style.display = 'none';
            if (fallback) fallback.classList.remove('hidden');
        }

        img.addEventListener('error', showFallback);

        if (img.complete && img.naturalWidth === 0) {
            showFallback();
        }

        img.addEventListener('load', function () {
            if (fallback) fallback.classList.add('hidden');
        });
    });

    /* FUNGSI SUARA */
    function speakText(text) {
        if (!speechSupported || !text) return;

        window.speechSynthesis.cancel();

        const utterance = new SpeechSynthesisUtterance(text);
        utterance.lang = 'id-ID';
        utterance.rate = 0.9;
        utterance.pitch = 1;

        window.speechSynthesis.speak(utterance);
    }

    /* PENANDA HIJAU */
    function clearHighlight() {
        if (lastHighlightedElement) {
            lastHighlightedElement.classList.remove('screen-reader-selected');
            lastHighlightedElement.removeAttribute('data-screen-reader-active');
            lastHighlightedElement = null;
        }
    }

    function highlightElement(target) {
        if (!target) return;

        clearHighlight();

        lastHighlightedElement = target;
        target.classList.add('screen-reader-selected');
        target.setAttribute('data-screen-reader-active', 'true');
    }

    /* MODE SCREEN READER */
    function setClickReader(enabled, announce) {
        clickReaderEnabled = enabled;

        document.body.classList.toggle('screen-reader-click-mode', enabled);

        if (!enabled) {
            clearHighlight();

            if (speechSupported) {
                window.speechSynthesis.cancel();
            }
        }

        if (readerLabel) {
            readerLabel.textContent = enabled
                ? 'Hentikan Bacaan'
                : 'Dengar Panduan';
        }

        if (readerButton) {
            readerButton.setAttribute(
                'aria-pressed',
                enabled ? 'true' : 'false'
            );

            readerButton.setAttribute(
                'aria-label',
                enabled
                    ? 'Nonaktifkan pembaca suara setiap klik'
                    : 'Aktifkan pembaca suara setiap klik'
            );
        }

        if (speechStatus) {
            speechStatus.textContent = enabled
                ? 'Mode pembaca suara aktif. Pilih tombol, menu, atau tautan.'
                : 'Mode pembaca suara nonaktif.';
        }

        if (announce) {
            speakText(
                enabled
                    ? 'Pembaca suara aktif. Pilih tombol, menu, atau tautan untuk mendengarkan keterangannya.'
                    : 'Pembaca suara dinonaktifkan.'
            );
        }
    }

    /* TOMBOL UTAMA */
    if (readerButton) {
        if (!speechSupported) {
            readerButton.disabled = true;

            if (speechStatus) {
                speechStatus.textContent =
                    'Browser ini tidak mendukung pembacaan suara.';
            }
        } else {
            readerButton.addEventListener('click', function (event) {
                event.preventDefault();
                event.stopPropagation();

                if (suppressNextReaderClick || dragMoved) {
                    suppressNextReaderClick = false;
                    dragMoved = false;
                    return;
                }

                setClickReader(!clickReaderEnabled, true);
            });
        }
    }

    /* BACA TOMBOL, MENU, TAUTAN, DAN MARKER PETA */
    document.addEventListener('click', function (event) {
        if (!clickReaderEnabled || !speechSupported) return;

        const clickedElement = event.target instanceof Element
            ? event.target
            : event.target.parentElement;

        if (!clickedElement) return;

        const target = clickedElement.closest(
            'button, a, [role="button"], [role="link"], ' +
            'input[type="submit"], input[type="button"], ' +
            '.leaflet-marker-icon, .leaflet-interactive'
        );

        if (!target) return;

        if (target.closest('#homeScreenReaderButtonContainer')) return;

        if (
            target.disabled ||
            target.getAttribute('aria-disabled') === 'true'
        ) {
            speakText('Tombol tidak tersedia.');
            return;
        }

        let text =
            target.getAttribute('aria-label') ||
            target.getAttribute('title') ||
            target.innerText ||
            target.textContent ||
            target.value ||
            '';

        text = text.replace(/\s+/g, ' ').trim();

        if (!text) {
            if (
                target.classList.contains('leaflet-marker-icon') ||
                target.classList.contains('leaflet-interactive')
            ) {
                text = 'Penanda lokasi pada peta.';
            } else {
                return;
            }
        }

        highlightElement(target);

        const isLink =
            target.tagName.toLowerCase() === 'a' ||
            target.getAttribute('role') === 'link';

        speakText(isLink ? 'Tautan. ' + text : text);
    });

    /* FOKUS KEYBOARD: HIGHLIGHT TANPA SUARA GANDA */
    document.addEventListener('focusin', function (event) {
        if (!clickReaderEnabled || !speechSupported) return;

        const focusedElement = event.target;

        if (!(focusedElement instanceof Element)) return;

        const target = focusedElement.closest(
            'button, a, [role="button"], [role="link"], input, select, textarea'
        );

        if (!target) return;

        if (target.closest('#homeScreenReaderButtonContainer')) return;

        if (keyboardNavigation) {
            highlightElement(target);
        }
    });

    /* DRAG BUTTON SCREEN READER */
    if (container) {
        container.addEventListener('pointerdown', function (event) {
            if (event.button !== undefined && event.button !== 0) return;

            dragging = true;
            dragMoved = false;
            activePointerId = event.pointerId;

            const rect = container.getBoundingClientRect();

            startX = event.clientX;
            startY = event.clientY;
            initialLeft = rect.left;
            initialTop = rect.top;

            container.style.left = rect.left + 'px';
            container.style.top = rect.top + 'px';
            container.style.right = 'auto';
            container.style.bottom = 'auto';

            try {
                container.setPointerCapture(event.pointerId);
            } catch (error) {}
        });

        container.addEventListener('pointermove', function (event) {
            if (!dragging || event.pointerId !== activePointerId) return;

            const dx = event.clientX - startX;
            const dy = event.clientY - startY;

            if (Math.abs(dx) > 5 || Math.abs(dy) > 5) {
                dragMoved = true;
                suppressNextReaderClick = true;
                container.classList.add('dragging');
            }

            if (!dragMoved) return;

            const maxLeft = Math.max(
                0,
                window.innerWidth - container.offsetWidth
            );

            const maxTop = Math.max(
                0,
                window.innerHeight - container.offsetHeight
            );

            container.style.left = Math.min(
                maxLeft,
                Math.max(0, initialLeft + dx)
            ) + 'px';

            container.style.top = Math.min(
                maxTop,
                Math.max(0, initialTop + dy)
            ) + 'px';
        });

        function finishDrag(event) {
            if (event.pointerId !== activePointerId) return;

            dragging = false;
            activePointerId = null;
            container.classList.remove('dragging');

            try {
                container.releasePointerCapture(event.pointerId);
            } catch (error) {}

            if (dragMoved) {
                setTimeout(function () {
                    dragMoved = false;
                    suppressNextReaderClick = false;
                }, 300);
            }
        }

        container.addEventListener('pointerup', finishDrag);
        container.addEventListener('pointercancel', finishDrag);
    }

    /* DOUBLE KLIK BAGIAN INFORMASI UNTUK MEMBACA ISINYA */
    document.addEventListener('dblclick', function (event) {
        if (!speechSupported) return;

        const clickedElement = event.target instanceof Element
            ? event.target
            : event.target.parentElement;

        if (!clickedElement) return;

        const target = clickedElement.closest(
            '#warga-intro-section, #warga-actions-section, ' +
            '#warga-tools-section, #warga-map-section, ' +
            '#warga-nearby-section, #warga-accessibility-section'
        );

        if (!target) return;

        const clone = target.cloneNode(true);

        clone.querySelectorAll(
            'button, script, style, .sr-only, ' +
            '[aria-hidden="true"], #warga-map, #warga-map-skeleton'
        ).forEach(function (element) {
            element.remove();
        });

        const text = (
            clone.innerText || clone.textContent || ''
        ).replace(/\s+/g, ' ').trim();

        if (text) {
            highlightElement(target);
            speakText(text);
        }
    });

    /* SEMBUNYIKAN SKELETON KETIKA PETA SIAP */
    const mapElement = document.getElementById('warga-map');
    const mapSkeleton = document.getElementById('warga-map-skeleton');

    if (mapElement && mapSkeleton) {
        function hideMapSkeleton() {
            if (!mapSkeleton.isConnected) return;

            mapSkeleton.style.transition = 'opacity .3s ease';
            mapSkeleton.style.opacity = '0';

            setTimeout(function () {
                if (mapSkeleton.isConnected) mapSkeleton.remove();
            }, 320);
        }

        if (mapElement.classList.contains('leaflet-container')) {
            hideMapSkeleton();
        } else {
            const mapObserver = new MutationObserver(function () {
                if (mapElement.classList.contains('leaflet-container')) {
                    mapObserver.disconnect();
                    hideMapSkeleton();
                }
            });

            mapObserver.observe(mapElement, {
                attributes: true,
                attributeFilter: ['class']
            });

            setTimeout(function () {
                mapObserver.disconnect();
                hideMapSkeleton();
            }, 8000);
        }
    }

    /* SEGARKAN PETA */
    const refreshButton = document.getElementById('btn-map-refresh');

    if (refreshButton) {
        refreshButton.addEventListener('click', function () {
            const status = document.getElementById('warga-map-status');

            if (status) status.textContent = 'Menyegarkan peta…';

            document.dispatchEvent(
                new CustomEvent('smartpath:refresh-map')
            );

            setTimeout(function () {
                if (
                    status &&
                    status.textContent === 'Menyegarkan peta…'
                ) {
                    status.textContent =
                        'Permintaan penyegaran peta telah dikirim.';
                }
            }, 3000);
        });
    }

    /* BERSIHKAN SUARA SAAT MENINGGALKAN HALAMAN */
    window.addEventListener('pagehide', function () {
        if (speechSupported) {
            window.speechSynthesis.cancel();
        }
    });
});
</script>
@endpush
