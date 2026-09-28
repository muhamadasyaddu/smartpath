@extends('layouts.admin')

@section('title', 'Dashboard Administrator')
@section('page_title', 'Dashboard Administrator')

@section('content')

<div
    id="admin-dashboard"
    data-live-url="{{ route('admin.dashboard.live') }}"
    class="-m-6 min-h-full bg-slate-50 p-3 sm:p-4 lg:-m-8 lg:p-4"
>
    <div class="mx-auto max-w-[1500px] space-y-3">

        {{-- =========================================================
             1. KPI UTAMA
        ========================================================== --}}

        <section
            class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4"
            aria-label="Ringkasan laporan SmartPath"
        >

            {{-- TOTAL LAPORAN --}}
            <article class="sp-card sp-kpi">

                <div class="flex items-start justify-between gap-3">

                    <div class="sp-icon">
                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.7"
                            aria-hidden="true"
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

                    <span class="sp-kpi-label">
                        Total Laporan
                    </span>

                </div>

                <div class="mt-1 flex items-end justify-between gap-4">

                    <div>

                        <p
                            id="admin-kpi-total"
                            class="sp-number"
                        >
                            {{ number_format($statistik['total'] ?? 0) }}
                        </p>

                        <p class="sp-caption">
                            Seluruh laporan induk
                        </p>

                    </div>

                    <svg
                        class="h-10 w-16 shrink-0 text-emerald-600"
                        viewBox="0 0 64 40"
                        fill="none"
                        aria-hidden="true"
                    >
                        <polyline
                            points="2,31 14,25 25,29 37,17 48,21 62,7"
                            stroke="currentColor"
                            stroke-width="1.7"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        />

                        <path
                            d="M57 7h5v5"
                            stroke="currentColor"
                            stroke-width="1.7"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        />
                    </svg>

                </div>

                <div class="mt-2 flex items-center gap-1.5 text-[10px] text-slate-500">

                    <span class="font-semibold text-emerald-700">
                        {{
                            $persentasePerubahan === null
                                ? '—'
                                : (
                                    ($persentasePerubahan >= 0 ? '+' : '')
                                    . $persentasePerubahan
                                    . '%'
                                )
                        }}
                    </span>

                    <span>
                        dari 7 hari sebelumnya
                    </span>

                </div>

            </article>


            {{-- PENDING VERIFIKASI --}}
            <article class="sp-card sp-kpi">

                <div class="flex items-start justify-between gap-3">

                    <div class="sp-icon">
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
                                r="8.5"
                            />

                            <path
                                stroke-linecap="round"
                                d="M12 7v5l3 2"
                            />
                        </svg>
                    </div>

                    <span class="sp-kpi-label">
                        Pending Verifikasi
                    </span>

                </div>

                <div class="mt-1">

                    <p
                        id="admin-kpi-menunggu"
                        class="sp-number"
                    >
                        {{ number_format($statistik['menunggu'] ?? 0) }}
                    </p>

                    <p class="sp-caption">
                        Perlu ditindaklanjuti
                    </p>

                </div>

                <div class="mt-2 flex items-center gap-1.5 text-[10px] text-slate-500">

                    <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>

                    <span>
                        Validasi foto, koordinat, dan kategori
                    </span>

                </div>

            </article>


            {{-- DIVERIFIKASI --}}
            <article class="sp-card sp-kpi">

                <div class="flex items-start justify-between gap-3">

                    <div class="sp-icon">
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
                                r="8.5"
                            />

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="m8 12 2.6 2.6L16.5 9"
                            />
                        </svg>
                    </div>

                    <span class="sp-kpi-label">
                        Laporan Diverifikasi
                    </span>

                </div>

                <div class="mt-1">

                    <p
                        id="admin-kpi-diverifikasi"
                        class="sp-number"
                    >
                        {{ number_format($statistik['diverifikasi'] ?? 0) }}
                    </p>

                    <p class="sp-caption">
                        Sudah melewati verifikasi
                    </p>

                </div>

                <div class="mt-2 flex items-center gap-1.5 text-[10px] text-slate-500">

                    <span class="h-1.5 w-1.5 rounded-full bg-green-600"></span>

                    <span>
                        Termasuk penanganan dan selesai
                    </span>

                </div>

            </article>


            {{-- PRIORITAS TINGGI --}}
            <article class="sp-card sp-kpi">

                <div class="flex items-start justify-between gap-3">

                    <div class="sp-icon">
                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.7"
                            aria-hidden="true"
                        >
                            <path
                                stroke-linejoin="round"
                                d="m12 3 9 17H3L12 3Z"
                            />

                            <path
                                stroke-linecap="round"
                                d="M12 9v5M12 17h.01"
                            />
                        </svg>
                    </div>

                    <span class="sp-kpi-label">
                        Prioritas Tinggi
                    </span>

                </div>

                <div class="mt-1">

                    <p
                        id="admin-kpi-kritis"
                        class="sp-number"
                    >
                        {{ number_format($statistik['kritis'] ?? 0) }}
                    </p>

                    <p class="sp-caption">
                        Perlu tindakan cepat
                    </p>

                </div>

                <div class="mt-2 flex items-center gap-1.5 text-[10px] text-slate-500">

                    <span class="h-1.5 w-1.5 rounded-full bg-red-600"></span>

                    <span>
                        Skor prioritas ≥ 70
                    </span>

                </div>

            </article>

        </section>


        {{-- =========================================================
             2. PETA + TOP PRIORITAS
        ========================================================== --}}

        <section
            class="grid grid-cols-1 gap-3 lg:grid-cols-[minmax(0,1fr)_260px]"
        >

            {{-- PETA --}}
            <article
                id="map-section"
                class="sp-card overflow-hidden"
            >

                <div
                    class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-200 px-3 py-2.5"
                >

                    <div>

                        <h2 class="text-[13px] font-semibold text-slate-950">
                            Peta Sebaran Hambatan
                        </h2>

                        <p class="mt-0.5 text-[9px] text-slate-500">
                            Titik laporan aktif di wilayah Kota Depok
                        </p>

                    </div>


                    <div class="flex items-center gap-2">

                        <label
                            for="map-category-filter"
                            class="sr-only"
                        >
                            Filter kategori peta
                        </label>

                        <select
                            id="map-category-filter"
                            class="h-7 rounded-md border border-slate-300 bg-white px-2 text-[10px] text-slate-700 outline-none"
                        >

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
                            for="map-period-filter"
                            class="sr-only"
                        >
                            Filter periode peta
                        </label>

                        <select
                            id="map-period-filter"
                            class="h-7 rounded-md border border-slate-300 bg-white px-2 text-[10px] text-slate-700 outline-none"
                        >

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
                            type="button"
                            id="map-recenter"
                            class="inline-flex h-7 w-7 items-center justify-center rounded-md border border-emerald-200 bg-emerald-50 text-emerald-700 transition hover:bg-emerald-100"
                            aria-label="Pusatkan peta ke Kota Depok"
                            title="Pusatkan peta"
                        >

                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.7"
                                class="h-4 w-4"
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


                <div class="relative">

                    <div
                        id="admin-map"
                        class="h-[255px] w-full bg-slate-100"
                        aria-label="Peta interaktif laporan hambatan aksesibilitas Kota Depok"
                    ></div>


                    {{-- LEGENDA PRIORITAS --}}
                    <div
                        class="pointer-events-none absolute bottom-2 left-2 z-[500] rounded-md border border-slate-200 bg-white/95 px-2.5 py-2 text-[9px] shadow-sm backdrop-blur-sm"
                    >

                        <p class="mb-1 font-semibold text-slate-900">
                            Legenda Prioritas
                        </p>

                        <div class="space-y-1 text-slate-600">

                            <div class="flex items-center gap-1.5">
                                <span class="sp-legend-dot high"></span>
                                <span>Tinggi</span>
                            </div>

                            <div class="flex items-center gap-1.5">
                                <span class="sp-legend-dot medium"></span>
                                <span>Sedang</span>
                            </div>

                            <div class="flex items-center gap-1.5">
                                <span class="sp-legend-dot low"></span>
                                <span>Rendah</span>
                            </div>

                        </div>

                    </div>


                    {{-- RINGKASAN PETA --}}
                    <div
                        class="pointer-events-none absolute bottom-2 right-2 z-[500] grid grid-cols-3 overflow-hidden rounded-md border border-slate-200 bg-white/95 text-center text-[9px] shadow-sm"
                    >

                        <div class="border-r border-slate-200 px-3 py-1.5">

                            <span
                                id="admin-map-total"
                                class="block font-semibold text-emerald-700"
                            >
                                {{ count($petaLaporan) }}
                            </span>

                            <span class="text-slate-500">
                                Titik Aktif
                            </span>

                        </div>


                        <div class="border-r border-slate-200 px-3 py-1.5">

                            <span
                                id="admin-map-area"
                                class="block font-semibold text-teal-700"
                            >
                                {{ $areaDipantau }}
                            </span>

                            <span class="text-slate-500">
                                Area Dipantau
                            </span>

                        </div>


                        <div class="px-3 py-1.5">

                            <span class="block font-semibold text-slate-800">
                                Depok
                            </span>

                            <span class="text-slate-500">
                                Wilayah
                            </span>

                        </div>

                    </div>

                </div>

            </article>


            {{-- TOP PRIORITAS --}}
            <article
                id="priority-panel"
                class="sp-card overflow-hidden"
            >

                <div
                    class="flex items-center justify-between border-b border-slate-200 px-3 py-2.5"
                >

                    <h2 class="text-[13px] font-semibold text-slate-950">
                        Top Prioritas Hari Ini
                    </h2>

                    <a
                        href="{{ route('admin.verifikasi.index') }}"
                        class="text-[9px] font-medium text-emerald-700 hover:text-emerald-800"
                    >
                        Lihat semua →
                    </a>

                </div>


                <div class="divide-y divide-slate-100">

                    @forelse($laporanPrioritasTinggi as $laporan)

                        <a
                            href="{{ route('admin.verifikasi.show', $laporan) }}"
                            class="flex gap-2.5 px-3 py-2.5 transition hover:bg-red-50"
                        >

                            <span
                                class="mt-0.5 flex h-10 w-10 shrink-0 items-center justify-center overflow-hidden rounded-md border border-slate-200 bg-slate-100"
                            >

                                @if($laporan->foto->first())

                                    <img
                                        src="{{ $laporan->foto->first()->url }}"
                                        alt=""
                                        class="h-full w-full object-cover"
                                        loading="lazy"
                                    >

                                @else

                                    <svg
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.4"
                                        class="h-5 w-5 text-slate-400"
                                        aria-hidden="true"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="m3 16 5-5 4 4 3-3 6 6"
                                        />

                                        <rect
                                            x="3"
                                            y="4"
                                            width="18"
                                            height="16"
                                            rx="2"
                                        />
                                    </svg>

                                @endif

                            </span>


                            <span class="min-w-0 flex-1">

                                <span class="block truncate text-[9px] font-semibold text-slate-900">
                                    {{ $laporan->judul }}
                                </span>

                                <span class="mt-0.5 block truncate text-[8px] text-slate-500">
                                    {{
                                        $laporan->alamat_lengkap
                                        ?: (
                                            $laporan->wilayah?->nama
                                            ?? 'Lokasi tidak tersedia'
                                        )
                                    }}
                                </span>

                                <span class="mt-1 flex items-center justify-between gap-2 text-[8px]">

                                    <span class="text-slate-400">
                                        {{ number_format($laporan->jumlah_pelapor) }}
                                        pelapor
                                    </span>

                                    <span class="rounded border border-red-200 bg-red-50 px-1.5 py-0.5 font-semibold text-red-700">
                                        Tinggi
                                    </span>

                                </span>

                            </span>

                        </a>

                    @empty

                        <div class="px-3 py-8 text-center">

                            <div class="mx-auto mb-2 flex h-8 w-8 items-center justify-center rounded-full bg-slate-50 text-slate-400">
                                <svg
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.6"
                                    class="h-4 w-4"
                                    aria-hidden="true"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M12 9v4M12 17h.01"
                                    />

                                    <path
                                        stroke-linejoin="round"
                                        d="m12 3 9 17H3L12 3Z"
                                    />
                                </svg>
                            </div>

                            <p class="text-[10px] text-slate-400">
                                Belum ada laporan prioritas tinggi.
                            </p>

                        </div>

                    @endforelse

                </div>

            </article>

        </section>


        {{-- =========================================================
             3. ANALYTICS
        ========================================================== --}}

        <section
            id="analytics"
            class="grid grid-cols-1 gap-3 lg:grid-cols-3"
        >

            {{-- DISTRIBUSI STATUS --}}
            <article class="sp-card p-3">

                <div class="mb-2 flex items-center justify-between">

                    <h2 class="text-[12px] font-semibold text-slate-950">
                        Distribusi Status
                    </h2>

                    <span class="text-[9px] text-emerald-700">
                        Semua
                    </span>

                </div>


                @php

                    $statusTotal = max(
                        1,
                        array_sum($statusChart)
                    );

                    $menunggu =
                        (int) (
                            $statusChart['menunggu_verifikasi']
                            ?? 0
                        );

                    $diverifikasi =
                        (int) (
                            $statusChart['diverifikasi']
                            ?? 0
                        );

                    $perbaikan =
                        (int) (
                            $statusChart['dalam_perbaikan']
                            ?? 0
                        );

                    $selesai =
                        (int) (
                            $statusChart['selesai']
                            ?? 0
                        );

                    $ditolak =
                        (int) (
                            $statusChart['ditolak']
                            ?? 0
                        );

                    $statusItems = [

                        [
                            'key' => 'menunggu_verifikasi',
                            'label' => 'Menunggu Verifikasi',
                            'class' => 'pending',
                            'value' => $menunggu,
                        ],

                        [
                            'key' => 'diverifikasi',
                            'label' => 'Diverifikasi',
                            'class' => 'verified',
                            'value' => $diverifikasi,
                        ],

                        [
                            'key' => 'dalam_perbaikan',
                            'label' => 'Dalam Perbaikan',
                            'class' => 'progress',
                            'value' => $perbaikan,
                        ],

                        [
                            'key' => 'selesai',
                            'label' => 'Selesai',
                            'class' => 'done',
                            'value' => $selesai,
                        ],

                        [
                            'key' => 'ditolak',
                            'label' => 'Ditolak',
                            'class' => 'rejected',
                            'value' => $ditolak,
                        ],

                    ];

                    $stop1 =
                        ($menunggu / $statusTotal) * 100;

                    $stop2 =
                        (($menunggu + $diverifikasi) / $statusTotal) * 100;

                    $stop3 =
                        (($menunggu + $diverifikasi + $perbaikan) / $statusTotal) * 100;

                    $stop4 =
                        (($menunggu + $diverifikasi + $perbaikan + $selesai) / $statusTotal) * 100;

                @endphp


                <div class="grid grid-cols-[108px_1fr] items-center gap-4">

                    <div
                        class="sp-donut"
                        style="
                            background:
                            conic-gradient(
                                #d97706 0 {{ $stop1 }}%,
                                #059669 {{ $stop1 }}% {{ $stop2 }}%,
                                #0d9488 {{ $stop2 }}% {{ $stop3 }}%,
                                #16a34a {{ $stop3 }}% {{ $stop4 }}%,
                                #dc2626 {{ $stop4 }}% 100%
                            );
                        "
                        role="img"
                        aria-label="Distribusi status laporan"
                    >

                        <div class="sp-donut-inner">

                            <strong>
                                {{ number_format($statistik['total'] ?? 0) }}
                            </strong>

                            <small>
                                Total
                            </small>

                        </div>

                    </div>


                    <div class="space-y-1.5">

                        @foreach($statusItems as $statusItem)

                            <div class="flex items-center justify-between gap-2 text-[9px]">

                                <span class="flex min-w-0 items-center gap-1.5 text-slate-600">

                                    <span
                                        class="sp-status-dot {{ $statusItem['class'] }}"
                                    ></span>

                                    <span class="truncate">
                                        {{ $statusItem['label'] }}
                                    </span>

                                </span>

                                <span class="font-semibold text-slate-900">

                                    {{ $statusItem['value'] }}

                                    <span class="font-normal text-slate-400">
                                        ({{
                                            round(
                                                (
                                                    $statusItem['value']
                                                    / $statusTotal
                                                ) * 100
                                            )
                                        }}%)
                                    </span>

                                </span>

                            </div>

                        @endforeach

                    </div>

                </div>

            </article>


            {{-- LAPORAN PER KATEGORI --}}
            <article class="sp-card p-3">

                <div class="mb-2 flex items-center justify-between">

                    <h2 class="text-[12px] font-semibold text-slate-950">
                        Laporan per Kategori
                    </h2>

                    <span class="text-[9px] text-emerald-700">
                        Aktif
                    </span>

                </div>


                @php

                    $maxKategori = max(
                        1,
                        (int) $perKategori->max('laporan_count')
                    );

                @endphp


                <div class="space-y-2.5">

                    @forelse(
                        $perKategori
                            ->sortByDesc('laporan_count')
                            ->take(5)
                        as $index => $kategori
                    )

                        @php
                            $totalKategori =
                                (int) $kategori->laporan_count;

                            $widthKategori =
                                (
                                    $totalKategori
                                    / $maxKategori
                                ) * 100;
                        @endphp

                        <div class="sp-category-item">

                            <div class="mb-1 flex items-center justify-between gap-2 text-[9px]">

                                <span class="truncate text-slate-600">
                                    {{ $kategori->nama }}
                                </span>

                                <span class="font-semibold text-slate-900">
                                    {{ $totalKategori }}
                                </span>

                            </div>

                            <div class="sp-category-track">

                                <div
                                    class="sp-category-bar category-{{ $index }}"
                                    style="width: {{ $widthKategori }}%;"
                                ></div>

                            </div>

                        </div>

                    @empty

                        <p class="py-6 text-center text-[10px] text-slate-400">
                            Belum ada data kategori.
                        </p>

                    @endforelse

                </div>

            </article>


            {{-- TREND LAPORAN --}}
            <article class="sp-card p-3">

                <div class="mb-2 flex items-center justify-between">

                    <h2 class="text-[12px] font-semibold text-slate-950">
                        Tren Laporan
                    </h2>

                    <span class="text-[9px] text-emerald-700">
                        7 Hari
                    </span>

                </div>


                @php

                    $maxTrend = max(
                        1,
                        (int) $tren7Hari->max('jumlah')
                    );

                    $trendPoints = [];

                    $countTrend = max(
                        1,
                        $tren7Hari->count() - 1
                    );

                    foreach ($tren7Hari as $i => $item) {

                        $x =
                            4
                            +
                            (
                                ($i / $countTrend)
                                * 92
                            );

                        $y =
                            86
                            -
                            (
                                (
                                    $item['jumlah']
                                    / $maxTrend
                                )
                                * 68
                            );

                        $trendPoints[] =
                            round($x, 2)
                            . ','
                            . round($y, 2);
                    }

                @endphp


                <div class="relative h-[100px] border-b border-l border-slate-200 px-1 pb-4 pt-2">

                    <svg
                        viewBox="0 0 100 92"
                        preserveAspectRatio="none"
                        class="sp-trend-chart h-full w-full overflow-visible"
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

                        @foreach($tren7Hari as $i => $item)

                            @php

                                $x =
                                    4
                                    +
                                    (
                                        ($i / $countTrend)
                                        * 92
                                    );

                                $y =
                                    86
                                    -
                                    (
                                        (
                                            $item['jumlah']
                                            / $maxTrend
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


                    <div class="absolute inset-x-0 bottom-0 flex justify-between text-[7px] text-slate-400">

                        @foreach($tren7Hari as $item)

                            <span>
                                {{ $item['label'] }}
                            </span>

                        @endforeach

                    </div>

                </div>

            </article>

        </section>


        {{-- =========================================================
             4. LAPORAN TERBARU + AKTIVITAS
        ========================================================== --}}

        <section
            class="grid grid-cols-1 gap-3 lg:grid-cols-[minmax(0,1fr)_310px]"
        >

            {{-- LAPORAN TERBARU --}}
            <article class="sp-card overflow-hidden">

                <div
                    class="flex items-center justify-between border-b border-slate-200 px-3 py-2.5"
                >

                    <h2 class="text-[12px] font-semibold text-slate-950">
                        Laporan Terbaru
                    </h2>

                    <a
                        href="{{ route('admin.verifikasi.index') }}"
                        class="text-[9px] font-medium text-emerald-700 hover:text-emerald-800"
                    >
                        Lihat semua →
                    </a>

                </div>


                <div class="overflow-x-auto">

                    <table class="min-w-[720px] w-full border-collapse text-left">

                        <thead class="border-b border-slate-200 bg-slate-50">

                            <tr class="text-[8px] uppercase tracking-[0.08em] text-slate-400">

                                <th class="px-3 py-2 font-semibold">
                                    Kode
                                </th>

                                <th class="px-3 py-2 font-semibold">
                                    Judul Laporan
                                </th>

                                <th class="px-3 py-2 font-semibold">
                                    Kategori
                                </th>

                                <th class="px-3 py-2 font-semibold">
                                    Lokasi
                                </th>

                                <th class="px-3 py-2 text-center font-semibold">
                                    Prioritas
                                </th>

                                <th class="px-3 py-2 font-semibold">
                                    Status
                                </th>

                            </tr>

                        </thead>


                        <tbody class="divide-y divide-slate-100">

                            @forelse($laporanTerbaru as $item)

                                @php

                                    $statusClass = match($item->status) {

                                        'menunggu_verifikasi' =>
                                            'pending',

                                        'diverifikasi' =>
                                            'verified',

                                        'dalam_perbaikan' =>
                                            'progress',

                                        'selesai' =>
                                            'done',

                                        'ditolak' =>
                                            'rejected',

                                        default =>
                                            'default',

                                    };

                                @endphp


                                <tr>

                                    <td
                                        class="whitespace-nowrap px-3 py-2 text-[8px] font-mono font-semibold text-slate-500"
                                    >
                                        {{ $item->kode_laporan }}
                                    </td>


                                    <td class="max-w-[220px] px-3 py-2">

                                        <a
                                            href="{{ route('admin.verifikasi.show', $item) }}"
                                            class="block truncate text-[9px] font-semibold text-slate-900"
                                        >
                                            {{ $item->judul }}
                                        </a>

                                        <span class="mt-0.5 block text-[8px] text-slate-400">
                                            {{ $item->created_at?->diffForHumans() }}
                                        </span>

                                    </td>


                                    <td class="px-3 py-2 text-[8px] text-slate-600">

                                        {{
                                            $item->kategoriHambatan?->nama
                                            ?? '—'
                                        }}

                                    </td>


                                    <td class="max-w-[170px] px-3 py-2 text-[8px] text-slate-600">

                                        <span class="block truncate">

                                            {{
                                                $item->alamat_lengkap
                                                ?: (
                                                    $item->wilayah?->nama
                                                    ?? '—'
                                                )
                                            }}

                                        </span>

                                    </td>


                                    <td class="px-3 py-2 text-center">

                                        @if($item->skor_prioritas !== null)

                                            @php
                                                $score = (float) $item->skor_prioritas;
                                            @endphp

                                            <span
                                                class="sp-priority-badge
                                                {{
                                                    $score >= 70
                                                        ? 'high'
                                                        : (
                                                            $score >= 40
                                                                ? 'medium'
                                                                : 'low'
                                                        )
                                                }}"
                                            >
                                                {{ $item->tingkat_prioritas }}
                                            </span>

                                        @else

                                            <span class="text-[8px] text-slate-400">
                                                Belum dinilai
                                            </span>

                                        @endif

                                    </td>


                                    <td class="px-3 py-2">

                                        <span
                                            class="sp-status {{ $statusClass }}"
                                        >
                                            {{ $item->status_label }}
                                        </span>

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td
                                        colspan="6"
                                        class="px-3 py-8 text-center text-[9px] text-slate-400"
                                    >
                                        Belum ada laporan yang tercatat.
                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </article>


            {{-- AKTIVITAS --}}
            <article class="sp-card overflow-hidden">

                <div
                    class="flex items-center justify-between border-b border-slate-200 px-3 py-2.5"
                >

                    <h2 class="text-[12px] font-semibold text-slate-950">
                        Aktivitas Terbaru
                    </h2>

                    <a
                        href="{{ route('admin.audit.index') }}"
                        class="text-[9px] font-medium text-emerald-700 hover:text-emerald-800"
                    >
                        Lihat semua →
                    </a>

                </div>


                <div class="divide-y divide-slate-100">

                    @forelse($aktivitasTerbaru as $aktivitas)

                        <div class="flex gap-2.5 px-3 py-2.5">

                            <span class="sp-activity-icon">

                                <svg
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.7"
                                    class="h-3.5 w-3.5"
                                    aria-hidden="true"
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


                            <div class="min-w-0 flex-1">

                                <p class="text-[9px] leading-4 text-slate-700">

                                    {{
                                        $aktivitas->keterangan
                                        ?: str_replace(
                                            '_',
                                            ' ',
                                            ucfirst(
                                                $aktivitas->aksi
                                            )
                                        )
                                    }}

                                </p>

                                <p class="mt-0.5 text-[8px] text-slate-400">

                                    {{
                                        $aktivitas->pengguna?->nama_lengkap
                                        ?? 'Sistem'
                                    }}

                                    ·

                                    {{
                                        $aktivitas->created_at?->diffForHumans()
                                    }}

                                </p>

                            </div>

                        </div>

                    @empty

                        <div class="px-3 py-8 text-center text-[9px] text-slate-400">
                            Belum ada aktivitas.
                        </div>

                    @endforelse

                </div>

            </article>

        </section>

    </div>

</div>

@endsection


{{-- =============================================================
     CSS DASHBOARD
     CSS dipisahkan ke public/css/smartpath-admin.css
============================================================= --}}

@push('styles')

<link
    rel="stylesheet"
    href="{{ asset('css/smartpath-admin.css') }}"
>

<link
    rel="stylesheet"
    href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
    crossorigin=""
>

@endpush


{{-- =============================================================
     DATA PETA
     JSON_HEX_* digunakan agar data database tidak menjadi
     potongan HTML/JavaScript mentah.
============================================================= --}}

@push('scripts')

<script
    src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
    crossorigin=""
></script>


<script
    id="smartpath-map-data"
    type="application/json"
>{{ json_encode(
    $petaLaporan,
    JSON_HEX_TAG
    | JSON_HEX_AMP
    | JSON_HEX_APOS
    | JSON_HEX_QUOT
) }}</script>


<script
    src="{{ asset('js/smartpath-admin.js') }}"
    defer
></script>

@endpush