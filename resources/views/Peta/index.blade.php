@extends('layouts.app')

@section('title', 'Peta Interaktif')

@push('styles')

<style>
    .smartpath-map-page {
        min-height: 100vh;
        background: #f8fafc;
        overflow-x: hidden;
    }

    /* Layout utama: daftar hambatan + ringkasan di atas,
       lalu sidebar filter + peta di bawah. */
    .smartpath-map-shell {
        display: grid;
        grid-template-columns: 270px minmax(0, 1fr);
        width: 100%;
        min-height: 650px;
        background: #f8fafc;
    }

    .smartpath-map-sidebar {
        background: #ffffff;
        border-right: 1px solid #e2e8f0;
        overflow: hidden;
        z-index: 900;
        min-height: 774px;
    }

    .smartpath-map-content {
        min-width: 0;
        min-height: 774px;
        position: relative;
        display: flex;
        flex-direction: column;
        background: #f8fafc;
    }

    #map-container {
        width: 100%;
        height: auto;
        min-height: 500px;
        background: #e2e8f0;
        flex: 1 1 auto;
    }

    .map-filter-section {
        padding: 15px;
        border-bottom: 1px solid #e2e8f0;
    }

    .map-filter-title {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 10px;
        color: #0f172a;
        font-size: 13px;
        font-weight: 700;
    }

    .map-filter-description {
        margin-bottom: 10px;
        color: #64748b;
        font-size: 12px;
        line-height: 1.6;
    }

    .map-check {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 5px 0;
        color: #475569;
        font-size: 12px;
        cursor: pointer;
    }

    .map-check input {
        width: 15px;
        height: 15px;
        accent-color: #059669;
    }

    .map-status-dot {
        width: 8px;
        height: 8px;
        flex: 0 0 auto;
        border-radius: 50%;
    }

    .map-status-dot.verified {
        background: #059669;
    }

    .map-status-dot.progress {
        background: #0d9488;
    }

    .map-status-dot.done {
        background: #16a34a;
    }

    .map-legend-item {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 4px 0;
        color: #475569;
        font-size: 12px;
    }

    .map-legend-pin {
        width: 11px;
        height: 11px;
        border-radius: 50%;
        border: 2px solid #fff;
        box-shadow: 0 0 0 1px rgba(15, 23, 42, 0.12);
    }

    .map-legend-pin.high {
        background: #dc2626;
    }

    .map-legend-pin.medium {
        background: #d97706;
    }

    .map-legend-pin.low {
        background: #16a34a;
    }

    .facility-marker {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 3px solid #ffffff;
        background: #0d9488;
        color: #ffffff;
        box-shadow: 0 3px 10px rgba(15, 23, 42, 0.25);
    }

    .facility-marker svg {
        width: 12px;
        height: 12px;
    }

    .smartpath-pin-wrapper {
        position: relative;
        width: 34px;
        height: 42px;
    }

    .smartpath-pin-pulse {
        position: absolute;
        left: 50%;
        top: 11px;
        width: 18px;
        height: 18px;
        transform: translate(-50%, -50%);
        border-radius: 50%;
        opacity: 0;
    }

    .smartpath-pin {
        position: absolute;
        left: 50%;
        top: 2px;
        width: 27px;
        height: 27px;
        transform: translateX(-50%) rotate(-45deg);
        border-radius: 50% 50% 50% 0;
        border: 3px solid #ffffff;
        box-shadow: 0 3px 9px rgba(15, 23, 42, 0.30);
    }

    .smartpath-pin::after {
        content: "";
        position: absolute;
        left: 7px;
        top: 7px;
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: #ffffff;
    }

    .smartpath-pin-wrapper.high .smartpath-pin {
        background: #dc2626;
    }

    .smartpath-pin-wrapper.medium .smartpath-pin {
        background: #d97706;
    }

    .smartpath-pin-wrapper.low .smartpath-pin {
        background: #16a34a;
    }

    .smartpath-pin-wrapper.high .smartpath-pin-pulse {
        background: rgba(220, 38, 38, 0.20);
        animation: smartpath-map-pulse 2s infinite;
    }

    @keyframes smartpath-map-pulse {
        0% {
            transform: translate(-50%, -50%) scale(.8);
            opacity: .8;
        }

        70% {
            transform: translate(-50%, -50%) scale(2.3);
            opacity: 0;
        }

        100% {
            opacity: 0;
        }
    }

    .leaflet-popup-content-wrapper {
        border-radius: 12px;
    }

    .leaflet-popup-content {
        margin: 12px 14px;
    }

    /* Keep Leaflet controls clear of the summary cards. */
    .leaflet-top.leaflet-left {
        top: 88px;
        left: 8px;
    }


    .smartpath-popup {
        width: 250px;
        font-family: Inter, sans-serif;
    }

    .smartpath-popup-image {
        width: 100%;
        height: 125px;
        object-fit: cover;
        border-radius: 8px;
        margin-bottom: 9px;
        background: #f1f5f9;
    }

    .smartpath-popup-title {
        color: #0f172a;
        font-size: 14px;
        font-weight: 700;
        line-height: 1.4;
    }

    .smartpath-popup-category {
        margin-top: 4px;
        color: #059669;
        font-size: 11px;
        font-weight: 600;
    }

    .smartpath-popup-address {
        margin-top: 7px;
        color: #64748b;
        font-size: 11px;
        line-height: 1.55;
    }

    .smartpath-popup-meta {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-wrap: wrap;
        margin-top: 9px;
    }

    .smartpath-popup-badge {
        display: inline-flex;
        padding: 4px 7px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 700;
    }

    .smartpath-popup-score {
        color: #64748b;
        font-size: 12px;
    }

    @media (max-width: 1023px) {

        .smartpath-map-shell {
            grid-template-columns: 1fr;
            min-height: 600px;
        }

        .smartpath-map-sidebar {
            position: fixed;
            inset: 64px auto 0 0;
            width: 290px;
            max-height: calc(100vh - 64px);
            transform: translateX(-100%);
            transition: transform .25s ease;
            box-shadow: 8px 0 24px rgba(15, 23, 42, .10);
            overflow-y: auto;
        }

        .smartpath-map-sidebar.open {
            transform: translateX(0);
        }

        .smartpath-map-content {
            width: 100%;
            min-height: 600px;
        }

        .smartpath-map-sidebar {
            min-height: 0;
        }

        #map-container {
            height: 600px;
            flex-basis: 600px;
        }

    }

    @media (max-width: 640px) {

        .smartpath-map-shell,
        .smartpath-map-content {
            min-height: 520px;
        }

        #map-container {
            height: 520px;
            min-height: 420px;
            flex-basis: 520px;
        }

    }

    /* ==============================
       NAVBAR DAFTAR HAMBATAN TERDEKAT
       ============================== */

    .nearby-navbar {
        position: absolute;
        top: 16px;
        left: 16px;
        right: 16px;
        z-index: 1000;
        min-height: 64px;
        padding: 12px 24px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        margin: 0;

        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;

        box-shadow: 0 4px 14px rgba(15, 23, 42, 0.08);
    }

    .nearby-navbar-title {
        display: flex;
        align-items: center;
        gap: 9px;
        margin: 0;

        color: #0f172a;
        font-size: 17px;
        font-weight: 700;
    }

    .nearby-location-btn {
        border: 0;
        border-radius: 8px;
        padding: 10px 16px;

        background: #047857;
        color: #ffffff;

        font-size: 12px;
        font-weight: 700;

        cursor: pointer;

        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
    }

    .nearby-location-btn:hover {
        background: #065f46;
    }

    .nearby-location-btn:disabled {
        opacity: .65;
        cursor: wait;
    }

    .nearby-location-btn:focus-visible {
        outline: 3px solid #34d399;
        outline-offset: 3px;
    }

    .leaflet-top.leaflet-left {
        top: 88px;
        left: 8px;
    }

    /* ==============================
       SUMMARY DI DALAM AREA PETA
       ============================== */

    .map-summary {
        position: absolute;
        top: auto;
        bottom: 16px;
        left: auto;
        right: 16px;

        z-index: 1000;

        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 10px;

        width: min(510px, calc(100% - 32px));
        margin: 0;

        background: transparent;
        border: 0;
        border-radius: 0;
        box-shadow: none;
        overflow: visible;

        pointer-events: none;
    }

    .map-summary-item {
        min-width: 0;
        min-height: 72px;
        background: rgba(255, 255, 255, 0.96);
        padding: 10px 12px;
        text-align: center;

        border: 1px solid #e2e8f0;
        border-radius: 10px;

        box-shadow: 0 6px 18px rgba(15, 23, 42, 0.12);
        pointer-events: auto;
    }

    .map-summary-item:last-child {
        border-right: 1px solid #e2e8f0;
    }

    .map-summary-number {
        display: block;
        color: #047857;
        font-size: 18px;
        font-weight: 700;
    }

    .map-summary-label {
        display: block;
        margin-top: 3px;
        color: #64748b;
        font-size: 12px;
    }

    @media (max-width: 1023px) {
        .nearby-navbar {
            top: 12px;
            left: 12px;
            right: 12px;
        }

        .map-summary {
            right: 12px;
            width: min(480px, calc(100% - 24px));
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 8px;
        }

        .map-summary-item {
            padding: 9px 6px;
        }
    }

    @media (max-width: 640px) {
        .nearby-navbar {
            top: 8px;
            left: 8px;
            right: 8px;
            min-height: 56px;
            padding: 9px 10px;
            gap: 8px;
        }

        .nearby-navbar-title {
            min-width: 0;
            flex: 1 1 auto;
            gap: 6px;
            font-size: 13px;
            line-height: 1.35;
        }

        .nearby-location-btn {
            flex: 0 0 auto;
            gap: 6px;
            min-height: 38px;
            padding: 8px 9px;
            font-size: 12px;
            white-space: nowrap;
        }

        .map-summary {
            right: 8px;
            width: min(420px, calc(100% - 16px));
            top: auto;
            bottom: 10px;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 5px;
        }

        .map-summary-item {
            min-height: 60px;
            padding: 7px 4px;
        }

        .map-summary-number {
            font-size: 14px;
        }

        .map-summary-label {
            font-size: 12px;
        }

        .leaflet-top.leaflet-left {
            top: 78px;
            left: 6px;
        }
    }

    @media (max-width: 380px) {
        .nearby-navbar-title {
            font-size: 11px;
        }

        .nearby-location-btn {
            padding-inline: 7px;
            font-size: 12px;
        }

        .map-summary-label {
            font-size: 12px;
        }
    }

