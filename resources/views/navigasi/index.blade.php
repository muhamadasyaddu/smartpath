@extends('layouts.app')

@section('title', 'Navigasi SmartPath')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/smartpath-warga.css') }}">
@endpush

@section('content')

<div class="warga-shell">
    @include('partials.sidebar-warga')

    <div class="warga-workspace">
        @include('partials.nav-public')
        @include('partials.bar-mobile-warga')

<style>
    .smartpath-navigation {
        min-height: calc(100vh - 64px);
        background: #f8fafc;
    }

    .navigation-shell {
        max-width: 1440px;
        margin: 0 auto;
        padding: 24px 24px 40px;
    }

    .navigation-header {
        margin-bottom: 16px;
    }

    .navigation-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: #047857;
        font-size: 13px;
        font-weight: 700;
    }

    .navigation-title {
        margin-top: 7px;
        color: #0f172a;
        font-size: clamp(25px, 3vw, 34px);
        line-height: 1.18;
        font-weight: 800;
        letter-spacing: -.025em;
    }

    .navigation-description {
        max-width: 760px;
        margin-top: 8px;
        color: #475569;
        font-size: 15px;
        line-height: 1.7;
    }

    .navigation-map-card {
        position: relative;
        display: grid;
        grid-template-columns: minmax(340px, 360px) minmax(0, 1fr);
        grid-template-rows: clamp(540px, 76vh, 760px);
        overflow: hidden;
        min-height: 0;
        height: auto;
        margin-top: 16px;
        scroll-margin-top: 80px;
        border: 1px solid #dbe4e9;
        border-radius: 12px;
        background: #e2e8f0;
        box-shadow: 0 2px 8px rgb(15 23 42 / 5%);
    }

    .navigation-map {
        position: relative;
        grid-column: 2;
        grid-row: 1;
        width: 100%;
        height: 100%;
        min-height: 0;
    }

    .navigation-map .leaflet-container {
        font-family: Inter, Arial, sans-serif;
    }

    .navigation-map-topbar {
        position: absolute;
        z-index: 800;
        top: 14px;
        left: auto;
        right: 14px;
        display: flex;
        justify-content: space-between;
        gap: 8px;
        pointer-events: none;
    }

    .navigation-map-title,
    .navigation-gps-status {
        pointer-events: auto;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 9px 12px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        background: #fff;
        color: #0f172a;
        font-size: 13px;
        font-weight: 700;
        box-shadow: 0 1px 4px rgb(15 23 42 / 8%);
    }

    .navigation-gps-status {
        color: #475569;
        font-size: 12px;
    }

    .navigation-gps-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #94a3b8;
    }

    .navigation-gps-dot.is-active {
        background: #059669;
        box-shadow: 0 0 0 4px rgb(5 150 105 / 12%);
    }

    .navigation-overlay {
        position: relative;
        z-index: 900;
        grid-column: 1;
        grid-row: 1;
        width: auto;
        max-height: none;
        min-height: 0;
        overflow-y: auto;
        border: 0;
        border-right: 1px solid #e2e8f0;
        border-radius: 0;
        background: #fff;
        box-shadow: none;
    }

    .navigation-map-card.is-navigation-active .navigation-overlay {
        width: auto;
        max-height: none;
    }

    .navigation-map-card.is-navigation-active .navigation-overlay__header {
        display: none;
    }

    .navigation-map-card.is-navigation-active .navigation-overlay__body {
        padding: 16px 20px;
    }

    .navigation-map-card.is-navigation-active
    .navigation-overlay__body > :not(#kontrol-navigasi):not(#status-perjalanan) {
        display: none;
    }

    .navigation-map-card.is-navigation-active #kontrol-navigasi,
    .navigation-map-card.is-navigation-active #status-perjalanan {
        margin-top: 0;
    }

    .navigation-overlay__header {
        padding: 20px 20px 16px;
        border-bottom: 1px solid #e2e8f0;
    }

    .navigation-overlay__heading {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .navigation-overlay__icon {
        display: flex;
        width: 38px;
        height: 38px;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        background: #ecfdf5;
        color: #047857;
    }

    .navigation-overlay h2 {
        margin: 0;
        color: #0f172a;
        font-size: 16px;
        line-height: 1.4;
        font-weight: 700;
    }

    .navigation-overlay__subheading {
        margin-top: 3px;
        color: #64748b;
        font-size: 13px;
        line-height: 1.6;
    }

    .navigation-overlay__body {
        padding: 20px;
    }

    .navigation-label {
        display: block;
        margin-bottom: 8px;
        color: #334155;
        font-size: 14px;
        font-weight: 650;
    }

    .navigation-input {
        width: 100%;
        min-height: 48px;
        padding: 11px 13px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        outline: none;
        background: #fff;
        color: #0f172a;
        font-size: 15px;
        line-height: 1.5;
    }

    .navigation-input:focus {
        border-color: #059669;
        box-shadow: 0 0 0 3px rgb(5 150 105 / 12%);
    }

    .navigation-helper {
        margin-top: 7px;
        color: #64748b;
        font-size: 12px;
        line-height: 1.55;
    }

    .navigation-actions {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 8px;
        margin-top: 12px;
    }

    .navigation-primary,
    .navigation-secondary {
        min-height: 46px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 650;
    }

    .navigation-primary {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        border: 1px solid #047857;
        background: #047857;
        color: #fff;
        cursor: pointer;
    }

    .navigation-primary:hover {
        background: #065f46;
        border-color: #065f46;
    }

    .navigation-primary:disabled {
        cursor: wait;
        opacity: .65;
    }

    .navigation-secondary {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 0 14px;
        border: 1px solid #cbd5e1;
        background: #fff;
        color: #334155;
        cursor: pointer;
    }

    .navigation-secondary:hover {
        background: #f8fafc;
    }

    .navigation-status,
    .navigation-result,
    .navigation-active {
        margin-top: 16px;
    }

    .alert {
        padding: 11px 12px;
        border: 1px solid transparent;
        border-radius: 10px;
        font-size: 13px;
        line-height: 1.55;
    }

    .alert-info {
        border-color: #bae6fd;
        background: #f0f9ff;
        color: #075985;
    }

    .alert-warning {
        border-color: #fde68a;
        background: #fffbeb;
        color: #92400e;
    }

    .alert-danger {
        border-color: #fecaca;
        background: #fef2f2;
        color: #991b1b;
    }

    .alert-success {
        border-color: #a7f3d0;
        background: #ecfdf5;
        color: #065f46;
    }

    .alert-secondary {
        border-color: #cbd5e1;
        background: #f8fafc;
        color: #475569;
    }

    .card {
        border: 1px solid #e2e8f0;
        border-radius: 11px;
        background: #fff;
    }

    .card-body {
        padding: 14px;
    }

    .card h2,
    .card h3 {
        color: #0f172a;
        font-size: 14px;
        line-height: 1.45;
        font-weight: 750;
    }

    .card p,
    .card .text-muted {
        color: #64748b;
        font-size: 13px;
        line-height: 1.55;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        min-height: 42px;
        padding: 9px 12px;
        border-radius: 9px;
        font-size: 13px;
        font-weight: 750;
        cursor: pointer;
    }

    .btn-success {
        border: 1px solid #047857;
        background: #047857;
        color: #fff;
    }

    .btn-success:hover {
        background: #065f46;
    }

    .btn-danger {
        border: 1px solid #fecaca;
        background: #fff;
        color: #b91c1c;
    }

    .btn-danger:hover {
        background: #fef2f2;
    }

    .btn-outline-primary {
        width: 100%;
        border: 1px solid #d1d5db;
        background: #fff;
        color: #0f172a;
        text-align: left;
    }

    .btn-outline-primary:hover {
        border-color: #34d399;
        background: #ecfdf5;
    }

    .hambatan-aman,
    .hambatan-ditemukan,
    .hambatan-pesan,
    .info-perjalanan {
        padding: 12px;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        font-size: 13px;
        line-height: 1.55;
    }

    .hambatan-aman {
        border-color: #bbf7d0;
        background: #f0fdf4;
        color: #166534;
    }

    .hambatan-ditemukan {
        border-color: #fde68a;
        background: #fffbeb;
        color: #92400e;
    }

    .hambatan-pesan {
        border-color: #fecaca;
        background: #fef2f2;
        color: #991b1b;
    }

    .info-perjalanan {
        border-color: #99f6e4;
        background: #f0fdfa;
        color: #115e59;
    }

    .hambatan-card {
        margin-top: 8px;
        padding: 11px;
        border: 1px solid #e2e8f0;
        border-radius: 9px;
        background: #fff;
    }

    .hambatan-card h4 {
        margin: 0 0 5px;
        color: #0f172a;
        font-size: 13px;
        font-weight: 750;
    }

    .hambatan-card p {
        margin: 4px 0;
        color: #475569;
        font-size: 12px;
        line-height: 1.5;
    }

    .status-gps {
        margin-top: 7px;
        color: #64748b;
        font-size: 12px;
    }

    .navigation-accessibility-note {
        margin-top: 18px;
        padding: 14px 16px;
        border: 1px solid #d1fae5;
        border-radius: 12px;
        background: #f0fdf4;
    }

    .navigation-accessibility-note__title {
        color: #065f46;
        font-size: 14px;
        font-weight: 750;
    }

    .navigation-accessibility-note p {
        margin-top: 4px;
        color: #166534;
        font-size: 13px;
        line-height: 1.6;
    }

    .leaflet-control-zoom a {
        width: 34px !important;
        height: 34px !important;
        line-height: 32px !important;
        font-size: 18px !important;
    }

    .smartpath-navigation .nav-icon {
        display: inline-block;
        flex: 0 0 auto;
        width: 1.15em;
        text-align: center;
        line-height: 1;
        vertical-align: -.08em;
    }

    .smartpath-navigation .nav-icon--primary {
        color: #047857;
    }

    .smartpath-navigation .nav-icon--muted {
        color: #64748b;
    }

    .smartpath-navigation .nav-icon--warning {
        color: #b45309;
    }

    .smartpath-navigation .nav-icon--danger {
        color: #b91c1c;
    }

    .smartpath-navigation button:focus-visible,
    .navigation-input:focus-visible,
    .navigation-primary:focus-visible,
    .navigation-secondary:focus-visible {
        outline: 3px solid rgb(13 148 136 / 35%);
        outline-offset: 3px;
    }

    /* SCREEN READER MELAYANG */
    #smartpath-screen-reader {
        position: fixed;
        right: 24px;
        bottom: 24px;
        z-index: 9999;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 9px;
        min-height: 48px;
        padding: 12px 17px;
        border: 1px solid #047857;
        border-radius: 999px;
        background: #047857;
        color: #fff;
        font-family: inherit;
        font-size: 14px;
        font-weight: 700;
        box-shadow: 0 4px 14px rgb(15 23 42 / 18%);
        cursor: grab;
        touch-action: none;
        user-select: none;
        -webkit-user-select: none;
    }

    #smartpath-screen-reader:hover {
        background: #065f46;
    }

    #smartpath-screen-reader:active {
        cursor: grabbing;
    }

    #smartpath-screen-reader:focus-visible {
        outline: 3px solid #10b981;
        outline-offset: 4px;
    }

    #smartpath-screen-reader i {
        font-size: 19px;
    }

    #smartpath-screen-reader:disabled {
        cursor: not-allowed;
        opacity: .7;
    }

    @media (max-width: 900px) {
        .navigation-shell {
            padding: 16px 12px 28px;
        }

        .navigation-map-card {
            display: block;
            position: relative;
            min-height: 620px;
            height: 76vh;
            overflow: hidden;
        }

        .navigation-map {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
        }

        .navigation-overlay {
            position: absolute;
            left: 18px;
            bottom: 18px;
            width: min(430px, calc(100% - 36px));
            max-height: calc(100% - 118px);
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            background: rgb(255 255 255 / 97%);
            box-shadow: 0 16px 45px rgb(15 23 42 / 16%);
            overflow-y: auto;
        }

        .navigation-map-card.is-navigation-active .navigation-overlay {
            width: min(340px, calc(100% - 36px));
            max-height: min(250px, calc(100% - 118px));
        }

        .navigation-map-topbar {
            top: 10px;
            left: 10px;
            right: 10px;
        }
    }

    @media (max-width: 640px) {
        .navigation-shell {
            padding: 18px 12px 28px;
        }

        .navigation-map-card {
            min-height: 640px;
            height: 78vh;
            border-radius: 12px;
        }

        .navigation-overlay {
            left: 10px;
            bottom: 10px;
            width: calc(100% - 20px);
            max-height: 54%;
            border-radius: 13px;
        }

        .navigation-actions {
            grid-template-columns: minmax(0, 1fr);
        }

        .navigation-secondary {
            width: 100%;
        }

        .navigation-title {
            font-size: 25px;
        }

        .navigation-description {
            font-size: 14px;
        }

        .navigation-overlay__body {
            padding: 16px;
        }

        #smartpath-screen-reader {
            right: 14px;
            bottom: 14px;
            padding: 12px 14px;
            font-size: 13px;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .smartpath-navigation *,
        .smartpath-navigation *::before,
        .smartpath-navigation *::after {
            scroll-behavior: auto !important;
            animation-duration: .01ms !important;
            animation-iteration-count: 1 !important;
            transition-duration: .01ms !important;
        }
    }
