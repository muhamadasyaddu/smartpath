
@extends('layouts.admin')

@section('title', 'Manajemen Laporan')

@push('styles')
<link
    rel="stylesheet"
    href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
>
<style>
    #admin-report-map {
        min-height: 360px;
        height: 420px;
        width: 100%;
        background: #f1f5f9;
        z-index: 1;
    }

    .sp-report-table th,
    .sp-report-table td {
        padding: 14px 16px;
        vertical-align: top;
        text-align: left;
    }

    .sp-report-table th {
        color: #475569;
        background: #f8fafc;
        font-size: 12px;
        font-weight: 700;
        white-space: nowrap;
    }

    .sp-report-table tbody tr:hover {
        background: #f8fafc;
    }

    .sp-report-table td {
        border-top: 1px solid #e2e8f0;
    }

    .sp-map-legend {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        color: #475569;
        font-size: 12px;
    }

    .sp-legend-dot {
        display: inline-block;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        margin-right: 5px;
    }

    @media (max-width: 640px) {
        #admin-report-map {
            height: 320px;
            min-height: 320px;
        }

        .sp-report-table th,
        .sp-report-table td {
            padding: 12px;
        }
    }
</style>
@endpush

@section('content')
@php
    $statusOptions = [
        'menunggu_verifikasi' => 'Menunggu verifikasi',
        'diverifikasi' => 'Terverifikasi',
        'ditolak' => 'Ditolak',
        'dalam_perbaikan' => 'Dalam perbaikan',
        'selesai' => 'Selesai',
        'diarsipkan' => 'Diarsipkan',
    ];
@endphp