</style>

@endpush

@section('content')

<div class="smartpath-map-page">

    <div class="smartpath-map-shell">

        <aside
            id="map-sidebar"
            class="smartpath-map-sidebar"
            aria-label="Filter peta"
        >

            <div class="map-filter-section">

                <div class="map-filter-title">
                    <span aria-hidden="true">☷</span>
                    <span>Filter Peta</span>
                </div>

                <p class="map-filter-description">
                    Sesuaikan tampilan data pada peta
                </p>

                <button
                    id="close-sidebar"
                    type="button"
                    class="lg:hidden"
                    aria-label="Tutup filter"
                >
                    Tutup
                </button>

            </div>

            <div class="map-filter-section">
                <div class="map-filter-title">Status Laporan</div>

                <label class="map-check">
                    <input type="checkbox" class="filter-status" value="diverifikasi" checked>
                    <span class="map-status-dot verified"></span>
                    <span>Diverifikasi</span>
                </label>

                <label class="map-check">
                    <input type="checkbox" class="filter-status" value="dalam_perbaikan" checked>
                    <span class="map-status-dot progress"></span>
                    <span>Dalam Perbaikan</span>
                </label>

                <label class="map-check">
                    <input type="checkbox" class="filter-status" value="selesai" checked>
                    <span class="map-status-dot done"></span>
                    <span>Selesai</span>
                </label>
            </div>

            <div class="map-filter-section">
                <div class="map-filter-title">Kategori Hambatan</div>

                @foreach($kategoriHambatan as $kategori)
                    <label class="map-check">
                        <input
                            type="checkbox"
                            class="filter-kategori"
                            value="{{ $kategori->id }}"
                            checked
                        >
                        <span
                            class="map-status-dot"
                            style="background: {{ $kategori->warna_penanda ?: '#64748b' }}"
                        ></span>
                        <span>{{ $kategori->nama }}</span>
                    </label>
                @endforeach
            </div>

            <div class="map-filter-section">
                <label class="map-check">
                    <input type="checkbox" id="toggle-fasilitas">
                    <span class="map-status-dot" style="background:#0d9488"></span>
                    <span>Tampilkan Fasilitas Publik</span>
                </label>
            </div>

            <div class="map-filter-section">
                <div class="map-filter-title">Prioritas</div>

                <div class="map-legend-item">
                    <span class="map-legend-pin high"></span>
                    <span>Tinggi skor ≥ 70</span>
                </div>
                <div class="map-legend-item">
                    <span class="map-legend-pin medium"></span>
                    <span>Sedang skor 40–69</span>
                </div>
                <div class="map-legend-item">
                    <span class="map-legend-pin low"></span>
                    <span>Rendah skor &lt; 40</span>
                </div>
            </div>

            <div class="map-filter-section">
                <button
                    id="toggle-sidebar"
                    type="button"
                    class="nearby-button"
                    aria-label="Buka atau tutup filter peta"
                >
                    Filter Peta
                </button>
            </div>

        </aside>

        <section
            class="smartpath-map-content"
            aria-label="Peta interaktif hambatan aksesibilitas"
            role="region"
        >
            <header class="nearby-navbar" aria-labelledby="nearby-map-title">
                <h1 id="nearby-map-title" class="nearby-navbar-title">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true" focusable="false">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z" />
                        <circle cx="12" cy="10" r="2.5" />
                    </svg>
                    <span>Daftar Hambatan Terdekat</span>
                </h1>

                <button
                    id="nearby-location"
                    type="button"
                    class="nearby-location-btn"
                    aria-label="Gunakan lokasi saya untuk mencari hambatan terdekat"
                >
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true" focusable="false">
                        <circle cx="12" cy="12" r="3" />
                        <path stroke-linecap="round" d="M12 2v3m0 14v3M2 12h3m14 0h3" />
                    </svg>
                    <span>Gunakan Lokasi Saya</span>
                </button>
            </header>

            <section class="map-summary" aria-label="Ringkasan peta">

                <div class="map-summary-item">
                    <span id="summary-total" class="map-summary-number">0</span>
                    <span class="map-summary-label">Total Marker</span>
                </div>

                <div class="map-summary-item">
                    <span id="summary-high" class="map-summary-number">0</span>
                    <span class="map-summary-label">Prioritas Tinggi</span>
                </div>

                <div class="map-summary-item">
                    <span id="summary-area" class="map-summary-number">Depok</span>
                    <span class="map-summary-label">Wilayah</span>
                </div>

            </section>

            <div id="map-container"></div>
        </section>

    </div>



