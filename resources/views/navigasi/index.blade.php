@extends('layouts.app')

@section('title', 'Navigasi SmartPath')

@section('content')

@include('partials.nav-public')

<style>
    .smartpath-navigation {
        min-height: calc(100vh - 64px);
        background: #f8fafc;
    }

    .navigation-shell {
        max-width: 1440px;
        margin: 0 auto;
        padding: 28px 24px 48px;
    }

    .navigation-header {
        margin-bottom: 18px;
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
        overflow: hidden;
        min-height: 620px;
        height: min(78vh, 780px);
        border: 1px solid #dbe4e9;
        border-radius: 16px;
        background: #e2e8f0;
        box-shadow: 0 8px 24px rgba(15, 23, 42, .07);
    }

    .navigation-map {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
    }

    .navigation-map .leaflet-container {
        font-family: 'Inter', sans-serif;
    }

    .navigation-map-topbar {
        position: absolute;
        z-index: 800;
        top: 16px;
        left: 16px;
        right: 16px;
        display: flex;
        justify-content: space-between;
        pointer-events: none;
    }

    .navigation-map-title,
    .navigation-gps-status {
        pointer-events: auto;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 9px 12px;
        border: 1px solid rgba(226, 232, 240, .95);
        border-radius: 10px;
        background: rgba(255, 255, 255, .94);
        color: #0f172a;
        font-size: 13px;
        font-weight: 700;
        box-shadow: 0 4px 14px rgba(15, 23, 42, .08);
        backdrop-filter: blur(8px);
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
        box-shadow: 0 0 0 4px rgba(5, 150, 105, .12);
    }

    .navigation-overlay {
        position: absolute;
        z-index: 900;
        left: 18px;
        bottom: 18px;
        width: min(430px, calc(100% - 36px));
        max-height: calc(100% - 118px);
        overflow-y: auto;
        border: 1px solid rgba(226, 232, 240, .96);
        border-radius: 16px;
        background: rgba(255, 255, 255, .97);
        box-shadow: 0 16px 45px rgba(15, 23, 42, .16);
        backdrop-filter: blur(12px);
    }

    .navigation-overlay__header {
        padding: 16px 18px 13px;
        border-bottom: 1px solid #eef2f7;
    }

    .navigation-overlay__heading {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .navigation-overlay__icon {
        display: flex;
        width: 34px;
        height: 34px;
        align-items: center;
        justify-content: center;
        border-radius: 9px;
        background: #ecfdf5;
        color: #047857;
    }

    .navigation-overlay h2 {
        margin: 0;
        color: #0f172a;
        font-size: 15px;
        line-height: 1.35;
        font-weight: 750;
    }

    .navigation-overlay__subheading {
        margin-top: 3px;
        color: #64748b;
        font-size: 13px;
        line-height: 1.5;
    }

    .navigation-overlay__body {
        padding: 16px 18px 18px;
    }

    .navigation-label {
        display: block;
        margin-bottom: 7px;
        color: #334155;
        font-size: 13px;
        font-weight: 700;
    }

    .navigation-input {
        width: 100%;
        min-height: 46px;
        padding: 11px 13px;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        outline: none;
        background: #ffffff;
        color: #0f172a;
        font-size: 14px;
        line-height: 1.5;
    }

    .navigation-input:focus {
        border-color: #059669;
        box-shadow: 0 0 0 3px rgba(5, 150, 105, .12);
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
        min-height: 44px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 750;
    }

    .navigation-primary {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        border: 1px solid #047857;
        background: #047857;
        color: #ffffff;
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
        background: #ffffff;
        color: #334155;
        cursor: pointer;
    }

    .navigation-secondary:hover {
        background: #f8fafc;
    }

    .navigation-status {
        margin-top: 12px;
    }

    .navigation-result,
    .navigation-active {
        margin-top: 12px;
    }

    .alert {
        border-radius: 10px;
        padding: 11px 12px;
        border: 1px solid transparent;
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
        background: #ffffff;
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
        color: #ffffff;
    }

    .btn-success:hover {
        background: #065f46;
    }

    .btn-danger {
        border: 1px solid #fecaca;
        background: #ffffff;
        color: #b91c1c;
    }

    .btn-danger:hover {
        background: #fef2f2;
    }

    .btn-outline-primary {
        width: 100%;
        border: 1px solid #d1d5db;
        background: #ffffff;
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
        border-radius: 10px;
        padding: 12px;
        border: 1px solid #e2e8f0;
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
        background: #ffffff;
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

    @media (max-width: 900px) {

        .navigation-shell {
            padding: 22px 16px 36px;
        }

        .navigation-map-card {
            min-height: 620px;
            height: 76vh;
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

        .navigation-map-topbar {
            top: 10px;
            left: 10px;
            right: 10px;
        }

        .navigation-overlay {
            left: 10px;
            bottom: 10px;
            width: calc(100% - 20px);
            max-height: 54%;
            border-radius: 13px;
        }

        .navigation-actions {
            grid-template-columns: 1fr;
        }

    }
</style>


<main class="smartpath-navigation">

    <div class="navigation-shell">

        <header class="navigation-header">

            <div>

                <div class="navigation-eyebrow">
                    <i
                        class="fa-solid fa-route"
                        aria-hidden="true"
                    ></i>

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


        {{-- ==========================================================
             MAP + FLOATING NAVIGATION PANEL
        =========================================================== --}}
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


            {{-- TOP BAR --}}
            <div class="navigation-map-topbar">

                <div
                    id="navigation-map-title"
                    class="navigation-map-title"
                >
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

                    <span id="navigation-gps-label">
                        GPS perangkat
                    </span>

                </div>

            </div>


            {{-- FLOATING CONTROL PANEL --}}
            <aside
                class="navigation-overlay"
                aria-label="Kontrol navigasi"
            >

                <div class="navigation-overlay__header">

                    <div class="navigation-overlay__heading">

                        <span
                            class="navigation-overlay__icon"
                            aria-hidden="true"
                        >
                            <i class="fa-solid fa-location-arrow"></i>
                        </span>

                        <div>

                            <h2>
                                Tentukan Lokasi Tujuan
                            </h2>

                            <p class="navigation-overlay__subheading">
                                Cari tempat atau alamat di sekitar Kota Depok.
                            </p>

                        </div>

                    </div>

                </div>


                <div class="navigation-overlay__body">

                    <label
                        for="tujuan"
                        class="navigation-label"
                    >
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

                    <p
                        id="tujuan-help"
                        class="navigation-helper"
                    >
                        Masukkan nama fasilitas, tempat umum,
                        atau alamat tujuan.
                    </p>


                    <div class="navigation-actions">

                        <button
                            type="button"
                            id="btn-mulai-navigasi"
                            class="navigation-primary"
                        >
                            <i
                                class="fa-solid fa-location-arrow"
                                aria-hidden="true"
                            ></i>

                            Cari Rute
                        </button>


                        <button
                            type="button"
                            id="btn-map-center"
                            class="navigation-secondary"
                            title="Kembali ke posisi awal peta"
                        >
                            <i
                                class="fa-solid fa-crosshairs"
                                aria-hidden="true"
                            ></i>

                            Posisi
                        </button>

                    </div>


                    <div
                        id="status-navigasi"
                        class="navigation-status"
                        role="status"
                        aria-live="polite"
                    ></div>


                    <div
                        id="kontrol-navigasi"
                        class="navigation-result"
                    ></div>


                    <div
                        id="status-perjalanan"
                        class="navigation-active"
                        role="status"
                        aria-live="polite"
                    ></div>

                </div>

            </aside>

        </section>


        {{-- HAMBATAN --}}
        <section
            id="hasil-hambatan"
            class="mt-5"
            aria-live="polite"
            aria-label="Informasi hambatan aksesibilitas sepanjang rute"
        ></section>


        {{-- ACCESSIBILITY --}}
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
                        Informasi hambatan tetap tersedia dalam bentuk teks
                        dan dapat dibacakan melalui fitur suara perangkat.
                        Kontrol utama navigasi ditempatkan langsung di dalam
                        area peta agar pengguna tidak perlu berpindah ke panel
                        jauh di bawah halaman.
                    </p>

                </div>

            </div>

        </section>

    </div>

</main>

@endsection


@push('scripts')

<script>
document.addEventListener('DOMContentLoaded', function () {

    // ==========================================================
    // ELEMEN HTML
    // ==========================================================
    const btnNavigasi = document.getElementById('btn-mulai-navigasi');
    const tujuanInput = document.getElementById('tujuan');
    const statusNavigasi = document.getElementById('status-navigasi');
    const kontrolNavigasi = document.getElementById('kontrol-navigasi');
    const statusPerjalanan = document.getElementById('status-perjalanan');
    const hasilHambatan = document.getElementById('hasil-hambatan');

    // ==========================================================
    // STATE NAVIGASI
    // ==========================================================
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

    // KONSTANTA BATAS JARAK
    const BATAS_PERINGATAN_HAMBATAN = 50; // Jarak peringatan hambatan (meter)
    const BATAS_TUJUAN = 20;              // Jarak sampai tujuan (meter)
    const BATAS_PRA_ARAHAN = 80;          // Mulai memberi peringatan pra-arahan (meter)
    const BATAS_ARAHAN = 50;              // Jarak eksekusi instruksi suara (meter)

    if (typeof L !== 'undefined') {
        petaNavigasi = L.map('peta-navigasi', { zoomControl: true })
            .setView([-6.4025, 106.8166], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(petaNavigasi);
    }

    // ==========================================================
    // EVENT LISTENER TOMBOL CARI RUTE
    // ==========================================================
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

        // Reset data navigasi sebelumnya
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

        // Loading state
        btnNavigasi.disabled = true;
        btnNavigasi.innerHTML = '📍 Mendeteksi lokasi...';
        statusNavigasi.innerHTML = `
            <div class="alert alert-info">
                Sedang mendeteksi lokasi kamu...
            </div>
        `;

        // Ambil GPS Awal
        navigator.geolocation.getCurrentPosition(
            function (position) {
                const latitude = position.coords.latitude;
                const longitude = position.coords.longitude;
                const accuracy = position.coords.accuracy;

                posisiAwal = { latitude, longitude };
                posisiSekarang = { latitude, longitude, accuracy };
                perbaruiMarkerPengguna(latitude, longitude, accuracy);

                console.log('GPS AWAL:', latitude, longitude, 'Akurasi:', accuracy);

                statusNavigasi.innerHTML = `
                    <div class="alert alert-info">
                        📍 Lokasi kamu berhasil dideteksi.<br>
                        Sedang mencari koordinat tujuan...
                    </div>
                `;

                // Cari Lokasi Tujuan via Server
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
                    console.log('RESPONSE PENCARIAN TUJUAN:', data);

                    if (!response.ok) {
                        throw new Error(data.message || 'Pencarian tujuan gagal.');
                    }
                    return data;
                })
                .then(data => {
                    if (!data.success) {
                        throw new Error(data.message || 'Tujuan tidak ditemukan.');
                    }

                    // Jika hasil pencarian berupa daftar pilihan lokasi (kategori)
                    if (data.tipe_pencarian === 'kategori' && Array.isArray(data.hasil)) {
                        console.log('PILIHAN TUJUAN:', data.hasil);
                        tampilkanPilihanTujuan(data.hasil);

                        statusNavigasi.innerHTML = `
                            <div class="alert alert-info">
                                <strong>📍 Pilih tujuan</strong><br>
                                Ditemukan ${data.hasil.length} pilihan di sekitar lokasi kamu. Silakan pilih salah satu tujuan.
                            </div>
                        `;
                        return null;
                    }

                    // Jika tujuan spesifik
                    tujuanNavigasi = {
                        nama: data.tujuan,
                        latitude: parseFloat(data.latitude),
                        longitude: parseFloat(data.longitude)
                    };

                    console.log('TUJUAN DITEMUKAN:', data);
                    console.log('JARAK TUJUAN DARI PENGGUNA:', data.jarak_pengguna, 'meter');

                    return cariRuteKeTujuan(latitude, longitude, data.latitude, data.longitude);
                })
                .then(rute => {
                    if (!rute) return; // Jika memilih dari kategori, rute diproses setelah diklik
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
                    btnNavigasi.innerHTML = '📍 Cari Rute';
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
                btnNavigasi.innerHTML = '📍 Cari Rute';
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            }
        );
    });

    // ==========================================================
    // FUNGSI PENCARIAN & PROSES RUTE
    // ==========================================================
    function tampilkanPilihanTujuan(hasil) {
        kontrolNavigasi.innerHTML = `
            <div class="card shadow-sm border-0 mt-4">
                <div class="card-body p-4">
                    <h2 class="h5 mb-3">📍 Pilih Tujuan</h2>
                    <p class="text-muted">Pilih salah satu tujuan yang berada di sekitar lokasi kamu.</p>
                    <div id="daftar-pilihan-tujuan"></div>
                </div>
            </div>
        `;

        const daftar = document.getElementById('daftar-pilihan-tujuan');

        hasil.forEach(function (item) {
            const jarak = formatJarak(item.jarak_pengguna);
            const tombol = document.createElement('button');

            tombol.type = 'button';
            tombol.className = 'btn btn-outline-primary w-100 text-start mb-3 p-3';
            tombol.innerHTML = `
                <strong>${escapeHtml(item.tujuan)}</strong><br>
                <small class="text-muted">📍 ${escapeHtml(jarak)}</small>
            `;

            tombol.addEventListener('click', function () {
                console.log('TUJUAN DIPILIH:', item);

                tujuanNavigasi = {
                    nama: item.tujuan,
                    latitude: parseFloat(item.latitude),
                    longitude: parseFloat(item.longitude)
                };

                statusNavigasi.innerHTML = `
                    <div class="alert alert-info">
                        📍 Tujuan dipilih.<br>
                        <strong>${escapeHtml(item.tujuan)}</strong><br>
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
        const dataRute = {
            latitude_awal: latitudeAwal,
            longitude_awal: longitudeAwal,
            latitude_tujuan: latitudeTujuan,
            longitude_tujuan: longitudeTujuan
        };

        console.log('DATA YANG DIKIRIM KE RUTE:', dataRute);

        return fetch('{{ route('navigasi.rute') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify(dataRute)
        })
        .then(async response => {
            const hasilRute = await response.json();
            console.log('RESPONSE RUTE:', hasilRute);

            if (!response.ok) {
                if (hasilRute.errors) {
                    const pesanValidasi = Object.values(hasilRute.errors).flat().join('<br>');
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

        console.log('RUTE BERHASIL:', rute);
        console.log('JARAK:', jarakKm, 'km');
        console.log('DURASI:', durasiMenit, 'menit');

        statusNavigasi.innerHTML = `
            <div class="alert alert-success">
                <strong>🛣️ Rute berhasil ditemukan.</strong>
                <div class="mt-2">
                    <div><strong>Tujuan:</strong> ${escapeHtml(dataTujuan())}</div>
                    <div><strong>Jarak:</strong> ${jarakKm} km</div>
                    <div><strong>Perkiraan waktu:</strong> ${durasiMenit} menit</div>
                    <div><strong>Lokasi awal:</strong> ${latitude}, ${longitude}</div>
                    <div><strong>Lokasi tujuan:</strong> ${tujuanNavigasi.latitude}, ${tujuanNavigasi.longitude}</div>
                    <div><strong>Akurasi GPS:</strong> ±${Math.round(accuracy)} meter</div>
                </div>
            </div>
        `;

        cekHambatanSepanjangRute(rute.geometry);
        tampilkanTombolMulai(jarakKm, durasiMenit);
    }

    // ==========================================================
    // KONTROL MULAI NAVIGASI
    // ==========================================================
    function tampilkanTombolMulai(jarakKm, durasiMenit) {
        kontrolNavigasi.innerHTML = `
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h2 class="h5 mb-3">🧭 Rute Siap Digunakan</h2>
                    <p class="text-muted">
                        Rute menuju tujuan sudah ditemukan. Tekan tombol berikut untuk mulai memantau posisi kamu selama perjalanan.
                    </p>
                    <div class="mb-3">
                        <div><strong>Jarak:</strong> ${jarakKm} km</div>
                        <div><strong>Perkiraan waktu:</strong> ${durasiMenit} menit</div>
                    </div>
                    <button type="button" id="btn-mulai-perjalanan" class="btn btn-success btn-mulai-perjalanan">
                        ▶ Mulai Perjalanan
                    </button>
                </div>
            </div>
        `;

        document.getElementById('btn-mulai-perjalanan').addEventListener('click', mulaiNavigasiAktif);
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
        hambatanSudahDiumumkan.clear();
        langkahSudahDiumumkan.clear();

        // Lewati step "depart" jika pertama kali
        langkahBerikutnyaIndex = 0;
        if (langkahNavigasi.length > 0 && langkahNavigasi[0].type === 'depart') {
            langkahBerikutnyaIndex = 1;
        }

        kontrolNavigasi.innerHTML = `
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h2 class="h5 mb-3">🧭 Navigasi Sedang Berjalan</h2>
                    <div class="alert alert-info">
                        <strong>Tujuan:</strong> ${escapeHtml(tujuanNavigasi.nama)}
                    </div>
                    <div id="info-posisi">📍 Menunggu pembaruan posisi GPS...</div>
                    <div class="mt-3">
                        <button type="button" id="btn-hentikan-navigasi" class="btn btn-danger btn-hentikan-navigasi">
                            ⏹ Hentikan Navigasi
                        </button>
                    </div>
                </div>
            </div>
        `;

        document.getElementById('btn-hentikan-navigasi').addEventListener('click', hentikanNavigasi);

        bacakanTeks('Navigasi dimulai. Tujuan kamu adalah ' + tujuanNavigasi.nama);
        mulaiPemantauanGPS();
    }

    // ==========================================================
    // PEMANTAUAN GPS (WATCH POSITION)
    // ==========================================================
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
                tampilkanPesanPerjalanan('Posisi GPS tidak dapat diperbarui. Pastikan lokasi perangkat tetap aktif.', 'error');
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
                color: '#ffffff',
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
        if (!petaNavigasi || !geometry || !Array.isArray(geometry.coordinates)) return;

        const koordinat = geometry.coordinates.map(function (item) {
            return [item[1], item[0]];
        });

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
                <h3>📍 Posisi Saat Ini</h3>
                <p><strong>Latitude:</strong> ${posisiSekarang.latitude}</p>
                <p><strong>Longitude:</strong> ${posisiSekarang.longitude}</p>
                <p><strong>Akurasi GPS:</strong> ±${Math.round(posisiSekarang.accuracy)} meter</p>
                <p><strong>Jarak ke tujuan:</strong> ${jarakText}</p>
                <p class="status-gps">🔄 Posisi GPS sedang diperbarui.</p>
            </div>
        `;
    }

    // ==========================================================
    // PENGECEKAN HAMBATAN
    // ==========================================================
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
                return response.text().then(text => {
                    console.error('Response error:', text);
                    throw new Error('Gagal mengecek hambatan.');
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                hambatanNavigasi = data.hambatan || [];
                tampilkanHambatan(hambatanNavigasi);
            } else {
                tampilkanPesanHambatan(data.message || 'Pengecekan hambatan gagal.', 'error');
            }
        })
        .catch(error => {
            console.error('Hambatan Error:', error);
            tampilkanPesanHambatan('Terjadi kesalahan saat mengecek hambatan.', 'error');
        });
    }

    function cekHambatanTerdekat(latitude, longitude) {
        if (!hambatanNavigasi || hambatanNavigasi.length === 0) return;

        hambatanNavigasi.forEach(function (hambatan) {
            if (!hambatan.latitude || !hambatan.longitude) return;

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

        let jarakText = jarak < 1 ? 'kurang dari satu meter'
            : (jarak >= 1000 ? (jarak / 1000).toFixed(1) + ' kilometer' : Math.round(jarak) + ' meter');

        const namaHambatan = hambatan.judul || 'hambatan aksesibilitas';
        const pesan = `Peringatan. Terdapat ${namaHambatan} sekitar ${jarakText} dari posisi kamu.`;

        tampilkanPeringatanAktif(pesan);
        bacakanTeks(pesan);
    }

    function tampilkanPeringatanAktif(pesan) {
        statusPerjalanan.innerHTML = `
            <div class="alert alert-warning" role="alert" aria-live="assertive">
                <strong>⚠️ Peringatan Hambatan</strong>
                <div class="mt-2">${escapeHtml(pesan)}</div>
            </div>
        `;
    }

    function tampilkanHambatan(hambatan) {
        if (!hambatan || hambatan.length === 0) {
            const pesan = 'Tidak ditemukan laporan hambatan aksesibilitas terverifikasi di sepanjang rute yang diperiksa.';
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
                <h3>⚠️ Hambatan ditemukan: ${hambatan.length}</h3>
        `;

        hambatan.forEach(function (item, index) {
            html += `
                <article class="hambatan-card">
                    <h4>Hambatan ${index + 1}: ${escapeHtml(item.judul)}</h4>
                    <p><strong>Kategori:</strong> ${escapeHtml(item.kategori || 'Tidak diketahui')}</p>
                    <p><strong>Jarak dari rute:</strong> ${item.jarak_dari_rute} meter</p>
                    <p><strong>Prioritas:</strong> ${escapeHtml(item.tingkat_prioritas || 'Belum dinilai')}</p>
                    <p><strong>Lokasi:</strong> ${escapeHtml(item.alamat || 'Alamat tidak tersedia')}</p>
                </article>
            `;
        });

        html += `</div>`;
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
        const classAlert = tipe === 'error' ? 'alert alert-danger' : 'alert alert-info';
        statusPerjalanan.innerHTML = `
            <div class="${classAlert}" role="alert" aria-live="polite">
                ${escapeHtml(pesan)}
            </div>
        `;
    }

    function sampaiTujuan(jarak) {
        if (!navigasiAktif) return;

        navigasiAktif = false;
        hentikanPemantauanGPS();

        const pesan = 'Anda telah sampai di tujuan ' + tujuanNavigasi.nama;

        statusPerjalanan.innerHTML = `
            <div class="alert alert-success" role="status" aria-live="assertive">
                <strong>🏁 Tujuan Tercapai</strong>
                <div class="mt-2">${escapeHtml(tujuanNavigasi.nama)}</div>
                <div class="mt-2">Anda telah sampai di tujuan.</div>
            </div>
        `;

        bacakanTeks(pesan);
    }

    // ==========================================================
    // VOICE GUIDANCE
    // ==========================================================
    function prosesVoiceGuidance(latitude, longitude) {
        if (!navigasiAktif) return;

        if (!langkahNavigasi.length) {
            console.log('Tidak ada langkah navigasi.');
            return;
        }

        const langkah = langkahNavigasi[langkahBerikutnyaIndex];

        if (!langkah) {
            console.log('Semua langkah navigasi sudah selesai.');
            return;
        }

        if (langkah.latitude === null || langkah.longitude === null) {
            console.log('Koordinat langkah tidak tersedia:', langkah);
            langkahBerikutnyaIndex++;
            return;
        }

        const jarak = hitungJarakMeter(
            latitude,
            longitude,
            langkah.latitude,
            langkah.longitude
        );

        console.log(
            'LANGKAH BERIKUTNYA:',
            langkahBerikutnyaIndex,
            '|',
            langkah.type,
            '|',
            langkah.modifier,
            '|',
            langkah.name,
            '| Jarak:',
            jarak.toFixed(1),
            'meter'
        );

        // 1. PERINGATAN AWAL (PRA-ARAHAN)
        if (
            jarak <= BATAS_PRA_ARAHAN &&
            jarak > BATAS_ARAHAN &&
            !langkahSudahDiumumkan.has(langkahBerikutnyaIndex)
        ) {
            const instruksiAwal = buatInstruksiSuara(langkah, 'awal');
            if (instruksiAwal) {
                console.log('VOICE PRA-ARAHAN:', instruksiAwal);
                bacakanTeks(instruksiAwal);
                langkahSudahDiumumkan.add(langkahBerikutnyaIndex);
            }
        }

        // 2. EKSEKUSI ARAHAN
        if (jarak <= BATAS_ARAHAN) {
            const instruksi = buatInstruksiSuara(langkah, 'aksi');
            if (instruksi) {
                console.log('VOICE GUIDANCE:', instruksi);
                bacakanTeks(instruksi);
            }

            langkahBerikutnyaIndex++;
            console.log('PINDAH KE LANGKAH:', langkahBerikutnyaIndex);

            // Informasi jalan berikutnya jika ada perubahan nama jalan
            const langkahBerikutnya = langkahNavigasi[langkahBerikutnyaIndex];
            if (
                langkahBerikutnya &&
                langkahBerikutnya.type === 'new name' &&
                langkahBerikutnya.name
            ) {
                const pesanJalan = `Lanjut di ${langkahBerikutnya.name}.`;
                console.log('VOICE JALAN:', pesanJalan);
                bacakanTeks(pesanJalan);
            }
        }
    }

    function buatInstruksiSuara(langkah, mode = 'aksi') {
        const type = langkah.type;
        const modifier = langkah.modifier;
        const namaJalan = langkah.name;

        if (type === 'depart') {
            return namaJalan ? `Mulai perjalanan melalui ${namaJalan}.` : 'Mulai perjalanan.';
        }

        if (type === 'arrive') {
            return 'Anda sudah mendekati tujuan.';
        }

        if (type === 'turn' || type === 'continue' || type === 'end of road') {
            if (modifier === 'left') {
                return mode === 'awal' ? 'Dalam 80 meter, belok kiri.' : (namaJalan ? `Belok kiri menuju ${namaJalan}.` : 'Belok kiri.');
            }
            if (modifier === 'right') {
                return mode === 'awal' ? 'Dalam 80 meter, belok kanan.' : (namaJalan ? `Belok kanan menuju ${namaJalan}.` : 'Belok kanan.');
            }
            if (modifier === 'slight left') {
                return mode === 'awal' ? 'Dalam 80 meter, ambil arah sedikit ke kiri.' : 'Ambil arah sedikit ke kiri.';
            }
            if (modifier === 'slight right') {
                return mode === 'awal' ? 'Dalam 80 meter, ambil arah sedikit ke kanan.' : 'Ambil arah sedikit ke kanan.';
            }
            if (modifier === 'sharp left') {
                return mode === 'awal' ? 'Dalam 80 meter, belok tajam ke kiri.' : 'Belok tajam ke kiri.';
            }
            if (modifier === 'sharp right') {
                return mode === 'awal' ? 'Dalam 80 meter, belok tajam ke kanan.' : 'Belok tajam ke kanan.';
            }
            return namaJalan ? `Lanjut melalui ${namaJalan}.` : 'Lanjut mengikuti rute.';
        }

        if (type === 'roundabout') {
            return mode === 'awal' ? 'Dalam 80 meter, masuk ke bundaran dan ikuti rute.' : 'Masuk bundaran dan ikuti rute.';
        }

        if (type === 'merge') {
            return mode === 'awal' ? 'Dalam 80 meter, bergabung ke jalur berikutnya.' : 'Bergabung ke jalur berikutnya.';
        }

        if (type === 'fork') {
            return mode === 'awal' ? 'Dalam 80 meter, ambil percabangan sesuai rute.' : 'Ambil percabangan sesuai rute.';
        }

        if (type === 'on ramp') {
            return mode === 'awal' ? 'Dalam 80 meter, masuk ke jalur berikutnya.' : 'Masuk ke jalur berikutnya.';
        }

        if (type === 'off ramp') {
            return mode === 'awal' ? 'Dalam 80 meter, keluar dari jalur sesuai rute.' : 'Keluar dari jalur sesuai rute.';
        }

        return null;
    }

    // ==========================================================
    // NAVIGASI DILANJUTKAN / DIHENTIKAN
    // ==========================================================
    function hentikanNavigasi() {
        navigasiAktif = false;
        hentikanPemantauanGPS();

        statusPerjalanan.innerHTML = `
            <div class="alert alert-secondary" role="status" aria-live="polite">
                ⏹ Navigasi dihentikan.
            </div>
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

    // ==========================================================
    // FUNGSI UTILITAS
    // ==========================================================
    function hitungJarakMeter(lat1, lon1, lat2, lon2) {
        const earthRadius = 6371000;
        const lat1Rad = degToRad(lat1);
        const lat2Rad = degToRad(lat2);
        const deltaLat = degToRad(lat2 - lat1);
        const deltaLon = degToRad(lon2 - lon1);

        const a = Math.sin(deltaLat / 2) * Math.sin(deltaLat / 2) +
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
        return (tujuanNavigasi && tujuanNavigasi.nama) ? tujuanNavigasi.nama : 'Tujuan tidak diketahui';
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
</script>

@endpush