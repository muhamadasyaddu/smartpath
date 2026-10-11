@extends('layouts.app')

@section('title', 'Dashboard Warga - SmartPath')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/smartpath-warga.css') }}">
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
                    {{-- Ilustrasi berada DI DALAM card hero, di sisi kanan. File: public/smartpath-banner.png --}}
                    <img
                        src="{{ asset('smartpath-banner.png') }}"
                        alt=""
                        aria-hidden="true"
                        class="pointer-events-none absolute right-[8%] top-0 hidden h-full w-[48%] object-contain object-center sm:block"
                        loading="eager"
                    >

                    {{-- Lapisan lembut agar tulisan tetap mudah dibaca --}}
                    <div class="pointer-events-none absolute inset-0 bg-gradient-to-r from-emerald-50 via-emerald-50/80 to-transparent" aria-hidden="true"></div>

                    <div class="relative z-10 flex min-h-[150px] flex-col justify-center gap-5 p-6 sm:w-[67%] sm:p-8 lg:w-[65%]">
                        <div class="max-w-2xl">
                            <span class="mb-1 inline-block text-xs font-bold uppercase tracking-wider text-emerald-700">
                                Selamat datang kembali,
                            </span>
                            <h1 class="flex items-center gap-2 text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">
                                Halo, {{ $user->nama_lengkap ?? 'Dewi Lestari' }} 👋
                            </h1>
                            <p class="mt-2 max-w-xl text-sm leading-relaxed text-slate-600">
                                Kelola laporan aksesibilitas, lihat kondisi jalur pedestrian, dan temukan hambatan terverifikasi di sekitar lokasi Anda.
                            </p>
                        </div>
                    </div>

                    <div class="absolute right-4 top-4 z-20 sm:right-5 sm:top-5">
                        <button
                            type="button"
                            id="btn-read-page"
                            class="warga-dashboard__voice-button inline-flex items-center gap-2.5 rounded-2xl bg-emerald-800 px-4 py-3 text-xs font-bold text-white shadow-sm transition hover:bg-emerald-900 focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:ring-offset-2 sm:px-5"
                            aria-label="Dengar panduan Dashboard Warga"
                            aria-pressed="false"
                        >
                            <i class="fa-solid fa-volume-high" aria-hidden="true"></i>
                            <span>Dengar Panduan</span>
                        </button>
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
                        <span class="inline-flex items-center gap-2 rounded-full bg-emerald-50 px-3 py-1.5 text-[10px] font-bold tracking-wide text-emerald-700 border border-emerald-100">
                            <i class="fa-solid fa-circle-check text-emerald-600"></i>
                            DATA TERVERIFIKASI
                        </span>
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

                        {{-- ICON --}}
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100">
                            <i class="fa-solid fa-universal-access text-2xl" aria-hidden="true"></i>
                        </div>

                        {{-- INFORMATION --}}
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
@endsection

@push('scripts')
    <script src="{{ asset('js/smartpath-warga.js') }}" defer></script>
@endpush