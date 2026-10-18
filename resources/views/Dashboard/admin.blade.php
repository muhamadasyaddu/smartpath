@extends('layouts.admin')

@section('title', 'Dashboard Administrator')
@section('page_title', 'Dashboard Administrator')

@section('content')

    
<style>
/* Mengunci elemen absolute (sr-only, tooltip) agar tetap di dalam area scroll dashboard */
#admin-dashboard {
    position: relative;
}

/* Card putih peta admin */
.sp-map-card {
    align-self: start;
    height: fit-content;
    min-height: 0;
    padding: 12px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    box-shadow: 0 2px 8px rgba(15, 23, 42, 0.05);
    box-sizing: border-box;
}

.sp-map-wrap {
    position: relative;
    width: 100%;
    min-width: 0;
    height: clamp(520px, 72vh, 760px);
    overflow: hidden;
    border-radius: 10px;
}

#admin-map {
    width: 100%;
    height: 100%;
    min-height: 0;
}

@media (max-width: 640px) {
    .sp-map-wrap { height: 480px; }
}



/* =====================================================
   GRID PETA + TOP PRIORITAS
   Dua kolom tetap pada desktop; Top Prioritas selalu di kanan
   dan tingginya mengikuti kartu peta.
===================================================== */
@media (min-width: 1024px) {
    #admin-dashboard .sp-main-grid {
        grid-template-columns: minmax(0, 1.9fr) minmax(320px, 1fr);
        grid-template-rows: auto;
        align-items: stretch;
    }

    #admin-dashboard #map-section {
        grid-column: 1;
        grid-row: 1;
        align-self: stretch;
        display: flex;
        flex-direction: column;
        height: auto;
    }

    #admin-dashboard #map-section .sp-map-wrap {
        flex: 1 1 auto;
        height: auto;
        min-height: 520px;
    }

    #admin-dashboard #priority-panel {
        grid-column: 2;
        grid-row: 1;
        min-width: 0;
    }
}


/* Animasi grafik */
.sp-category-bar { animation: barMasuk 1.2s ease-out; }
@keyframes barMasuk { from { width: 0; } }

.sp-trend-chart polyline {
    stroke-dasharray: 1000;
    stroke-dashoffset: 1000;
    animation: garisBerjalan 2s ease forwards;
}
@keyframes garisBerjalan { to { stroke-dashoffset: 0; } }

.sp-status-chart,
.sp-status-chart svg,
.sp-status-chart circle,
.sp-status-chart path {
    animation: statusMuncul 1.5s ease-out;
    transform-origin: center;
}
@keyframes statusMuncul {
    from { opacity: 0; transform: scale(0.7); }
    to   { opacity: 1; transform: scale(1); }
}

/* =====================================================
   TREN LAPORAN
===================================================== */
#admin-dashboard .sp-trend-wrap {
    position: relative;
    padding: 12px 14px 10px;
}
#admin-dashboard .sp-trend-chart {
    display: block;
    width: 100%;
    height: auto;
    overflow: visible;
}
#admin-dashboard .sp-trend-chart line {
    stroke: #e2e8f0;
    stroke-width: 1;
}
#admin-dashboard .sp-trend-chart text {
    fill: #64748b;
    font-size: 9.5px;
}
#admin-dashboard .sp-trend-chart polyline {
    fill: none;
    stroke: #0d9488;
    stroke-width: 2;
    stroke-linecap: round;
    stroke-linejoin: round;
}
#admin-dashboard .sp-trend-chart circle {
    fill: #ffffff;
    stroke: #0d9488;
    stroke-width: 2;
}
#admin-dashboard .sp-trend-chart circle.is-last { fill: #0d9488; }
#admin-dashboard .sp-trend-note {
    position: absolute;
    top: 14px;
    right: 16px;
    display: grid;
    gap: 1px;
    padding: 6px 10px;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    background: #ffffff;
    box-shadow: 0 2px 8px rgba(15, 23, 42, .08);
}
#admin-dashboard .sp-trend-note strong { font-size: 12px; color: #0f172a; }
#admin-dashboard .sp-trend-note span   { font-size: 12px; color: #475569; }

/* =====================================================
   LAPORAN TERBARU + AKTIVITAS TERBARU
===================================================== */
#admin-dashboard .sp-bottom-grid {
    display: grid;
    grid-template-columns: minmax(0, 1fr);
    gap: 14px;
    align-items: stretch;
    margin-bottom: 0;
}
@media (min-width: 1280px) {
    #admin-dashboard .sp-bottom-grid {
            grid-template-columns: minmax(0, 2.6fr) minmax(280px, 1fr);
    }
}
#admin-dashboard .sp-bottom-grid > .sp-card {
    display: flex;
    flex-direction: column;
    min-width: 0;
}

