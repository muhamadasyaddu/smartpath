@extends('layouts.app')

@section('title', 'Dashboard Warga - SmartPath')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/smartpath-warga.css') }}">
    <style>
        /* ===== SCREEN READER DRAGGABLE BUTTON ===== */
        #homeScreenReaderButtonContainer {
            touch-action: pan-y; /* izinkan scroll vertikal, blokir horizontal saat drag */
            user-select: none;
            transition: box-shadow 0.2s ease;
        }
        #homeScreenReaderButtonContainer.dragging {
            cursor: grabbing;
            box-shadow: 0 12px 32px rgba(0,0,0,0.25);
            opacity: 0.95;
            touch-action: none; /* hanya saat drag aktif */
        }
        #homeScreenReaderButton { cursor: grab; }
        #homeScreenReaderButtonContainer.dragging #homeScreenReaderButton { cursor: grabbing; }

        /* ===== ANIMATIONS ===== */
        @keyframes fadeSlideUp {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }
        @keyframes softPulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.6; }
        }

        .warga-animate-in {
            animation: fadeSlideUp 0.4s cubic-bezier(0.16, 1, 0.3, 1) both;
        }
        .warga-animate-in[data-delay="1"] { animation-delay: 0.08s; }
        .warga-animate-in[data-delay="2"] { animation-delay: 0.16s; }
        .warga-animate-in[data-delay="3"] { animation-delay: 0.24s; }
        .warga-animate-in[data-delay="4"] { animation-delay: 0.32s; }
        .warga-animate-in[data-delay="5"] { animation-delay: 0.4s; }

        /* ===== SKIP LINK (WCAG 2.4.1) ===== */
        .skip-to-content {
            position: absolute;
            top: -100px;
            left: 1rem;
            z-index: 10000;
            padding: 0.75rem 1.25rem;
            background: #065f46;
            color: #fff;
            font-weight: 700;
            font-size: 0.875rem;
            border-radius: 0.75rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            transition: top 0.2s ease;
        }
        .skip-to-content:focus {
            top: 1rem;
            outline: 3px solid #34d399;
            outline-offset: 2px;
        }

        /* ===== HERO BANNER ===== */
        .warga-hero-banner {
            position: relative;
            background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 50%, #a7f3d0 100%);
        }
        .warga-hero-banner::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at 20% 20%, rgba(16,185,129,0.12) 0%, transparent 40%),
                radial-gradient(circle at 80% 80%, rgba(5,150,105,0.1) 0%, transparent 50%);
            pointer-events: none;
        }
        .warga-hero-banner__dot {
            animation: softPulse 2s ease-in-out 3; /* hanya 3x lalu berhenti */
        }

        /* ===== QUICK ACTION CARDS ===== */
        /* overflow TIDAK di-hidden agar focus ring tidak terpotong */
        .warga-action {
            position: relative;
            isolation: isolate;
            transition: transform 0.25s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.25s ease;
        }
        .warga-action:hover { transform: translateY(-3px); }
        .warga-action:active { transform: translateY(-1px) scale(0.995); }
        .warga-action__icon {
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .warga-action:hover .warga-action__icon {
            transform: scale(1.08);
        }

        /* ===== MAP CARD ===== */
        .warga-map-card { transition: box-shadow 0.3s ease; }
        .warga-map-card:hover { box-shadow: 0 8px 30px rgba(16,185,129,0.1); }
        #warga-map {
            z-index: 0;
            background: #f1f5f9;
        }
        .leaflet-container {
            font-family: inherit;
            border-radius: 0;
        }

        /* ===== NEARBY ITEMS ===== */
        .warga-nearby-item {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            padding: 0.875rem 1rem;
            border-radius: 0.875rem;
            border: 1px solid #e2e8f0;
            background: #fff;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }
        .warga-nearby-item:hover {
            border-color: #a7f3d0;
            box-shadow: 0 2px 12px rgba(16,185,129,0.08);
        }

        /* ===== SKELETON LOADER ===== */
        @keyframes skeletonShimmer {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
        .warga-skeleton {
            background: linear-gradient(90deg, #e2e8f0 25%, #f1f5f9 50%, #e2e8f0 75%);
            background-size: 200% 100%;
            animation: skeletonShimmer 1.5s ease-in-out infinite;
            border-radius: 0.5rem;
        }

        /* ===== FOCUS VISIBLE ===== */
        a:focus-visible,
        button:focus-visible,
        [tabindex]:focus-visible {
            outline: 3px solid #059669;
            outline-offset: 2px;
            border-radius: 0.5rem;
        }
        @media (max-width: 900px) {
    .warga-topbar-desktop { display: none; }
}

        /* ===== REDUCED MOTION ===== */
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
            .warga-hero-banner__dot { animation: none !important; }
            .warga-skeleton { animation: none !important; background: #e2e8f0; }
        }

        /* ===== RESPONSIVE FONT SCALING (aksesibilitas text zoom) ===== */
        .warga-hero-banner { min-height: auto; }
        .warga-hero-banner__content { padding: clamp(1.5rem, 4vw, 2rem); }
    </style>
@endpush

@section('content')
    {{-- Skip to content (WCAG 2.4.1) --}}
    <a href="#warga-dashboard" class="skip-to-content">Lewati ke konten utama</a>

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
                    <div class="warga-dashboard__flash mb-6 flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-800 warga-animate-in" role="status" aria-live="polite">
                        <i class="fa-solid fa-circle-check mt-0.5 text-lg text-emerald-600" aria-hidden="true"></i>
                        <span>{{ session('sukses') }}</span>
                    </div>
                @endif

                {{-- ===== HERO BANNER ===== --}}
                <header id="warga-intro-section" class="warga-hero-banner warga-animate-in relative mb-6 overflow-hidden rounded-3xl border border-emerald-100 shadow-sm">
                    <img
                        src="{{ asset('smartpath-banner.png') }}"
                        alt=""
                        aria-hidden="true"
                        class="smartpath-hero-img pointer-events-none absolute -right-4 top-1/2 hidden h-[120%] w-[45%] -translate-y-1/2 object-contain object-center opacity-90 sm:block lg:w-[42%]"
                        loading="eager"
                    >
                    {{-- Fallback dekoratif jika gambar gagal (tidak menghilang) --}}
                    <div class="smartpath-hero-fallback pointer-events-none absolute -right-8 top-1/2 hidden h-40 w-40 -translate-y-1/2 rounded-full bg-emerald-300/30 blur-2xl sm:block" aria-hidden="true"></div>

                    <div class="pointer-events-none absolute inset-0 bg-gradient-to-r from-emerald-50 via-emerald-50/90 to-transparent sm:via-emerald-50/60" aria-hidden="true"></div>

                    <div class="warga-hero-banner__content relative z-10 flex flex-col justify-center gap-4 sm:w-[68%] lg:w-[65%]">
                        <div class="max-w-2xl">
                            <span class="mb-2 inline-flex items-center gap-2 rounded-full bg-white/70 px-3 py-1 text-xs font-bold uppercase tracking-wider text-emerald-800 backdrop-blur-sm">
                                <span class="warga-hero-banner__dot relative flex h-2 w-2" aria-hidden="true">
                                    <span class="absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                    <span class="relative inline-flex h-2 w-2 rounded-full bg-emerald-600"></span>
                                </span>
                                Selamat datang kembali
                            </span>
                            <h1 class="flex flex-wrap items-center gap-2 text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl lg:text-4xl">
                                Halo, {{ $user->nama_lengkap ?? 'Dewi Lestari' }}
                                <span class="inline-block text-2xl sm:text-3xl" aria-hidden="true">👋</span>
                            </h1>
                            <p class="mt-2 max-w-xl text-sm leading-relaxed text-slate-700 sm:text-base">
                                Kelola laporan aksesibilitas, lihat kondisi jalur pedestrian, dan temukan hambatan terverifikasi di sekitar lokasi Anda.
                            </p>

                            @if(($jumlahLaporan ?? 0) > 0)
                                <div class="mt-4 inline-flex items-center gap-2 rounded-full bg-white/80 px-4 py-2 text-xs font-semibold text-emerald-800 shadow-sm backdrop-blur-sm">
                                    <i class="fa-solid fa-file-lines" aria-hidden="true"></i>
                                    <span>{{ $jumlahLaporan }} laporan telah Anda kirimkan</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </header>

                {{-- ===== QUICK ACTIONS ===== --}}
                <section id="warga-actions-section" class="warga-animate-in mb-6" data-delay="1" aria-labelledby="warga-actions-title">
                    <h2 id="warga-actions-title" class="sr-only">Akses cepat Dashboard Warga</h2>

                    <div class="warga-quick-actions grid gap-4 sm:gap-5 md:grid-cols-3">
                        <a href="{{ route('laporan.create') }}" class="warga-action group relative flex items-center justify-between overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-700 to-emerald-900 p-5 text-white shadow-md ring-1 ring-emerald-900/20 sm:p-6" aria-label="Buat laporan aksesibilitas baru">
                            <div class="flex items-center gap-4">
                                <div class="warga-action__icon flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-white/20 ring-1 ring-white/30">
                                    <i class="fa-solid fa-file-circle-plus text-xl" aria-hidden="true"></i>
                                </div>
                                <div>
                                    <h3 class="text-base font-bold">Buat Laporan</h3>
                                    <p class="mt-0.5 text-sm text-emerald-100">Laporkan hambatan aksesibilitas di ruang publik.</p>
                                </div>
                            </div>
                            <i class="fa-solid fa-arrow-right shrink-0 text-sm opacity-70 transition-transform group-hover:translate-x-1" aria-hidden="true"></i>
                        </a>

                        <a href="{{ route('laporan.index') }}" class="warga-action group relative flex items-center justify-between overflow-hidden rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:border-emerald-300 hover:shadow-md sm:p-6">
                            <div class="flex items-center gap-4">
                                <div class="warga-action__icon flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100">
                                    <i class="fa-solid fa-file-lines text-xl" aria-hidden="true"></i>
                                </div>
                                <div>
                                    <h3 class="text-base font-bold text-slate-800">Laporan Saya</h3>
                                    <p class="mt-0.5 text-sm text-slate-600">Pantau status laporan Anda.</p>
                                </div>
                            </div>
                            <i class="fa-solid fa-chevron-right shrink-0 text-sm text-slate-400 transition-transform group-hover:translate-x-1" aria-hidden="true"></i>
                        </a>

                        <a href="{{ route('peta.index') }}" class="warga-action group relative flex items-center justify-between overflow-hidden rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:border-emerald-300 hover:shadow-md sm:p-6">
                            <div class="flex items-center gap-4">
                                <div class="warga-action__icon flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100">
                                    <i class="fa-solid fa-map-location-dot text-xl" aria-hidden="true"></i>
                                </div>
                                <div>
                                    <h3 class="text-base font-bold text-slate-800">Peta Aksesibilitas</h3>
                                    <p class="mt-0.5 text-sm text-slate-600">Jelajahi titik hambatan & fasilitas.</p>
                                </div>
                            </div>
                            <i class="fa-solid fa-chevron-right shrink-0 text-sm text-slate-400 transition-transform group-hover:translate-x-1" aria-hidden="true"></i>
                        </a>
                    </div>
                </section>

                {{-- ===== NAVIGASI AKTIF ===== --}}
                <section id="warga-tools-section" class="warga-animate-in mb-6" data-delay="2" aria-label="Navigasi aktif">
                    <div class="flex flex-col items-start gap-4 rounded-3xl border border-slate-200 bg-white p-5 shadow-sm transition hover:shadow-md sm:flex-row sm:items-center sm:justify-between sm:p-6">
                        <div class="flex items-center gap-4">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-50 to-teal-50 text-emerald-700 ring-1 ring-emerald-100">
                                <i class="fa-solid fa-route text-xl" aria-hidden="true"></i>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-slate-900">Navigasi Aktif</h3>
                                <p class="mt-0.5 text-sm text-slate-600">Tentukan tujuan dan dapatkan panduan suara serta rute ramah disabilitas.</p>
                            </div>
                        </div>
                        <a href="{{ route('navigasi.index') }}" class="inline-flex shrink-0 items-center gap-2 rounded-xl bg-emerald-100 px-5 py-3 text-sm font-bold text-emerald-800 transition hover:bg-emerald-200 hover:shadow-sm">
                            <span>Mulai</span>
                            <i class="fa-solid fa-chevron-right text-xs" aria-hidden="true"></i>
                        </a>
                    </div>
                </section>

                {{-- ===== MAP SECTION ===== --}}
                <section id="warga-map-section" class="warga-map-card warga-animate-in mb-6 overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm" data-delay="3" aria-labelledby="warga-map-title">
                    <div class="flex flex-col gap-3 border-b border-slate-100 px-5 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100">
                                <i class="fa-solid fa-map-location-dot" aria-hidden="true"></i>
                            </div>
                            <div>
                                <h2 id="warga-map-title" class="text-base font-bold text-slate-900">Peta Aksesibilitas</h2>
                                <p class="text-sm text-slate-600">Lokasi hambatan aksesibilitas dan fasilitas publik di sekitar Anda.</p>
                            </div>
                        </div>
                        <button type="button" id="btn-map-refresh" class="inline-flex items-center gap-2 self-start rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 sm:self-auto" aria-label="Segarkan data peta">
                            <i class="fa-solid fa-rotate-right" aria-hidden="true"></i>
                            <span>Segarkan</span>
                        </button>
                    </div>

                    {{-- Legenda warna — dengan ikon pembeda untuk colorblind --}}
                    <ul class="flex flex-wrap items-center gap-x-5 gap-y-2 border-b border-slate-100 bg-slate-50/50 px-5 py-3 text-xs font-semibold text-slate-700 sm:px-6" aria-label="Keterangan warna dan ikon marker peta">
                        <li class="flex items-center gap-2">
                            <span class="inline-flex h-4 w-4 items-center justify-center rounded-full bg-red-600 text-[9px] font-black text-white shadow-sm ring-2 ring-red-100" aria-hidden="true">▲</span>
                            Prioritas Tinggi (≥70)
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="inline-flex h-4 w-4 items-center justify-center rounded-full bg-orange-500 text-[9px] font-black text-white shadow-sm ring-2 ring-orange-100" aria-hidden="true">●</span>
                            Sedang (40–69)
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="inline-flex h-4 w-4 items-center justify-center rounded-full bg-green-600 text-[9px] font-black text-white shadow-sm ring-2 ring-green-100" aria-hidden="true">▼</span>
                            Rendah (&lt;40)
                        </li>
                    </ul>

                    <div class="relative">
                        <div id="warga-map" class="warga-dashboard__map h-[24rem] w-full sm:h-[28rem]" role="region" aria-label="Peta interaktif aksesibilitas"></div>
                        {{-- Skeleton overlay saat map belum siap --}}
                        <div id="warga-map-skeleton" class="warga-skeleton absolute inset-0 flex items-center justify-center" aria-hidden="true">
                            <span class="text-sm font-semibold text-slate-500">Memuat peta…</span>
                        </div>
                    </div>

                    <div id="warga-map-status" class="border-t border-slate-100 bg-slate-50/50 px-5 py-3 text-sm text-slate-700 sm:px-6" role="status" aria-live="polite">
                        Memuat peta…
                    </div>
                </section>

                {{-- ===== NEARBY SECTION ===== --}}
                <section id="warga-nearby-section" class="warga-nearby warga-animate-in mb-6 rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6" data-delay="4" aria-labelledby="warga-nearby-title">
                    <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100">
                                <i class="fa-solid fa-location-crosshairs text-lg" aria-hidden="true"></i>
                            </div>
                            <div>
                                <h2 id="warga-nearby-title" class="text-base font-bold text-slate-900">Hambatan Terdekat</h2>
                                <p class="text-sm text-slate-600">Hambatan di sekitar lokasi Anda dalam radius 50 meter.</p>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-2">
                            <button type="button" id="btn-warga-location" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 shadow-sm transition hover:border-emerald-300 hover:bg-emerald-50 disabled:cursor-wait disabled:opacity-60" aria-describedby="warga-nearby-status">
                                <i class="fa-solid fa-location-arrow text-emerald-600" aria-hidden="true"></i>
                                <span class="btn-label">Gunakan Lokasi Saya</span>
                            </button>
                            <button type="button" id="btn-read-nearby" class="inline-flex items-center gap-2 rounded-xl bg-emerald-100 px-4 py-2.5 text-sm font-bold text-emerald-800 transition hover:bg-emerald-200 disabled:cursor-not-allowed disabled:opacity-50" aria-label="Dengarkan hasil hambatan terdekat">
                                <i class="fa-solid fa-volume-high" aria-hidden="true"></i>
                                <span>Dengarkan</span>
                            </button>
                        </div>
                    </div>

                    <div id="warga-nearby-list" class="warga-nearby-list grid gap-3">
                        <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 px-4 py-8 text-center">
                            <i class="fa-solid fa-location-dot mb-2 text-2xl text-slate-400" aria-hidden="true"></i>
                            <p class="text-sm text-slate-600">Tekan <strong>"Gunakan Lokasi Saya"</strong> untuk melihat hambatan dalam radius 50 meter.</p>
                        </div>
                    </div>

                    <p id="warga-nearby-summary" class="mt-4 text-sm font-semibold text-slate-700"></p>
                    <p id="warga-nearby-status" class="sr-only" role="status" aria-live="polite"></p>
                </section>

                {{-- ===== ACCESSIBILITY NOTE ===== --}}
                <aside
                    id="warga-accessibility-section"
                    class="warga-accessibility-note warga-animate-in mb-8 rounded-2xl border border-emerald-200 bg-gradient-to-br from-white to-emerald-50/60 p-5 shadow-sm sm:p-6"
                    data-delay="5"
                    role="note"
                    aria-label="Informasi aksesibilitas"
                >
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-start">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100">
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
                                    <span>Ramah Aksesibilitas</span>
                                </span>
                                <span class="inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-800">
                                    <i class="fa-solid fa-keyboard" aria-hidden="true"></i>
                                    <span>Dukungan Keyboard</span>
                                </span>
                                <span class="inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-800">
                                    <i class="fa-solid fa-volume-high" aria-hidden="true"></i>
                                    <span>Narasi Suara</span>
                                </span>
                            </div>
                        </div>
                    </div>
                </aside>

            </main>
        </div>
    </div>

    {{-- ===== SCREEN READER FLOATING BUTTON ===== --}}
    <div id="homeScreenReaderButtonContainer" class="fixed bottom-5 right-5 z-[9998] flex flex-wrap justify-end gap-2">
        <button id="homeScreenReaderButton" type="button"
            class="inline-flex items-center gap-2 rounded-full border-2 border-emerald-700 bg-emerald-700 px-4 py-3 text-sm font-semibold text-white shadow-lg transition hover:bg-emerald-800 hover:shadow-xl focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-emerald-300"
            aria-label="Dengar panduan halaman. Tekan lagi untuk berhenti.">
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
            /* ============================================================
               0. HERO IMAGE FALLBACK (tanpa inline handler, aman CSP)
               ============================================================ */
            document.querySelectorAll('img.smartpath-hero-img').forEach((img) => {
                const fallback = img.parentElement.querySelector('.smartpath-hero-fallback');
                const onError = () => {
                    img.style.display = 'none';
                    if (fallback) fallback.classList.remove('hidden');
                };
                if (img.complete && img.naturalWidth === 0) onError();
                img.addEventListener('error', onError);
                img.addEventListener('load', () => {
                    if (fallback) fallback.classList.add('hidden');
                });
            });

            /* ============================================================
               1. DRAGGABLE SCREEN READER BUTTON — Pointer Events API
               ============================================================ */
            const container = document.getElementById('homeScreenReaderButtonContainer');
            const readerButton = document.getElementById('homeScreenReaderButton');
            const readerLabel = document.getElementById('homeScreenReaderLabel');
            const speechStatus = document.getElementById('homeSpeechStatus');

            let pointerDown = false;
            let dragStartPoint = null;
            let dragStartRect = null;
            let hasMoved = false;
            let activePointerId = null;

            if (container) {
                container.addEventListener('pointerdown', onPointerDown);
                container.addEventListener('pointermove', onPointerMove);
                container.addEventListener('pointerup', onPointerUp);
                container.addEventListener('pointercancel', onPointerUp);
            }

            function onPointerDown(e) {
                // Hanya tombol kiri mouse / sentuhan
                if (e.button !== undefined && e.button !== 0) return;

                pointerDown = true;
                hasMoved = false;
                activePointerId = e.pointerId;
                dragStartPoint = { x: e.clientX, y: e.clientY };

                const rect = container.getBoundingClientRect();
                dragStartRect = { left: rect.left, top: rect.top };

                try { container.setPointerCapture(e.pointerId); } catch (_) {}

                // Pindahkan posisi dari fixed-bottom-right ke fixed-left-top
                container.style.bottom = 'auto';
                container.style.right = 'auto';
                container.style.left = `${rect.left}px`;
                container.style.top = `${rect.top}px`;
            }

            function onPointerMove(e) {
                if (!pointerDown || e.pointerId !== activePointerId) return;

                const dx = e.clientX - dragStartPoint.x;
                const dy = e.clientY - dragStartPoint.y;

                if (!hasMoved && (Math.abs(dx) > 4 || Math.abs(dy) > 4)) {
                    hasMoved = true;
                    container.classList.add('dragging');
                }

                if (!hasMoved) return; // belum melewati threshold, jangan geser

                const maxLeft = window.innerWidth - container.offsetWidth;
                const maxTop = window.innerHeight - container.offsetHeight;
                const newLeft = Math.min(Math.max(0, dragStartRect.left + dx), Math.max(0, maxLeft));
                const newTop = Math.min(Math.max(0, dragStartRect.top + dy), Math.max(0, maxTop));

                container.style.left = `${newLeft}px`;
                container.style.top = `${newTop}px`;
            }

            function onPointerUp(e) {
                if (e.pointerId !== activePointerId) return;
                pointerDown = false;
                activePointerId = null;
                container.classList.remove('dragging');
                try { container.releasePointerCapture(e.pointerId); } catch (_) {}

                // Delay reset hasMoved agar klik yang menyertai pointerup masih terblokir
                if (hasMoved) {
                    setTimeout(() => { hasMoved = false; }, 0);
                }
            }

            /* ============================================================
               2. SCREEN READER — Narasi terkurasi per region
               ============================================================ */
            if (readerButton && 'speechSynthesis' in window) {
                let readingPage = false;
                let speechRunId = 0;

                const speakText = (text, onEnd) => {
                    window.speechSynthesis.cancel();
                    const currentRunId = ++speechRunId;

                    const sentences = (text.replace(/\s+/g, ' ').match(/[^.!?]+[.!?]+|[^.!?]+$/g) || [])
                        .map(s => s.trim())
                        .filter(Boolean);

                    let i = 0;
                    const next = () => {
                        if (currentRunId !== speechRunId) return;
                        if (i >= sentences.length) { onEnd && onEnd(); return; }

                        const u = new SpeechSynthesisUtterance(sentences[i++]);
                        u.lang = 'id-ID';
                        u.rate = 0.8;
                        u.pitch = 1;
                        u.onend = () => setTimeout(next, 350);
                        u.onerror = () => {
                            if (currentRunId !== speechRunId) return;
                            if (speechStatus) speechStatus.textContent = 'Suara tidak dapat diputar.';
                            readingPage = false;
                            readerLabel.textContent = 'Dengar Panduan';
                        };
                        window.speechSynthesis.speak(u);
                    };
                    next();
                };

                // Ambil narasi terkurasi: buang elemen yang tidak seharusnya dibaca
                const buildNarrative = (root) => {
                    if (!root) return '';

                    // Clone agar tidak mengubah DOM asli
                    const clone = root.cloneNode(true);
                    // Hapus elemen sr-only, aria-hidden, role=status, script, style, skeleton
                    clone.querySelectorAll('.sr-only, [aria-hidden="true"], [role="status"], script, style, .warga-skeleton')
                        .forEach(el => el.remove());

                    // Kumpulkan teks per bagian penting
                    const parts = [];
                    const pushSection = (label, text) => {
                        const clean = (text || '').replace(/\s+/g, ' ').trim();
                        if (clean) parts.push(`${label}: ${clean}`);
                    };

                    // Hero
                    const hero = clone.querySelector('#warga-intro-section');
                    if (hero) pushSection('Sambutan', hero.innerText);

                    // Quick actions
                    const actions = clone.querySelectorAll('#warga-actions-section .warga-action');
                    if (actions.length) {
                        const items = Array.from(actions).map(a => a.innerText.replace(/\s+/g, ' ').trim()).join('. ');
                        pushSection('Akses cepat', items);
                    }

                    // Navigasi aktif
                    const tools = clone.querySelector('#warga-tools-section');
                    if (tools) pushSection('Navigasi aktif', tools.innerText);

                    // Peta (ambil legend + status saja, bukan seluruh peta)
                    const mapLegend = clone.querySelector('#warga-map-section ul');
                    const mapStatus = clone.querySelector('#warga-map-status');
                    let mapText = '';
                    if (mapLegend) mapText += mapLegend.innerText + '. ';
                    if (mapStatus) mapText += mapStatus.innerText;
                    pushSection('Peta aksesibilitas', mapText);

                    // Nearby
                    const nearby = clone.querySelector('#warga-nearby-section');
                    if (nearby) {
                        // Jangan baca tombol
                        nearby.querySelectorAll('button').forEach(b => b.remove());
                        pushSection('Hambatan terdekat', nearby.innerText);
                    }

                    // Accessibility note
                    const note = clone.querySelector('#warga-accessibility-section');
                    if (note) pushSection('Informasi aksesibilitas', note.innerText);

                    return parts.join('. ');
                };

                const buildSidebarNarrative = (sidebar) => {
                    if (!sidebar) return '';
                    const clone = sidebar.cloneNode(true);
                    clone.querySelectorAll('.sr-only, [aria-hidden="true"], [role="status"], script, style')
                        .forEach(el => el.remove());
                    return clone.innerText.replace(/\s+/g, ' ').trim();
                };

                readerButton.addEventListener('click', (e) => {
                    // Blokir klik yang menyertai drag
                    if (hasMoved) {
                        e.preventDefault();
                        e.stopPropagation();
                        return;
                    }

                    // Toggle stop
                    if (window.speechSynthesis.speaking || readingPage) {
                        speechRunId++;
                        window.speechSynthesis.cancel();
                        readingPage = false;
                        readerLabel.textContent = 'Dengar Panduan';
                        if (speechStatus) speechStatus.textContent = 'Pembacaan dihentikan.';
                        return;
                    }

                    // Tentukan region berdasarkan posisi tombol
                    const btnRect = readerButton.getBoundingClientRect();
                    const sidebar = document.getElementById('warga-sidebar');
                    let content = '';
                    let statusMsg = '';

                    const isOverSidebar = sidebar &&
                        btnRect.left < sidebar.getBoundingClientRect().right &&
                        btnRect.top < sidebar.getBoundingClientRect().bottom;

                    if (isOverSidebar) {
                        content = buildSidebarNarrative(sidebar);
                        statusMsg = 'Menu sidebar sedang dibacakan.';
                    } else {
                        content = buildNarrative(document.getElementById('warga-dashboard'));
                        statusMsg = 'Konten dashboard sedang dibacakan.';
                    }

                    if (!content) {
                        if (speechStatus) speechStatus.textContent = 'Tidak ada konten untuk dibacakan.';
                        return;
                    }

                    readingPage = true;
                    readerLabel.textContent = 'Hentikan Bacaan';
                    if (speechStatus) speechStatus.textContent = statusMsg;

                    speakText(content, () => {
                        readingPage = false;
                        readerLabel.textContent = 'Dengar Panduan';
                        if (speechStatus) speechStatus.textContent = 'Pembacaan selesai.';
                    });
                });
            }

            /* ============================================================
               3. MAP SKELETON — sembunyikan saat Leaflet siap
               ============================================================ */
            const mapSkeleton = document.getElementById('warga-map-skeleton');
            const mapEl = document.getElementById('warga-map');
            if (mapEl && mapSkeleton) {
                const hideSkeleton = () => {
                    mapSkeleton.style.transition = 'opacity 0.3s ease';
                    mapSkeleton.style.opacity = '0';
                    setTimeout(() => { mapSkeleton.remove(); }, 320);
                };
                // Jika Leaflet sudah init
                if (mapEl.classList.contains('leaflet-container')) hideSkeleton();
                else {
                    // Observer: pantau munculnya class leaflet-container
                    const obs = new MutationObserver(() => {
                        if (mapEl.classList.contains('leaflet-container')) {
                            obs.disconnect();
                            hideSkeleton();
                        }
                    });
                    obs.observe(mapEl, { attributes: true, attributeFilter: ['class'] });
                    // Fallback: 8 detik
                    setTimeout(() => {
                        obs.disconnect();
                        if (mapSkeleton.isConnected) hideSkeleton();
                    }, 8000);
                }
            }

            /* ============================================================
               4. MAP REFRESH — dispatch event, beri fallback reload status
               ============================================================ */
            const mapRefreshBtn = document.getElementById('btn-map-refresh');
            if (mapRefreshBtn) {
                mapRefreshBtn.addEventListener('click', () => {
                    const status = document.getElementById('warga-map-status');
                    if (status) status.textContent = 'Menyegarkan peta…';

                    const icon = mapRefreshBtn.querySelector('i');
                    if (icon) {
                        icon.style.transition = 'transform 0.6s ease';
                        icon.style.transform = 'rotate(360deg)';
                        setTimeout(() => { icon.style.transform = ''; }, 650);
                    }

                    // Dispatch custom event agar smartpath-warga.js bisa listen:
                    //   document.addEventListener('smartpath:refresh-map', () => { ... });
                    document.dispatchEvent(new CustomEvent('smartpath:refresh-map'));

                    // Fallback: jika 3 detik tidak ada yang update status, beri tahu
                    const prevStatus = status ? status.textContent : '';
                    setTimeout(() => {
                        if (status && status.textContent === 'Menyegarkan peta…') {
                            status.textContent = prevStatus || 'Peta siap.';
                        }
                    }, 3000);
                });
            }

            /* ============================================================
               6. NEARBY LOCATION BUTTON — disable saat proses (jika JS utama belum handle)
               ============================================================ */
            const locBtn = document.getElementById('btn-warga-location');
            if (locBtn && !locBtn.dataset.wired) {
                // Hanya tambahkan indikator visual; logika fetch tetap di smartpath-warga.js
                locBtn.addEventListener('click', () => {
                    // Tidak set disabled permanen — hanya animasi ringan
                    const label = locBtn.querySelector('.btn-label');
                    if (label && label.textContent === 'Gunakan Lokasi Saya') {
                        label.textContent = 'Mencari lokasi…';
                        setTimeout(() => {
                            // Reset jika JS utama tidak mengubahnya dalam 10 detik
                            if (label.textContent === 'Mencari lokasi…') {
                                label.textContent = 'Gunakan Lokasi Saya';
                            }
                        }, 10000);
                    }
                });
            }
        });
    </script>
@endpush