</style>

<main class="smartpath-navigation">
    <div class="navigation-shell">

        <header class="navigation-header">
            <div>
                <div class="navigation-eyebrow">
                    <i class="fa-solid fa-route" aria-hidden="true"></i>
                    Navigasi Aksesibilitas
                </div>

                <h1 class="navigation-title">
                    Temukan rute yang lebih mudah diakses
                </h1>

                <p class="navigation-description">
                    Tentukan tujuan perjalanan untuk mendapatkan rute pejalan kaki
                    dan peringatan hambatan aksesibilitas yang telah terverifikasi.
                </p>
            </div>
        </header>

        <section class="navigation-accessibility-note">
            <div class="flex gap-3">
                <i
                    class="fa-solid fa-universal-access mt-0.5 text-emerald-700"
                    aria-hidden="true"
                ></i>

                <div>
                    <div class="navigation-accessibility-note__title">
                        Informasi aksesibilitas
                    </div>

                    <p>
                        Informasi hambatan tersedia dalam bentuk teks dan dapat
                        dibacakan melalui fitur suara. Gunakan tombol Screen Reader di bagian bawah halaman
                        untuk mendengarkan informasi halaman.
                    </p>
                </div>
            </div>
        </section>

        <section
            class="navigation-map-card"
            aria-labelledby="navigation-map-title"
        >
            <div
                id="peta-navigasi"
                class="navigation-map"
                role="application"
                aria-label="Peta navigasi SmartPath Kota Depok"
            ></div>

            <div class="navigation-map-topbar">
                <div id="navigation-map-title" class="navigation-map-title">
                    <i
                        class="fa-solid fa-map-location-dot text-emerald-700"
                        aria-hidden="true"
                    ></i>
                    Peta Perjalanan
                </div>

                <div class="navigation-gps-status">
                    <span
                        id="navigation-gps-dot"
                        class="navigation-gps-dot"
                        aria-hidden="true"
                    ></span>

                    <span id="navigation-gps-label">GPS perangkat</span>
                </div>
            </div>

            <aside class="navigation-overlay" aria-label="Kontrol navigasi">
                <div class="navigation-overlay__header">
                    <div class="navigation-overlay__heading">
                        <span class="navigation-overlay__icon" aria-hidden="true">
                            <i class="fa-solid fa-location-arrow"></i>
                        </span>

                        <div>
                            <h2>Tentukan Lokasi Tujuan</h2>
                            <p class="navigation-overlay__subheading">
                                Cari tempat atau alamat di sekitar Kota Depok.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="navigation-overlay__body">
                    <label for="tujuan" class="navigation-label">
                        Tujuan perjalanan
                    </label>

                    <input
                        type="text"
                        id="tujuan"
                        class="navigation-input"
                        placeholder="Contoh: Stasiun Depok"
                        autocomplete="street-address"
                        aria-describedby="tujuan-help"
                    >

                    <p id="tujuan-help" class="navigation-helper">
                        Masukkan nama fasilitas, tempat umum, atau alamat tujuan.
                    </p>

                    <div class="navigation-actions">
                        <button
                            type="button"
                            id="btn-mulai-navigasi"
                            class="navigation-primary"
                        >
                            <i class="fa-solid fa-route" aria-hidden="true"></i>
                            <span>Cari Rute</span>
                        </button>

                        <button
                            type="button"
                            id="btn-map-center"
                            class="navigation-secondary"
                            title="Tampilkan posisi saya pada peta"
                            aria-label="Tampilkan posisi saya pada peta"
                        >
                            <i
                                class="fa-solid fa-location-crosshairs"
                                aria-hidden="true"
                            ></i>
                            <span>Lokasi saya</span>
                        </button>
                    </div>

                    <div
                        id="status-navigasi"
                        class="navigation-status"
                        role="status"
                        aria-live="polite"
                    ></div>

                    <div id="kontrol-navigasi" class="navigation-result"></div>

                    <div
                        id="status-perjalanan"
                        class="navigation-active"
                        role="status"
                        aria-live="polite"
                    ></div>
                </div>
            </aside>
        </section>

        <section
            id="hasil-hambatan"
            class="mt-5"
            aria-live="polite"
            aria-label="Informasi hambatan aksesibilitas sepanjang rute"
        ></section>
    </div>
