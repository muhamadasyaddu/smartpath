@extends('layouts.admin')

@section('title', 'Dashboard Administrator')
@section('page_title', 'Dashboard Administrator')

@section('content')
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

    $maxKategori = max(
        1,
        (int) (($perKategori ?? collect())->max('laporan_count') ?? 0)
    );

    $kategoriDashboard = ($perKategori ?? collect())
        ->sortByDesc('laporan_count')
        ->take(5)
        ->values();

    $kategoriShortLabel = static function ($nama) {
        $nama = (string) $nama;

        return match (true) {
            str_contains(
                strtolower($nama),
                'guiding block'
            ) => 'Guiding Block',

            str_contains(
                strtolower($nama),
                'trotoar'
            ) => 'Trotoar',

            str_contains(
                strtolower($nama),
                'ramp'
            ) => 'Ramp / Akses Masuk',

            str_contains(
                strtolower($nama),
                'penyeberangan'
            ) => 'Fasilitas Penyeberangan',

            default => 'Lainnya',
        };
    };

    $trendItems = collect($tren7Hari ?? []);

    $trendMax = max(
        1,
        (int) $trendItems->max('jumlah')
    );

    $trendCount = max(
        1,
        $trendItems->count() - 1
    );

    $trendPoints = [];

    foreach ($trendItems as $index => $item) {
        $x =
            4
            +
            (
                ($index / $trendCount)
                * 92
            );

        $y =
            86
            -
            (
                (
                    (int) ($item['jumlah'] ?? 0)
                    / $trendMax
                )
                * 68
            );

        $trendPoints[] =
            round($x, 2)
            . ','
            . round($y, 2);
    }

    $trendLast = $trendItems->last();

    $change = $persentasePerubahan;
@endphp