/* Tabel: tanpa min-width paksa, tanpa scroll pada layar normal */
#admin-dashboard .sp-table-card .sp-table-scroll {
    flex: 1 1 auto;
    width: 100%;
    overflow-x: auto;
}
#admin-dashboard .sp-table-card table {
    width: 100%;
    height: 100%;
    min-width: 0;
    table-layout: auto;
    border-collapse: collapse;
}
#admin-dashboard .sp-table-card :is(th, td) { width: auto; }
#admin-dashboard .sp-table-card th {
    padding: 11px 10px;
    border-bottom: 1px solid #e2e8f0;
    background: #ffffff;
    color: #334155;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0;
    text-align: left;
    text-transform: none;
    white-space: nowrap;
}
#admin-dashboard .sp-table-card td {
    padding: 12px 10px;
    border-bottom: 1px solid #f1f5f9;
    color: #475569;
    font-size: 12.5px;
    vertical-align: middle;
}
#admin-dashboard .sp-table-card tbody tr:last-child td { border-bottom: 0; }

/* Kolom pendek: lebar mengikuti isi, tidak pernah patah */
#admin-dashboard .sp-table-card .sp-col-fit {
    width: 1%;
    white-space: nowrap;
}
/* Kolom teks panjang: dipotong dengan elipsis (isi lengkap di atribut title) */
#admin-dashboard .sp-table-card .sp-col-flex {
    max-width: 0;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
#admin-dashboard .sp-table-card .sp-col-judul  { width: 46%; }
#admin-dashboard .sp-table-card .sp-col-lokasi { width: 38%; }
/* Kategori memakai label singkat, jadi lebarnya mengikuti isi dan tidak dipotong */
#admin-dashboard .sp-table-card .sp-col-kategori {
    width: 1%;
    max-width: none;
    overflow: visible;
    white-space: nowrap;
}

#admin-dashboard .sp-code {
    color: #475569 !important;
    font-family: Inter, ui-sans-serif, system-ui, sans-serif;
    font-size: 12px;
    font-weight: 600;
    font-variant-numeric: tabular-nums;
}
/* slate-400 gagal kontras 4,5:1 (proposal 7.5 butir 1) -> slate-500 */
#admin-dashboard .sp-time {
    color: #64748b !important;
    white-space: nowrap;
}

#admin-dashboard .sp-priority-badge,
#admin-dashboard .sp-status {
    display: inline-flex;
    align-items: center;
    padding: 3px 9px;
    border-radius: 6px;
    font-size: 11.5px;
    font-weight: 700;
    line-height: 1.4;
    white-space: nowrap;
}
#admin-dashboard .sp-priority-badge.none {
    border: 1px solid #e2e8f0;
    background: #f8fafc;
    color: #475569;
}

/* Aktivitas: timeline vertikal sesuai mockup */
#admin-dashboard .sp-activity-card .sp-activity-list {
    flex: 1 1 auto;
    display: flex;
    flex-direction: column;
}
#admin-dashboard .sp-activity-item {
    position: relative;
    flex: 1 1 0;
    display: grid;
    grid-template-columns: 28px minmax(0, 1fr) auto;
    align-items: center;
    gap: 12px;
    min-height: 56px;
    padding: 10px 16px;
    border-bottom: 0;
}
/* Garis penghubung antar ikon: pusat ikon = padding 16px + 14px */
#admin-dashboard .sp-activity-item::before,
#admin-dashboard .sp-activity-item::after {
    content: "";
    position: absolute;
    left: 29.25px;
    width: 1.5px;
    background: #cbd5e1;
}
#admin-dashboard .sp-activity-item::before {
    top: 0;
    height: calc(50% - 14px);
}
#admin-dashboard .sp-activity-item::after {
    top: calc(50% + 14px);
    bottom: 0;
}
#admin-dashboard .sp-activity-item:first-child::before { display: none; }

#admin-dashboard .sp-activity-icon {
    position: relative;
    z-index: 1;
    width: 28px;
    height: 28px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 1.5px solid #334155;
    border-radius: 50%;
    background: #fff;
    color: #334155;
}
#admin-dashboard .sp-activity-icon svg {
    width: 14px;
    height: 14px;
    fill: none;
    stroke: currentColor;
    stroke-width: 1.7;
    stroke-linecap: round;
    stroke-linejoin: round;
}
#admin-dashboard .sp-activity-item p {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    margin: 0;
    color: #0f172a;
    font-size: 12.5px;
    font-weight: 600;
    line-height: 1.4;
}
#admin-dashboard .sp-activity-meta {
    display: block;
    margin-top: 2px;
    overflow: hidden;
    color: #64748b;
    font-size: 11.5px;
    text-overflow: ellipsis;
    white-space: nowrap;
}
#admin-dashboard .sp-activity-time {
    color: #64748b;
    font-size: 11.5px;
    white-space: nowrap;
}



//* =====================================================
   TOMBOL LOKASI MENYATU DENGAN ZOOM LEAFLET
   Ukuran disamakan dengan tombol zoom bawaan Leaflet.
===================================================== */

#admin-dashboard .leaflet-control-zoom .sp-locate-button {
    display: flex;
    align-items: center;
    justify-content: center;

    box-sizing: border-box;
    width: 26px;
    height: 26px;
    margin: 0;
    padding: 0;

    border: 0;
    border-top: 1px solid #ccc;
    border-radius: 0;

    background: #ffffff;
    color: #334155;
    text-decoration: none;
    cursor: pointer;
}

#admin-dashboard .leaflet-control-zoom .sp-locate-button:hover {
    background: #ecfdf5;
    color: #047857;
}

