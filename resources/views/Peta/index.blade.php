@extends('layouts.app')

@section('title', 'Peta Interaktif')

@push('styles')
<style>
    /* ==========================================================
       SMARTPATH MAP — Refactor 2026
       ========================================================== */
    :root {
        --sp-border: #111827;
        --sp-border-soft: #e5e7eb;
        --sp-text: #111827;
        --sp-muted: #64748b;
        --sp-emerald: #059669;
        --sp-teal: #0d9488;
        --sp-green: #16a34a;
        --sp-red: #dc2626;
        --sp-amber: #d97706;
        --sp-slate: #94a3b8;
    }

    .sp-map-page {
        min-height: calc(100vh - 32px);
        margin: 16px 28px;
        background: #fff;
        border: 1px solid var(--sp-border);
        border-radius: 8px;
        overflow: hidden;
        box-sizing: border-box;
        font-family: 'Inter', system-ui, sans-serif;
    }

    .sp-map-shell {
        display: grid;
        grid-template-columns: 240px minmax(0, 1fr);
        width: 100%;
        height: 680px;
        background: #fff;
    }

    .sp-map-sidebar {
        background: #fff;
        border-right: 1px solid #1f2937;
        overflow-y: auto;
        overflow-x: hidden;
        z-index: 900;
        box-sizing: border-box;
    }

    .sp-map-content {
        min-width: 0;
        min-height: 0;
        height: 100%;
        position: relative;
        display: flex;
        flex-direction: column;
        background: #fff;
        overflow: hidden;
    }

    #map-container {
        width: 100%;
        height: 100%;
        background: #e5e7eb;
        flex: 1 1 auto;
    }

    /* ============ HEADER ============ */
    .sp-header {
        height: 52px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        padding: 0 14px;
        border-bottom: 1px solid var(--sp-border);
        background: #fff;
        box-sizing: border-box;
    }

    .sp-brand {
        display: flex;
        align-items: center;
        gap: 9px;
        min-width: 180px;
        color: var(--sp-text);
    }

    .sp-brand-logo {
        width: 28px;
        height: 28px;
        border: 1px solid var(--sp-border);
        border-radius: 5px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-sizing: border-box;
    }

    .sp-brand-logo svg {
        width: 18px;
        height: 18px;
    }

    .sp-brand-name {
        font-size: 15px;
        font-weight: 700;
        letter-spacing: -0.3px;
    }

    .sp-brand-image {
        width: 150px;
        height: 34px;
        object-fit: contain;
        object-position: left center;
    }

    .sp-header-stats {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 20px;
        flex: 1 1 auto;
        color: var(--sp-text);
        font-size: 11px;
        white-space: nowrap;
    }

    .sp-header-stats > span {
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .sp-dot {
        width: 8px;
        height: 8px;
        border: 1px solid var(--sp-border);
        border-radius: 50%;
        box-sizing: border-box;
    }

    .sp-square {
        width: 8px;
        height: 8px;
        border: 1px solid var(--sp-border);
        box-sizing: border-box;
    }

    .sp-header-actions {
        min-width: 180px;
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 8px;
    }

    .sp-header-btn {
        height: 30px;
        padding: 0 11px;
        border: 1px solid var(--sp-border);
        border-radius: 6px;
        background: #fff;
        color: var(--sp-text);
        font-size: 11px;
        font-family: inherit;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        box-sizing: border-box;
        cursor: pointer;
        text-decoration: none;
        transition: background .15s ease;
    }

    .sp-header-btn:hover {
        background: #f3f4f6;
    }

    /* ============ SIDEBAR ============ */
    .sp-filter-section {
        padding: 10px 12px;
        border-bottom: 1px solid #d1d5db;
    }

    .sp-filter-title {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 7px;
        color: var(--sp-text);
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.4px;
    }

    .sp-filter-desc {
        margin: 0 0 8px;
        color: var(--sp-muted);
        font-size: 10px;
        line-height: 1.4;
    }

    .sp-check {
        display: flex;
        align-items: center;
        gap: 7px;
        min-height: 22px;
        padding: 2px 0;
        color: var(--sp-text);
        font-size: 11px;
        cursor: pointer;
        line-height: 1.3;
    }

    .sp-check input {
        width: 13px;
        height: 13px;
        margin: 0;
        accent-color: var(--sp-emerald);
        flex: 0 0 auto;
        cursor: pointer;
    }

    .sp-check .sp-status-dot {
        width: 8px;
        height: 8px;
        flex: 0 0 auto;
        border-radius: 50%;
    }

    .sp-check .sp-count {
        margin-left: auto;
        min-width: 26px;
        padding: 1px 5px;
        border: 1px solid #d1d5db;
        border-radius: 3px;
        text-align: center;
        font-size: 10px;
        font-weight: 600;
        line-height: 1.4;
        box-sizing: border-box;
        color: var(--sp-muted);
    }

    .sp-legend {
        display: flex;
        align-items: center;
        gap: 7px;
        padding: 3px 0;
        color: var(--sp-text);
        font-size: 10px;
    }

    .sp-legend-pin {
        width: 9px;
        height: 9px;
        border-radius: 50%;
        border: 1px solid var(--sp-border);
        box-sizing: border-box;
    }

    .sp-legend-pin.high   { background: var(--sp-red); }
    .sp-legend-pin.medium { background: var(--sp-amber); }
    .sp-legend-pin.low    { background: var(--sp-green); }

    .sp-legend-score {
        margin-left: auto;
        white-space: nowrap;
        font-size: 9px;
        color: var(--sp-muted);
    }

    .sp-info-box {
        padding: 9px 10px;
        border: 1px solid var(--sp-border);
        border-radius: 5px;
        font-size: 10px;
        line-height: 1.5;
        color: var(--sp-text);
        background: #f9fafb;
    }

    /* ============ OVERLAY: NEARBY NAVBAR ============ */
    .sp-nearby-navbar {
        position: absolute;
        top: 12px;
        right: 12px;
        z-index: 1000;
        padding: 6px 10px;
        background: #fff;
        border: 1px solid var(--sp-border);
        border-radius: 6px;
        display: flex;
        align-items: center;
        gap: 10px;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.08);
    }

    .sp-nearby-title {
        display: flex;
        align-items: center;
        gap: 5px;
        margin: 0;
        color: var(--sp-text);
        font-size: 10px;
        font-weight: 700;
        white-space: nowrap;
    }

    .sp-location-btn {
        border: 1px solid var(--sp-border);
        border-radius: 5px;
        padding: 5px 10px;
        background: #fff;
        color: var(--sp-text);
        font-size: 10px;
        font-weight: 700;
        font-family: inherit;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        box-sizing: border-box;
    }

    .sp-location-btn:hover { background: #f3f4f6; }
    .sp-location-btn:disabled { opacity: .6; cursor: wait; }
    .sp-location-btn:focus-visible {
        outline: 3px solid #34d399;
        outline-offset: 2px;
    }

    /* ============ OVERLAY: SUMMARY ============ */
    .sp-summary {
        position: absolute;
        right: 12px;
        bottom: 12px;
        z-index: 1000;
        width: 140px;
        background: #fff;
        border: 1px solid var(--sp-border);
        border-radius: 7px;
        overflow: hidden;
    }

    .sp-summary-item {
        padding: 5px 10px;
        display: grid;
        grid-template-columns: 1fr auto;
        align-items: center;
        column-gap: 8px;
        border-bottom: 1px solid var(--sp-border-soft);
        font-size: 10px;
    }

    .sp-summary-item:last-child { border-bottom: 0; }

    .sp-summary-num {
        order: 2;
        color: var(--sp-text);
        font-weight: 700;
    }

    .sp-summary-label {
        order: 1;
        color: var(--sp-text);
        font-size: 9px;
    }

    /* ============ LEAFLET ============ */
    .leaflet-top.leaflet-left { top: 12px; left: 8px; }
    .leaflet-popup-content-wrapper { border-radius: 8px; }
    .leaflet-popup-content { margin: 10px 12px; }

    .sp-facility-marker {
        width: 24px; height: 24px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        border: 3px solid #fff;
        background: var(--sp-teal);
        color: #fff;
        box-shadow: 0 3px 10px rgba(15,23,42,.25);
    }
    .sp-facility-marker svg { width: 12px; height: 12px; }

    .sp-pin-wrap {
        position: relative;
        width: 34px; height: 42px;
    }

    .sp-pin-pulse {
        position: absolute;
        left: 50%; top: 11px;
        width: 18px; height: 18px;
        transform: translate(-50%, -50%);
        border-radius: 50%;
        opacity: 0;
    }

    .sp-pin {
        position: absolute;
        left: 50%; top: 2px;
        width: 27px; height: 27px;
        transform: translateX(-50%) rotate(-45deg);
        border-radius: 50% 50% 50% 0;
        border: 3px solid #fff;
        box-shadow: 0 3px 9px rgba(15,23,42,.30);
    }

    .sp-pin::after {
        content: "";
        position: absolute;
        left: 7px; top: 7px;
        width: 7px; height: 7px;
        border-radius: 50%;
        background: #fff;
    }

    .sp-pin-wrap.high   .sp-pin { background: var(--sp-red); }
    .sp-pin-wrap.medium .sp-pin { background: var(--sp-amber); }
    .sp-pin-wrap.low    .sp-pin { background: var(--sp-green); }

    .sp-pin-wrap.high .sp-pin-pulse {
        background: rgba(220,38,38,.20);
        animation: sp-pulse 2s infinite;
    }

    @keyframes sp-pulse {
        0%   { transform: translate(-50%,-50%) scale(.8); opacity: .8; }
        70%  { transform: translate(-50%,-50%) scale(2.3); opacity: 0; }
        100% { opacity: 0; }
    }

    /* ============ POPUP ============ */
    .sp-popup {
        width: 250px;
        font-family: inherit;
    }

    .sp-popup-img {
        width: 100%;
        height: 125px;
        object-fit: cover;
        border-radius: 8px;
        margin-bottom: 9px;
        background: #f1f5f9;
    }

    .sp-popup-title    { color: #0f172a; font-size: 14px; font-weight: 700; line-height: 1.4; }
    .sp-popup-category { margin-top: 4px; color: var(--sp-emerald); font-size: 11px; font-weight: 600; }
    .sp-popup-address  { margin-top: 7px; color: var(--sp-muted); font-size: 11px; line-height: 1.55; }
    .sp-popup-meta     { display: flex; align-items: center; gap: 6px; flex-wrap: wrap; margin-top: 9px; }
    .sp-popup-badge    { display: inline-flex; padding: 4px 7px; border-radius: 6px; font-size: 12px; font-weight: 700; }
    .sp-popup-score    { color: var(--sp-muted); font-size: 12px; }
    .sp-popup-footer   { margin-top: 7px; color: var(--sp-muted); font-size: 12px; }

    /* ============ RESPONSIVE ============ */
    @media (max-width: 1023px) {
        .sp-map-page { margin: 8px; }
        .sp-header { padding: 0 10px; }
        .sp-brand, .sp-header-actions { min-width: 140px; }
        .sp-brand-image { width: 100px; height: 24px; }
        .sp-header-stats { gap: 10px; }

        .sp-map-shell {
            grid-template-columns: 1fr;
            height: calc(100vh - 90px);
            min-height: 520px;
        }

        .sp-map-sidebar {
            position: fixed;
            inset: 64px auto 0 0;
            width: 280px;
            max-height: calc(100vh - 64px);
            transform: translateX(-100%);
            transition: transform .25s ease;
            box-shadow: 8px 0 24px rgba(15,23,42,.10);
        }

        .sp-map-sidebar.open { transform: translateX(0); }
        #map-container { height: 520px; flex-basis: 520px; }

        .sp-mobile-only { display: block !important; }
    }

    @media (max-width: 640px) {
        .sp-map-page { margin: 4px; border-radius: 5px; }
        .sp-header { height: 46px; }
        .sp-header-stats { display: none; }
        .sp-header-actions { min-width: 0; }
        .sp-header-btn { height: 28px; padding: 0 8px; font-size: 10px; }
        #map-container { min-height: 520px; }
        .sp-summary { width: 120px; right: 8px; bottom: 8px; }
        .sp-nearby-navbar { top: 8px; right: 8px; padding: 5px 8px; }
        .sp-nearby-title { font-size: 9px; }
    }

    .sp-mobile-only { display: none; }

    .sp-map-page {
        width: 100%;
        height: 100vh;
        min-height: 560px;
        margin: 0;
        border-color: #dbe7e4;
        border-radius: 0;
        box-shadow: none;
        font-family: 'Inter', system-ui, sans-serif;
    }

    .sp-map-shell {
        height: calc(100vh - 60px);
        min-height: 500px;
        grid-template-columns: 264px minmax(0, 1fr);
    }

    .sp-map-sidebar {
        border-right-color: #e2e8f0;
        background: #fbfdfc;
    }

    .sp-filter-section {
        padding: 14px 16px;
        border-bottom-color: #e2e8f0;
    }

    .sp-map-sidebar {
        height: 100%;
    }

    .sp-filter-title {
        color: #1e293b;
        font-size: 12px;
        letter-spacing: .035em;
    }

    .sp-filter-desc {
        font-size: 11px;
    }

    .sp-check {
        min-height: 30px;
        gap: 9px;
        border-radius: 7px;
        color: #334155;
        font-size: 12px;
    }

    .sp-check input {
        width: 15px;
        height: 15px;
    }

    .sp-header {
        height: 60px;
        padding: 0 20px;
        border-bottom-color: #e2e8f0;
    }

    .sp-brand-menu {
        display: none;
        width: 36px;
        height: 36px;
        align-items: center;
        justify-content: center;
        border: 1px solid #cbd5e1;
        border-radius: 9px;
        background: #ffffff;
        color: #334155;
        font: inherit;
        cursor: pointer;
    }

    .sp-brand-menu:focus-visible,
    .sp-map-stat:focus-visible {
        outline: 3px solid rgba(16, 185, 129, .35);
        outline-offset: 2px;
    }

    .sp-map-stat {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 8px;
        border: 1px solid transparent;
        border-radius: 8px;
        background: transparent;
        color: inherit;
        font: inherit;
        cursor: pointer;
    }

    .sp-map-stat:hover,
    .sp-map-stat[aria-pressed="true"] {
        border-color: #a7f3d0;
        background: #ecfdf5;
        color: #047857;
    }

    .sp-summary .sp-map-stat {
        display: grid;
        width: 100%;
        grid-template-columns: 1fr auto;
        text-align: left;
    }

    .sp-map-data-status {
        position: absolute;
        z-index: 1000;
        top: 16px;
        left: 16px;
        max-width: min(360px, calc(100% - 32px));
        padding: 9px 12px;
        border: 1px solid #dbe7e4;
        border-radius: 10px;
        background: rgba(255, 255, 255, .96);
        color: #475569;
        font-size: 12px;
        line-height: 1.45;
        box-shadow: 0 4px 14px rgba(15, 23, 42, .08);
    }

    .sp-map-data-status:empty {
        display: none;
    }

    .sp-map-data-status.is-error {
        border-color: #fecaca;
        background: #fef2f2;
        color: #991b1b;
    }

    .sp-header-btn,
    .sp-location-btn {
        min-height: 36px;
        padding: 0 13px;
        border-color: #cbd5e1;
        border-radius: 9px;
        font-size: 12px;
    }

    .sp-header-btn:hover,
    .sp-location-btn:hover {
        border-color: #a7f3d0;
        background: #ecfdf5;
        color: #047857;
    }

    .sp-nearby-navbar,
    .sp-summary {
        border-color: #dbe7e4;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(15, 23, 42, .12);
    }

    .sp-nearby-navbar {
        top: 16px;
        right: 16px;
        padding: 9px 11px;
    }

    .sp-nearby-title {
        font-size: 12px;
    }

    .sp-summary {
        right: 16px;
        bottom: 16px;
        width: 160px;
    }

    .sp-summary-item {
        padding: 8px 11px;
        font-size: 11px;
    }

    .sp-summary-label {
        font-size: 10px;
    }

    @media (max-width: 1023px) {
        .sp-map-page {
            margin: 0;
            border-radius: 0;
        }

        .sp-map-shell {
            height: calc(100vh - 60px);
            min-height: 500px;
        }

        .sp-brand-menu {
            display: inline-flex !important;
        }

        .sp-map-sidebar {
            inset: 60px auto 0 0;
            height: calc(100vh - 60px);
            max-height: calc(100vh - 60px);
        }
    }

    @media (max-width: 640px) {
        .sp-map-page {
            margin: 0;
            border-radius: 0;
        }

        .sp-header {
            height: 52px;
            padding: 0 10px;
        }

        .sp-map-shell {
            height: calc(100vh - 52px);
            min-height: 500px;
        }

        .sp-brand-menu {
            width: 32px;
            height: 32px;
        }

        .sp-nearby-navbar {
            top: 8px;
            right: 8px;
        }

        .sp-summary {
            right: 8px;
            bottom: 8px;
        }

        .sp-map-data-status {
            top: 66px;
            left: 8px;
        }
    }
</style>
@endpush

@section('content')

<div class="sp-map-page">

    {{-- ============ HEADER ============ --}}
    <header class="sp-header">
        <div class="sp-brand">
            <button id="open-sidebar" type="button" class="sp-brand-menu sp-mobile-only"
                    aria-label="Buka filter peta" aria-controls="map-sidebar" aria-expanded="false">
                <i class="fa-solid fa-bars" aria-hidden="true"></i>
            </button>
            <img src="{{ asset('logo-smartpath-cropped.png') }}" alt="SmartPath" class="sp-brand-image">
        </div>

        <div class="sp-header-stats">
            <button type="button" id="filter-high-header" class="sp-map-stat" aria-pressed="false"
                    aria-label="Tampilkan laporan prioritas tinggi">
                <i class="sp-dot"></i> Prioritas Tinggi: <b id="header-high">0</b>
            </button>
            <button type="button" id="filter-all-header" class="sp-map-stat" aria-pressed="true"
                    aria-label="Tampilkan semua laporan">
                <i class="sp-square"></i> Total: <b id="header-total">0</b>
            </button>
        </div>

        <div class="sp-header-actions">
            <a href="{{ route('laporan.create') }}" class="sp-header-btn">＋ Lapor</a>
            <button type="button" id="btn-export" class="sp-header-btn">⇩ Ekspor</button>
        </div>
    </header>

    <div class="sp-map-shell">

        {{-- ============ SIDEBAR ============ --}}
        <aside id="map-sidebar" class="sp-map-sidebar" aria-label="Filter peta">

            <div class="sp-filter-section">
                <div class="sp-filter-title">⌕ Filter Peta</div>
                <p class="sp-filter-desc">Sesuaikan tampilan data pada peta</p>
                <button id="close-sidebar" type="button" class="sp-mobile-only"
                        style="width:100%;padding:6px;border:1px solid #111827;border-radius:5px;background:#fff;cursor:pointer;font-size:11px;font-family:inherit;">
                    Tutup Filter
                </button>
            </div>

            {{-- STATUS --}}
            <div class="sp-filter-section">
                <div class="sp-filter-title">Status Laporan</div>

                @php
                    $statusList = [
                        ['value' => 'menunggu_verifikasi', 'label' => 'Menunggu Verifikasi', 'color' => '#94a3b8'],
                        ['value' => 'diverifikasi',        'label' => 'Diverifikasi',        'color' => '#059669'],
                        ['value' => 'dalam_perbaikan',     'label' => 'Dalam Perbaikan',     'color' => '#0d9488'],
                        ['value' => 'selesai',             'label' => 'Selesai',             'color' => '#16a34a'],
                    ];
                @endphp

                @foreach($statusList as $status)
                    <label class="sp-check">
                        <input type="checkbox" class="filter-status"
                               value="{{ $status['value'] }}" checked>
                        <span class="sp-status-dot" style="background:{{ $status['color'] }}"></span>
                        <span>{{ $status['label'] }}</span>
                        <span class="sp-count" data-count-for="{{ $status['value'] }}">0</span>
                    </label>
                @endforeach
            </div>

            {{-- KATEGORI --}}
            <div class="sp-filter-section">
                <div class="sp-filter-title">Kategori Hambatan</div>

                @forelse($kategoriHambatan as $kategori)
                    <label class="sp-check">
                        <input type="checkbox" class="filter-kategori"
                               value="{{ $kategori->id }}" checked>
                        <span class="sp-status-dot"
                              style="background:{{ $kategori->warna_penanda ?: '#64748b' }}"></span>
                        <span>{{ $kategori->nama }}</span>
                    </label>
                @empty
                    <p class="sp-filter-desc">Belum ada kategori.</p>
                @endforelse
            </div>

            {{-- FASILITAS --}}
            <div class="sp-filter-section">
                <label class="sp-check">
                    <input type="checkbox" id="toggle-fasilitas">
                    <span class="sp-status-dot" style="background:#0d9488"></span>
                    <span>Tampilkan Fasilitas Publik</span>
                    <span class="sp-count" id="facility-count">0</span>
                </label>
            </div>

            {{-- PRIORITAS --}}
            <div class="sp-filter-section">
                <div class="sp-filter-title">Prioritas</div>

                <div class="sp-legend">
                    <span class="sp-legend-pin high"></span>
                    <span>Tinggi</span>
                    <span class="sp-legend-score">Skor ≥ 70</span>
                </div>
                <div class="sp-legend">
                    <span class="sp-legend-pin medium"></span>
                    <span>Sedang</span>
                    <span class="sp-legend-score">Skor 40–69</span>
                </div>
                <div class="sp-legend">
                    <span class="sp-legend-pin low"></span>
                    <span>Rendah</span>
                    <span class="sp-legend-score">Skor &lt; 40</span>
                </div>
            </div>

            <div class="sp-filter-section">
                <div class="sp-info-box">
                    <strong>ⓘ</strong> Klik marker untuk melihat detail laporan.
                </div>
            </div>
        </aside>

        {{-- ============ MAP CONTENT ============ --}}
        <section class="sp-map-content" aria-label="Peta interaktif" role="region">

            {{-- NEARBY NAVBAR (overlay) --}}
            <header class="sp-nearby-navbar">
                <h1 class="sp-nearby-title">
                    <svg viewBox="0 0 24 24" width="14" height="14" fill="none"
                         stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"/>
                        <circle cx="12" cy="10" r="2.5"/>
                    </svg>
                    <span>Hambatan Terdekat</span>
                </h1>
                <button id="nearby-location" type="button" class="sp-location-btn"
                        aria-label="Gunakan lokasi saya">
                    <svg viewBox="0 0 24 24" width="13" height="13" fill="none"
                         stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <circle cx="12" cy="12" r="3"/>
                        <path stroke-linecap="round" d="M12 2v3m0 14v3M2 12h3m14 0h3"/>
                    </svg>
                    <span>Lokasi Saya</span>
                </button>
            </header>

            {{-- SUMMARY --}}
            <section class="sp-summary" aria-label="Ringkasan peta">
                <button type="button" id="filter-all-summary" class="sp-summary-item sp-map-stat" aria-pressed="true"
                        aria-label="Tampilkan semua laporan">
                    <span id="summary-total" class="sp-summary-num">0</span>
                    <span class="sp-summary-label">Total</span>
                </button>
                <button type="button" id="filter-high-summary" class="sp-summary-item sp-map-stat" aria-pressed="false"
                        aria-label="Tampilkan laporan prioritas tinggi">
                    <span id="summary-high" class="sp-summary-num">0</span>
                    <span class="sp-summary-label">Tinggi</span>
                </button>
                <div class="sp-summary-item">
                    <span class="sp-summary-num">Depok</span>
                    <span class="sp-summary-label">Area</span>
                </div>
            </section>

            <p id="map-data-status" class="sp-map-data-status" role="status" aria-live="polite">
                Memuat laporan dan fasilitas publik...
            </p>

            <div id="map-container"></div>
        </section>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    'use strict';

    const mapElement = document.getElementById('map-container');
    const dataStatus = document.getElementById('map-data-status');
    if (!mapElement || typeof window.L === 'undefined') {
        console.error('SmartPath: Leaflet tidak tersedia.');
        if (dataStatus) {
            dataStatus.textContent = 'Peta tidak dapat dimuat. Muat ulang halaman atau periksa koneksi internet.';
            dataStatus.classList.add('is-error');
        }
        return;
    }

    // ============ INIT MAP ============
    const map = window.L.map(mapElement, {
        center: [-6.4025, 106.7942],
        zoom: 13,
        zoomControl: true
    });

    window.L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    const laporanLayer   = window.L.layerGroup().addTo(map);
    const fasilitasLayer = window.L.layerGroup();

    let laporanData    = [];
    let fasilitasData  = [];
    const reportMarkers = new Map();
    let userMarker = null;
    let userCircle = null;
    let initialFitDone = false;
    let highPriorityOnly = false;
    let reportsLoadError = '';
    let facilitiesLoadError = '';

    // ============ HELPERS ============
    function escapeHtml(v) {
        return String(v ?? '').replace(/[&<>'"]/g, c => ({
            '&':'&amp;', '<':'&lt;', '>':'&gt;', "'":'&#039;', '"':'&quot;'
        })[c]);
    }

    function getPriority(score) {
        if (score === null || score === undefined || score === '') {
            return { key: 'low', label: 'Belum Dinilai', color: '#64748b' };
        }
        const n = Number(score);
        if (n >= 70) return { key: 'high',   label: 'Tinggi', color: '#dc2626' };
        if (n >= 40) return { key: 'medium', label: 'Sedang', color: '#d97706' };
        return              { key: 'low',    label: 'Rendah', color: '#16a34a' };
    }

    function isHighPriority(report) {
        return Number(report.skor_prioritas) >= 70 ||
            String(report.tingkat_prioritas || '').toLowerCase() === 'tinggi';
    }

    function updateDataStatus() {
        if (!dataStatus) return;

        const errors = [reportsLoadError, facilitiesLoadError].filter(Boolean);
        dataStatus.textContent = errors.join(' ');
        dataStatus.classList.toggle('is-error', errors.length > 0);

        if (errors.length === 0) {
            dataStatus.textContent = '';
        }
    }

    function calculateDistance(lat1, lng1, lat2, lng2) {
        const R = 6371000;
        const φ1 = lat1 * Math.PI / 180;
        const φ2 = lat2 * Math.PI / 180;
        const Δφ = (lat2 - lat1) * Math.PI / 180;
        const Δλ = (lng2 - lng1) * Math.PI / 180;
        const a = Math.sin(Δφ/2)**2 + Math.cos(φ1)*Math.cos(φ2)*Math.sin(Δλ/2)**2;
        return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    }

    // ============ ICONS ============
    function createReportIcon(report) {
        const p = getPriority(report.skor_prioritas);
        return window.L.divIcon({
            className: 'sp-report-icon',
            html: `
                <div class="sp-pin-wrap ${p.key}" role="img"
                     aria-label="Marker laporan ${escapeHtml(p.label)}">
                    <span class="sp-pin-pulse"></span>
                    <span class="sp-pin"></span>
                </div>
            `,
            iconSize: [34, 42],
            iconAnchor: [17, 41],
            popupAnchor: [0, -36]
        });
    }

    function createFacilityIcon() {
        return window.L.divIcon({
            className: 'sp-facility-icon',
            html: `
                <div class="sp-facility-marker">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M4 20h16M6 20V7a2 2 0 012-2h8a2 2 0 012 2v13M9 9h1m-1 3h1m4-3h1m-1 3h1M9 20v-4h6v4"/>
                    </svg>
                </div>
            `,
            iconSize: [24, 24],
            iconAnchor: [12, 12]
        });
    }

    // ============ POPUPS ============
    function createReportPopup(report) {
        const p = getPriority(report.skor_prioritas);
        const scoreText = (report.skor_prioritas === null || report.skor_prioritas === undefined)
            ? 'Belum dinilai'
            : Number(report.skor_prioritas).toFixed(2);

        const image = report.foto_utama
            ? `<img class="sp-popup-img" src="${escapeHtml(report.foto_utama)}"
                    alt="Foto ${escapeHtml(report.judul)}">`
            : '';

        return `
            <article class="sp-popup">
                ${image}
                <div class="sp-popup-title">${escapeHtml(report.judul || 'Laporan Hambatan')}</div>
                <div class="sp-popup-category">${escapeHtml(report.kategori || 'Kategori tidak tersedia')}</div>
                <div class="sp-popup-address">${escapeHtml(report.alamat_lengkap || 'Alamat tidak tersedia')}</div>
                <div class="sp-popup-meta">
                    <span class="sp-popup-badge"
                          style="color:${p.color};background:${p.color}15;border:1px solid ${p.color}35;">
                        Prioritas ${escapeHtml(p.label)}
                    </span>
                    <span class="sp-popup-score">Skor ${escapeHtml(scoreText)}</span>
                </div>
                <div class="sp-popup-footer">
                    ${escapeHtml(report.status_label || report.status)} ·
                    ${escapeHtml(report.jumlah_pelapor || 1)} pelapor
                </div>
            </article>
        `;
    }

    function createFacilityPopup(f) {
        return `
            <div style="min-width:190px;font-family:inherit;">
                <strong style="color:#0f172a;font-size:13px;">${escapeHtml(f.nama)}</strong>
                <div style="margin-top:4px;color:#0d9488;font-size:11px;font-weight:600;">
                    ${escapeHtml(f.jenis_label)}
                </div>
                <div style="margin-top:5px;color:#64748b;font-size:12px;line-height:1.5;">
                    ${escapeHtml(f.alamat || 'Alamat tidak tersedia')}
                </div>
            </div>
        `;
    }

    // ============ COUNTERS ============
    function updateCounters(allReports) {
        // Header stats
        const total = document.getElementById('header-total');
        const high  = document.getElementById('header-high');
        if (total) total.textContent = allReports.length;
        if (high) {
            high.textContent = allReports.filter(isHighPriority).length;
        }

        // Per-status counters
        document.querySelectorAll('[data-count-for]').forEach(el => {
            const status = el.getAttribute('data-count-for');
            const count = allReports.filter(r => String(r.status) === status).length;
            el.textContent = count;
        });
    }

    // ============ RENDER ============
    function renderReports() {
        laporanLayer.clearLayers();
        reportMarkers.clear();

        const activeStatuses  = Array.from(document.querySelectorAll('.filter-status:checked')).map(c => c.value);
        const activeCategories = Array.from(document.querySelectorAll('.filter-kategori:checked')).map(c => String(c.value));

        const matchingFilters = laporanData.filter(r => {
            const okStatus = activeStatuses.includes(String(r.status));
            const okCat = activeCategories.length === 0 ||
                          activeCategories.includes(String(r.kategori_id));
            return okStatus && okCat;
        });
        const visible = highPriorityOnly
            ? matchingFilters.filter(isHighPriority)
            : matchingFilters;

        updateCounters(laporanData);

        visible.forEach(report => {
            const lat = Number(report.latitude);
            const lng = Number(report.longitude);
            if (!Number.isFinite(lat) || !Number.isFinite(lng)) return;

            const marker = window.L.marker([lat, lng], {
                icon: createReportIcon(report),
                title: report.judul,
                alt: `Lokasi hambatan ${report.judul}`
            });

            marker.bindPopup(createReportPopup(report), { maxWidth: 320, minWidth: 220 });
            laporanLayer.addLayer(marker);
            reportMarkers.set(String(report.id ?? report.uuid ?? report.judul), marker);

            const el = marker.getElement();
            if (el) {
                const p = getPriority(report.skor_prioritas);
                el.setAttribute('role', 'button');
                el.setAttribute('tabindex', '0');
                el.setAttribute('aria-label',
                    `Laporan ${report.judul || 'hambatan'}. Prioritas ${p.label}. Skor ${report.skor_prioritas ?? 'belum dinilai'}.`
                );
                el.addEventListener('keydown', e => {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        marker.openPopup();
                    } else if (e.key === 'Escape' && marker.isPopupOpen()) {
                        marker.closePopup();
                    }
                });
            }
        });

        const highCount = matchingFilters.filter(isHighPriority).length;

        document.getElementById('summary-total').textContent = visible.length;
        document.getElementById('summary-high').textContent  = highCount;
        document.querySelectorAll('#filter-high-header, #filter-high-summary').forEach(button => {
            button.setAttribute('aria-pressed', String(highPriorityOnly));
        });
        document.querySelectorAll('#filter-all-header, #filter-all-summary').forEach(button => {
            button.setAttribute('aria-pressed', String(!highPriorityOnly));
        });

        if (!initialFitDone && visible.length > 0) {
            const points = visible
                .map(r => [Number(r.latitude), Number(r.longitude)])
                .filter(p => Number.isFinite(p[0]) && Number.isFinite(p[1]));

            if (points.length === 1)      map.setView(points[0], 16);
            else if (points.length > 1)   map.fitBounds(points, { padding: [30, 30], maxZoom: 15 });

            initialFitDone = true;
        }
    }

    function renderFacilities() {
        fasilitasLayer.clearLayers();
        fasilitasData.forEach(f => {
            const lat = Number(f.latitude);
            const lng = Number(f.longitude);
            if (!Number.isFinite(lat) || !Number.isFinite(lng)) return;

            const marker = window.L.marker([lat, lng], {
                icon: createFacilityIcon(),
                title: `Fasilitas publik ${f.nama}`,
                alt: `Fasilitas publik ${f.nama}`,
                keyboard: true
            });
            marker.bindPopup(createFacilityPopup(f));
            fasilitasLayer.addLayer(marker);

            const element = marker.getElement();
            if (element) {
                element.setAttribute('role', 'button');
                element.setAttribute('tabindex', '0');
                element.setAttribute('aria-label', `Fasilitas publik ${f.nama}. ${f.jenis_label || ''}`);
                element.addEventListener('keydown', event => {
                    if (event.key === 'Enter' || event.key === ' ') {
                        event.preventDefault();
                        marker.openPopup();
                    } else if (event.key === 'Escape' && marker.isPopupOpen()) {
                        marker.closePopup();
                    }
                });
            }
        });
    }

    // ============ LOAD DATA ============
    async function loadReports() {
        try {
            const res = await fetch('{{ route("peta.data") }}', {
                headers: { 'Accept': 'application/json' }
            });
            if (!res.ok) throw new Error('Gagal mengambil laporan.');
            const data = await res.json();
            if (!Array.isArray(data)) throw new Error('Format data laporan tidak sesuai.');
            laporanData = data;

            // Debug: cek status unik di data
            const uniqueStatuses = [...new Set(laporanData.map(r => r.status))];
            console.log('[SmartPath] Status unik di data:', uniqueStatuses);
            console.log('[SmartPath] Contoh data:', laporanData[0]);

            renderReports();
            reportsLoadError = '';
        } catch (err) {
            console.error('SmartPath Peta:', err);
            reportsLoadError = 'Data laporan tidak dapat dimuat. Coba muat ulang halaman.';
        } finally {
            updateDataStatus();
        }
    }

    async function loadFacilities() {
        try {
            const res = await fetch('{{ route("peta.fasilitas") }}', {
                headers: { 'Accept': 'application/json' }
            });
            if (!res.ok) throw new Error('Gagal mengambil fasilitas.');
            const data = await res.json();
            if (!Array.isArray(data)) throw new Error('Format data fasilitas tidak sesuai.');
            fasilitasData = data;
            renderFacilities();
            const facilityCount = document.getElementById('facility-count');
            if (facilityCount) facilityCount.textContent = fasilitasData.length;
            if (document.getElementById('toggle-fasilitas').checked) {
                fasilitasLayer.addTo(map);
            }
            facilitiesLoadError = '';
        } catch (err) {
            console.error('SmartPath Fasilitas:', err);
            facilitiesLoadError = 'Data fasilitas publik tidak dapat dimuat.';
        } finally {
            updateDataStatus();
        }
    }

    // ============ EVENTS ============
    document.querySelectorAll('.filter-status, .filter-kategori').forEach(el => {
        el.addEventListener('change', renderReports);
    });

    document.querySelectorAll('#filter-high-header, #filter-high-summary').forEach(button => {
        button.addEventListener('click', function () {
            highPriorityOnly = true;
            renderReports();
        });
    });

    document.querySelectorAll('#filter-all-header, #filter-all-summary').forEach(button => {
        button.addEventListener('click', function () {
            highPriorityOnly = false;
            document.querySelectorAll('.filter-status, .filter-kategori').forEach(filter => {
                filter.checked = true;
            });
            renderReports();
        });
    });

    const facilitiesToggle = document.getElementById('toggle-fasilitas');
    facilitiesToggle.addEventListener('change', function () {
        if (this.checked) fasilitasLayer.addTo(map);
        else map.removeLayer(fasilitasLayer);
    });

    // Location button
    document.getElementById('nearby-location').addEventListener('click', function () {
        const button = this;
        if (!navigator.geolocation) {
            alert('Browser tidak mendukung fitur lokasi.');
            return;
        }
        button.disabled = true;
        const originalHTML = button.innerHTML;
        button.innerHTML = '<span>Mengambil...</span>';

        navigator.geolocation.getCurrentPosition(
            pos => {
                const uLat = pos.coords.latitude;
                const uLng = pos.coords.longitude;
                const radius = 50;

                if (userMarker) map.removeLayer(userMarker);
                if (userCircle) map.removeLayer(userCircle);

                userMarker = window.L.circleMarker([uLat, uLng], {
                    radius: 7, color: '#fff', weight: 3,
                    fillColor: '#2563eb', fillOpacity: 1, zIndexOffset: 1000
                }).addTo(map).bindPopup('<strong>Lokasi Anda</strong>');
                const userMarkerElement = userMarker.getElement();
                if (userMarkerElement) {
                    userMarkerElement.setAttribute('role', 'img');
                    userMarkerElement.setAttribute('aria-label', 'Lokasi Anda saat ini');
                }

                userCircle = window.L.circle([uLat, uLng], {
                    radius: radius, color: '#2563eb', weight: 1,
                    fillColor: '#2563eb', fillOpacity: 0.08
                }).addTo(map);

                const nearby = laporanData
                    .map(r => ({
                        report: r,
                        distance: calculateDistance(uLat, uLng, Number(r.latitude), Number(r.longitude))
                    }))
                    .filter(i => i.distance <= radius)
                    .sort((a, b) => a.distance - b.distance)
                    .slice(0, 6);

                map.setView([uLat, uLng], 17);

                nearby.forEach(item => {
                    const r = item.report;
                    const m = reportMarkers.get(String(r.id ?? r.uuid ?? r.judul));
                    if (m) m.openPopup();
                });

                button.disabled = false;
                button.innerHTML = originalHTML;
            },
            () => {
                button.disabled = false;
                button.innerHTML = originalHTML;
                alert('Lokasi tidak dapat diperoleh. Pastikan izin lokasi diberikan.');
            },
            { enableHighAccuracy: true, timeout: 15000, maximumAge: 60000 }
        );
    });

    // Export CSV
    document.getElementById('btn-export').addEventListener('click', function () {
        if (!laporanData.length) {
            alert('Tidak ada data untuk diekspor.');
            return;
        }

        const headers = ['ID', 'Judul', 'Kategori', 'Status', 'Skor Prioritas', 'Latitude', 'Longitude', 'Alamat'];
        const rows = laporanData.map(r => [
            r.id ?? '',
            r.judul ?? '',
            r.kategori ?? '',
            r.status_label || r.status || '',
            r.skor_prioritas ?? '',
            r.latitude ?? '',
            r.longitude ?? '',
            (r.alamat_lengkap || '').replace(/[\r\n]+/g, ' ')
        ]);

        const csv = [headers, ...rows]
            .map(row => row.map(cell => `"${String(cell).replace(/"/g, '""')}"`).join(','))
            .join('\n');

        const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = `smartpath-laporan-${new Date().toISOString().slice(0,10)}.csv`;
        link.click();
        URL.revokeObjectURL(link.href);
    });

    // Sidebar toggle (mobile)
    const sidebar = document.getElementById('map-sidebar');
    const openSidebar = document.getElementById('open-sidebar');
    const closeSidebar = document.getElementById('close-sidebar');

    if (openSidebar && sidebar) {
        openSidebar.addEventListener('click', () => {
            sidebar.classList.add('open');
            openSidebar.setAttribute('aria-expanded', 'true');
            setTimeout(() => map.invalidateSize(), 250);
        });
    }

    if (closeSidebar) {
        closeSidebar.addEventListener('click', () => {
            sidebar.classList.remove('open');
            if (openSidebar) openSidebar.setAttribute('aria-expanded', 'false');
            setTimeout(() => map.invalidateSize(), 250);
        });
    }

    document.addEventListener('keydown', event => {
        if (event.key === 'Escape' && sidebar && sidebar.classList.contains('open')) {
            sidebar.classList.remove('open');
            if (openSidebar) {
                openSidebar.setAttribute('aria-expanded', 'false');
                openSidebar.focus();
            }
        }
    });

    window.addEventListener('resize', () => map.invalidateSize(), { passive: true });

    // ============ BOOT ============
    loadReports();
    loadFacilities();
    setTimeout(() => map.invalidateSize(), 250);
});
</script>
@endpush