</main>

{{-- SCREEN READER MELAYANG --}}
<button
    type="button"
    id="smartpath-screen-reader"
    aria-label="Mulai membacakan isi halaman"
    aria-pressed="false"
>
    <i class="fa-solid fa-blind" aria-hidden="true"></i>
    <span id="smartpath-screen-reader-label">Dengar Panduan</span>
</button>

<span
    id="smartpath-screen-reader-status"
    class="sr-only"
    role="status"
    aria-live="polite"
></span>

    </div>
</div>

@endsection

@push('scripts')

<script src="{{ asset('js/smartpath-warga.js') }}" defer></script>
<script src="{{ asset('js/smartpath-read-page.js') }}" defer></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const btnNavigasi = document.getElementById('btn-mulai-navigasi');
    const tujuanInput = document.getElementById('tujuan');
    const statusNavigasi = document.getElementById('status-navigasi');
    const kontrolNavigasi = document.getElementById('kontrol-navigasi');
    const statusPerjalanan = document.getElementById('status-perjalanan');
    const hasilHambatan = document.getElementById('hasil-hambatan');
    const navigationMapCard = document.querySelector('.navigation-map-card');
    const btnMapCenter = document.getElementById('btn-map-center');

    let watchId = null;
    let navigasiAktif = false;
    let posisiSekarang = null;
    let posisiAwal = null;
    let tujuanNavigasi = null;
    let geometryNavigasi = null;
    let hambatanNavigasi = [];
    let hambatanSudahDiumumkan = new Set();
    let langkahNavigasi = [];
    let langkahBerikutnyaIndex = 0;
    let langkahSudahDiumumkan = new Set();
    let petaNavigasi = null;
    let markerPengguna = null;
    let markerTujuan = null;
    let garisRute = null;

    const BATAS_PERINGATAN_HAMBATAN = 50;
    const BATAS_TUJUAN = 20;
    const BATAS_PRA_ARAHAN = 80;
    const BATAS_ARAHAN = 50;

    if (typeof L !== 'undefined') {
        petaNavigasi = L.map('peta-navigasi', { zoomControl: true })
            .setView([-6.4025, 106.8166], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(petaNavigasi);
    }

    btnMapCenter.addEventListener('click', function () {
        if (!petaNavigasi) {
            tampilkanPesanPerjalanan(
                'Peta tidak dapat dimuat. Silakan muat ulang halaman.',
                'error'
            );
            return;
        }

        if (!navigator.geolocation) {
            tampilkanPesanPerjalanan(
                'Browser atau perangkat ini tidak mendukung GPS.',
                'error'
            );
            return;
        }

        btnMapCenter.disabled = true;
        statusNavigasi.innerHTML = `
            <div class="alert alert-info">
                Sedang mencari posisi kamu...
            </div>
        `;

        navigator.geolocation.getCurrentPosition(
            function (position) {
                const latitude = position.coords.latitude;
                const longitude = position.coords.longitude;
                const accuracy = position.coords.accuracy;

                perbaruiMarkerPengguna(latitude, longitude, accuracy);

                const akurasiTeks = Number.isFinite(accuracy)
                    ? accuracy <= 50
                        ? 'Lokasi cukup akurat'
                        : accuracy <= 200
                            ? 'Akurasi lokasi terbatas'
                            : 'Lokasi belum cukup akurat'
                    : 'Akurasi lokasi belum tersedia';

                statusNavigasi.innerHTML = `
                    <div class="alert ${
                        accuracy <= 50 ? 'alert-success' : 'alert-warning'
                    }" role="status">
                        <strong>${akurasiTeks}</strong>
                        <p class="mt-1">
                            ${
                                accuracy > 200
                                    ? 'Coba pindah ke area yang lebih terbuka dan perbarui lokasi.'
                                    : 'Posisi perangkat ditampilkan pada peta.'
                            }
                        </p>
                    </div>
                `;

                btnMapCenter.disabled = false;
            },
            function (error) {
                console.error('GPS Error:', error);
                let pesan = 'Terjadi kesalahan saat mendapatkan lokasi.';

                switch (error.code) {
                    case error.PERMISSION_DENIED:
                        pesan = 'Izin lokasi ditolak. Izinkan akses lokasi pada browser untuk menampilkan posisi.';
                        break;
                    case error.POSITION_UNAVAILABLE:
                        pesan = 'Lokasi tidak tersedia. Pastikan layanan lokasi perangkat aktif.';
                        break;
                    case error.TIMEOUT:
                        pesan = 'Waktu untuk mendapatkan lokasi habis. Silakan coba lagi.';
                        break;
                }

                tampilkanPesanPerjalanan(pesan, 'error');
                btnMapCenter.disabled = false;
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            }
        );
    });

    btnNavigasi.addEventListener('click', function () {
        const tujuan = tujuanInput.value.trim();

        if (tujuan === '') {
            statusNavigasi.innerHTML = `
                <div class="alert alert-warning">
                    Silakan masukkan tujuan terlebih dahulu.
                </div>
            `;
            tujuanInput.focus();
            return;
        }

        if (!navigator.geolocation) {
            statusNavigasi.innerHTML = `
                <div class="alert alert-danger">
                    Perangkat atau browser kamu tidak mendukung GPS.
                </div>
            `;
            return;
        }

        hentikanPemantauanGPS();
        navigasiAktif = false;
        posisiSekarang = null;
        posisiAwal = null;
        tujuanNavigasi = null;
        geometryNavigasi = null;
        hambatanNavigasi = [];
        hambatanSudahDiumumkan.clear();
        langkahSudahDiumumkan.clear();
        langkahBerikutnyaIndex = 0;

        kontrolNavigasi.innerHTML = '';
        statusPerjalanan.innerHTML = '';
        hasilHambatan.innerHTML = '';
        navigationMapCard.classList.remove('is-navigation-active');

        btnNavigasi.disabled = true;
        btnNavigasi.innerHTML =
            '<i class="fa-solid fa-location-dot" aria-hidden="true"></i> Mendeteksi lokasi...';

        statusNavigasi.innerHTML = `
            <div class="alert alert-info">
                Sedang mendeteksi lokasi kamu...
            </div>
        `;

        navigator.geolocation.getCurrentPosition(
            function (position) {
                const latitude = position.coords.latitude;
                const longitude = position.coords.longitude;
                const accuracy = position.coords.accuracy;

                posisiAwal = { latitude, longitude };
                posisiSekarang = { latitude, longitude, accuracy };
                perbaruiMarkerPengguna(latitude, longitude, accuracy);

                statusNavigasi.innerHTML = `
                    <div class="alert alert-info">
                        Sedang mencari koordinat tujuan...
                    </div>
                `;

                fetch('{{ route('navigasi.cari-tujuan') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        tujuan: tujuan,
                        latitude: latitude,
                        longitude: longitude
                    })
                })
                .then(async response => {
                    const data = await response.json();

                    if (!response.ok) {
                        throw new Error(data.message || 'Pencarian tujuan gagal.');
                    }

                    return data;
                })
                .then(data => {
                    if (!data.success) {
                        throw new Error(data.message || 'Tujuan tidak ditemukan.');
                    }

                    if (
                        data.tipe_pencarian === 'kategori' &&
                        Array.isArray(data.hasil)
                    ) {
                        tampilkanPilihanTujuan(data.hasil);

                        statusNavigasi.innerHTML = `
                            <div class="alert alert-info">
                                <h2 class="h5 mb-3">
                                    <i class="fa-solid fa-location-dot" aria-hidden="true"></i>
                                    Pilih Tujuan
                                </h2>
                                Ditemukan ${data.hasil.length} pilihan di sekitar lokasi kamu.
                                Silakan pilih salah satu tujuan.
                            </div>
                        `;

                        return null;
                    }

                    tujuanNavigasi = {
                        nama: data.tujuan,
                        latitude: parseFloat(data.latitude),
                        longitude: parseFloat(data.longitude)
                    };

                    return cariRuteKeTujuan(
                        latitude,
                        longitude,
                        data.latitude,
                        data.longitude
                    );
                })
                .then(rute => {
                    if (!rute) return;
                    prosesHasilRute(rute, latitude, longitude, accuracy);
                })
                .catch(error => {
                    console.error('Navigasi Error:', error);
                    statusNavigasi.innerHTML = `
                        <div class="alert alert-danger">
                            ${escapeHtml(error.message)}
                        </div>
                    `;
                })
                .finally(() => {
                    btnNavigasi.disabled = false;
                    btnNavigasi.innerHTML =
                        '<i class="fa-solid fa-route" aria-hidden="true"></i> Cari Rute';
                });
            },
            function (error) {
                console.error('GPS Error:', error);
                let pesan = '';

                switch (error.code) {
                    case error.PERMISSION_DENIED:
                        pesan = 'Izin lokasi ditolak. Silakan izinkan akses lokasi pada browser.';
                        break;
                    case error.POSITION_UNAVAILABLE:
                        pesan = 'Lokasi tidak tersedia. Pastikan GPS/lokasi perangkat aktif.';
                        break;
                    case error.TIMEOUT:
                        pesan = 'Waktu untuk mendapatkan lokasi habis. Silakan coba lagi.';
                        break;
                    default:
                        pesan = 'Terjadi kesalahan saat mendapatkan lokasi.';
                }

                statusNavigasi.innerHTML = `
                    <div class="alert alert-danger">${escapeHtml(pesan)}</div>
                `;

                btnNavigasi.disabled = false;
                btnNavigasi.innerHTML =
                    '<i class="fa-solid fa-route" aria-hidden="true"></i> Cari Rute';
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            }
        );
    });

    function tampilkanPilihanTujuan(hasil) {
        kontrolNavigasi.innerHTML = `
            <div class="card shadow-sm border-0 mt-4">
                <div class="card-body p-4">
                    <strong>
                        <i class="fa-solid fa-location-dot" aria-hidden="true"></i>
                        Pilih tujuan
                    </strong>
                    <p class="text-muted">
                        Pilih salah satu tujuan yang berada di sekitar lokasi kamu.
                    </p>
                    <div id="daftar-pilihan-tujuan"></div>
                </div>
            </div>
        `;

        const daftar = document.getElementById('daftar-pilihan-tujuan');

        hasil.forEach(function (item) {
            const tombol = document.createElement('button');
            tombol.type = 'button';
            tombol.className = 'btn btn-outline-primary w-100 text-start mb-3 p-3';

            tombol.innerHTML = `
                <strong>${escapeHtml(item.tujuan)}</strong><br>
                <small class="text-muted">
                    <i class="fa-solid fa-route" aria-hidden="true"></i>
                    ${escapeHtml(formatJarak(item.jarak_pengguna))}
                </small>
            `;

            tombol.addEventListener('click', function () {
                tujuanNavigasi = {
                    nama: item.tujuan,
                    latitude: parseFloat(item.latitude),
                    longitude: parseFloat(item.longitude)
                };

                statusNavigasi.innerHTML = `
                    <div class="alert alert-info">
                        <i class="fa-solid fa-location-dot" aria-hidden="true"></i>
                        Tujuan dipilih: <strong>${escapeHtml(item.tujuan)}</strong>.
                        Sedang mencari rute perjalanan...
                    </div>
                `;

                kontrolNavigasi.innerHTML = '';

                cariRuteKeTujuan(
                    posisiAwal.latitude,
                    posisiAwal.longitude,
                    item.latitude,
                    item.longitude
                )
                .then(function (rute) {
                    if (rute) {
                        prosesHasilRute(
                            rute,
                            posisiAwal.latitude,
                            posisiAwal.longitude,
                            posisiSekarang ? posisiSekarang.accuracy : 0
                        );
                    }
                })
                .catch(function (error) {
                    console.error('Error rute:', error);
                    statusNavigasi.innerHTML = `
                        <div class="alert alert-danger">
                            ${escapeHtml(error.message)}
                        </div>
                    `;
                });
            });

            daftar.appendChild(tombol);
        });
    }

    function cariRuteKeTujuan(latitudeAwal, longitudeAwal, latitudeTujuan, longitudeTujuan) {
        return fetch('{{ route('navigasi.rute') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                latitude_awal: latitudeAwal,
                longitude_awal: longitudeAwal,
                latitude_tujuan: latitudeTujuan,
                longitude_tujuan: longitudeTujuan
            })
        })
        .then(async response => {
            const hasilRute = await response.json();

            if (!response.ok) {
                if (hasilRute.errors) {
                    const pesanValidasi = Object.values(hasilRute.errors)
                        .flat()
                        .join('\n');

                    throw new Error(pesanValidasi);
                }

                throw new Error(hasilRute.message || 'Rute tidak dapat ditemukan.');
            }

            if (!hasilRute.success) {
                throw new Error(hasilRute.message || 'Rute tidak ditemukan.');
            }

            return hasilRute;
        });
    }

    function prosesHasilRute(rute, latitude, longitude, accuracy) {
        geometryNavigasi = rute.geometry;
        window.ruteNavigasi = rute.geometry;
        langkahNavigasi = rute.steps || [];
        langkahBerikutnyaIndex = 0;

        tampilkanRuteDiPeta(rute.geometry, latitude, longitude);

        const jarakKm = (rute.distance / 1000).toFixed(2);
        const durasiMenit = Math.ceil(rute.duration / 60);

        statusNavigasi.innerHTML = `
            <div class="alert alert-success">
                <strong>
                    <i class="fa-solid fa-route" aria-hidden="true"></i>
                    Rute berhasil ditemukan.
                </strong>
                <div class="mt-2">
                    <div><strong>Tujuan:</strong> ${escapeHtml(dataTujuan())}</div>
                    <div><strong>Jarak:</strong> ${jarakKm} km</div>
                    <div><strong>Perkiraan waktu:</strong> ${durasiMenit} menit</div>
                    <div><strong>Akurasi GPS:</strong> ±${Math.round(accuracy)} meter</div>
                </div>
            </div>
        `;

        cekHambatanSepanjangRute(rute.geometry);
        tampilkanTombolMulai(jarakKm, durasiMenit);
    }

    function tampilkanTombolMulai(jarakKm, durasiMenit) {
        const tujuan = dataTujuan();
        const jarak = Number(jarakKm);
        const durasi = Number(durasiMenit);

        const jarakTeks = Number.isFinite(jarak)
            ? `${jarak.toLocaleString('id-ID', {
                minimumFractionDigits: 1,
                maximumFractionDigits: 2
            })} km`
            : 'Belum tersedia';

        let durasiTeks = 'Belum tersedia';

        if (Number.isFinite(durasi) && durasi >= 0) {
            const jam = Math.floor(durasi / 60);
            const menit = durasi % 60;

            if (jam > 0 && menit > 0) {
                durasiTeks = `${jam} jam ${menit} menit`;
            } else if (jam > 0) {
                durasiTeks = `${jam} jam`;
            } else {
                durasiTeks = `${menit} menit`;
            }
        }

        kontrolNavigasi.innerHTML = `
            <section class="route-summary" aria-labelledby="route-summary-title">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-slate-500">
                            Tujuan perjalanan
                        </p>
                        <h2 id="route-summary-title"
                            class="mt-1 break-words text-lg font-bold text-slate-900">
                            ${escapeHtml(tujuan || 'Tujuan terpilih')}
                        </h2>
                    </div>

                    <span class="shrink-0 rounded-full bg-emerald-50
                                 px-3 py-1 text-xs font-semibold text-emerald-800">
                        Rute tersedia
                    </span>
                </div>

                <div class="my-4 grid grid-cols-2 gap-4 border-y
                            border-slate-200 py-4">
                    <div>
                        <p class="text-sm text-slate-500">Jarak</p>
                        <p class="mt-1 text-xl font-bold text-slate-900">
                            ${jarakTeks}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-slate-500">Perkiraan waktu</p>
                        <p class="mt-1 text-xl font-bold text-slate-900">
                            ${durasiTeks}
                        </p>
                    </div>
                </div>

                <p class="mb-4 text-sm leading-6 text-slate-600">
                    Perkiraan berdasarkan rute yang ditemukan. Kondisi jalur
                    dan hambatan di lapangan dapat berubah.
                </p>

                <button
                    type="button"
                    id="btn-mulai-perjalanan"
                    class="btn btn-success btn-mulai-perjalanan"
                >
                    <i class="fa-solid fa-route" aria-hidden="true"></i>
                    Mulai navigasi
                </button>
            </section>
        `;

        const tombolMulai = document.getElementById('btn-mulai-perjalanan');

        if (tombolMulai) {
            tombolMulai.addEventListener('click', mulaiNavigasiAktif);
        }
    }

    function mulaiNavigasiAktif() {
        if (!tujuanNavigasi) {
            tampilkanPesanPerjalanan('Tujuan belum tersedia.', 'error');
            return;
        }

        if (!geometryNavigasi) {
            tampilkanPesanPerjalanan('Data rute belum tersedia.', 'error');
            return;
        }

        if (!navigator.geolocation) {
            tampilkanPesanPerjalanan('GPS tidak tersedia pada perangkat ini.', 'error');
            return;
        }

        hentikanPemantauanGPS();

        navigasiAktif = true;
        navigationMapCard.classList.add('is-navigation-active');
        hambatanSudahDiumumkan.clear();
        langkahSudahDiumumkan.clear();

        langkahBerikutnyaIndex = 0;

        if (
            langkahNavigasi.length > 0 &&
            langkahNavigasi[0].type === 'depart'
        ) {
            langkahBerikutnyaIndex = 1;
        }

        kontrolNavigasi.innerHTML = `
            <h2 class="h5 mb-3">
                <i class="fa-solid fa-compass" aria-hidden="true"></i>
                Navigasi Sedang Berjalan
            </h2>

            <div class="alert alert-info">
                <strong>Tujuan:</strong>
                ${escapeHtml(tujuanNavigasi.nama)}
            </div>

            <div id="info-posisi">
                <i class="fa-solid fa-location-dot" aria-hidden="true"></i>
                Menunggu pembaruan posisi GPS...
            </div>

            <div class="mt-3">
                <button
                    type="button"
                    id="btn-hentikan-navigasi"
                    class="btn btn-danger btn-hentikan-navigasi"
                >
                    <i class="fa-solid fa-stop" aria-hidden="true"></i>
                    Hentikan Navigasi
                </button>
            </div>
        `;

        document.getElementById('btn-hentikan-navigasi')
            .addEventListener('click', hentikanNavigasi);

        bacakanTeks('Navigasi dimulai. Tujuan kamu adalah ' + tujuanNavigasi.nama);

        navigationMapCard.scrollIntoView({
            behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches
                ? 'auto'
                : 'smooth',
            block: 'start'
        });

        mulaiPemantauanGPS();
    }

    function mulaiPemantauanGPS() {
        watchId = navigator.geolocation.watchPosition(
            function (position) {
                if (!navigasiAktif) return;

                const latitude = position.coords.latitude;
                const longitude = position.coords.longitude;
                const accuracy = position.coords.accuracy;

                posisiSekarang = { latitude, longitude, accuracy };
                perbaruiMarkerPengguna(latitude, longitude, accuracy);
                updateInfoPosisi();

                const jarakKeTujuan = hitungJarakMeter(
                    latitude,
                    longitude,
                    tujuanNavigasi.latitude,
                    tujuanNavigasi.longitude
                );

                if (jarakKeTujuan <= BATAS_TUJUAN) {
                    sampaiTujuan(jarakKeTujuan);
                    return;
                }

                cekHambatanTerdekat(latitude, longitude);
                prosesVoiceGuidance(latitude, longitude);
            },
            function (error) {
                console.error('Watch GPS Error:', error);
                tampilkanPesanPerjalanan(
                    'Posisi GPS tidak dapat diperbarui. Pastikan lokasi perangkat tetap aktif.',
                    'error'
                );
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 3000
            }
        );
    }

    function perbaruiMarkerPengguna(latitude, longitude, accuracy = 0) {
        if (!petaNavigasi) return;

        const posisi = [latitude, longitude];
        const popup = `Posisi kamu<br>Akurasi GPS: ±${Math.round(accuracy)} meter`;

        if (!markerPengguna) {
            markerPengguna = L.circleMarker(posisi, {
                radius: 9,
                color: '#fff',
                weight: 3,
                fillColor: '#2563eb',
                fillOpacity: 1
            }).addTo(petaNavigasi);
        } else {
            markerPengguna.setLatLng(posisi);
        }

        markerPengguna.bindPopup(popup);

        if (navigasiAktif) {
            petaNavigasi.panTo(posisi, { animate: true, duration: 0.5 });
        } else {
            petaNavigasi.setView(posisi, 15);
        }
    }

    function tampilkanRuteDiPeta(geometry, latitudeAwal, longitudeAwal) {
        if (!petaNavigasi || !geometry || !Array.isArray(geometry.coordinates)) {
            return;
        }

        const koordinat = geometry.coordinates.map(item => [item[1], item[0]]);

        if (garisRute) petaNavigasi.removeLayer(garisRute);

        garisRute = L.polyline(koordinat, {
            color: '#2563eb',
            weight: 7,
            opacity: 0.85,
            lineCap: 'round',
            lineJoin: 'round'
        }).addTo(petaNavigasi);

        if (markerTujuan) petaNavigasi.removeLayer(markerTujuan);

        markerTujuan = L.marker([
            tujuanNavigasi.latitude,
            tujuanNavigasi.longitude
        ]).addTo(petaNavigasi).bindPopup(
            `<strong>Tujuan</strong><br>${escapeHtml(tujuanNavigasi.nama)}`
        );

        perbaruiMarkerPengguna(
            latitudeAwal,
            longitudeAwal,
            posisiSekarang ? posisiSekarang.accuracy : 0
        );

        petaNavigasi.fitBounds(garisRute.getBounds(), { padding: [32, 32] });
    }

    function updateInfoPosisi() {
        const infoPosisi = document.getElementById('info-posisi');

        if (!infoPosisi || !posisiSekarang) return;

        const jarakKeTujuan = hitungJarakMeter(
            posisiSekarang.latitude,
            posisiSekarang.longitude,
            tujuanNavigasi.latitude,
            tujuanNavigasi.longitude
        );

        const jarakText = jarakKeTujuan >= 1000
            ? (jarakKeTujuan / 1000).toFixed(2) + ' km'
            : Math.round(jarakKeTujuan) + ' meter';

        infoPosisi.innerHTML = `
            <div class="info-perjalanan">
                <h3>
                    <i class="fa-solid fa-location-crosshairs" aria-hidden="true"></i>
                    Posisi Saat Ini
                </h3>
                <p><strong>Latitude:</strong> ${posisiSekarang.latitude}</p>
                <p><strong>Longitude:</strong> ${posisiSekarang.longitude}</p>
                <p><strong>Akurasi GPS:</strong> ±${Math.round(posisiSekarang.accuracy)} meter</p>
                <p><strong>Jarak ke tujuan:</strong> ${jarakText}</p>
                <p class="status-gps">
                    <i class="fa-solid fa-arrows-rotate" aria-hidden="true"></i>
                    Posisi GPS sedang diperbarui.
                </p>
            </div>
        `;
    }

    function cekHambatanSepanjangRute(geometry) {
        fetch("{{ route('navigasi.cek-hambatan') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ geometry })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Gagal mengecek hambatan.');
            }

            return response.json();
        })
        .then(data => {
            if (data.success) {
                hambatanNavigasi = data.hambatan || [];
                tampilkanHambatan(hambatanNavigasi);
            } else {
                tampilkanPesanHambatan(
                    data.message || 'Pengecekan hambatan gagal.',
                    'error'
                );
            }
        })
        .catch(error => {
            console.error('Hambatan Error:', error);
            tampilkanPesanHambatan(
                'Terjadi kesalahan saat mengecek hambatan.',
                'error'
            );
        });
    }

    function cekHambatanTerdekat(latitude, longitude) {
        if (!hambatanNavigasi || hambatanNavigasi.length === 0) return;

        hambatanNavigasi.forEach(function (hambatan) {
            if (
                hambatan.latitude == null ||
                hambatan.longitude == null
            ) {
                return;
            }

            const jarak = hitungJarakMeter(
                latitude,
                longitude,
                parseFloat(hambatan.latitude),
                parseFloat(hambatan.longitude)
            );

            if (jarak <= BATAS_PERINGATAN_HAMBATAN) {
                beriPeringatanHambatan(hambatan, jarak);
            }
        });
    }

    function beriPeringatanHambatan(hambatan, jarak) {
        const id = String(hambatan.id);

        if (hambatanSudahDiumumkan.has(id)) return;
        hambatanSudahDiumumkan.add(id);

        const jarakText = jarak < 1
            ? 'kurang dari satu meter'
            : jarak >= 1000
                ? (jarak / 1000).toFixed(1) + ' kilometer'
                : Math.round(jarak) + ' meter';

        const namaHambatan = hambatan.judul || 'hambatan aksesibilitas';
        const pesan = `Peringatan. Terdapat ${namaHambatan} sekitar ${jarakText} dari posisi kamu.`;

        tampilkanPeringatanAktif(pesan);
        bacakanTeks(pesan);
    }

    function tampilkanPeringatanAktif(pesan) {
        statusPerjalanan.innerHTML = `
            <div class="alert alert-warning" role="alert" aria-live="assertive">
                <strong>
                    <i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i>
                    Peringatan Hambatan
                </strong>
                <div class="mt-2">${escapeHtml(pesan)}</div>
            </div>
        `;
    }

    function tampilkanHambatan(hambatan) {
        if (!hambatan || hambatan.length === 0) {
            const pesan =
                'Tidak ditemukan laporan hambatan aksesibilitas terverifikasi di sepanjang rute yang diperiksa.';

            hasilHambatan.innerHTML = `
                <div class="hambatan-aman" role="status" aria-live="polite">
                    <h3>Tidak ditemukan hambatan</h3>
                    <p>${escapeHtml(pesan)}</p>
                </div>
            `;

            if (!navigasiAktif) bacakanTeks(pesan);
            return;
        }

        let html = `
            <div class="hambatan-ditemukan" role="alert" aria-live="assertive">
                <h3>
                    <i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i>
                    Hambatan ditemukan: ${hambatan.length}
                </h3>
        `;

        hambatan.forEach(function (item, index) {
            html += `
                <article class="hambatan-card">
                    <h4>Hambatan ${index + 1}: ${escapeHtml(item.judul)}</h4>
                    <p><strong>Kategori:</strong> ${escapeHtml(item.kategori || 'Tidak diketahui')}</p>
                    <p><strong>Jarak dari rute:</strong> ${escapeHtml(item.jarak_dari_rute)} meter</p>
                    <p><strong>Prioritas:</strong> ${escapeHtml(item.tingkat_prioritas || 'Belum dinilai')}</p>
                    <p><strong>Lokasi:</strong> ${escapeHtml(item.alamat || 'Alamat tidak tersedia')}</p>
                </article>
            `;
        });

        html += '</div>';
        hasilHambatan.innerHTML = html;

        if (!navigasiAktif) {
            let teksSuara = `Ditemukan ${hambatan.length} hambatan aksesibilitas sepanjang rute. `;

            hambatan.forEach(function (item, index) {
                teksSuara += `Hambatan ${index + 1}: ${item.judul || 'Tidak diketahui'}. Jarak dari rute ${item.jarak_dari_rute} meter. `;
            });

            bacakanTeks(teksSuara);
        }
    }

    function tampilkanPesanHambatan(pesan, tipe = 'info') {
        hasilHambatan.innerHTML = `
            <div class="hambatan-pesan" role="alert" aria-live="assertive">
                ${escapeHtml(pesan)}
            </div>
        `;
    }

    function tampilkanPesanPerjalanan(pesan, tipe = 'info') {
        const classAlert = tipe === 'error'
            ? 'alert alert-danger'
            : 'alert alert-info';

        statusPerjalanan.innerHTML = `
            <div class="${classAlert}" role="alert" aria-live="polite">
                ${escapeHtml(pesan)}
            </div>
        `;
    }

    function sampaiTujuan() {
        if (!navigasiAktif) return;

        navigasiAktif = false;
        hentikanPemantauanGPS();

        const pesan = 'Anda telah sampai di tujuan ' + tujuanNavigasi.nama;

        statusPerjalanan.innerHTML = `
            <div class="alert alert-success" role="status" aria-live="assertive">
                <strong>Tujuan Tercapai</strong>
                <div class="mt-2">${escapeHtml(tujuanNavigasi.nama)}</div>
                <div class="mt-2">Anda telah sampai di tujuan.</div>
            </div>
        `;

        bacakanTeks(pesan);
    }

    function prosesVoiceGuidance(latitude, longitude) {
        if (!navigasiAktif || !langkahNavigasi.length) return;

        const langkah = langkahNavigasi[langkahBerikutnyaIndex];

        if (!langkah) return;

        if (langkah.latitude == null || langkah.longitude == null) {
            langkahBerikutnyaIndex++;
            return;
        }

        const jarak = hitungJarakMeter(
            latitude,
            longitude,
            langkah.latitude,
            langkah.longitude
        );

        if (
            jarak <= BATAS_PRA_ARAHAN &&
            jarak > BATAS_ARAHAN &&
            !langkahSudahDiumumkan.has(langkahBerikutnyaIndex)
        ) {
            const instruksiAwal = buatInstruksiSuara(langkah, 'awal');

            if (instruksiAwal) {
                bacakanTeks(instruksiAwal);
                langkahSudahDiumumkan.add(langkahBerikutnyaIndex);
            }
        }

        if (jarak <= BATAS_ARAHAN) {
            const instruksi = buatInstruksiSuara(langkah, 'aksi');

            if (instruksi) bacakanTeks(instruksi);

            langkahBerikutnyaIndex++;

            const langkahBerikutnya = langkahNavigasi[langkahBerikutnyaIndex];

            if (
                langkahBerikutnya &&
                langkahBerikutnya.type === 'new name' &&
                langkahBerikutnya.name
            ) {
                bacakanTeks(`Lanjut di ${langkahBerikutnya.name}.`);
            }
        }
    }

    function buatInstruksiSuara(langkah, mode = 'aksi') {
        const type = langkah.type;
        const modifier = langkah.modifier;
        const namaJalan = langkah.name;

        if (type === 'depart') {
            return namaJalan
                ? `Mulai perjalanan melalui ${namaJalan}.`
                : 'Mulai perjalanan.';
        }

        if (type === 'arrive') {
            return 'Anda sudah mendekati tujuan.';
        }

        if (type === 'turn' || type === 'continue' || type === 'end of road') {
            const awalan = mode === 'awal' ? 'Dalam 80 meter, ' : '';

            if (modifier === 'left') {
                return mode === 'awal'
                    ? `${awalan}belok kiri.`
                    : namaJalan
                        ? `Belok kiri menuju ${namaJalan}.`
                        : 'Belok kiri.';
            }

            if (modifier === 'right') {
                return mode === 'awal'
                    ? `${awalan}belok kanan.`
                    : namaJalan
                        ? `Belok kanan menuju ${namaJalan}.`
                        : 'Belok kanan.';
            }

            if (modifier === 'slight left') {
                return `${awalan}ambil arah sedikit ke kiri.`;
            }

            if (modifier === 'slight right') {
                return `${awalan}ambil arah sedikit ke kanan.`;
            }

            if (modifier === 'sharp left') {
                return `${awalan}belok tajam ke kiri.`;
            }

            if (modifier === 'sharp right') {
                return `${awalan}belok tajam ke kanan.`;
            }

            return namaJalan
                ? `Lanjut melalui ${namaJalan}.`
                : 'Lanjut mengikuti rute.';
        }

        if (type === 'roundabout') {
            return mode === 'awal'
                ? 'Dalam 80 meter, masuk ke bundaran dan ikuti rute.'
                : 'Masuk bundaran dan ikuti rute.';
        }

        if (type === 'merge') {
            return mode === 'awal'
                ? 'Dalam 80 meter, bergabung ke jalur berikutnya.'
                : 'Bergabung ke jalur berikutnya.';
        }

        if (type === 'fork') {
            return mode === 'awal'
                ? 'Dalam 80 meter, ambil percabangan sesuai rute.'
                : 'Ambil percabangan sesuai rute.';
        }

        if (type === 'on ramp') {
            return mode === 'awal'
                ? 'Dalam 80 meter, masuk ke jalur berikutnya.'
                : 'Masuk ke jalur berikutnya.';
        }

        if (type === 'off ramp') {
            return mode === 'awal'
                ? 'Dalam 80 meter, keluar dari jalur sesuai rute.'
                : 'Keluar dari jalur sesuai rute.';
        }

        return null;
    }

    function hentikanNavigasi() {
        navigasiAktif = false;
        navigationMapCard.classList.remove('is-navigation-active');
        hentikanPemantauanGPS();

        statusPerjalanan.innerHTML = `
            <i class="fa-solid fa-stop" aria-hidden="true"></i>
            Navigasi dihentikan.
        `;

        bacakanTeks('Navigasi dihentikan.');

        kontrolNavigasi.innerHTML = `
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h2 class="h5">Navigasi dihentikan</h2>
                    <p class="text-muted">
                        Kamu dapat mencari rute baru dengan memasukkan tujuan lain.
                    </p>
                </div>
            </div>
        `;
    }

    function hentikanPemantauanGPS() {
        if (watchId !== null) {
            navigator.geolocation.clearWatch(watchId);
            watchId = null;
        }
    }

    function hitungJarakMeter(lat1, lon1, lat2, lon2) {
        const earthRadius = 6371000;
        const lat1Rad = degToRad(lat1);
        const lat2Rad = degToRad(lat2);
        const deltaLat = degToRad(lat2 - lat1);
        const deltaLon = degToRad(lon2 - lon1);

        const a =
            Math.sin(deltaLat / 2) * Math.sin(deltaLat / 2) +
            Math.cos(lat1Rad) * Math.cos(lat2Rad) *
            Math.sin(deltaLon / 2) * Math.sin(deltaLon / 2);

        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

        return earthRadius * c;
    }

    function degToRad(degree) {
        return (degree * Math.PI) / 180;
    }

    function formatJarak(jarak) {
        if (jarak === null || jarak === undefined) {
            return 'Jarak tidak diketahui';
        }

        jarak = parseFloat(jarak);

        if (isNaN(jarak)) {
            return 'Jarak tidak diketahui';
        }

        if (jarak >= 1000) {
            return (jarak / 1000).toFixed(2) + ' km dari lokasi kamu';
        }

        return Math.round(jarak) + ' meter dari lokasi kamu';
    }

    function dataTujuan() {
        return tujuanNavigasi && tujuanNavigasi.nama
            ? tujuanNavigasi.nama
            : 'Tujuan tidak diketahui';
    }

    function escapeHtml(text) {
        if (text === null || text === undefined) return '';

        const div = document.createElement('div');
        div.textContent = String(text);

        return div.innerHTML;
    }

    function bacakanTeks(teks) {
        if (!('speechSynthesis' in window)) {
            console.warn('Browser tidak mendukung Text-to-Speech.');
            return;
        }

        window.speechSynthesis.cancel();

        const suara = new SpeechSynthesisUtterance(teks);
        suara.lang = 'id-ID';
        suara.rate = 0.9;
        suara.pitch = 1;
        suara.volume = 1;

        window.speechSynthesis.speak(suara);
    }
});