#admin-dashboard .leaflet-control-zoom .sp-locate-button:focus-visible {
    position: relative;
    z-index: 1000;
    outline: 2px solid #059669;
    outline-offset: -2px;
}

#admin-dashboard .leaflet-control-zoom .sp-locate-button svg {
    display: block;
    flex: 0 0 auto;
}

#admin-dashboard .leaflet-control-zoom .sp-locate-button.is-locating svg {
    animation: sp-crosshair-pulse 1s ease-in-out infinite;
}

@keyframes sp-crosshair-pulse {
    50% {
        opacity: 0.45;
        transform: scale(0.88);
    }
}

@media (prefers-reduced-motion: reduce) {
    #admin-dashboard .leaflet-control-zoom .sp-locate-button.is-locating svg {
        animation: none;
    }
}

#admin-dashboard .sp-locate-button {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 30px;
    height: 30px;
    padding: 0;
    border: 0;
    border-top: 1px solid #ccc;
    background: #ffffff;
    color: #334155;
    text-decoration: none;
    cursor: pointer;
    box-sizing: border-box;
}

#admin-dashboard .sp-locate-button:hover {
    background: #ecfdf5;
    color: #047857;
}

#admin-dashboard .sp-locate-button:focus-visible {
    position: relative;
    z-index: 2;
    outline: 2px solid #059669;
    outline-offset: -2px;
}

#admin-dashboard .sp-locate-button.is-locating svg {
    animation: sp-crosshair-pulse 1s ease-in-out infinite;
}

@keyframes sp-crosshair-pulse {
    50% {
        opacity: 0.45;
        transform: scale(0.88);
    }
}

@media (prefers-reduced-motion: reduce) {
    #admin-dashboard .sp-locate-button.is-locating svg {
        animation: none;
    }
}


#admin-dashboard .sp-locate-button:hover {
    background: #ecfdf5;
    color: #047857;
}

#admin-dashboard .sp-locate-button:focus-visible {
    position: relative;
    z-index: 2;
    outline: 2px solid #059669;
    outline-offset: -2px;
}

#admin-dashboard .sp-locate-button:disabled {
    cursor: wait;
    color: #059669;
}

#admin-dashboard .sp-locate-button.is-locating svg {
    animation: sp-crosshair-pulse 1s ease-in-out infinite;
}

@keyframes sp-crosshair-pulse {
    50% {
        opacity: 0.45;
        transform: scale(0.88);
    }
}

@media (prefers-reduced-motion: reduce) {
    #admin-dashboard .sp-locate-button.is-locating svg {
        animation: none;
    }
}

</style>

@php
    $statusTotal = max(1, array_sum($statusChart ?? []));

    $statusRows = [
        [
            'key' => 'menunggu_verifikasi',
            'label' => 'Menunggu Verifikasi',
            'class' => 'pending',
            'value' => (int) ($statusChart['menunggu_verifikasi'] ?? 0),
        ],
        [
            'key' => 'diverifikasi',
            'label' => 'Diverifikasi',
            'class' => 'verified',
            'value' => (int) ($statusChart['diverifikasi'] ?? 0),
        ],
        [
            'key' => 'dalam_perbaikan',
            'label' => 'Dalam Perbaikan',
            'class' => 'progress',
            'value' => (int) ($statusChart['dalam_perbaikan'] ?? 0),
        ],
        [
            'key' => 'selesai',
            'label' => 'Selesai',
            'class' => 'done',
            'value' => (int) ($statusChart['selesai'] ?? 0),
        ],
        [
            'key' => 'ditolak',
            'label' => 'Ditolak',
            'class' => 'rejected',
            'value' => (int) ($statusChart['ditolak'] ?? 0),
        ],
    ];

    $statusStops = [];
    $cursor = 0;

    $statusColors = [
        'pending' => '#D97706',
        'verified' => '#059669',
        'progress' => '#0D9488',
        'done' => '#16A34A',
        'rejected' => '#DC2626',
    ];

    foreach ($statusRows as $row) {
        $next = $cursor + (($row['value'] / $statusTotal) * 100);

        $statusStops[] =
            $statusColors[$row['class']]
            . ' '
            . $cursor
            . '% '
            . $next
            . '%';

        $cursor = $next;
    }

        $kategoriShortLabel = static function ($nama) {
        $nama = strtolower((string) $nama);

        return match (true) {
            str_contains($nama, 'guiding block') => 'Guiding Block',
            str_contains($nama, 'trotoar') => 'Trotoar',
            str_contains($nama, 'ramp') => 'Ramp / Akses Masuk',
            str_contains($nama, 'penyeberangan') => 'Fasilitas Penyeberangan',
            default => 'Lainnya',
        };
    };

    // Kategori dengan label singkat yang sama digabung, bukan ditampilkan dobel.
    $kategoriDashboard = ($perKategori ?? collect())
        ->groupBy(fn ($kategori) => $kategoriShortLabel($kategori->nama))
        ->map(fn ($grup, $label) => (object) [
            'label' => $label,
            'laporan_count' => (int) $grup->sum('laporan_count'),
        ])
        ->sortByDesc('laporan_count')
        ->take(5)
        ->values();

    $maxKategori = max(1, (int) $kategoriDashboard->max('laporan_count'));

    // "10 menit yang lalu" -> "10 menit lalu" (sesuai mockup)
    $waktuRelatif = static function ($tanggal) {
        if (! $tanggal) {
            return '—';
        }

        return str_replace(
            ' yang lalu',
            ' lalu',
            $tanggal->copy()->locale('id')->diffForHumans()
        );
    };

    // ---- Tren 7 hari: koordinat SVG (sumbu Y 4 tick seperti mockup) ----
    $trendItems = collect($tren7Hari ?? [])->values();
    $trendPeak  = (int) $trendItems->max('jumlah');

    $trendStep = collect([1, 2, 5, 10, 20, 25, 50, 100, 200, 250, 500, 1000, 2000, 5000])
        ->first(fn ($langkah) => $langkah >= ($trendPeak / 3))
        ?? (int) ceil($trendPeak / 3);

    $trendTop = $trendStep * 3;

    $trendTicks = collect(range(0, 3))->map(fn ($k) => [
        'value' => $k * $trendStep,
        'y'     => round(130 - ($k / 3) * 116, 2),
    ]);

    $trendLastIndex = max(1, $trendItems->count() - 1);

    $trendPlot = $trendItems->map(function ($item, $i) use ($trendTop, $trendLastIndex) {
        $jumlah = (int) ($item['jumlah'] ?? 0);

        return [
            'x'      => round(34 + ($i / $trendLastIndex) * 274, 2),
            'y'      => round(130 - ($jumlah / $trendTop) * 116, 2),
            'jumlah' => $jumlah,
            'label'  => \Carbon\Carbon::parse($item['tanggal'])->format('d/m'),
        ];
    });

    $trendPolyline = $trendPlot->map(fn ($p) => $p['x'] . ',' . $p['y'])->implode(' ');
    $trendSummary  = $trendPlot->map(fn ($p) => $p['label'] . ': ' . $p['jumlah'] . ' laporan')->implode('; ');
    $trendLast     = $trendItems->last();

    $change = $persentasePerubahan;
