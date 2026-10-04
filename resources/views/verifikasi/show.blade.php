@extends('layouts.admin')

@section('title', 'Verifikasi - ' . $laporan->kode_laporan)
@section('page_title', 'Verifikasi Laporan')

@push('styles')

<link
    rel="stylesheet"
    href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
    crossorigin=""
>

<style>
    #verifikasi-map {
        width: 100%;
        height: 320px;
        min-height: 320px;
        border-radius: 0.75rem;
        overflow: hidden;
        border: 1px solid #e2e8f0;
        background: #f1f5f9;
        z-index: 1;
    }

    #verifikasi-map .leaflet-control {
        z-index: 500;
    }

    .verification-location-marker {
        position: relative;
        width: 34px;
        height: 34px;
    }

    .verification-location-marker .pin {
        position: absolute;
        left: 50%;
        top: 1px;
        width: 28px;
        height: 28px;
        transform: translateX(-50%) rotate(-45deg);
        border-radius: 50% 50% 50% 0;
        background: #dc2626;
        border: 3px solid #ffffff;
        box-shadow: 0 3px 10px rgba(15, 23, 42, 0.30);
    }

    .verification-location-marker .pin::after {
        content: "";
        position: absolute;
        width: 8px;
        height: 8px;
        left: 7px;
        top: 7px;
        border-radius: 50%;
        background: #ffffff;
    }

    .verification-location-marker .pulse {
        position: absolute;
        left: 50%;
        top: 10px;
        width: 18px;
        height: 18px;
        transform: translate(-50%, -50%);
        border-radius: 50%;
        background: rgba(220, 38, 38, 0.18);
        animation: verification-marker-pulse 2s ease-out infinite;
    }

    @keyframes verification-marker-pulse {
        0% {
            transform: translate(-50%, -50%) scale(0.8);
            opacity: 0.8;
        }

        70% {
            transform: translate(-50%, -50%) scale(2.2);
            opacity: 0;
        }

        100% {
            transform: translate(-50%, -50%) scale(2.2);
            opacity: 0;
        }
    }

    @media (max-width: 640px) {
        #verifikasi-map {
            height: 280px;
            min-height: 280px;
        }
    }
</style>

@endpush