</div>

@endsection


@push('scripts')

<script>
document.addEventListener('DOMContentLoaded', function () {

    'use strict';

    const mapElement =
        document.getElementById('map-container');

    if (!mapElement || typeof window.L === 'undefined') {
        console.error(
            'SmartPath: Leaflet tidak tersedia pada halaman Peta.'
        );

        return;
    }

    const sidebar =
        document.getElementById('map-sidebar');

    const toggleSidebar =
        document.getElementById('toggle-sidebar');

    const closeSidebar =
        document.getElementById('close-sidebar');

    const map =
        window.L.map(
            mapElement,
            {
                center: [
                    -6.4025,
                    106.7942
                ],
                zoom: 13,
                zoomControl: true
            }
        );

    window.L.tileLayer(
        'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
        {
            maxZoom: 19,
            attribution:
                '&copy; OpenStreetMap contributors'
        }
    ).addTo(map);


    const laporanLayer =
        window.L.layerGroup().addTo(map);

    const fasilitasLayer =
        window.L.layerGroup();


    let laporanData = [];

    let fasilitasData = [];

    const reportMarkers = new Map();

    let userLocationMarker = null;

    let userLocationCircle = null;

    let initialFitDone = false;


    function escapeHtml(value) {

        return String(value ?? '').replace(
            /[&<>'"]/g,
            function (character) {

                const entities = {
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    "'": '&#039;',
                    '"': '&quot;'
                };

                return entities[character];
            }
        );
    }


    function getPriority(score) {

        if (score === null || score === undefined || score === '') {
            return {
                key: 'low',
                label: 'Belum Dinilai',
                color: '#64748b'
            };
        }

        const value =
            Number(score);

        if (value >= 70) {
            return {
                key: 'high',
                label: 'Tinggi',
                color: '#dc2626'
            };
        }

        if (value >= 40) {
            return {
                key: 'medium',
                label: 'Sedang',
                color: '#d97706'
            };
        }

        return {
            key: 'low',
            label: 'Rendah',
            color: '#16a34a'
        };
    }


    function createReportIcon(report) {

        const priority =
            getPriority(
                report.skor_prioritas
            );

        return window.L.divIcon({

            className:
                'smartpath-report-icon',

            html: `
                <div
                    class="smartpath-pin-wrapper ${priority.key}"
                    role="img"
                    aria-label="Marker laporan ${escapeHtml(priority.label)}"
                >
                    <span class="smartpath-pin-pulse"></span>
                    <span class="smartpath-pin"></span>
                </div>
            `,

            iconSize: [
                34,
                42
            ],

            iconAnchor: [
                17,
                41
            ],

            popupAnchor: [
                0,
                -36
            ]
        });
    }


    function createReportPopup(report) {

        const priority =
            getPriority(
                report.skor_prioritas
            );

        const scoreText =
            report.skor_prioritas === null
                || report.skor_prioritas === undefined
                ? 'Belum dinilai'
                : Number(
                    report.skor_prioritas
                ).toFixed(2);

        const image =
            report.foto_utama
                ? `
                    <img
                        class="smartpath-popup-image"
                        src="${escapeHtml(report.foto_utama)}"
                        alt="Foto hambatan ${escapeHtml(report.judul)}"
                    >
                `
                : '';

        return `
            <article class="smartpath-popup">

                ${image}

                <div class="smartpath-popup-title">
                    ${escapeHtml(report.judul || 'Laporan Hambatan')}
                </div>

                <div class="smartpath-popup-category">
                    ${escapeHtml(report.kategori || 'Kategori tidak tersedia')}
                </div>

                <div class="smartpath-popup-address">
                    ${escapeHtml(
                        report.alamat_lengkap
                        || 'Alamat tidak tersedia'
                    )}
                </div>

                <div class="smartpath-popup-meta">

                    <span
                        class="smartpath-popup-badge"
                        style="
                            color:${priority.color};
                            background:${priority.color}15;
                            border:1px solid ${priority.color}35;
                        "
                    >
                        Prioritas ${escapeHtml(priority.label)}
                    </span>

                    <span class="smartpath-popup-score">
                        Skor ${escapeHtml(scoreText)}
                    </span>

                </div>

                <div
                    style="
                        margin-top:7px;
                        color:#64748b;
                        font-size:12px;
                    "
                >
                    ${escapeHtml(report.status_label || report.status)}
                    ·
                    ${escapeHtml(report.jumlah_pelapor || 1)}
                    pelapor
                </div>

            </article>
        `;
    }


    function renderReports() {

        laporanLayer.clearLayers();
        reportMarkers.clear();

        const activeStatuses =
            Array.from(
                document.querySelectorAll(
                    '.filter-status:checked'
                )
            ).map(
                checkbox => checkbox.value
            );

        const activeCategories =
            Array.from(
                document.querySelectorAll(
                    '.filter-kategori:checked'
                )
            ).map(
                checkbox => String(
                    checkbox.value
                )
            );

        const visibleReports =
            laporanData.filter(function (report) {

                const statusAllowed =
                    activeStatuses.includes(
                        report.status
                    );

                const categoryAllowed =
                    activeCategories.length === 0
                        ||
                        activeCategories.includes(
                            String(
                                report.kategori_id
                            )
                        );

                return (
                    statusAllowed
                    &&
                    categoryAllowed
                );
            });


        visibleReports.forEach(
            function (report) {

                const latitude =
                    Number(report.latitude);

                const longitude =
                    Number(report.longitude);

                if (
                    !Number.isFinite(latitude)
                    ||
                    !Number.isFinite(longitude)
                ) {
                    return;
                }

                const marker =
                    window.L.marker(
                        [
                            latitude,
                            longitude
                        ],
                        {
                            icon:
                                createReportIcon(
                                    report
                                ),
                            title:
                                report.judul,
                            alt:
                                `Lokasi hambatan ${report.judul}`
                        }
                    );

                marker.bindPopup(
                    createReportPopup(
                        report
                    ),
                    {
                        maxWidth: 320,
                        minWidth: 220
                    }
                );

                laporanLayer.addLayer(
                    marker
                );

                reportMarkers.set(
                    String(report.id ?? report.uuid ?? report.judul),
                    marker
                );


                const markerElement =
                    marker.getElement();

                if (markerElement) {

                    const priority =
                        getPriority(
                            report.skor_prioritas
                        );

                    markerElement.setAttribute(
                        'role',
                        'button'
                    );

                    markerElement.setAttribute(
                        'tabindex',
                        '0'
                    );

                    markerElement.setAttribute(
                        'aria-label',
                        `Laporan ${
                            report.judul || 'hambatan aksesibilitas'
                        }. Prioritas ${
                            priority.label
                        }. Skor ${
                            report.skor_prioritas ?? 'belum dinilai'
                        }.`
                    );

                    markerElement.addEventListener(
                        'keydown',
                        function (event) {

                            if (
                                event.key === 'Enter'
                                ||
                                event.key === ' '
                            ) {

                                event.preventDefault();

                                marker.openPopup();

                            }

                        }
                    );
                }
            }
        );




        const highCount =
            visibleReports.filter(
                function (report) {
                    return (
                        report.skor_prioritas !== null
                        &&
                        Number(
                            report.skor_prioritas
                        ) >= 70
                    );
                }
            ).length;


        document.getElementById(
            'summary-total'
        ).textContent =
            visibleReports.length;

        document.getElementById(
            'summary-high'
        ).textContent =
            highCount;


        if (
            !initialFitDone
            &&
            visibleReports.length > 0
        ) {

            const points =
                visibleReports
                    .map(
                        report => [
                            Number(report.latitude),
                            Number(report.longitude)
                        ]
                    )
                    .filter(
                        point =>
                            Number.isFinite(point[0])
                            &&
                            Number.isFinite(point[1])
                    );

            if (points.length === 1) {

                map.setView(
                    points[0],
                    16
                );

            } else if (points.length > 1) {

                map.fitBounds(
                    points,
                    {
                        padding: [
                            30,
                            30
                        ],
                        maxZoom: 15
                    }
                );
            }

            initialFitDone = true;
        }
    }


    function renderFacilities() {

        fasilitasLayer.clearLayers();

        fasilitasData.forEach(
            function (facility) {

                const latitude =
                    Number(
                        facility.latitude
                    );

                const longitude =
                    Number(
                        facility.longitude
                    );

                if (
                    !Number.isFinite(latitude)
                    ||
                    !Number.isFinite(longitude)
                ) {
                    return;
                }

                const icon =
                    window.L.divIcon({

                        className:
                            'smartpath-facility-icon',

                        html: `
                            <div class="facility-marker">
                                <svg
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M4 20h16M6 20V7a2 2 0 012-2h8a2 2 0 012 2v13M9 9h1m-1 3h1m4-3h1m-1 3h1M9 20v-4h6v4"
                                    />
                                </svg>
                            </div>
                        `,

                        iconSize: [
                            24,
                            24
                        ],

                        iconAnchor: [
                            12,
                            12
                        ]
                    });

                const marker =
                    window.L.marker(
                        [
                            latitude,
                            longitude
                        ],
                        {
                            icon: icon,
                            title: facility.nama
                        }
                    );

                marker.bindPopup(`
                    <div style="min-width:190px;font-family:Inter,sans-serif;">
                        <strong style="color:#0f172a;font-size:13px;">
                            ${escapeHtml(facility.nama)}
                        </strong>

                        <div style="margin-top:4px;color:#0d9488;font-size:11px;font-weight:600;">
                            ${escapeHtml(facility.jenis_label)}
                        </div>

                        <div style="margin-top:5px;color:#64748b;font-size:12px;line-height:1.5;">
                            ${escapeHtml(facility.alamat || 'Alamat tidak tersedia')}
                        </div>
                    </div>
                `);

                fasilitasLayer.addLayer(
                    marker
                );
            }
        );
    }


    async function loadReports() {

        try {

            const response =
                await fetch(
                    '{{ route("peta.data") }}',
                    {
                        headers: {
                            'Accept':
                                'application/json'
                        }
                    }
                );

            if (!response.ok) {
                throw new Error(
                    'Gagal mengambil laporan.'
                );
            }

            laporanData =
                await response.json();

            renderReports();

        } catch (error) {

            console.error(
                'SmartPath Peta:',
                error
            );
        }
    }


    async function loadFacilities() {

        try {

            const response =
                await fetch(
                    '{{ route("peta.fasilitas") }}',
                    {
                        headers: {
                            'Accept':
                                'application/json'
                        }
                    }
                );

            if (!response.ok) {
                throw new Error(
                    'Gagal mengambil fasilitas.'
                );
            }

            fasilitasData =
                await response.json();

            renderFacilities();

        } catch (error) {

            console.error(
                'SmartPath Fasilitas:',
                error
            );
        }
    }


    document
        .querySelectorAll(
            '.filter-status, .filter-kategori'
        )
        .forEach(
            function (element) {

                element.addEventListener(
                    'change',
                    renderReports
                );
            }
        );


    const fasilitasToggle =
        document.getElementById(
            'toggle-fasilitas'
        );

    fasilitasToggle.addEventListener(
        'change',
        function () {

            if (this.checked) {
                fasilitasLayer.addTo(map);
            } else {
                map.removeLayer(
                    fasilitasLayer
                );
            }
        }
    );


    document
        .getElementById('nearby-location')
        .addEventListener(
            'click',
            function () {

                const button = this;

                if (!navigator.geolocation) {
                    alert('Browser tidak mendukung fitur lokasi.');
                    return;
                }

                button.disabled = true;
                button.innerHTML = '<span aria-hidden="true">⌖</span><span>Mengambil...</span>';

                navigator.geolocation.getCurrentPosition(
                    function (position) {

                        const userLat = position.coords.latitude;
                        const userLng = position.coords.longitude;
                        const radius = 50;

                        if (userLocationMarker) {
                            map.removeLayer(userLocationMarker);
                        }

                        if (userLocationCircle) {
                            map.removeLayer(userLocationCircle);
                        }

                        userLocationMarker = window.L.circleMarker(
                            [userLat, userLng],
                            {
                                radius: 7,
                                color: '#ffffff',
                                weight: 3,
                                fillColor: '#2563eb',
                                fillOpacity: 1,
                                zIndexOffset: 1000
                            }
                        )
                        .addTo(map)
                        .bindPopup('<strong>Lokasi Anda</strong><br>Hambatan terdekat ditampilkan pada marker peta.');

                        userLocationCircle = window.L.circle(
                            [userLat, userLng],
                            {
                                radius: radius,
                                color: '#2563eb',
                                weight: 1,
                                fillColor: '#2563eb',
                                fillOpacity: 0.08
                            }
                        ).addTo(map);

                        const nearby = laporanData
                            .map(function (report) {
                                return {
                                    report: report,
                                    distance: calculateDistance(
                                        userLat,
                                        userLng,
                                        Number(report.latitude),
                                        Number(report.longitude)
                                    )
                                };
                            })
                            .filter(function (item) {
                                return item.distance <= radius;
                            })
                            .sort(function (a, b) {
                                return a.distance - b.distance;
                            })
                            .slice(0, 6);

                        map.setView([userLat, userLng], 17);

                        // Hambatan terdekat ditampilkan langsung melalui marker.
                        nearby.forEach(function (item) {
                            const report = item.report;
                            const marker = reportMarkers.get(
                                String(report.id ?? report.uuid ?? report.judul)
                            );

                            if (marker) {
                                marker.openPopup();
                            }
                        });

                        button.disabled = false;
                        button.innerHTML = '<span aria-hidden="true">⌖</span><span>Lokasi Saya</span>';

                    },
                    function () {

                        button.disabled = false;
                        button.innerHTML = '<span aria-hidden="true">⌖</span><span>Lokasi Saya</span>';

                        alert(
                            'Lokasi tidak dapat diperoleh. Pastikan izin lokasi pada browser telah diberikan.'
                        );
                    },
                    {
                        enableHighAccuracy: true,
                        timeout: 15000,
                        maximumAge: 60000
                    }
                );
            }
        );


    function calculateDistance(
        lat1,
        lng1,
        lat2,
        lng2
    ) {

        const earthRadius =
            6371000;

        const latFrom =
            lat1 * Math.PI / 180;

        const latTo =
            lat2 * Math.PI / 180;

        const latDelta =
            (lat2 - lat1)
            * Math.PI
            / 180;

        const lngDelta =
            (lng2 - lng1)
            * Math.PI
            / 180;

        const a =
            Math.sin(
                latDelta / 2
            ) ** 2
            +
            Math.cos(latFrom)
            *
            Math.cos(latTo)
            *
            Math.sin(
                lngDelta / 2
            ) ** 2;

        const c =
            2 *
            Math.atan2(
                Math.sqrt(a),
                Math.sqrt(1 - a)
            );

        return earthRadius * c;
    }


    toggleSidebar.addEventListener(
        'click',
        function () {
            sidebar.classList.toggle(
                'open'
            );

            setTimeout(
                function () {
                    map.invalidateSize();
                },
                250
            );
        }
    );


    closeSidebar.addEventListener(
        'click',
        function () {
            sidebar.classList.remove(
                'open'
            );
        }
    );


    window.addEventListener(
        'resize',
        function () {
            map.invalidateSize();
        },
        {
            passive: true
        }
    );


    loadReports();
    loadFacilities();

    setTimeout(
        function () {
            map.invalidateSize();
        },
        250
    );
});
</script>

@endpush