@endphp


<div
    id="admin-dashboard"
    data-live-url="{{ route('admin.dashboard.live') }}"
    data-dashboard-url="{{ route('admin.dashboard') }}"
    class="smartpath-dashboard"
>

    <div class="smartpath-dashboard__inner">

        {{-- =====================================================
             KPI
        ====================================================== --}}

        <section
            class="sp-kpi-grid"
            aria-label="Ringkasan laporan SmartPath"
        >

            <article class="sp-card sp-kpi sp-kpi--total">

                <div class="sp-kpi__head">

                    <div
                        class="sp-kpi__icon"
                        aria-hidden="true"
                    >
                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.7"
                        >
                            <path
                                stroke-linejoin="round"
                                d="M6 3h9l3 3v15H6z"
                            />

                            <path
                                stroke-linecap="round"
                                d="M9 11h6M9 15h6M9 7h3"
                            />
                        </svg>
                    </div>

                    <span>Total Laporan</span>

                </div>

                <div class="sp-kpi__value-row">

                    <div>

                        <strong id="admin-kpi-total">
                            {{ number_format($statistik['total'] ?? 0) }}
                        </strong>

                        <span class="sp-kpi__caption">
                            Seluruh laporan
                        </span>

                    </div>

                    <svg
                        class="sp-sparkline"
                        viewBox="0 0 64 40"
                        aria-hidden="true"
                    >
                        <polyline
                            points="2,31 14,25 25,29 37,17 48,21 62,7"
                        />

                        <path d="M57 7h5v5"/>
                    </svg>

                </div>

                <div class="sp-kpi__foot">

                    <span
                        class="sp-kpi__change
                        {{ $change !== null && $change < 0 ? 'is-down' : '' }}"
                    >
                        {{
                            $change === null
                                ? '—'
                                : (
                                    ($change >= 0 ? '↑ ' : '↓ ')
                                    . abs($change)
                                    . '%'
                                )
                        }}
                    </span>

                    <span>
                        dari minggu lalu
                    </span>

                </div>

            </article>


            <article class="sp-card sp-kpi sp-kpi--pending">

                <div class="sp-kpi__head">

                    <div
                        class="sp-kpi__icon"
                        aria-hidden="true"
                    >
                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.7"
                        >
                            <circle
                                cx="12"
                                cy="12"
                                r="8.5"
                            />

                            <path
                                stroke-linecap="round"
                                d="M12 7v5l3 2"
                            />
                        </svg>
                    </div>

                    <span>
                        Pending Verifikasi
                    </span>

                </div>

                <strong
                    id="admin-kpi-menunggu"
                    class="sp-kpi__number"
                >
                    {{ number_format($statistik['menunggu'] ?? 0) }}
                </strong>

                <span class="sp-kpi__caption">
                    Perlu ditindaklanjuti
                </span>

                <div class="sp-kpi__foot">
                    <span class="sp-status-dot pending"></span>
                    <span>Validasi laporan masuk</span>
                </div>

            </article>


            <article class="sp-card sp-kpi sp-kpi--verified">

                <div class="sp-kpi__head">

                    <div
                        class="sp-kpi__icon"
                        aria-hidden="true"
                    >
                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.7"
                        >
                            <circle
                                cx="12"
                                cy="12"
                                r="8.5"
                            />

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="m8 12 2.6 2.6L16.5 9"
                            />
                        </svg>
                    </div>

                    <span>
                        Laporan Diverifikasi
                    </span>

                </div>

                <strong
                    id="admin-kpi-diverifikasi"
                    class="sp-kpi__number"
                >
                    {{ number_format($statistik['diverifikasi'] ?? 0) }}
                </strong>

                <span class="sp-kpi__caption">
                    Selesai diverifikasi
                </span>

                <div class="sp-kpi__foot">
                    <span class="sp-status-dot verified"></span>
                    <span>Valid dan dapat diprioritaskan</span>
                </div>

            </article>


            <article class="sp-card sp-kpi sp-kpi--high">

                <div class="sp-kpi__head">

                    <div
                        class="sp-kpi__icon"
                        aria-hidden="true"
                    >
                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.7"
                        >
                            <path
                                stroke-linejoin="round"
                                d="m12 3 9 17H3L12 3Z"
                            />

                            <path
                                stroke-linecap="round"
                                d="M12 9v4M12 16h.01"
                            />
                        </svg>
                    </div>

                    <span>
                        Prioritas Tinggi
                    </span>

                </div>

                <strong
                    id="admin-kpi-kritis"
                    class="sp-kpi__number"
                >
                    {{ number_format($statistik['kritis'] ?? 0) }}
                </strong>

                <span class="sp-kpi__caption">
                    Perlu tindakan cepat
                </span>

                <div class="sp-kpi__foot">
                    <span class="sp-status-dot rejected"></span>
                    <span>Skor prioritas ≥ 70</span>
                </div>

            </article>

        </section>


                {{-- =====================================================
             MAP + TOP PRIORITY
        ====================================================== --}}

        <section class="sp-main-grid">

            {{-- ================= KOLOM KIRI: PETA ================= --}}
            <article id="map-section" class="sp-card sp-map-card">

                <div class="sp-card__header">

                    <h2>Peta Sebaran Hambatan</h2>

                    <div class="sp-map-filters">

                        <label class="sr-only" for="map-category-filter">Kategori</label>
                        <select id="map-category-filter">
                            <option value="">Semua Kategori</option>
                            @foreach($perKategori as $kategori)
                                <option value="{{ $kategori->id }}">{{ $kategori->nama }}</option>
                            @endforeach
                        </select>

                        <label class="sr-only" for="map-period-filter">Periode</label>
                        <select id="map-period-filter">
                            <option value="7">7 Hari Terakhir</option>
                            <option value="30">30 Hari Terakhir</option>
                            <option value="0">Semua Waktu</option>
                        </select>

                        <button
                            id="map-recenter"
                            type="button"
                            aria-label="Corong Filter Peta"
                            title="Corong Filter Peta"
                            class="inline-flex items-center justify-center p-2 text-slate-600 bg-white border border-slate-300 rounded-lg shadow-sm hover:bg-slate-50 hover:text-emerald-600 hover:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 transition-all duration-150"
                        >
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3" />
                            </svg>
                        </button>

                    </div>

                </div>

                <div class="sp-map-wrap">

                    <div
                        id="admin-map"
                        aria-label="Peta interaktif laporan hambatan aksesibilitas Kota Depok"
                    ></div>

                    <div class="sp-map-legend" aria-label="Legenda prioritas">
                        <strong>Legenda Prioritas</strong>
                        <span><i class="sp-legend-pin high"></i> Tinggi</span>
                        <span><i class="sp-legend-pin medium"></i> Sedang</span>
                        <span><i class="sp-legend-pin low"></i> Rendah</span>
                    </div>

                    <div class="sp-map-summary" aria-label="Ringkasan peta">
                        <div>
                            <strong id="admin-map-total">{{ count($petaLaporan) }}</strong>
                            <span>Titik Aktif</span>
                        </div>
                        <div>
                            <strong id="admin-map-area">{{ $areaDipantau }}</strong>
                            <span>Area Dipantau</span>
                        </div>
                        <div>
                            <strong>Depok</strong>
                            <span>Wilayah</span>
                        </div>
                    </div>

                </div>

            </article>


            {{-- ============ KOLOM KANAN: TOP PRIORITAS ============ --}}
            <article id="priority-panel" class="sp-card sp-priority-card">

                <div class="sp-card__header">

                    <h2>Top Prioritas Hari Ini</h2>

                    <a href="{{ route('admin.verifikasi.index') }}">
                        Lihat semua <span aria-hidden="true">→</span>
                    </a>

                </div>

                <div class="sp-priority-list">

                    @forelse($laporanPrioritasTinggi as $laporan)

                        @php
                            $score = $laporan->skor_prioritas !== null
                                ? (float) $laporan->skor_prioritas
                                : null;

                            $priorityClass = $score === null
                                ? 'low'
                                : ($score >= 70 ? 'high' : ($score >= 40 ? 'medium' : 'low'));

                            $photo = $laporan->foto->firstWhere('adalah_utama', true)
                                ?? $laporan->foto->sortBy('urutan')->first();
                        @endphp

                        <a
                            class="sp-priority-item"
                            href="{{ route('admin.verifikasi.show', $laporan) }}"
                        >

                            <span class="sp-priority-thumb">
                                @if($photo)
                                    <img src="{{ $photo->url }}" alt="" loading="lazy">
                                @else
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                        <rect x="4" y="4" width="16" height="16" rx="2" />
                                        <path d="m6 18 5-5 3 3 2-2 2 4" />
                                    </svg>
                                @endif
                            </span>

                            <span class="sp-priority-body">

                                <span class="sp-priority-title">{{ $laporan->judul }}</span>

                                <span class="sp-priority-address">
                                    {{ $laporan->alamat_lengkap ?: ($laporan->wilayah?->nama ?? 'Lokasi tidak tersedia') }}
                                </span>

                                <span class="sp-priority-meta">
                                    <span>♟ {{ number_format((int) $laporan->jumlah_pelapor) }} pelapor</span>
                                    <span>
                                        ⌖
                                        {{
                                            $laporan->jarak_fasilitas_meter !== null
                                                ? number_format((float) $laporan->jarak_fasilitas_meter, 0) . ' m'
                                                : '—'
                                        }}
                                    </span>
                                </span>

                            </span>

                            <span class="sp-priority-side">
                                <span class="sp-priority-badge {{ $priorityClass }}">
                                    {{ $laporan->tingkat_prioritas }}
                                </span>
                                <span class="sp-chevron" aria-hidden="true">›</span>
                            </span>

                        </a>

                    @empty

                        <div class="sp-empty-state">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                <path stroke-linejoin="round" d="m12 3 9 17H3L12 3Z" />
                                <path stroke-linecap="round" d="M12 9v4M12 16h.01" />
                            </svg>
                            <span>Belum ada laporan dengan skor prioritas.</span>
                        </div>

                    @endforelse

                </div>

            </article>

        </section>


        {{-- =====================================================
             ANALYTICS
        ====================================================== --}}

        <section
            id="analytics"
            class="sp-analytics-grid"
        >

            {{-- DISTRIBUSI STATUS --}}

            <article class="sp-card sp-analytics-card">

                <div class="sp-card__header">

                    <h2>
                        Distribusi Status
                    </h2>

                      <a href="{{ route('admin.verifikasi.index') }}"
       class="text-sm font-semibold text-emerald-700 hover:text-emerald-900 hover:underline">
        Semua
    </a>

                </div>


                <div class="sp-status-chart">

                    <div
                        class="sp-donut"
                        style="--donut: conic-gradient({{ implode(', ', $statusStops) }});"
                        role="img"
                        aria-label="Distribusi status laporan"
                    >

                        <div>
                            <strong>
                                {{ number_format($statistik['total'] ?? 0) }}
                            </strong>

                            <span>
                                Total
                            </span>
                        </div>

                    </div>


                    <div class="sp-status-list">

                        @foreach($statusRows as $row)

                            <div class="sp-status-row">

                                <span>
                                    <i
                                        class="sp-status-dot {{ $row['class'] }}"
                                    ></i>

                                    {{ $row['label'] }}
                                </span>

                                <strong>

                                    {{ $row['value'] }}

                                    <small>
                                        ({{
                                            round(
                                                (
                                                    $row['value']
                                                    / $statusTotal
                                                ) * 100
                                            )
                                        }}%)
                                    </small>

                                </strong>

                            </div>

                        @endforeach

                    </div>

                </div>

            </article>


            {{-- KATEGORI --}}

            <article class="sp-card sp-analytics-card">

                <div class="sp-card__header">

                    <h2>
                        Laporan per Kategori
                    </h2>

                    <span>
                        Aktif
                    </span>

                </div>


                <div class="sp-category-list">

                    @forelse($kategoriDashboard as $index => $kategori)

                        @php
                            $categoryWidth =
                                (
                                    (int) $kategori->laporan_count
                                    / $maxKategori
                                ) * 100;
                        @endphp

                        <div class="sp-category-row">

                            <div>

                                <span>
                                    {{ $kategori->label }}
                                </span>

                                <strong>
                                    {{ (int) $kategori->laporan_count }}
                                </strong>

                            </div>

                            <div class="sp-category-track">

                                <span
                                    class="sp-category-bar category-{{ $index }}"
                                    style="width: {{ $categoryWidth }}%"
                                ></span>

                            </div>

                        </div>

                    @empty

                        <div class="sp-empty-inline">
                            Belum ada data kategori.
                        </div>

                    @endforelse

                </div>

            </article>


                       {{-- TREND --}}

            <article class="sp-card sp-analytics-card">

                <div class="sp-card__header">
                    <h2>Tren Laporan</h2>
                    <span>7 Hari</span>
                </div>

                <div class="sp-trend-wrap">

                    <svg
                        class="sp-trend-chart"
                        viewBox="0 0 320 160"
                        role="img"
                        aria-labelledby="trend-title trend-desc"
                    >
                        <title id="trend-title">Tren laporan tujuh hari terakhir</title>
                        <desc id="trend-desc">{{ $trendSummary }}</desc>

                        @foreach($trendTicks as $tick)
                            <line x1="34" y1="{{ $tick['y'] }}" x2="308" y2="{{ $tick['y'] }}"/>
                            <text x="26" y="{{ $tick['y'] + 3 }}" text-anchor="end">{{ $tick['value'] }}</text>
                        @endforeach

                        <polyline pathLength="1000" points="{{ $trendPolyline }}"/>

                        @foreach($trendPlot as $point)
                            <circle
                                cx="{{ $point['x'] }}"
                                cy="{{ $point['y'] }}"
                                r="3"
                                @class(['is-last' => $loop->last])
                            />
                            <text x="{{ $point['x'] }}" y="152" text-anchor="middle">{{ $point['label'] }}</text>
                        @endforeach
                    </svg>

                    @if($trendLast)
                        <div class="sp-trend-note">
                            <strong>
                                {{ \Carbon\Carbon::parse($trendLast['tanggal'])->locale('id')->translatedFormat('d M') }}
                            </strong>
                            <span>{{ (int) ($trendLast['jumlah'] ?? 0) }} laporan</span>
                        </div>
                    @endif

                </div>

            </article>

        </section>


        {{-- =====================================================
             LAPORAN TERBARU + AKTIVITAS
        ====================================================== --}}

        <section class="sp-bottom-grid">

            <article class="sp-card sp-table-card">

                <div class="sp-card__header">
                    <h2>Laporan Terbaru</h2>

                    <a href="{{ route('admin.laporan.index') }}">
                        Lihat semua <span aria-hidden="true">→</span>
                    </a>
                </div>

                <div class="sp-table-scroll">
                    <table>
                        <thead>
                            <tr>
                                <th scope="col" class="sp-col-fit">Kode</th>
                                <th scope="col" class="sp-col-flex sp-col-judul">Judul Laporan</th>
                                <th scope="col" class="sp-col-flex sp-col-kategori">Kategori</th>
                                <th scope="col" class="sp-col-flex sp-col-lokasi">Lokasi</th>
                                <th scope="col" class="sp-col-fit">Prioritas</th>
                                <th scope="col" class="sp-col-fit">Status</th>
                                <th scope="col" class="sp-col-fit">Waktu</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($laporanTerbaru as $item)

                                @php
                                    $score = $item->skor_prioritas !== null
                                        ? (float) $item->skor_prioritas
                                        : null;

                                    $priorityClass = $score === null
                                        ? 'none'
                                        : ($score >= 70 ? 'high' : ($score >= 40 ? 'medium' : 'low'));

                                    $statusClass = match ($item->status) {
                                        'menunggu_verifikasi' => 'pending',
                                        'diverifikasi'        => 'verified',
                                        'dalam_perbaikan'     => 'progress',
                                        'selesai'             => 'done',
                                        'ditolak'             => 'rejected',
                                        default               => 'default',
                                    };

                                    // Label ringkas seperti mockup; label lengkap ada di title.
                                    $statusRingkas = match ($item->status) {
                                        'menunggu_verifikasi' => 'Menunggu',
                                        'diverifikasi'        => 'Diverifikasi',
                                        'dalam_perbaikan'     => 'Dalam Perbaikan',
                                        'selesai'             => 'Selesai',
                                        'ditolak'             => 'Ditolak',
                                        default               => $item->status_label,
                                    };

                                    $lokasi = $item->alamat_lengkap
                                        ?: ($item->wilayah?->nama ?? '—');
                                @endphp

                                <tr>
                                    <td class="sp-code sp-col-fit">{{ $item->kode_laporan }}</td>

                                    <td class="sp-col-flex sp-col-judul">
                                        <a
                                            href="{{ route('admin.verifikasi.show', $item) }}"
                                            class="sp-table-title"
                                            title="{{ $item->judul }}"
                                        >{{ $item->judul }}</a>
                                    </td>

                                    <td class="sp-col-flex sp-col-kategori"
                                        title="{{ $item->kategoriHambatan?->nama }}">
                                        {{ $kategoriShortLabel($item->kategoriHambatan?->nama) }}
                                    </td>

                                    <td class="sp-col-flex sp-col-lokasi sp-location-cell"
                                        title="{{ $lokasi }}">
                                        {{ $lokasi }}
                                    </td>

                                    <td class="sp-col-fit">
                                        <span class="sp-priority-badge {{ $priorityClass }}">
                                            @if($score !== null)
                                                {{ $item->tingkat_prioritas }}
                                            @else
                                                <span aria-hidden="true">—</span>
                                                <span class="sr-only">Belum dinilai</span>
                                            @endif
                                        </span>
                                    </td>

                                    <td class="sp-col-fit">
                                        <span
                                            class="sp-status {{ $statusClass }}"
                                            title="{{ $item->status_label }}"
                                        >{{ $statusRingkas }}</span>
                                    </td>

                                    <td class="sp-time sp-col-fit">
                                        {{ $waktuRelatif($item->created_at) }}
                                    </td>
                                </tr>

                            @empty
                                <tr>
                                    <td colspan="7" class="sp-table-empty">
                                        Belum ada laporan yang tercatat.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </article>


            <article class="sp-card sp-activity-card">

                <div class="sp-card__header">
                    <h2>Aktivitas Terbaru</h2>

                    <a href="{{ route('admin.audit.index') }}">
                        Lihat semua <span aria-hidden="true">→</span>
                    </a>
                </div>

                                @php
                    // Path SVG (viewBox 24x24, gaya outline) per jenis ikon.
                    $svgAktivitas = [
                        'centang' => '<path d="M8 12.5l2.7 2.7L16 9.5"/>',
                        'dokumen' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M16 13H8M16 17H8M10 9H8"/>',
                        'gerigi'  => '<path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/>',
                        'jempol'  => '<path d="M7 10v12"/><path d="M15 5.88 14 10h5.83a2 2 0 0 1 1.92 2.56l-2.33 8A2 2 0 0 1 17.5 22H4a2 2 0 0 1-2-2v-8a2 2 0 0 1 2-2h2.76a2 2 0 0 0 1.79-1.11L12 2a3.13 3.13 0 0 1 3 3.88Z"/>',
                        'silang'  => '<path d="M9 9l6 6M15 9l-6 6"/>',
                        'kembali' => '<path d="M9 14 4 9l5-5"/><path d="M4 9h10.5a5.5 5.5 0 0 1 0 11H11"/>',
                        'cabang'  => '<path d="M16 3h5v5M4 20 21 3M21 16v5h-5M15 15l6 6M4 4l5 5"/>',
                        'pensil'  => '<path d="M17 3a2.85 2.85 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/>',
                        'sampah'  => '<path d="M3 6h18"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>',
                    ];

                    // %s diisi nama pengguna pelaku aksi.
                    $petaAktivitas = [
                        'buat_laporan'                  => ['dokumen', 'Laporan baru masuk'],
                        'verifikasi_setujui'            => ['centang', 'Laporan diverifikasi oleh %s'],
                        'verifikasi_tolak'              => ['silang',  'Laporan ditolak oleh %s'],
                        'verifikasi_kembalikan'         => ['kembali', 'Laporan dikembalikan oleh %s'],
                        'laporan_dipisahkan_dari_induk' => ['cabang',  'Laporan dipisahkan dari induk'],
                        'status_dalam_perbaikan'        => ['gerigi',  'Status berubah menjadi Dalam Perbaikan'],
                        'status_selesai'                => ['jempol',  'Laporan selesai ditangani'],
                        'ubah_laporan'                  => ['pensil',  'Laporan diperbarui'],
                        'hapus_laporan'                 => ['sampah',  'Laporan dihapus'],
                    ];

                    $laporanAktivitas = $laporanAktivitas ?? collect();
                @endphp

                <div class="sp-activity-list" role="list">

                    @forelse($aktivitasTerbaru as $aktivitas)

                        @php
                            $namaPelaku = $aktivitas->pengguna?->nama_lengkap ?? 'Sistem';
                            $pemetaan   = $petaAktivitas[$aktivitas->aksi] ?? null;

                            $kunciIkon = $pemetaan[0] ?? 'dokumen';

                            $teksAktivitas = $pemetaan
                                ? sprintf($pemetaan[1], $namaPelaku)
                                : ($aktivitas->keterangan
                                    ?: ucfirst(str_replace('_', ' ', $aktivitas->aksi)));

                            $laporanTerkait = $laporanAktivitas->get($aktivitas->id_terkait);

                            $teksMeta = $laporanTerkait
                                ? trim($laporanTerkait->kode_laporan
                                    . ' • '
                                    . ($laporanTerkait->kategoriHambatan?->nama ?? '-'), ' •-')
                                : $namaPelaku;
                        @endphp

                        <div class="sp-activity-item" role="listitem">

                            <span class="sp-activity-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" focusable="false">
                                    {!! $svgAktivitas[$kunciIkon] ?? $svgAktivitas['dokumen'] !!}
                                </svg>
                            </span>

                            <div>
                                <p title="{{ $teksAktivitas }}">{{ $teksAktivitas }}</p>
                                <span class="sp-activity-meta" title="{{ $teksMeta }}">
                                    {{ $teksMeta }}
                                </span>
                            </div>

                            <time
                                class="sp-activity-time"
                                datetime="{{ $aktivitas->created_at?->toIso8601String() }}"
                                title="{{ $aktivitas->created_at?->translatedFormat('d M Y H:i') }}"
                            >
                                {{ $waktuRelatif($aktivitas->created_at) }}
                            </time>

                        </div>

                    @empty
                        <div class="sp-empty-inline">
                            Belum ada aktivitas laporan.
                        </div>
                    @endforelse

                </div>

            </article>

        </section>

    </div>
</div>

@endsection


@push('styles')

<link
    rel="stylesheet"
    href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
    crossorigin=""
>

@endpush


@push('scripts')

<script
    src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
    crossorigin=""
></script>

<script
    id="smartpath-map-data"
    type="application/json"
>{!! json_encode(
    $petaLaporan,
    JSON_HEX_TAG
    | JSON_HEX_AMP
    | JSON_HEX_APOS
    | JSON_HEX_QUOT
) !!}</script>

<script
    src="{{ asset('js/smartpath-admin.js') }}"
    defer
></script>

@endpush