/* ==========================================================
   SCREEN READER MELAYANG — BACA HALAMAN DAN BISA DIGESER
========================================================== */
document.addEventListener('DOMContentLoaded', function () {
    const tombol = document.getElementById('smartpath-screen-reader');
    const label = document.getElementById('smartpath-screen-reader-label');
    const status = document.getElementById('smartpath-screen-reader-status');

    if (!tombol || !label || !status) return;

    if (!('speechSynthesis' in window)) {
        tombol.disabled = true;
        status.textContent = 'Browser ini belum mendukung fitur pembaca suara.';
        return;
    }

    let membaca = false;
    let menggeser = false;
    let digeser = false;
    let mulaiX = 0;
    let mulaiY = 0;
    let awalX = 0;
    let awalY = 0;
    let idBacaan = 0;

    function ubahStatus(aktif, pesan) {
        membaca = aktif;

        tombol.setAttribute('aria-pressed', aktif ? 'true' : 'false');
        tombol.setAttribute(
            'aria-label',
            aktif
                ? 'Hentikan pembacaan halaman'
                : 'Mulai membacakan isi halaman'
        );

        label.textContent = aktif ? 'Hentikan Bacaan' : 'Dengar Panduan';
        status.textContent = pesan || '';
    }

    function hentikanBacaan() {
        idBacaan++;
        window.speechSynthesis.cancel();
        ubahStatus(false, 'Pembacaan dihentikan.');
    }

    function mulaiBacaan() {
        const konten = document.querySelector('.smartpath-navigation');

        if (!konten) {
            ubahStatus(false, 'Konten halaman tidak ditemukan.');
            return;
        }

        const salinan = konten.cloneNode(true);

        salinan.querySelectorAll(
            'script, style, button, input, textarea, select, ' +
            '[aria-hidden="true"], #peta-navigasi'
        ).forEach(function (elemen) {
            elemen.remove();
        });

        const teks = salinan.innerText.replace(/\s+/g, ' ').trim();

        if (!teks) {
            ubahStatus(false, 'Tidak ada teks halaman yang dapat dibacakan.');
            return;
        }

        window.speechSynthesis.cancel();

        const nomorBacaan = ++idBacaan;
        const suara = new SpeechSynthesisUtterance(teks);

        suara.lang = 'id-ID';
        suara.rate = 0.9;
        suara.pitch = 1;
        suara.volume = 1;

        suara.onend = function () {
            if (nomorBacaan !== idBacaan) return;
            ubahStatus(false, 'Pembacaan halaman selesai.');
        };

        suara.onerror = function (event) {
            if (nomorBacaan !== idBacaan) return;

            if (
                event.error !== 'canceled' &&
                event.error !== 'interrupted'
            ) {
                ubahStatus(false, 'Suara tidak dapat diputar. Silakan coba lagi.');
            }
        };

        ubahStatus(true, 'Halaman sedang dibacakan.');
        window.speechSynthesis.speak(suara);
    }

    tombol.addEventListener('click', function (event) {
        if (digeser) {
            digeser = false;
            event.preventDefault();
            return;
        }

        if (membaca || window.speechSynthesis.speaking) {
            hentikanBacaan();
        } else {
            mulaiBacaan();
        }
    });

    function mulaiGeser(event) {
        if (event.type === 'mousedown' && event.button !== 0) return;

        const titik = event.type === 'touchstart'
            ? event.touches[0]
            : event;

        menggeser = true;
        digeser = false;

        mulaiX = titik.clientX;
        mulaiY = titik.clientY;

        const rect = tombol.getBoundingClientRect();

        awalX = rect.left;
        awalY = rect.top;

        tombol.style.right = 'auto';
        tombol.style.bottom = 'auto';
        tombol.style.left = awalX + 'px';
        tombol.style.top = awalY + 'px';
    }

    function gerakkanTombol(event) {
        if (!menggeser) return;

        const titik = event.type === 'touchmove'
            ? event.touches[0]
            : event;

        const dx = titik.clientX - mulaiX;
        const dy = titik.clientY - mulaiY;

        if (Math.abs(dx) > 5 || Math.abs(dy) > 5) {
            digeser = true;
        }

        if (!digeser) return;

        if (event.cancelable) event.preventDefault();

        const batasX = window.innerWidth - tombol.offsetWidth;
        const batasY = window.innerHeight - tombol.offsetHeight;

        const x = Math.max(0, Math.min(batasX, awalX + dx));
        const y = Math.max(0, Math.min(batasY, awalY + dy));

        tombol.style.left = x + 'px';
        tombol.style.top = y + 'px';
    }

    function selesaiGeser() {
        menggeser = false;
    }

    tombol.addEventListener('mousedown', mulaiGeser);
    document.addEventListener('mousemove', gerakkanTombol);
    document.addEventListener('mouseup', selesaiGeser);

    tombol.addEventListener('touchstart', mulaiGeser, {
        passive: true
    });

    document.addEventListener('touchmove', gerakkanTombol, {
        passive: false
    });

    document.addEventListener('touchend', selesaiGeser);
    document.addEventListener('touchcancel', selesaiGeser);
});
</script>

@endpush