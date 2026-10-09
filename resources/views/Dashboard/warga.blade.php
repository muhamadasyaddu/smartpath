@extends('layouts.app')

@section('title', 'Dashboard Warga - SmartPath')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/smartpath-warga.css') }}">
@endpush

@section('content')
    <div class="warga-shell">
        @include('partials.sidebar-warga')

        <div class="warga-workspace">
            @include('partials.nav-public')
            @include('partials.bar-mobile-warga')

    <main
        id="warga-dashboard"
        class="warga-dashboard mx-auto w-full max-w-7xl px-4 py-10 sm:px-6 lg:px-8"
        data-laporan-url="{{ route('peta.data') }}"
        data-fasilitas-url="{{ route('peta.fasilitas') }}"
        data-detail-template="{{ route('laporan.show', ['laporan' => '__REPORT_ID__']) }}"
        data-pilot-bounds='@json(config("smartpath.pilot.bounds"))'
        data-pilot-center='@json(config("smartpath.pilot.center"))'
        data-gps-timeout="{{ config('smartpath.location.watch_timeout_ms', 15000) }}"
        data-warning-accuracy="{{ config('smartpath.location.warning_accuracy_meters', 100) }}"
        data-manual-accuracy="{{ config('smartpath.location.manual_recommended_accuracy_meters', 500) }}"
        data-user-name="{{ $user->nama_lengkap ?? 'ratna agustina' }}"
        data-report-count="{{ $jumlahLaporan ?? 0 }}"
    >
        @if(session('sukses'))
            <div class="warga-dashboard__flash mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800" role="status">
                {{ session('sukses') }}
            </div>
        @endif

        {{-- TARGET 1: INTRO SECTION --}}
        <header id="warga-intro-section" class="warga-dashboard__header mb-8 flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between p-4 rounded-2xl transition-all duration-300">
            <div>
                <p class="warga-dashboard__eyebrow text-xs font-bold uppercase tracking-wider text-emerald-700">
                    DASHBOARD WARGA
                </p>
                <h1 class="warga-dashboard__title mt-1 text-3xl font-bold tracking-tight text-slate-900">
                    Halo, {{ $user->nama_lengkap ?? 'ratna agustina' }}
                </h1>
                <p class="warga-dashboard__intro mt-2 max-w-2xl text-sm leading-6 text-slate-600">
                    Kelola laporan aksesibilitas, lihat kondisi jalur pedestrian, dan temukan hambatan terverifikasi di sekitar lokasi Anda.
                </p>
            </div>

            <button
                type="button"
                id="btn-read-page"
                class="warga-dashboard__voice-button inline-flex w-fit items-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-2.5 text-xs font-bold text-emerald-700 shadow-xs transition hover:border-emerald-300 hover:bg-emerald-100 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2"
                aria-label="Dengar panduan Dashboard Warga"
                aria-pressed="false"
            >
                <i class="fa-solid fa-volume-high" aria-hidden="true"></i>
                <span>Dengar Panduan</span>
            </button>
        </header>

        {{-- TARGET 2: ACTIONS SECTION --}}
        <section id="warga-actions-section" class="p-2 rounded-2xl transition-all duration-300" aria-labelledby="warga-actions-title">
            <h2 id="warga-actions-title" class="sr-only">Akses cepat Dashboard Warga</h2>

            <div class="warga-quick-actions grid gap-6 md:grid-cols-3">
                <a href="{{ route('laporan.create') }}" class="warga-action warga-action--primary group rounded-2xl bg-emerald-600 p-6 text-white shadow-xs transition hover:bg-emerald-700 hover:shadow-md">
                    <div class="warga-action__icon flex h-10 w-10 items-center justify-center rounded-xl bg-white/15">
                        <i class="fa-solid fa-file-circle-plus text-lg" aria-hidden="true"></i>
                    </div>
                    <div class="warga-action__body mt-4">
                        <h2 class="font-bold text-base">Buat Laporan</h2>
                        <span class="text-xs text-emerald-100">Laporkan hambatan aksesibilitas yang Anda temukan di ruang pedestrian.</span>
                    </div>
                </a>

                <a href="{{ route('laporan.index') }}" class="warga-action group rounded-2xl border border-slate-200 bg-white p-6 shadow-xs transition hover:border-emerald-400 hover:shadow-md">
                    <div class="warga-action__icon flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                        <i class="fa-solid fa-file-lines" aria-hidden="true"></i>
                    </div>
                    <div class="warga-action__body mt-4">
                        <h2 class="font-bold text-base text-slate-800">Laporan Saya</h2>
                        <span class="text-xs text-slate-500">Lihat laporan yang pernah Anda kirim dan pantau statusnya.</span>
                    </div>
                </a>

                <a href="{{ route('peta.index') }}" class="warga-action group rounded-2xl border border-slate-200 bg-white p-6 shadow-xs transition hover:border-emerald-400 hover:shadow-md">
                    <div class="warga-action__icon flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                        <i class="fa-solid fa-map-location-dot" aria-hidden="true"></i>
                    </div>
                    <div class="warga-action__body mt-4">
                        <h2 class="font-bold text-base text-slate-800">Peta Aksesibilitas</h2>
                        <span class="text-xs text-slate-500">Jelajahi titik hambatan terverifikasi dan fasilitas publik yang tersedia.</span>
                    </div>
                </a>
            </div>
        </section>

        {{-- TARGET 3: MAP SECTION --}}
        <section id="warga-map-section" class="warga-map-card mt-8 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xs transition-all duration-300">
            <div class="warga-map-card__header flex flex-col gap-3 border-b border-slate-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="warga-section-heading flex items-center gap-3">
                    <div class="warga-section-heading__icon flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                        <i class="fa-solid fa-map" aria-hidden="true"></i>
                    </div>
                    <div>
                        <h2 id="warga-map-title" class="text-base font-bold text-slate-900">Peta Aksesibilitas</h2>
                        <p class="mt-0.5 text-xs text-slate-500">Data laporan yang ditampilkan pada peta telah melalui proses verifikasi.</p>
                    </div>
                </div>
                <span class="warga-verified-badge inline-flex w-fit items-center gap-2 rounded-full bg-emerald-50 px-3 py-1.5 text-[10px] font-bold tracking-wide text-emerald-700">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500" aria-hidden="true"></span>
                    DATA TERVERIFIKASI
                </span>
            </div>

            <div id="warga-map" class="warga-dashboard__map h-96" role="region" aria-label="Peta interaktif aksesibilitas"></div>
        </section>

        {{-- TARGET 4: NEARBY SECTION --}}
        <section id="warga-nearby-section" class="warga-nearby mt-8 p-4 rounded-2xl transition-all duration-300">
            <div class="warga-nearby__header mb-4 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="warga-dashboard__eyebrow text-xs font-semibold uppercase tracking-wider text-emerald-600">Mode Eksplorasi Pasif</p>
                    <h2 id="warga-nearby-title" class="mt-1 text-xl font-bold tracking-tight text-slate-900">Hambatan Terdekat</h2>
                </div>
            </div>
            <div id="warga-nearby-list" class="warga-nearby-list grid gap-3"></div>
        </section>

        {{-- TARGET 5: TOOLS SECTION --}}
        <section id="warga-tools-section" class="warga-secondary-links mt-8 grid gap-6 md:grid-cols-2 transition-all duration-300">
            <a href="{{ route('peta.nearby') }}" class="warga-secondary-link group rounded-2xl border border-slate-200 bg-white p-6 shadow-xs">
                <div class="warga-secondary-link__body">
                    <h2 class="font-bold">Mode Tunanetra (Nearby)</h2>
                    <p class="text-xs text-slate-500 mt-1">Buka Mode Tunanetra untuk mendengarkan hambatan terdekat lewat suara.</p>
                </div>
            </a>
            <a href="{{ route('navigasi.index') }}" class="warga-secondary-link group rounded-2xl border border-slate-200 bg-white p-6 shadow-xs">
                <div class="warga-secondary-link__body">
                    <h2 class="font-bold">Navigasi Aktif</h2>
                    <p class="text-xs text-slate-500 mt-1">Tentukan tujuan perjalanan dan panduan suara aktif.</p>
                </div>
            </a>
        </section>

        {{-- TARGET 6: ACCESSIBILITY SECTION --}}
        <aside id="warga-accessibility-section" class="warga-accessibility-note mt-6 rounded-2xl border border-emerald-100 bg-emerald-50 px-5 py-4 transition-all duration-300">
            <div class="flex gap-3">
                <i class="fa-solid fa-universal-access mt-0.5 text-emerald-700" aria-hidden="true"></i>
                <p class="text-xs leading-6 text-emerald-900">
                    Informasi hambatan disajikan dalam bentuk teks dan suara agar mudah dipahami oleh pembaca layar.
                </p>
            </div>
        </aside>

    </main>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/smartpath-warga.js') }}" defer></script>
@endpush