<main class="space-y-6">

    {{-- Header --}}
    <section class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <p class="text-sm font-semibold text-emerald-700">
                ADMINISTRATOR / PENGELOLAAN DATA
            </p>

            <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-900">
                Manajemen Laporan
            </h1>

            <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">
                Pantau laporan hambatan aksesibilitas, periksa jarak
                antarlaporan sejenis, dan lanjutkan verifikasi berdasarkan
                bukti yang tersedia.
            </p>
        </div>

        <div class="flex shrink-0 items-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3">
            <span class="h-2.5 w-2.5 rounded-full bg-emerald-600"></span>

            <span class="text-sm font-semibold text-emerald-900">
                Radius operasional {{ $radiusMeter }} m
            </span>
        </div>
    </section>

    {{-- Flash messages --}}
    @if(session('sukses'))
        <div
            class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-900"
            role="status"
            aria-live="polite"
        >
            {{ session('sukses') }}
        </div>
    @endif

    @if(session('galat'))
        <div
            class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800"
            role="alert"
        >
            {{ session('galat') }}
        </div>
    @endif

    {{-- Statistik keseluruhan --}}
    <section
        class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4"
        aria-label="Ringkasan laporan"
    >
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-slate-500">Seluruh laporan</p>
            <p class="mt-3 text-3xl font-bold text-slate-900">
                {{ number_format($statusCounts['semua']) }}
            </p>
            <p class="mt-2 text-xs text-slate-500">Semua status laporan</p>
        </article>

        <article class="rounded-2xl border border-amber-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-slate-500">Menunggu verifikasi</p>
            <p class="mt-3 text-3xl font-bold text-amber-700">
                {{ number_format($statusCounts['menunggu_verifikasi']) }}
            </p>
            <p class="mt-2 text-xs text-slate-500">Perlu pemeriksaan administrator</p>
        </article>

        <article class="rounded-2xl border border-emerald-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-slate-500">Terverifikasi</p>
            <p class="mt-3 text-3xl font-bold text-emerald-700">
                {{ number_format($statusCounts['diverifikasi']) }}
            </p>
            <p class="mt-2 text-xs text-slate-500">Laporan yang disetujui</p>
        </article>

        <article class="rounded-2xl border border-teal-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-slate-500">Selesai ditangani</p>
            <p class="mt-3 text-3xl font-bold text-teal-700">
                {{ number_format($statusCounts['selesai']) }}
            </p>
            <p class="mt-2 text-xs text-slate-500">Status penyelesaian tercatat</p>
        </article>
    </section>

    {{-- Peta --}}
    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 p-5 sm:p-6">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">
                        Peta Sebaran Laporan
                    </h2>

                    <p class="mt-1 text-sm leading-6 text-slate-500">
                        Lingkaran pada setiap titik menunjukkan radius
                        pemeriksaan. Pilih marker untuk melihat informasi laporan.
                    </p>
                </div>

                <span class="inline-flex w-fit items-center rounded-lg bg-slate-100 px-3 py-2 text-xs font-semibold text-slate-700">
                    {{ $laporan->total() }} laporan pada hasil pencarian
                </span>
            </div>
        </div>

        <div class="p-3 sm:p-5">
            <div
                id="admin-report-map"
                role="region"
                aria-label="Peta interaktif laporan dan radius pemeriksaan"
            ></div>

            
            <p class="mt-3 text-xs leading-5 text-slate-500">
                Peta menampilkan laporan pada halaman yang sedang dibuka.
                Lingkaran menunjukkan jarak geografis dalam meter, bukan
                jarak tempuh berjalan kaki. Peta bukan pengganti pemeriksaan foto.
            </p>
        </div>
    </section>

    {{-- Filter --}}
    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <div class="mb-4">
            <h2 class="text-base font-bold text-slate-900">
                Pencarian dan penyaringan
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Cari laporan berdasarkan kode, judul, alamat, atau deskripsi.
            </p>
        </div>

        <form
            method="GET"
            action="{{ route('admin.laporan.index') }}"
            class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4"
        >
            <div class="xl:col-span-2">
                <label for="report-search" class="mb-1.5 block text-sm font-medium text-slate-700">
                    Kata kunci
                </label>

                <input
                    id="report-search"
                    type="search"
                    name="q"
                    value="{{ request('q') }}"
                    placeholder="Contoh: LP-202610, guiding block, nama jalan..."
                    class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 focus:border-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-100"
                >
            </div>

            <div>
                <label for="report-status" class="mb-1.5 block text-sm font-medium text-slate-700">
                    Status laporan
                </label>

                <select
                    id="report-status"
                    name="status"
                    class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-100"
                >
                    <option value="">Semua status</option>

                    @foreach($statusOptions as $value => $label)
                        <option
                            value="{{ $value }}"
                            @selected(request('status') === $value)
                        >
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="report-category" class="mb-1.5 block text-sm font-medium text-slate-700">
                    Kategori hambatan
                </label>

                <select
                    id="report-category"
                    name="kategori"
                    class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-100"
                >
                    <option value="">Semua kategori</option>

                    @foreach($kategoriHambatan as $kategori)
                        <option
                            value="{{ $kategori->id }}"
                            @selected((string) request('kategori') === (string) $kategori->id)
                        >
                            {{ $kategori->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex flex-wrap items-center gap-2 md:col-span-2 xl:col-span-4">
                <button
                    type="submit"
                    class="inline-flex items-center justify-center rounded-xl bg-emerald-700 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-800 focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:ring-offset-2"
                >
                    Terapkan filter
                </button>

                <a
                    href="{{ route('admin.laporan.index') }}"
                    class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-2"
                >
                    Reset
                </a>
            </div>
        </form>
    </section>

    {{-- Tabel laporan --}}
    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 p-5 sm:p-6">
            <h2 class="text-lg font-bold text-slate-900">
                Daftar Laporan
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Periksa alamat, status, skor prioritas, dan jarak
                dari laporan pembanding terdekat.
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="sp-report-table w-full min-w-[1100px] border-collapse text-sm">
                <thead>
                    <tr>
                        <th scope="col">Laporan</th>
                        <th scope="col">Lokasi dan alamat</th>
                        <th scope="col">Status</th>
                        <th scope="col">Pemeriksaan radius</th>
                        <th scope="col">Prioritas</th>
                        <th scope="col">Tindakan</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($laporan as $item)
                        <tr>
                            <td>
                                <p class="font-bold text-slate-900">
                                    {{ $item->kode_laporan }}
                                </p>

                                <p class="mt-1 max-w-[220px] font-semibold text-slate-700">
                                    {{ $item->judul }}
                                </p>

                                <p class="mt-2 text-xs text-slate-500">
                                    {{ $item->kategoriHambatan?->nama ?? 'Kategori tidak tersedia' }}
                                </p>

                                <p class="mt-1 text-xs text-slate-500">
                                    {{ $item->created_at?->locale('id')->translatedFormat('d M Y, H:i') }}
                                </p>

                                <p class="mt-1 text-xs text-slate-500">
                                    Pelapor:
                                    {{ $item->pelapor?->name ?? 'Tidak tersedia' }}
                                </p>
                            </td>

                            <td>
                                <p class="max-w-[300px] font-medium leading-6 text-slate-800">
                                    {{ $item->alamat_lengkap ?: ($item->wilayah?->nama ?? 'Alamat belum tersedia') }}
                                </p>

                                <p class="mt-2 break-all font-mono text-xs text-slate-500">
                                    {{ $item->latitude }},
                                    {{ $item->longitude }}
                                </p>

                                @if($item->laporan_induk_id)
                                    <p class="mt-2 text-xs font-semibold text-amber-800">
                                        Terhubung ke laporan induk
                                        {{ $item->laporanInduk?->kode_laporan ?? '#' . $item->laporan_induk_id }}
                                    </p>
                                @endif
                            </td>

                            <td>
                                <span @class([
                                    'inline-flex rounded-lg border px-2.5 py-1 text-xs font-semibold',
                                    'border-amber-200 bg-amber-50 text-amber-800' => $item->status === 'menunggu_verifikasi',
                                    'border-emerald-200 bg-emerald-50 text-emerald-800' => $item->status === 'diverifikasi',
                                    'border-red-200 bg-red-50 text-red-800' => $item->status === 'ditolak',
                                    'border-sky-200 bg-sky-50 text-sky-800' => $item->status === 'dalam_perbaikan',
                                    'border-teal-200 bg-teal-50 text-teal-800' => $item->status === 'selesai',
                                    'border-slate-200 bg-slate-100 text-slate-700' => $item->status === 'diarsipkan',
                                ])>
                                    {{ $item->status_label }}
                                </span>
                            </td>

                            <td>
                                @if($item->laporanPembandingTerdekat)
                                    @if($item->dalam_radius_50m)
                                        <span class="inline-flex rounded-lg border border-amber-200 bg-amber-50 px-2.5 py-1 text-xs font-bold text-amber-800">
                                            Dalam radius
                                        </span>
                                    @else
                                        <span class="inline-flex rounded-lg border border-slate-200 bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-700">
                                            Di luar radius
                                        </span>
                                    @endif

                                    <p class="mt-2 text-lg font-bold tabular-nums text-slate-900">
                                        {{ number_format($item->jarak_pembanding_meter, 2, ',', '.') }} m
                                    </p>

                                    <p class="mt-1 text-xs leading-5 text-slate-500">
                                        Pembanding:
                                        {{ $item->laporanPembandingTerdekat->kode_laporan }}
                                    </p>
                                @else
                                    <span class="inline-flex rounded-lg border border-slate-200 bg-slate-50 px-2.5 py-1 text-xs font-semibold text-slate-600">
                                        Belum ada pembanding
                                    </span>

                                    <p class="mt-2 max-w-[180px] text-xs leading-5 text-slate-500">
                                        Tidak ditemukan laporan induk aktif dengan kategori yang sama.
                                    </p>
                                @endif
                            </td>

                            <td>
                                @if($item->skor_prioritas !== null)
                                    <p class="text-xl font-bold tabular-nums text-slate-900">
                                        {{ number_format((float) $item->skor_prioritas, 2, ',', '.') }}
                                    </p>

                                    <p @class([
                                        'mt-1 text-xs font-bold',
                                        'text-red-700' => $item->skor_prioritas >= 70,
                                        'text-amber-700' => $item->skor_prioritas >= 40 && $item->skor_prioritas < 70,
                                        'text-emerald-700' => $item->skor_prioritas < 40,
                                    ])>
                                        {{ $item->tingkat_prioritas }}
                                    </p>
                                @else
                                    <span class="text-xs font-medium text-slate-500">
                                        Belum dinilai
                                    </span>
                                @endif
                            </td>

                            <td>
                                <a
                                    href="{{ route('admin.verifikasi.show', $item) }}"
                                    class="inline-flex items-center justify-center rounded-lg border border-emerald-200 bg-white px-3 py-2 text-xs font-bold text-emerald-800 transition hover:bg-emerald-50 focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:ring-offset-2"
                                >
                                    Detail / Verifikasi
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center">
                                <p class="font-semibold text-slate-800">
                                    Tidak ada laporan yang sesuai.
                                </p>

                                <p class="mt-2 text-sm text-slate-500">
                                    Coba ubah kata kunci atau filter pencarian.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($laporan->hasPages())
            <div class="border-t border-slate-200 p-4 sm:p-5">
                {{ $laporan->links('pagination::tailwind') }}
            </div>
        @endif
    </section>

</main>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const mapElement = document.getElementById('admin-report-map');

    if (!mapElement) {
        return;
    }

    if (typeof L === 'undefined') {
        mapElement.textContent =
            'Peta tidak dapat dimuat. Periksa koneksi internet.';
        return;
    }

    const reports = @js($mapReports);
    const center = @js(config('smartpath.pilot.center'));
    const radius = Number(@js($radiusMeter));

    const map = L.map(mapElement, {
        scrollWheelZoom: false
    }).setView(
        [Number(center.latitude), Number(center.longitude)],
        13
    );

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    const bounds = [];

    function escapeHtml(value) {
        return String(value ?? '').replace(/[&<>"']/g, function (char) {
            return {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            }[char];
        });
    }

    function priorityColor(score) {
        if (score === null || score === undefined) {
            return '#64748b';
        }

        if (Number(score) >= 70) {
            return '#dc2626';
        }

        if (Number(score) >= 40) {
            return '#d97706';
        }

        return '#059669';
    }

    reports.forEach(function (report) {
        const lat = Number(report.latitude);
        const lng = Number(report.longitude);

        if (
            !Number.isFinite(lat) ||
            !Number.isFinite(lng) ||
            lat < -90 || lat > 90 ||
            lng < -180 || lng > 180
        ) {
            return;
        }

        const point = [lat, lng];
        const color = priorityColor(report.skor);

        // Lingkaran memperlihatkan radius pemeriksaan geografis.
        L.circle(point, {
            radius: radius,
            color: '#0f766e',
            weight: 1,
            opacity: 0.7,
            fillColor: '#14b8a6',
            fillOpacity: 0.07,
            interactive: false
        }).addTo(map);

        const marker = L.circleMarker(point, {
            radius: 7,
            color: '#ffffff',
            weight: 2,
            fillColor: color,
            fillOpacity: 1
        }).addTo(map);

        const distanceText = report.jarak === null
            ? 'Belum ada laporan pembanding'
            : (
                Number(report.jarak).toLocaleString('id-ID', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }) + ' meter'
            );

        const radiusText = report.kode_pembanding
            ? (
                report.dalam_radius
                    ? 'Dalam radius operasional'
                    : 'Di luar radius operasional'
            )
            : 'Belum ada pembanding';

        marker.bindPopup(
            '<div style="min-width:210px;max-width:280px">' +
                '<strong>' + escapeHtml(report.kode) + '</strong>' +
                '<p style="margin-top:6px;font-weight:600">' +
                    escapeHtml(report.judul) +
                '</p>' +
                '<p style="margin-top:6px;font-size:12px">' +
                    escapeHtml(report.alamat) +
                '</p>' +
                '<p style="margin-top:8px;font-size:12px">' +
                    'Status: ' + escapeHtml(report.status) +
                '</p>' +
                '<p style="margin-top:4px;font-size:12px">' +
                    'Prioritas: ' + escapeHtml(report.prioritas) +
                '</p>' +
                '<p style="margin-top:4px;font-size:12px">' +
                    'Jarak pembanding: ' + escapeHtml(distanceText) +
                '</p>' +
                '<p style="margin-top:4px;font-size:12px;font-weight:600">' +
                    escapeHtml(radiusText) +
                '</p>' +
                '<p style="margin-top:6px;font-size:11px;color:#64748b">' +
                    'Pembanding: ' +
                    escapeHtml(report.kode_pembanding || 'Tidak tersedia') +
                '</p>' +
            '</div>'
        );

        bounds.push(point);
    });

    if (bounds.length > 0) {
        map.fitBounds(bounds, {
            padding: [24, 24],
            maxZoom: 16
        });
    }

    // Pastikan ukuran peta sesuai setelah layout selesai dirender.
    window.setTimeout(function () {
        map.invalidateSize();
    }, 150);
});
</script>
@endpush