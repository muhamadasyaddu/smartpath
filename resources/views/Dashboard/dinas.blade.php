@extends('layouts.admin')

@section('title', 'Dashboard Dinas PUPR')
@section('page_title', 'Monitoring Infrastruktur Dinas PUPR')

@section('content')
<div class="space-y-6">

    {{-- ==========================================================
    1. KARTU STATISTIK PENANGANAN
    ========================================================== --}}
    <div
        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4"
        role="region"
        aria-label="Statistik Penanganan PUPR"
    >
        {{-- 1. TOTAL MASUK --}}
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M5 3a1 1 0 011-1h1v1h9.5l1.8 4-1.8 4H7v10H5V3z"/>
                    </svg>
                </div>
                <span class="text-xs font-semibold text-slate-400">Total</span>
            </div>
            <p class="text-2xl font-bold text-slate-900">{{ $statistik['total'] ?? 0 }}</p>
            <p class="text-xs text-slate-500 mt-1">Laporan masuk</p>
        </div>

        {{-- 2. MENUNGGU VERIFIKASI --}}
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 4h12M6 20h12M8 4c0 4 2 5 4 6-2 1-4 2-4 6m8-12c0 4-2 5-4 6 2 1 4 2 4 6"/>
                    </svg>
                </div>
                <span class="text-xs font-semibold text-amber-600">Menunggu</span>
            </div>
            <p class="text-2xl font-bold text-slate-900">{{ $statistik['menunggu'] ?? 0 }}</p>
            <p class="text-xs text-slate-500 mt-1">Perlu verifikasi</p>
        </div>

        {{-- 3. DALAM PERBAIKAN --}}
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-cyan-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.7 6.3a4 4 0 01-5.4 5.4L4 17l3 3 5.3-5.3a4 4 0 005.4-5.4l-2.2 2.2-2.1-.5-.5-2.1 2.2-2.2z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13l6 6M19 13l-6 6"/>
                    </svg>
                </div>
                <span class="text-xs font-semibold text-cyan-600">Proses</span>
            </div>
            <p class="text-2xl font-bold text-slate-900">{{ $statistik['dalam_perbaikan'] ?? 0 }}</p>
            <p class="text-xs text-slate-500 mt-1">Dalam perbaikan</p>
        </div>

        {{-- 4. SELESAI --}}
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/>
                        <circle cx="12" cy="12" r="9" stroke-width="2"/>
                    </svg>
                </div>
                <span class="text-xs font-semibold text-emerald-600">Selesai</span>
            </div>
            <p class="text-2xl font-bold text-slate-900">{{ $statistik['selesai'] ?? 0 }}</p>
            <p class="text-xs text-slate-500 mt-1">Terkonfirmasi</p>
        </div>

        {{-- 5. KRITIS --}}
        <div class="bg-white rounded-xl border border-red-200 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M10.3 3.8L2.7 17a2 2 0 001.7 3h15.2a2 2 0 001.7-3L13.7 3.8a2 2 0 00-3.4 0z"/>
                    </svg>
                </div>
                <span class="text-xs font-semibold text-red-600">Kritis</span>
            </div>
            <p class="text-2xl font-bold text-slate-900">{{ $statistik['kritis'] ?? 0 }}</p>
            <p class="text-xs text-slate-500 mt-1">Prioritas tinggi</p>
        </div>
    </div>

    {{-- ==========================================================
    2. PETA SEBARAN LOKASI & WIDGET REALISASI ANGGARAN
    ========================================================== --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        
        {{-- PETA INTERAKTIF (Kiri -  Lebar) --}}
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm lg:col-span-2 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-semibold text-slate-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Peta Sebaran Infrastruktur
                    </h2>
                    <span class="text-xs text-slate-400">Peta Real-time</span>
                </div>
                
                {{-- Container Leaflet Map --}}
                <div id="map-pemerintah" class="h-80 w-full rounded-lg border border-slate-200 bg-slate-50 z-0"></div>
            </div>

            {{-- Legend Peta --}}
            <div class="mt-4 pt-3 border-t border-slate-100 flex flex-wrap items-center gap-4 text-xs text-slate-600">
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-red-500"></span> Prioritas Tinggi</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-amber-500"></span> Prioritas Sedang</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-blue-500"></span> Prioritas Rendah</span>
            </div>
        </div>

        {{-- KOLOM KANAN (1/3 Lebar) - REALISASI ANGGARAN --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm flex flex-col justify-between h-full">
                <div>
                    <h2 class="font-semibold text-slate-900 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Realisasi Anggaran
                    </h2>

                  <div class="space-y-6">

    @if(isset($dataAnggaran) && $dataAnggaran->count() > 0)

        @foreach($dataAnggaran as $item)

            @php
                $persen = $item->pagu > 0
                    ? min(
                        100,
                        round(
                            ($item->realisasi / $item->pagu) * 100
                        )
                    )
                    : 0;
            @endphp

            <div>

                <div class="flex justify-between items-center text-xs mb-1">

                    <span class="font-semibold text-slate-700">
                        {{ $item->nama_kategori }}
                    </span>

                    <span class="font-bold text-slate-900">
                        {{ $persen }}%
                    </span>

                </div>


                <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden mb-1">

                    <div
                        class="bg-emerald-700 h-2 rounded-full transition-all duration-300"
                        style="width: {{ $persen }}%"
                    ></div>

                </div>


                <div class="flex justify-between text-[11px] text-slate-400">

                    <span>
                        Rp {{ number_format($item->realisasi, 0, ',', '.') }}
                    </span>

                    <span>
                        Rp {{ number_format($item->pagu, 0, ',', '.') }}
                    </span>

                </div>

            </div>

        @endforeach

    @else

        <div class="text-center py-6">

            <p class="text-sm font-medium text-slate-500">
                Belum ada data anggaran
            </p>

            <p class="text-xs text-slate-400 mt-1">
                Data akan muncul setelah terdapat rencana perbaikan.
            </p>

        </div>

    @endif

</div>
                </div>

                {{-- Total Ringkasan Anggaran --}}
               <div class="mt-4 pt-3 border-t border-slate-100 text-xs bg-slate-50 p-2.5 rounded-lg border border-slate-200">

    <div class="flex justify-between font-semibold text-slate-700">

        <span>Total Anggaran:</span>

        <span>
            Rp {{ number_format($totalAnggaran, 0, ',', '.') }}
        </span>

    </div>

    <div class="flex justify-between font-bold text-slate-900 mt-0.5">

        <span>Realisasi:</span>

        <span>
            Rp {{ number_format($totalRealisasi, 0, ',', '.') }}
            ({{ $persentaseRealisasi }}%)
        </span>

    </div>

</div>
            </div>
        </div>
    </div>

    {{-- ==========================================================
    3. PRIORITAS PERBAIKAN & TABEL ANTRIAN LAPORAN TERBARU
    ========================================================== --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        
        {{-- WIDGET: PRIORITAS PERBAIKAN --}}
<div class="lg:col-span-1 bg-white rounded-xl border border-slate-200 p-4 shadow-sm h-fit self-start">            <div>
                <h2 class="font-semibold text-slate-900 mb-3 flex items-center gap-2 text-sm">
                    <svg class="w-4 h-4 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/>
                    </svg>
                    Prioritas Perbaikan
                </h2>

                <div class="space-y-2">
                    {{-- 1. Kritis --}}
                    <div class="flex items-center justify-between p-2.5 rounded-lg bg-red-50 border border-red-100">
                        <div class="flex items-center gap-3">
                            <span class="text-lg font-bold text-red-700 w-7 text-center">{{ $prioritas['kritis'] ?? 18 }}</span>
                            <div>
                                <p class="text-xs font-bold text-red-900">Kritis</p>
                                <p class="text-[11px] text-red-600">Penanganan segera</p>
                            </div>
                        </div>
                    </div>

                    {{-- 2. Tinggi --}}
                    <div class="flex items-center justify-between p-2.5 rounded-lg bg-amber-50 border border-amber-100">
                        <div class="flex items-center gap-3">
                            <span class="text-lg font-bold text-amber-700 w-7 text-center">{{ $prioritas['tinggi'] ?? 42 }}</span>
                            <div>
                                <p class="text-xs font-bold text-amber-900">Tinggi</p>
                                <p class="text-[11px] text-amber-600">Antrian perbaikan</p>
                            </div>
                        </div>
                    </div>

                    {{-- 3. Menengah --}}
                    <div class="flex items-center justify-between p-2.5 rounded-lg bg-blue-50 border border-blue-100">
                        <div class="flex items-center gap-3">
                            <span class="text-lg font-bold text-blue-700 w-7 text-center">{{ $prioritas['menengah'] ?? 95 }}</span>
                            <div>
                                <p class="text-xs font-bold text-blue-900">Menengah</p>
                                <p class="text-[11px] text-blue-600">Dijadwalkan</p>
                            </div>
                        </div>
                    </div>

                    {{-- 4. Rendah --}}
                    <div class="flex items-center justify-between p-2.5 rounded-lg bg-slate-50 border border-slate-200">
                        <div class="flex items-center gap-3">
                            <span class="text-lg font-bold text-slate-700 w-7 text-center">{{ $prioritas['rendah'] ?? 93 }}</span>
                            <div>
                                <p class="text-xs font-bold text-slate-900">Rendah</p>
                                <p class="text-[11px] text-slate-500">Monitoring</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- TABEL: DAFTAR LAPORAN MASUK --}}
        <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
            <div class="px-5 py-3 border-b border-slate-200 flex items-center justify-between">
                <h2 class="font-semibold text-slate-900 text-sm flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <rect x="3" y="3" width="18" height="18" rx="3" stroke-width="2"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h10M7 16h6"/>
                    </svg>
                    Laporan Terbaru Masuk
                </h2>
                <a href="{{ route('admin.verifikasi.index') }}" class="text-xs font-semibold text-emerald-700 hover:text-emerald-800 transition-colors">Lihat Semua Antrian →</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left text-slate-600">
                    <thead class="border-b border-slate-100 bg-slate-50 text-slate-400 uppercase tracking-wider text-[11px]">
                        <tr>
                            <th class="px-5 py-2.5 font-semibold">Kode</th>
                            <th class="px-5 py-2.5 font-semibold">Judul Laporan</th>
                            <th class="px-5 py-2.5 font-semibold">Kategori Hambatan</th>
                            <th class="px-5 py-2.5 font-semibold">Status</th>
                            <th class="px-5 py-2.5 font-semibold text-center">Skor Prioritas</th>
                            <th class="px-5 py-2.5 font-semibold text-right">Tanggal Masuk</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @forelse($laporanTerbaru ?? [] as $item)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-5 py-2.5 font-mono font-bold text-slate-700">{{ $item->kode_laporan }}</td>
                                <td class="px-5 py-2.5 font-semibold text-slate-800">
                                    <a href="{{ route('admin.verifikasi.show', $item) }}" class="hover:text-emerald-700">
                                        {{ Str::limit($item->judul, 35) }}
                                    </a>
                                </td>
                                <td class="px-5 py-2.5">
                                    <span class="inline-flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full" style="background-color: {{ $item->kategoriHambatan->warna_penanda ?? '#64748b' }}"></span>
                                        {{ $item->kategoriHambatan->nama ?? 'Umum' }}
                                    </span>
                                </td>
                                <td class="px-5 py-2.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold {{ $item->warna }}">
                                        {{ $item->status_label }}
                                    </span>
                                </td>
                                <td class="px-5 py-2.5 text-center font-bold text-red-600">
                                    {{ number_format($item->skor_prioritas ?? 0, 1) }}
                                </td>
                                <td class="px-5 py-2.5 text-right text-slate-400">
                                    {{ $item->created_at?->format('d M Y H:i') ?? '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-6 text-center text-slate-400">Belum ada data laporan masuk.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>
@endsection

@push('scripts')
{{-- Leaflet JS & CSS untuk Peta --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inisialisasi Peta
    var map = L.map('map-pemerintah').setView([-6.4025, 106.7942], 12);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 18,
        attribution: '© OpenStreetMap'
    }).addTo(map);

    // Render Data Marker Peta dari Backend Controller
    var petaData = @json($petaLaporan ?? []);

    if (Array.isArray(petaData) && petaData.length > 0) {
        var bounds = [];

        petaData.forEach(function(laporan) {
            if (laporan.latitude && laporan.longitude) {
                var lat = parseFloat(laporan.latitude);
                var lng = parseFloat(laporan.longitude);

                var marker = L.marker([lat, lng], {
                    title: `Lokasi laporan ${laporan.judul || laporan.kode_laporan || 'hambatan'}`,
                    alt: `Lokasi laporan ${laporan.judul || laporan.kode_laporan || 'hambatan'}`,
                    keyboard: true
                }).addTo(map);
                var markerElement = marker.getElement();
                if (markerElement) {
                    markerElement.setAttribute(
                        'aria-label',
                        `Laporan ${laporan.judul || laporan.kode_laporan || 'hambatan aksesibilitas'}`
                    );
                }
                
                var popupHtml = `
                    <div style="font-size:12px; min-width:140px;">
                        <strong style="color:#0f172a;">${laporan.kode_laporan}</strong><br>
                        <span style="color:#64748b;">${laporan.judul ?? ''}</span><br>
                        <div style="margin-top:4px;">Skor: <b style="color:#dc2626;">${laporan.skor_prioritas ?? '-'}</b></div>
                    </div>
                `;
                marker.bindPopup(popupHtml);
                bounds.push([lat, lng]);
            }
        });

        if (bounds.length > 0) {
            map.fitBounds(bounds, { padding: [30, 30] });
        }
    }
});
</script>
@endpush