@section('content')
<div class="space-y-6">

    <nav class="text-sm text-slate-400" aria-label="Breadcrumb">
        <ol class="flex items-center gap-2">
            <li>
                <a href="{{ route('admin.verifikasi.index') }}" class="hover:text-emerald-700 transition-colors">
                    Verifikasi
                </a>
            </li>
            <li aria-hidden="true">/</li>
            <li class="text-slate-700 font-medium" aria-current="page">
                {{ $laporan->kode_laporan }}
            </li>
        </ol>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="lg:col-span-2 space-y-6">

            {{-- Detail laporan --}}
            <section class="bg-white rounded-xl border border-slate-200 p-6">
                <div class="flex items-start justify-between gap-3 mb-4">
                    <div>
                        <span class="text-xs font-mono text-slate-400">
                            {{ $laporan->kode_laporan }}
                        </span>

                        <h2 class="text-lg font-bold text-slate-900 mt-1">
                            {{ $laporan->judul }}
                        </h2>
                    </div>

                    <span class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-semibold {{ $laporan->warna }} whitespace-nowrap">
                        {{ $laporan->status_label }}
                    </span>
                </div>

                <div class="flex flex-wrap items-center gap-2 mb-4">
                    @if($laporan->kategoriHambatan)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-100 text-sm text-slate-700">
                            <span
                                class="w-2.5 h-2.5 rounded-full"
                                style="background-color: {{ $laporan->kategoriHambatan->warna_penanda ?? '#64748b' }}"
                                aria-hidden="true"
                            ></span>
                            {{ $laporan->kategoriHambatan->nama }}
                        </span>
                    @endif

                    <span class="text-xs text-slate-400">
                        {{ $laporan->created_at->locale('id')->translatedFormat('d M Y H:i') }}
                    </span>
                </div>

                <p class="text-slate-700 leading-relaxed">
                    {{ $laporan->deskripsi }}
                </p>

                <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                    <div class="rounded-lg bg-slate-50 border border-slate-100 p-3">
                        <p class="text-xs text-slate-400 mb-1">Pelapor</p>
                        <p class="font-medium text-slate-700">
                            {{ $laporan->pelapor->nama_lengkap ?? '-' }}
                        </p>
                    </div>

                    <div class="rounded-lg bg-slate-50 border border-slate-100 p-3">
                        <p class="text-xs text-slate-400 mb-1">Sumber Koordinat</p>
                        <p class="font-medium text-slate-700">
                            {{ $laporan->sumber_koordinat === 'gps_otomatis' ? 'GPS Otomatis' : 'Penandaan Manual' }}
                        </p>
                    </div>
                </div>
            </section>

            {{-- Foto --}}
            <section class="bg-white rounded-xl border border-slate-200 p-6">
                <h3 class="font-semibold text-slate-900 mb-4">
                    Bukti Foto
                </h3>

                @if($laporan->fotoLaporan->count())
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        @foreach($laporan->fotoLaporan as $foto)
                            <div class="relative overflow-hidden rounded-lg border border-slate-200">
                                <img
                                    src="{{ $foto->url }}"
                                    alt="Foto laporan {{ $loop->iteration }}"
                                    class="w-full h-40 object-cover"
                                    loading="lazy"
                                >

                                @if($foto->adalah_utama)
                                    <span class="absolute top-1.5 left-1.5 px-1.5 py-0.5 bg-emerald-600 text-white text-[10px] font-bold rounded-md">
                                        Utama
                                    </span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-red-600">
                        Tidak ada foto yang tersimpan.
                    </p>
                @endif
            </section>

            {{-- Lokasi --}}
            <section class="bg-white rounded-xl border border-slate-200 p-6">
                <h3 class="font-semibold text-slate-900 mb-4">
                    Lokasi Hambatan
                </h3>

                <div
                    id="verifikasi-map"
                    role="application"
                    aria-label="Peta lokasi laporan"
                ></div>

                <div class="mt-3 space-y-1">
                    <p class="text-sm text-slate-600">
                        {{ $laporan->alamat_lengkap ?: 'Alamat tidak tersedia.' }}
                    </p>

                    <p class="text-xs text-slate-400 font-mono">
                        {{ $laporan->latitude }}, {{ $laporan->longitude }}
                    </p>
                </div>
            </section>

            {{-- Indikasi duplikasi --}}
            @if($laporan->laporanInduk)
                <section class="bg-amber-50 rounded-xl border border-amber-200 p-6">
                    <h3 class="font-semibold text-amber-800 mb-2">
                        Laporan Terhubung
                    </h3>

                    <p class="text-sm text-amber-700 mb-2">
                        Sistem mendeteksi laporan ini berada pada kategori yang sama
                        dan radius deduplikasi 50 meter dari laporan induk.
                    </p>

                    <a
                        href="{{ route('admin.verifikasi.show', $laporan->laporanInduk) }}"
                        class="inline-flex items-center gap-1 text-sm font-medium text-amber-800 hover:text-amber-900"
                    >
                        {{ $laporan->laporanInduk->kode_laporan }}
                        — {{ $laporan->laporanInduk->judul }}
                    </a>
                </section>
            @endif

            {{-- Riwayat verifikasi --}}
            @if($laporan->verifikasi->count())
                <section class="bg-white rounded-xl border border-slate-200 p-6">
                    <h3 class="font-semibold text-slate-900 mb-4">
                        Riwayat Verifikasi
                    </h3>

                    <div class="space-y-4">
                        @foreach($laporan->verifikasi as $verifikasi)
                            <div class="flex gap-3 p-3 rounded-lg bg-slate-50">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold {{ $verifikasi->warna }} shrink-0">
                                    {{ $verifikasi->keputusan_label }}
                                </span>

                                <div>
                                    <p class="text-sm text-slate-700">
                                        {{ $verifikasi->catatan_admin ?: 'Tidak ada catatan.' }}
                                    </p>

                                    <p class="text-xs text-slate-400 mt-1">
                                        {{ $verifikasi->admin->nama_lengkap ?? 'Sistem' }}
                                        •
                                        {{ $verifikasi->created_at->format('d M Y H:i') }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif
        </div>

        {{-- Sidebar --}}
        <aside class="space-y-6">

            {{-- SLA --}}
            <section class="bg-white rounded-xl border border-slate-200 p-6">
                <h3 class="font-semibold text-slate-900 mb-4 text-sm uppercase tracking-wider">
                    SLA Verifikasi
                </h3>

                @if(
                    $laporan->status === 'menunggu_verifikasi'
                    && auth()->user()->isAdmin()
                )
                

                    @php
                        $slaExpired = $laporan->created_at->lt(now()->subHours(48));
                        $elapsedHours = (int) $laporan->created_at->diffInHours(now());
                    @endphp

                    @if($slaExpired)
                        <div class="rounded-xl bg-red-50 border border-red-200 p-4">
                            <p class="text-sm font-semibold text-red-800">
                                Melewati target 2×24 jam
                            </p>
                            <p class="text-xs text-red-600 mt-1">
                                Laporan telah menunggu sekitar {{ $elapsedHours }} jam.
                            </p>
                        </div>
                    @else
                        <div class="rounded-xl bg-emerald-50 border border-emerald-200 p-4">
                            <p class="text-sm font-semibold text-emerald-800">
                                Masih dalam SLA
                            </p>
                            <p class="text-xs text-emerald-700 mt-1">
                                Menunggu sekitar {{ $elapsedHours }} jam.
                            </p>
                        </div>
                    @endif
                @else
                    <p class="text-sm text-slate-500">
                        Laporan sudah diproses.
                    </p>
                @endif
            </section>

            {{-- Prioritas --}}
            <section class="bg-white rounded-xl border border-slate-200 p-6">
                <h3 class="font-semibold text-slate-900 mb-4 text-sm uppercase tracking-wider">
                    Skor Prioritas
                </h3>

                @if($laporan->skor_prioritas !== null)
                    <div class="text-center mb-4">
                        <p class="text-3xl font-bold text-slate-900">
                            {{ number_format((float) $laporan->skor_prioritas, 2) }}
                        </p>

                        <p class="text-xs text-slate-400 uppercase mt-1">
                            {{ $laporan->tingkat_prioritas }}
                        </p>
                    </div>
                @else
                    <p class="text-sm text-slate-400 text-center py-4">
                        Skor akan dihitung setelah laporan induk terverifikasi.
                    </p>
                @endif

                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Jumlah Pelapor</span>
                        <span class="font-medium">{{ $laporan->jumlah_pelapor }}</span>
                    </div>

                    @if($laporan->skor_prioritas !== null)
                        <div class="flex justify-between">
                            <span class="text-slate-500">Keparahan</span>
                            <span class="font-medium">{{ number_format((float) $laporan->skor_keparahan, 2) }}</span>
                        </div>

                        <div class="flex justify-between">
                            <span class="text-slate-500">Pelapor</span>
                            <span class="font-medium">{{ number_format((float) $laporan->skor_pelapor, 2) }}</span>
                        </div>

                        <div class="flex justify-between">
                            <span class="text-slate-500">Fasilitas</span>
                            <span class="font-medium">{{ number_format((float) $laporan->skor_fasilitas, 2) }}</span>
                        </div>
                    @endif
                </div>
            </section>

            {{-- Pelapor --}}
            <section class="bg-white rounded-xl border border-slate-200 p-6">
                <h3 class="font-semibold text-slate-900 mb-3 text-sm uppercase tracking-wider">
                    Pelapor
                </h3>

                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-emerald-100 rounded-full flex items-center justify-center text-emerald-700 font-semibold text-sm">
                        {{ strtoupper(substr($laporan->pelapor->nama_lengkap ?? 'NA', 0, 2)) }}
                    </div>

                    <div>
                        <p class="text-sm font-medium text-slate-700">
                            {{ $laporan->pelapor->nama_lengkap ?? '-' }}
                        </p>

                        <p class="text-xs text-slate-400">
                            {{ $laporan->pelapor->email ?? '-' }}
                        </p>
                    </div>
                </div>
            </section>

            {{-- Aksi --}}
            @if(
                $laporan->status === 'menunggu_verifikasi'
                && auth()->user()->isAdmin()
            )
            @if(
                $laporan->status === 'menunggu_verifikasi'
                && auth()->user()->isDinas()
            )
                <section
                    class="rounded-xl border border-emerald-200 bg-emerald-50 p-5"
                    aria-labelledby="dinas-verifikasi-info"
                >

                    <div class="flex gap-3">

                        <div
                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white text-emerald-700 border border-emerald-100"
                            aria-hidden="true"
                        >
                            <i class="fa-solid fa-eye"></i>
                        </div>

                        <div>

                            <h3
                                id="dinas-verifikasi-info"
                                class="text-sm font-semibold text-emerald-900"
                            >
                                Mode Monitoring Dinas
                            </h3>

                            <p
                                class="mt-1 text-xs leading-5 text-emerald-800"
                            >
                                Laporan ini masih menunggu verifikasi administrator.
                                Petugas Dinas dapat meninjau bukti foto, kategori,
                                koordinat, dan indikasi duplikasi, sedangkan keputusan
                                verifikasi dilakukan oleh administrator sistem.
                            </p>

                        </div>

                    </div>

                </section>
            @endif


                <section class="bg-white rounded-xl border border-slate-200 p-6">
                    <h3 class="font-semibold text-slate-900 mb-4">
                        Keputusan Verifikasi
                    </h3>

                    <form
                        id="verifikasi-form"
                        method="POST"
                        action=""
                        class="space-y-4"
                    >
                        @csrf

                        <div>
                            <label
                                for="catatan_admin"
                                class="block text-sm font-medium text-slate-700 mb-1.5"
                            >
                                Catatan Admin
                            </label>

                            <textarea
                                id="catatan_admin"
                                name="catatan_admin"
                                rows="4"
                                maxlength="1000"
                                class="w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm"
                                placeholder="Untuk penolakan, tuliskan alasan yang jelas dan dapat dipahami pelapor."
                            >{{ old('catatan_admin') }}</textarea>

                            @error('catatan_admin')
                                <p class="mt-1 text-sm text-red-600" role="alert">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label
                                for="kategori_koreksi"
                                class="block text-sm font-medium text-slate-700 mb-1.5"
                            >
                                Koreksi Kategori (opsional)
                            </label>

                            <select
                                id="kategori_koreksi"
                                name="kategori_koreksi"
                                class="w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm"
                            >
                                <option value="">
                                    Tidak ada koreksi
                                </option>

                                @foreach(\App\Models\KategoriHambatan::aktif()->urutTampil()->get() as $kategori)
                                    <option
                                        value="{{ $kategori->id }}"
                                        {{ old('kategori_koreksi') == $kategori->id ? 'selected' : '' }}
                                    >
                                        {{ $kategori->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-2 pt-2">
                            <button
                                type="button"
                                onclick="submitVerification('{{ route('admin.verifikasi.approve', $laporan) }}', false)"
                                class="w-full inline-flex items-center justify-center gap-2 bg-emerald-600 text-white font-medium px-4 py-2.5 rounded-xl hover:bg-emerald-700 transition-colors text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2"
                            >
                                Setujui Laporan
                            </button>

                            <button
                                type="button"
                                onclick="submitVerification('{{ route('admin.verifikasi.reject', $laporan) }}', true)"
                                class="w-full inline-flex items-center justify-center gap-2 bg-white text-red-600 font-medium px-4 py-2.5 rounded-xl border border-red-200 hover:bg-red-50 transition-colors text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                            >
                                Tolak Laporan
                            </button>
                        </div>
                    </form>
                </section>
            @endif
        </aside>
    </div>
</div>
@endsection

@push('scripts')

<script
    src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
    crossorigin=""
></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    'use strict';

    const mapElement = document.getElementById('verifikasi-map');

    if (!mapElement) {
        return;
    }

    if (typeof window.L === 'undefined') {
        console.error('SmartPath: Leaflet.js tidak tersedia.');
        mapElement.innerHTML = `
            <div class="flex h-full items-center justify-center bg-slate-50 p-6 text-center">
                <div>
                    <p class="font-semibold text-slate-700">
                        Peta tidak dapat dimuat.
                    </p>
                    <p class="mt-1 text-sm text-slate-500">
                        Periksa koneksi internet atau pemuatan Leaflet.
                    </p>
                </div>
            </div>
        `;

        return;
    }

    const latitude = Number(@json((float) $laporan->latitude));
    const longitude = Number(@json((float) $laporan->longitude));

    if (
        !Number.isFinite(latitude) ||
        !Number.isFinite(longitude)
    ) {
        mapElement.innerHTML = `
            <div class="flex h-full items-center justify-center bg-slate-50 p-6 text-center">
                <div>
                    <p class="font-semibold text-slate-700">
                        Koordinat laporan tidak valid.
                    </p>
                    <p class="mt-1 text-sm text-slate-500">
                        Periksa kembali data latitude dan longitude laporan.
                    </p>
                </div>
            </div>
        `;

        return;
    }

    const map = window.L.map(
        mapElement,
        {
            center: [
                latitude,
                longitude
            ],
            zoom: 17,
            zoomControl: true,
            scrollWheelZoom: true
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

    const markerIcon = window.L.divIcon({
        className: 'verification-location-marker-wrapper',

        html: `
            <div
                class="verification-location-marker"
                role="img"
                aria-label="Lokasi hambatan laporan"
            >
                <span class="pulse"></span>
                <span class="pin"></span>
            </div>
        `,

        iconSize: [
            34,
            34
        ],

        iconAnchor: [
            17,
            33
        ],

        popupAnchor: [
            0,
            -30
        ]
    });

    const marker = window.L.marker(
        [
            latitude,
            longitude
        ],
        {
            icon: markerIcon,
            title: @json($laporan->judul),
            alt: 'Lokasi hambatan'
        }
    ).addTo(map);

    const popupContent = `
        <div style="min-width:220px;max-width:280px;font-family:Inter,system-ui,sans-serif;">
            <div style="font-size:14px;font-weight:700;color:#0f172a;margin-bottom:5px;">
                ${escapeHtml(@json($laporan->judul))}
            </div>

            <div style="font-size:12px;line-height:1.6;color:#64748b;">
                ${escapeHtml(@json($laporan->alamat_lengkap ?: 'Alamat tidak tersedia.'))}
            </div>

            <div style="margin-top:8px;font-size:11px;color:#94a3b8;font-family:ui-monospace,SFMono-Regular,Menlo,monospace;">
                ${latitude.toFixed(7)}, ${longitude.toFixed(7)}
            </div>
        </div>
    `;

    marker
        .bindPopup(
            popupContent,
            {
                maxWidth: 320,
                closeButton: true
            }
        )
        .openPopup();

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

    window.setTimeout(
        function () {
            map.invalidateSize();
        },
        250
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
});


function submitVerification(action, isReject) {
    const form =
        document.getElementById('verifikasi-form');

    if (!form) {
        return;
    }

    if (isReject) {
        const note =
            document
                .getElementById('catatan_admin')
                .value
                .trim();

        if (!note) {
            alert('Alasan penolakan wajib diisi.');

            document
                .getElementById('catatan_admin')
                .focus();

            return;
        }

        if (
            !confirm(
                'Tolak laporan ini? Alasan penolakan akan disimpan dan dapat dilihat pelapor.'
            )
        ) {
            return;
        }
    } else {
        if (
            !confirm(
                'Setujui laporan ini? Laporan akan menjadi terverifikasi dan diproses ke tahap prioritas.'
            )
        ) {
            return;
        }
    }

    form.action = action;
    form.submit();
}
</script>

@endpush