<div
    id="admin-dashboard"
    data-live-url="{{ route('admin.dashboard.live') }}"
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

            <article
                id="map-section"
                class="sp-card sp-map-card"
            >

                <div class="sp-card__header">

                    <div>

                        <h2>
                            Peta Sebaran Hambatan
                        </h2>

                        <p>
                            Titik laporan aksesibilitas Kota Depok
                        </p>

                    </div>

                    <div class="sp-map-filters">

                        <label
                            class="sr-only"
                            for="map-category-filter"
                        >
                            Kategori
                        </label>

                        <select id="map-category-filter">

                            <option value="">
                                Semua Kategori
                            </option>

                            @foreach($perKategori as $kategori)

                                <option value="{{ $kategori->id }}">
                                    {{ $kategori->nama }}
                                </option>

                            @endforeach

                        </select>


                        <label
                            class="sr-only"
                            for="map-period-filter"
                        >
                            Periode
                        </label>

                        <select id="map-period-filter">

                            <option value="7">
                                7 Hari Terakhir
                            </option>

                            <option value="30">
                                30 Hari Terakhir
                            </option>

                            <option value="0">
                                Semua Waktu
                            </option>

                        </select>


                        <button
                            id="map-recenter"
                            type="button"
                            aria-label="Pusatkan peta ke Kota Depok"
                            title="Pusatkan peta"
                        >

                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.7"
                                aria-hidden="true"
                            >
                                <circle
                                    cx="12"
                                    cy="12"
                                    r="3"
                                />

                                <path
                                    stroke-linecap="round"
                                    d="M12 2v3M12 19v3M2 12h3M19 12h3"
                                />
                            </svg>

                        </button>

                    </div>

                </div>


                <div class="sp-map-wrap">

                    <div
                        id="admin-map"
                        aria-label="Peta interaktif laporan hambatan aksesibilitas Kota Depok"
                    ></div>


                    <div
                        class="sp-map-legend"
                        aria-label="Legenda prioritas"
                    >

                        <strong>
                            Legenda Prioritas
                        </strong>

                        <span>
                            <i class="sp-legend-pin high"></i>
                            Tinggi
                        </span>

                        <span>
                            <i class="sp-legend-pin medium"></i>
                            Sedang
                        </span>

                        <span>
                            <i class="sp-legend-pin low"></i>
                            Rendah
                        </span>

                    </div>


                    <div
                        class="sp-map-summary"
                        aria-label="Ringkasan peta"
                    >

                        <div>
                            <strong id="admin-map-total">
                                {{ count($petaLaporan) }}
                            </strong>

                            <span>
                                Titik Aktif
                            </span>
                        </div>

                        <div>
                            <strong id="admin-map-area">
                                {{ $areaDipantau }}
                            </strong>

                            <span>
                                Area Dipantau
                            </span>
                        </div>

                        <div>
                            <strong>
                                Depok
                            </strong>

                            <span>
                                Wilayah
                            </span>
                        </div>

                    </div>

                </div>

            </article>


            <article
                id="priority-panel"
                class="sp-card sp-priority-card"
            >

                <div class="sp-card__header">

                    <h2>
                        Top Prioritas Hari Ini
                    </h2>

                    <a
                        href="{{ route('admin.verifikasi.index') }}"
                    >
                        Lihat semua
                        <span aria-hidden="true">
                            →
                        </span>
                    </a>

                </div>


                <div class="sp-priority-list">

                    @forelse($laporanPrioritasTinggi as $laporan)

                        @php

                            $score =
                                $laporan->skor_prioritas !== null
                                    ? (float) $laporan->skor_prioritas
                                    : null;

                            $priorityClass =
                                $score === null
                                    ? 'low'
                                    : (
                                        $score >= 70
                                            ? 'high'
                                            : (
                                                $score >= 40
                                                    ? 'medium'
                                                    : 'low'
                                            )
                                    );

                            $photo =
                                $laporan->foto
                                    ->firstWhere(
                                        'adalah_utama',
                                        true
                                    )
                                ??
                                $laporan->foto
                                    ->sortBy('urutan')
                                    ->first();

                        @endphp


                        <a
                            class="sp-priority-item"
                            href="{{ route('admin.verifikasi.show', $laporan) }}"
                        >

                            <span class="sp-priority-thumb">

                                @if($photo)

                                    <img
                                        src="{{ $photo->url }}"
                                        alt=""
                                        loading="lazy"
                                    >

                                @else

                                    <svg
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.5"
                                        aria-hidden="true"
                                    >
                                        <rect
                                            x="4"
                                            y="4"
                                            width="16"
                                            height="16"
                                            rx="2"
                                        />

                                        <path
                                            d="m6 18 5-5 3 3 2-2 2 4"
                                        />
                                    </svg>

                                @endif

                            </span>


                            <span class="sp-priority-body">

                                <span class="sp-priority-title">
                                    {{ $laporan->judul }}
                                </span>

                                <span class="sp-priority-address">
                                    {{
                                        $laporan->alamat_lengkap
                                        ?: (
                                            $laporan->wilayah?->nama
                                            ?? 'Lokasi tidak tersedia'
                                        )
                                    }}
                                </span>

                                <span class="sp-priority-meta">

                                    <span>
                                        ♟
                                        {{
                                            number_format(
                                                (int) $laporan->jumlah_pelapor
                                            )
                                        }}
                                        pelapor
                                    </span>

                                    <span>
                                        ⌖
                                        {{
                                            $laporan->jarak_fasilitas_meter !== null
                                                ? number_format(
                                                    (float) $laporan->jarak_fasilitas_meter,
                                                    0
                                                ) . ' m'
                                                : '—'
                                        }}
                                    </span>

                                </span>

                            </span>


                            <span class="sp-priority-side">

                                <span
                                    class="sp-priority-badge {{ $priorityClass }}"
                                >
                                    {{ $laporan->tingkat_prioritas }}
                                </span>

                                <span
                                    class="sp-chevron"
                                    aria-hidden="true"
                                >
                                    ›
                                </span>

                            </span>

                        </a>

                    @empty

                        <div class="sp-empty-state">

                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.5"
                                aria-hidden="true"
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

                            <span>
                                Belum ada laporan dengan skor prioritas.
                            </span>

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

                    <span>
                        Semua
                    </span>

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
                                    {{ $kategoriShortLabel($kategori->nama) }}
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

                    <h2>
                        Tren Laporan
                    </h2>

                    <span>
                        7 Hari
                    </span>

                </div>


                <div class="sp-trend-wrap">

                    <svg
                        class="sp-trend-chart"
                        viewBox="0 0 100 92"
                        preserveAspectRatio="none"
                        role="img"
                        aria-label="Tren laporan selama tujuh hari"
                    >

                        <line
                            x1="4"
                            y1="18"
                            x2="96"
                            y2="18"
                        />

                        <line
                            x1="4"
                            y1="52"
                            x2="96"
                            y2="52"
                        />

                        <polyline
                            points="{{ implode(' ', $trendPoints) }}"
                        />

                        @foreach($trendItems as $index => $item)

                            @php

                                $x =
                                    4
                                    +
                                    (
                                        ($index / $trendCount)
                                        * 92
                                    );

                                $y =
                                    86
                                    -
                                    (
                                        (
                                            (int) (
                                                $item['jumlah']
                                                ?? 0
                                            )
                                            / $trendMax
                                        )
                                        * 68
                                    );

                            @endphp

                            <circle
                                cx="{{ $x }}"
                                cy="{{ $y }}"
                                r="1.5"
                            />

                        @endforeach

                    </svg>


                    <div class="sp-trend-labels">

                        @foreach($trendItems as $item)

                            <span>
                                {{ $item['label'] ?? '' }}
                            </span>

                        @endforeach

                    </div>


                    @if($trendLast)

                        <div class="sp-trend-note">

                            <strong>
                                {{ $trendLast['label'] ?? '' }}
                            </strong>

                            <span>
                                {{
                                    (int) (
                                        $trendLast['jumlah']
                                        ?? 0
                                    )
                                }}
                                laporan
                            </span>

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

                    <h2>
                        Laporan Terbaru
                    </h2>

                    <a href="{{ route('laporan.index') }}">
                        Lihat semua
                        <span aria-hidden="true">
                            →
                        </span>
                    </a>

                </div>


                <div class="sp-table-scroll">

                    <table>

                        <thead>

                            <tr>
                                <th>Kode</th>
                                <th>Judul Laporan</th>
                                <th>Kategori</th>
                                <th>Lokasi</th>
                                <th>Prioritas</th>
                                <th>Status</th>
                                <th>Waktu</th>
                            </tr>

                        </thead>


                        <tbody>

                            @forelse($laporanTerbaru as $item)

                                @php

                                    $score =
                                        $item->skor_prioritas !== null
                                            ? (float) $item->skor_prioritas
                                            : null;

                                    $priorityClass =
                                        $score === null
                                            ? 'low'
                                            : (
                                                $score >= 70
                                                    ? 'high'
                                                    : (
                                                        $score >= 40
                                                            ? 'medium'
                                                            : 'low'
                                                    )
                                            );

                                    $statusClass =
                                        match ($item->status) {

                                            'menunggu_verifikasi'
                                                => 'pending',

                                            'diverifikasi'
                                                => 'verified',

                                            'dalam_perbaikan'
                                                => 'progress',

                                            'selesai'
                                                => 'done',

                                            'ditolak'
                                                => 'rejected',

                                            default
                                                => 'default',
                                        };

                                @endphp


                                <tr>

                                    <td class="sp-code">
                                        {{ $item->kode_laporan }}
                                    </td>


                                    <td>

                                        <a
                                            href="{{ route('admin.verifikasi.show', $item) }}"
                                            class="sp-table-title"
                                        >
                                            {{ $item->judul }}
                                        </a>

                                    </td>


                                    <td>
                                        {{
                                            $kategoriShortLabel(
                                                $item->kategoriHambatan?->nama
                                                ?? '—'
                                            )
                                        }}
                                    </td>


                                    <td class="sp-location-cell">

                                        {{
                                            $item->alamat_lengkap
                                            ?: (
                                                $item->wilayah?->nama
                                                ?? '—'
                                            )
                                        }}

                                    </td>


                                    <td>

                                        @if($score !== null)

                                            <span
                                                class="sp-priority-badge {{ $priorityClass }}"
                                            >
                                                {{ $item->tingkat_prioritas }}
                                            </span>

                                        @else

                                            <span class="sp-priority-badge low">
                                                —
                                            </span>

                                        @endif

                                    </td>


                                    <td>

                                        <span
                                            class="sp-status {{ $statusClass }}"
                                        >
                                            {{ $item->status_label }}
                                        </span>

                                    </td>


                                    <td class="sp-time">
                                        {{ $item->created_at?->diffForHumans() }}
                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td
                                        colspan="7"
                                        class="sp-table-empty"
                                    >
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

                    <h2>
                        Aktivitas Terbaru
                    </h2>

                    <a href="{{ route('admin.audit.index') }}">
                        Lihat semua
                        <span aria-hidden="true">
                            →
                        </span>
                    </a>

                </div>


                <div class="sp-activity-list">

                    @forelse($aktivitasTerbaru as $aktivitas)

                        <div class="sp-activity-item">

                            <span
                                class="sp-activity-icon"
                                aria-hidden="true"
                            >
                                <svg
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.6"
                                >
                                    <circle
                                        cx="12"
                                        cy="12"
                                        r="8.5"
                                    />

                                    <path
                                        stroke-linecap="round"
                                        d="M12 6v6l4 2"
                                    />
                                </svg>
                            </span>


                            <div>

                                <p>
                                    {{
                                        $aktivitas->keterangan
                                        ?:
                                        str_replace(
                                            '_',
                                            ' ',
                                            ucfirst(
                                                $aktivitas->aksi
                                            )
                                        )
                                    }}
                                </p>

                                <span>
                                    {{
                                        $aktivitas->pengguna?->nama_lengkap
                                        ?? 'Sistem'
                                    }}
                                    ·
                                    {{
                                        $aktivitas->created_at?->diffForHumans()
                                    }}
                                </span>

                            </div>

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