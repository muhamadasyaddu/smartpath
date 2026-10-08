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
        data-user-name="{{ $user->nama_lengkap ?? 'Warga' }}"
        data-report-count="{{ $jumlahLaporan ?? 0 }}"
    >
        @if(session('sukses'))
            <div
                class="warga-dashboard__flash mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800"
                role="status"
            >
                {{ session('sukses') }}
            </div>
        @endif

        <header id="warga-intro-section" class="warga-dashboard__header mb-8 flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="warga-dashboard__eyebrow text-sm font-semibold uppercase tracking-[0.08em] text-emerald-600">
                    Dashboard Warga
                </p>

                    <h1 class="warga-dashboard__title mt-2 text-3xl font-bold tracking-tight text-slate-900">
                        Halo, {{ $user->nama_lengkap ?? 'Warga' }}
                    </h1>

                <p class="warga-dashboard__intro mt-2 max-w-2xl text-sm leading-6 text-slate-600">
                    Kelola laporan aksesibilitas, lihat kondisi jalur pedestrian,
                    dan temukan hambatan terverifikasi di sekitar lokasi Anda.
                </p>
            </div>

            <button
                type="button"
                id="btn-read-page"
                class="warga-dashboard__voice-button inline-flex w-fit items-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-2.5 text-xs font-bold text-emerald-700 shadow-sm transition hover:border-emerald-300 hover:bg-emerald-100 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2"
                aria-label="Dengar panduan Dashboard Warga"
                aria-pressed="false"
            >
                <i class="fa-solid fa-volume-high" aria-hidden="true"></i>
                <span>Dengar Panduan</span>
            </button>
        </header>

        <section id="warga-actions-section" aria-labelledby="warga-actions-title">
            <h2 id="warga-actions-title" class="sr-only">
                Akses cepat Dashboard Warga
            </h2>

            <div class="warga-quick-actions grid gap-6 md:grid-cols-3">

                {{-- BUAT LAPORAN --}}
                <a
                    href="{{ route('laporan.create') }}"
                    class="warga-action warga-action--primary group rounded-2xl bg-emerald-600 p-6 text-white shadow-sm transition hover:bg-emerald-700 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2"
                >
                    <div class="warga-action__icon flex h-10 w-10 items-center justify-center rounded-xl bg-white/15">
                        <i
                            class="fa-solid fa-file-circle-plus text-lg"
                            aria-hidden="true"
                        ></i>
                    </div>

                    <div class="warga-action__body">
                        <h2>Buat Laporan</h2>
                        <span>
                            Laporkan hambatan aksesibilitas yang Anda temukan
                            di ruang pedestrian.
                        </span>
                    </div>
                    <i class="warga-action__arrow fa-solid fa-arrow-right" aria-hidden="true"></i>
                </a>

                {{-- LAPORAN SAYA --}}
                <a
                    href="{{ route('laporan.index') }}"
                    class="warga-action group rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:border-emerald-400 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2"
                >
                    <div class="warga-action__icon flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                        <i class="fa-solid fa-file-lines" aria-hidden="true"></i>
                    </div>

                    <div class="warga-action__body">
                        <h2>Laporan Saya</h2>
                        <span>Lihat laporan yang pernah Anda kirim dan pantau statusnya.</span>
                    </div>
                    <i class="warga-action__arrow fa-solid fa-arrow-right" aria-hidden="true"></i>
                </a>

                {{-- PETA AKSESIBILITAS --}}
                <a
                    href="{{ route('peta.index') }}"
                    class="warga-action group rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:border-emerald-400 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2"
                >
                    <div class="warga-action__icon flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                        <i
                            class="fa-solid fa-map-location-dot"
                            aria-hidden="true"
                        ></i>
                    </div>

                    <div class="warga-action__body">
                        <h2>Peta Aksesibilitas</h2>
                        <span>
                            Jelajahi titik hambatan terverifikasi dan fasilitas publik
                            yang tersedia.
                        </span>
                    </div>
                    <i class="warga-action__arrow fa-solid fa-arrow-right" aria-hidden="true"></i>
                </a>

            </div>
        </section>

        {{-- ==========================================================
             PETA AKSESIBILITAS
        =========================================================== --}}
        <section
            id="warga-map-section"
            class="warga-map-card mt-8 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
            aria-labelledby="warga-map-title"
        >
            <div class="warga-map-card__header flex flex-col gap-3 border-b border-slate-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">

                <div class="warga-section-heading flex items-center gap-3">

                    <div class="warga-section-heading__icon flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                        <i
                            class="fa-solid fa-map"
                            aria-hidden="true"
                        ></i>
                    </div>

                    <div>
                        <h2
                            id="warga-map-title"
                            class="text-base font-bold text-slate-900"
                        >
                            Peta Aksesibilitas
                        </h2>

                        <p class="mt-0.5 text-xs text-slate-500">
                            Data laporan yang ditampilkan pada peta telah melalui
                            proses verifikasi.
                        </p>
                    </div>

                </div>

                <span class="warga-verified-badge inline-flex w-fit items-center gap-2 rounded-full bg-emerald-50 px-3 py-1.5 text-[10px] font-bold tracking-wide text-emerald-700">
                    <span
                        class="h-1.5 w-1.5 rounded-full bg-emerald-500"
                        aria-hidden="true"
                    ></span>

                    DATA TERVERIFIKASI
                </span>

            </div>

            <div
                id="warga-map"
                class="warga-dashboard__map"
                role="region"
                aria-label="Peta interaktif aksesibilitas SmartPath Kota Depok"
            ></div>

            <p
                id="warga-map-status"
                class="warga-map-status px-5 pt-3 text-xs text-slate-500"
                role="status"
                aria-live="polite"
            >
                Peta sedang disiapkan.
            </p>

            <div class="warga-map-card__footer flex flex-wrap items-center justify-between gap-3 px-5 pb-5 pt-3">

                <div class="flex flex-wrap gap-2">

                    <button
                        type="button"
                        id="btn-warga-location"
                        class="warga-map-control warga-control-button inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-600 transition hover:border-emerald-400 hover:text-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2"
                        aria-busy="false"
                    >
                        <i
                            class="fa-solid fa-location-crosshairs"
                            aria-hidden="true"
                        ></i>

                        <span>Gunakan Lokasi Saya</span>
                    </button>

                    <button
                        type="button"
                        id="btn-warga-facilities"
                        class="warga-map-control warga-control-button inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-600 transition hover:border-emerald-400 hover:text-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2"
                        aria-pressed="false"
                    >
                        <i
                            class="fa-solid fa-building"
                            aria-hidden="true"
                        ></i>

                        <span>Fasilitas Publik</span>
                    </button>

                </div>

                <a
                    href="{{ route('peta.index') }}"
                    class="warga-map-link inline-flex items-center gap-1.5 text-xs font-bold text-emerald-600 transition hover:text-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2"
                >
                    Buka Peta Lengkap

                    <i
                        class="fa-solid fa-arrow-right text-[10px]"
                        aria-hidden="true"
                    ></i>
                </a>

            </div>
        </section>

        {{-- ==========================================================
             NEARBY OBSTACLES LIST
        =========================================================== --}}
        <section
            id="warga-nearby-section"
            class="warga-nearby mt-8"
            aria-labelledby="warga-nearby-title"
        >

            <div class="warga-nearby__header mb-4 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">

                <div>

                    <p class="warga-dashboard__eyebrow text-xs font-semibold uppercase tracking-[0.08em] text-emerald-600">
                        Mode Eksplorasi Pasif
                    </p>

                    <h2
                        id="warga-nearby-title"
                        class="mt-1 text-xl font-bold tracking-tight text-slate-900"
                    >
                        Hambatan Terdekat
                    </h2>

                    <p class="warga-dashboard__intro mt-1 max-w-2xl text-sm leading-6 text-slate-600">
                        Daftar teks ini membantu pengguna pembaca layar mengetahui
                        hambatan aksesibilitas terverifikasi dalam radius 50 meter
                        dari lokasi mereka.
                    </p>

                </div>

                <div class="warga-nearby__tools flex items-center gap-2">

                    <span
                        id="warga-nearby-summary"
                        class="warga-location-status rounded-full bg-slate-100 px-3 py-1.5 text-[11px] font-semibold text-slate-600"
                    >
                        Lokasi belum digunakan
                    </span>

                    <button
                        type="button"
                        id="btn-read-nearby"
                        class="warga-control-button warga-control-button--accent inline-flex items-center gap-1.5 rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs font-bold text-emerald-700 transition hover:bg-emerald-100 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2"
                        aria-pressed="false"
                        disabled
                    >
                        <i
                            class="fa-solid fa-volume-high"
                            aria-hidden="true"
                        ></i>

                        <span>Dengar Hambatan Terdekat</span>
                    </button>

                </div>

            </div>

            <div
                id="warga-nearby-status"
                class="warga-dashboard__sr-status"
                role="status"
                aria-live="polite"
                aria-atomic="true"
            ></div>

            <div
                id="warga-nearby-list"
                class="warga-nearby-list grid gap-3"
                aria-label="Daftar hambatan aksesibilitas terdekat"
            >

                <div
                    class="warga-nearby-empty"
                    role="status"
                >
                    <span
                        class="warga-nearby-empty__icon"
                        aria-hidden="true"
                    >
                        <i class="fa-solid fa-location-crosshairs"></i>
                    </span>

                    <div>
                        <h3>Lokasi belum digunakan</h3>

                        <p>
                            Gunakan tombol “Gunakan Lokasi Saya” pada peta
                            untuk mencari hambatan terverifikasi dalam radius
                            50 meter.
                        </p>
                    </div>
                </div>

            </div>

        </section>

        {{-- ==========================================================
             FITUR TAMBAHAN
        =========================================================== --}}
        <section
            id="warga-tools-section"
            class="warga-secondary-links mt-8 grid gap-6 md:grid-cols-2"
            aria-labelledby="warga-tools-title"
        >

            <h2
                id="warga-tools-title"
                class="sr-only"
            >
                Fitur tambahan
            </h2>

            {{-- NEARBY --}}
            <a
                href="{{ route('peta.nearby') }}"
                class="warga-secondary-link group rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:border-emerald-400 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2"
            >
                <div class="warga-secondary-link__icon flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                    <i
                        class="fa-solid fa-location-dot"
                        aria-hidden="true"
                    ></i>
                </div>

                <div class="warga-secondary-link__body">
                    <h2>Nearby</h2>
                    <p>
                        Buka halaman Nearby untuk membaca daftar hambatan
                        terverifikasi berdasarkan lokasi Anda.
                    </p>
                    <span class="warga-secondary-link__action">
                        Lihat Nearby
                        <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                    </span>
                </div>
                <i class="warga-secondary-link__arrow fa-solid fa-arrow-right" aria-hidden="true"></i>
            </a>

            {{-- NAVIGASI --}}
            <a
                href="{{ route('navigasi.index') }}"
                class="warga-secondary-link group rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:border-emerald-400 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2"
            >
                <div class="warga-secondary-link__icon warga-secondary-link__icon--teal flex h-10 w-10 items-center justify-center rounded-xl bg-teal-50 text-teal-700">
                    <i
                        class="fa-solid fa-route"
                        aria-hidden="true"
                    ></i>
                </div>

                <div class="warga-secondary-link__body">
                    <h2>Navigasi Aktif</h2>
                    <p>
                        Tentukan tujuan perjalanan dan gunakan panduan suara
                        serta peringatan hambatan pada halaman navigasi.
                    </p>
                    <span class="warga-secondary-link__action">
                        Mulai Navigasi
                        <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                    </span>
                </div>
                <i class="warga-secondary-link__arrow fa-solid fa-arrow-right" aria-hidden="true"></i>
            </a>

        </section>

        {{-- CATATAN ACCESSIBILITY --}}
        <aside
            id="warga-accessibility-section"
            class="warga-accessibility-note mt-6 rounded-2xl border border-emerald-100 bg-emerald-50 px-5 py-4"
            aria-label="Informasi aksesibilitas"
        >
            <div class="flex gap-3">

                <i
                    class="fa-solid fa-universal-access mt-0.5 text-emerald-700"
                    aria-hidden="true"
                ></i>

                <p class="text-xs leading-6 text-emerald-900">
                    Informasi hambatan pada daftar Nearby disajikan dalam bentuk
                    teks dan dilengkapi status prioritas agar dapat dipahami
                    tanpa bergantung pada warna atau peta visual.
                </p>

            </div>
        </aside>

    </main>
        </div>
    </div>
@endsection

@push('scripts')
    <script
        src="{{ asset('js/smartpath-warga.js') }}"
        defer
    ></script>
@endpush