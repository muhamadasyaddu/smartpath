@extends('layouts.app')

@section('title', 'Detail Laporan - ' . $laporan->judul)

@push('styles')
<style>
    /* Screen Reader melayang */
    #detailScreenReaderContainer {
        position: fixed;
        right: 20px;
        bottom: 20px;
        z-index: 9998;
        display: flex;
        justify-content: flex-end;
        max-width: calc(100vw - 24px);
        touch-action: none;
        user-select: none;
        -webkit-user-select: none;
        cursor: grab;
    }

    #detailScreenReaderContainer:active {
        cursor: grabbing;
    }

    #detailScreenReaderButton {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 48px;
        padding: 12px 18px;
        border: 1px solid #047857;
        border-radius: 9999px;
        background: #047857;
        color: #ffffff;
        font-size: 14px;
        font-weight: 600;
        box-shadow: 0 8px 20px rgb(15 23 42 / 18%);
        cursor: pointer;
        touch-action: none;
        transition: background-color 0.2s ease;
    }

    #detailScreenReaderButton:hover {
        background: #065f46;
    }

    #detailScreenReaderButton:focus-visible {
        outline: 3px solid #10b981;
        outline-offset: 4px;
    }

    #detailSpeechStatus {
        position: absolute;
        width: 1px;
        height: 1px;
        padding: 0;
        margin: -1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        white-space: nowrap;
        border: 0;
    }

    @media (max-width: 640px) {
        #detailScreenReaderContainer {
            right: 12px;
            bottom: 12px;
        }

        #detailScreenReaderButton {
            min-height: 46px;
            padding: 10px 14px;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        #detailScreenReaderButton {
            transition: none;
        }
    }
</style>
@endpush

@section('content')
@include('partials.nav-public')

@php
    /*
    |--------------------------------------------------------------------------
    | Data tampilan
    |--------------------------------------------------------------------------
    */

    $status = $laporan->status;
    $statusLabel = $laporan->status_label;

    $isOwner = auth()->check()
        && (int) auth()->id() === (int) $laporan->pelapor_id;

    $canManage = $isOwner
        && $status === 'menunggu_verifikasi';

    $koordinatValid = is_numeric($laporan->latitude)
        && is_numeric($laporan->longitude)
        && (float) $laporan->latitude >= -90
        && (float) $laporan->latitude <= 90
        && (float) $laporan->longitude >= -180
        && (float) $laporan->longitude <= 180;

    $fotoList = $laporan->fotoLaporan
        ->sortBy([
            ['adalah_utama', 'desc'],
            ['urutan', 'asc'],
        ])
        ->values();

    /*
     * Menghitung kelompok laporan terkait.
     */
    $induk = $laporan->terhubung_ke_induk ? $laporan->laporanInduk : null;
    $root = $induk ?: $laporan;

    $anakAktif = $root->laporanAnak
        ->where('status_duplikasi', \App\Models\Laporan::DUPLIKASI_DIKONFIRMASI)
        ->whereNotIn('status', ['ditolak', 'diarsipkan']);

    $pelaporUnik = $anakAktif
        ->pluck('pelapor_id')
        ->push($root->pelapor_id)
        ->filter()
        ->unique()
        ->count();

    $adaIndikasiDuplikasi = $induk !== null
        || $anakAktif->isNotEmpty();

    $skor = is_numeric($laporan->skor_prioritas)
        ? (float) $laporan->skor_prioritas
        : null;

    $prioritas = $laporan->tingkat_prioritas;

    $prioritasLabel = match ($prioritas) {
        'tinggi' => 'Tinggi',
        'sedang' => 'Sedang',
        'rendah' => 'Rendah',
        default => 'Belum ditentukan',
    };

    $prioritasClass = match ($prioritas) {
        'tinggi' => 'text-red-700 bg-red-50 border-red-200',
        'sedang' => 'text-amber-700 bg-amber-50 border-amber-200',
        'rendah' => 'text-emerald-700 bg-emerald-50 border-emerald-200',
        default => 'text-slate-600 bg-slate-50 border-slate-200',
    };

    $penolakan = $laporan->verifikasi
        ->where('keputusan', 'ditolak')
        ->sortByDesc('created_at')
        ->first();
@endphp

<div id="detail-laporan-content" class="min-h-screen bg-slate-50/70">
    <div class="mx-auto max-w-[1440px] px-4 py-6 sm:px-6 lg:px-8 lg:py-8">

        {{-- Breadcrumb --}}
        <nav aria-label="Breadcrumb" class="mb-6">
            <ol class="flex flex-wrap items-center gap-2 text-sm">
                <li>
                    <a href="{{ route('laporan.index') }}"
                       class="font-medium text-slate-500 transition hover:text-emerald-700">
                        Laporan
                    </a>
                </li>

                <li aria-hidden="true" class="text-slate-400">›</li>

                <li aria-current="page" class="font-semibold text-slate-800">
                    {{ $laporan->kode_laporan }}
                </li>
            </ol>
        </nav>

        <div class="grid grid-cols-1 items-start gap-6 xl:grid-cols-[minmax(0,1.85fr)_minmax(310px,0.95fr)]">

            {{-- =====================================================
                 KOLOM KIRI
            ====================================================== --}}
            <div class="min-w-0 space-y-5">

                {{-- Detail utama laporan --}}
                <section
                    class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7"
                    aria-labelledby="report-heading">

                    <div class="mb-5 flex flex-wrap items-start justify-between gap-4">
                        <div class="min-w-0">
                            <p class="font-mono text-xs font-medium tracking-wide text-slate-500">
                                {{ $laporan->kode_laporan }}
                            </p>

                            <h1 id="report-heading"
                                class="mt-2 text-xl font-bold leading-snug text-slate-900 sm:text-2xl">
                                {{ $laporan->judul }}
                            </h1>
                        </div>

                        <span class="inline-flex shrink-0 items-center gap-2 rounded-lg border px-3 py-2 text-xs font-semibold {{ $laporan->warna }}">
                            @if ($status === 'menunggu_verifikasi')
                                <i class="fa-regular fa-clock" aria-hidden="true"></i>
                            @elseif ($status === 'ditolak')
                                <i class="fa-solid fa-circle-xmark" aria-hidden="true"></i>
                            @elseif ($status === 'selesai')
                                <i class="fa-solid fa-circle-check" aria-hidden="true"></i>
                            @else
                                <i class="fa-solid fa-circle-info" aria-hidden="true"></i>
                            @endif

                            {{ $statusLabel }}
                        </span>
                    </div>

                    <div class="mb-5 flex flex-wrap gap-2">
                        @if ($laporan->kategoriHambatan)
                            <span class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700">
                                <span
                                    class="h-2.5 w-2.5 rounded-full"
                                    style="background-color: {{ $laporan->kategoriHambatan->warna_penanda }}"
                                    aria-hidden="true"></span>

                                {{ $laporan->kategoriHambatan->nama }}
                            </span>
                        @endif

                        <span class="inline-flex items-center gap-2 rounded-lg border px-3 py-2 text-sm font-semibold {{ $prioritasClass }}">
                            <i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i>
                            Prioritas {{ $prioritasLabel }}
                        </span>
                    </div>

                    <div class="text-sm leading-7 text-slate-700 sm:text-base">
                        {{ $laporan->deskripsi }}
                    </div>

                    @if ($laporan->status === 'ditolak' && $penolakan)
                        <div class="mt-5 rounded-xl border border-red-200 bg-red-50 p-4"
                             role="alert">

                            <h2 class="flex items-center gap-2 font-semibold text-red-800">
                                <i class="fa-solid fa-circle-exclamation" aria-hidden="true"></i>
                                Alasan Penolakan
                            </h2>

                            <p class="mt-2 text-sm leading-6 text-red-800">
                                {{ $penolakan->catatan_admin }}
                            </p>

                            <p class="mt-2 text-xs text-red-700">
                                Diverifikasi pada
                                {{ $penolakan->created_at?->format('d M Y, H:i') }}
                            </p>
                        </div>
                    @endif
                </section>

                {{-- Galeri foto laporan --}}
                <section
                    class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7"
                    aria-labelledby="photos-heading">

                    @if ($fotoList->isNotEmpty())
                        @php
                            $fotoUtama = $fotoList->first();
                        @endphp

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-12 md:items-start">

                            {{-- Tombol navigasi foto --}}
                            <div class="flex flex-col justify-between md:col-span-7 lg:col-span-8">
                                <div>
                                    <h2 id="photos-heading"
                                        class="mb-4 flex items-center gap-3 text-lg font-bold text-slate-900">

                                        <i class="fa-regular fa-images text-emerald-700"
                                           aria-hidden="true"></i>

                                        Foto Laporan

                                        <span class="text-sm font-medium text-slate-400">
                                            ({{ $fotoList->count() }})
                                        </span>
                                    </h2>

                                    <div class="flex flex-wrap items-center gap-2.5 sm:gap-3"
                                         aria-label="Pilih foto laporan">

                                        @foreach ($fotoList as $foto)
                                            <button
                                                type="button"
                                                class="report-photo-button inline-flex items-center gap-2 rounded-xl border px-3.5 py-2 text-sm font-medium transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:ring-offset-2 {{ $loop->first ? 'border-emerald-600 bg-emerald-50 text-emerald-900 font-semibold shadow-sm' : 'border-slate-200 bg-white text-slate-700 hover:border-emerald-300 hover:bg-slate-50' }}"
                                                data-photo-url="{{ $foto->url }}"
                                                data-photo-alt="Foto laporan {{ $laporan->judul }}, gambar {{ $loop->iteration }}"
                                                aria-pressed="{{ $loop->first ? 'true' : 'false' }}"
                                                aria-label="Tampilkan foto {{ $loop->iteration }}{{ $foto->adalah_utama ? ', foto utama' : '' }}">

                                                <i class="fa-regular fa-image text-xs {{ $loop->first ? 'text-emerald-700' : 'text-slate-400' }}"
                                                   aria-hidden="true"></i>

                                                <span>
                                                    @if ($foto->adalah_utama || $loop->first)
                                                        Utama
                                                    @else
                                                        Foto Laporan {{ $loop->iteration }}
                                                    @endif
                                                </span>
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            {{-- Pratinjau foto utama --}}
                            <div class="md:col-span-5 lg:col-span-4">
                                <div class="relative flex aspect-[4/3] w-full items-center justify-center overflow-hidden rounded-xl border border-slate-200 bg-slate-900/5 shadow-inner">

                                    <img
                                        id="report-main-photo"
                                        src="{{ $fotoUtama->url }}"
                                        alt="Foto laporan {{ $laporan->judul }}"
                                        class="h-full w-full object-contain"
                                        loading="eager">
                                </div>
                            </div>
                        </div>
                    @else
                        <h2 id="photos-heading"
                            class="mb-4 flex items-center gap-3 text-lg font-bold text-slate-900">

                            <i class="fa-regular fa-images text-emerald-700"
                               aria-hidden="true"></i>

                            Foto Laporan

                            <span class="text-sm font-medium text-slate-400">(0)</span>
                        </h2>

                        <div class="flex min-h-44 flex-col items-center justify-center rounded-xl border border-dashed border-slate-300 bg-slate-50 px-5 text-center">

                            <i class="fa-regular fa-image text-3xl text-slate-400"
                               aria-hidden="true"></i>

                            <p class="mt-3 text-sm font-medium text-slate-700">
                                Belum ada foto laporan
                            </p>

                            <p class="mt-1 text-xs text-slate-500">
                                Foto akan ditampilkan di sini jika tersedia.
                            </p>
                        </div>
                    @endif
                </section>

                {{-- Peta lokasi --}}
                <section
                    class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7"
                    aria-labelledby="location-heading">

                    <h2 id="location-heading"
                        class="mb-5 flex items-center gap-3 text-lg font-bold text-slate-900">

                        <i class="fa-solid fa-location-dot text-emerald-700"
                           aria-hidden="true"></i>

                        Lokasi
                    </h2>

                    @if ($koordinatValid)
                        <div
                            id="detail-map"
                            class="z-0 h-[300px] w-full overflow-hidden rounded-xl border border-slate-200 sm:h-[360px]"
                            role="region"
                            aria-label="Peta interaktif lokasi laporan">
                        </div>
                    @else
                        <div class="flex min-h-48 items-center justify-center rounded-xl border border-dashed border-slate-300 bg-slate-50 p-5 text-center">
                            <p class="text-sm text-slate-600">
                                Koordinat lokasi belum tersedia atau tidak valid.
                            </p>
                        </div>
                    @endif

                    <div class="mt-4 flex items-start gap-3">
                        <i class="fa-solid fa-location-dot mt-1 text-slate-400"
                           aria-hidden="true"></i>

                        <div class="min-w-0">
                            <p class="text-sm leading-6 text-slate-700">
                                {{ $laporan->alamat_lengkap ?: ($laporan->wilayah?->nama ?? 'Alamat belum tersedia') }}
                            </p>

                            @if ($koordinatValid)
                                <p class="mt-1 break-words font-mono text-xs text-slate-500">
                                    {{ number_format((float) $laporan->latitude, 6, '.', '') }},
                                    {{ number_format((float) $laporan->longitude, 6, '.', '') }}
                                </p>
                            @endif
                        </div>
                    </div>
                </section>

                {{-- Riwayat status --}}
                <section
                    class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7"
                    aria-labelledby="history-heading">

                    <h2 id="history-heading"
                        class="mb-6 flex items-center gap-3 text-lg font-bold text-slate-900">

                        <i class="fa-solid fa-clock-rotate-left text-emerald-700"
                           aria-hidden="true"></i>

                        Riwayat Status
                    </h2>

                    @if ($laporan->riwayatStatus->isNotEmpty())
                        <ol class="space-y-5">
                            @foreach ($laporan->riwayatStatus->sortBy('created_at') as $riwayat)
                                <li class="relative flex gap-4">

                                    <div class="flex shrink-0 flex-col items-center">
                                        <span
                                            class="mt-1 h-3.5 w-3.5 rounded-full border-2 border-emerald-600 bg-white"
                                            aria-hidden="true"></span>

                                        @if (!$loop->last)
                                            <span
                                                class="mt-2 min-h-8 w-px flex-1 bg-slate-200"
                                                aria-hidden="true"></span>
                                        @endif
                                    </div>

                                    <div class="min-w-0 flex-1 pb-2">
                                        <p class="text-sm font-semibold text-slate-800">
                                            {{ $riwayat->status_label_baru ?? $riwayat->status_baru }}

                                            @if ($riwayat->status_sebelumnya)
                                                <span class="font-normal text-slate-500">
                                                    — dari
                                                    {{ $riwayat->status_label_sebelumnya ?? $riwayat->status_sebelumnya }}
                                                </span>
                                            @else
                                                <span class="font-normal text-slate-500">
                                                    — Status awal
                                                </span>
                                            @endif
                                        </p>

                                        @if ($riwayat->keterangan)
                                            <p class="mt-1 text-sm leading-6 text-slate-600">
                                                {{ $riwayat->keterangan }}
                                            </p>
                                        @endif

                                        <p class="mt-2 text-xs text-slate-500">
                                            {{ $riwayat->diubahOleh?->nama_lengkap ?? 'Sistem' }}
                                            •
                                            {{ $riwayat->created_at?->format('d M Y, H:i') }}
                                        </p>
                                    </div>
                                </li>
                            @endforeach
                        </ol>
                    @else
                        <div class="rounded-xl bg-slate-50 p-4">
                            <p class="text-sm text-slate-600">
                                Belum ada riwayat perubahan status yang tercatat.
                            </p>

                            <p class="mt-1 text-xs text-slate-500">
                                Laporan dibuat pada
                                {{ $laporan->created_at?->format('d M Y, H:i') }}.
                            </p>
                        </div>
                    @endif
                </section>
            </div>

            {{-- =====================================================
                 KOLOM KANAN
            ====================================================== --}}
            <aside class="min-w-0 space-y-5">

                {{-- Skor prioritas --}}
                <section
                    class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6"
                    aria-labelledby="priority-heading">

                    <h2 id="priority-heading"
                        class="flex items-center gap-3 text-sm font-bold uppercase tracking-wide text-slate-800">

                        <i class="fa-solid fa-chart-column text-emerald-700"
                           aria-hidden="true"></i>

                        Skor Prioritas
                    </h2>

                    @if ($skor !== null && $laporan->status !== 'menunggu_verifikasi' && $laporan->status !== 'ditolak')
                        @php
                            $circleColor = match ($prioritas) {
                                'tinggi' => '#dc2626',
                                'sedang' => '#d97706',
                                'rendah' => '#059669',
                                default => '#64748b',
                            };
                        @endphp

                        <div class="my-6 flex justify-center">
                            <div
                                class="relative flex h-36 w-36 items-center justify-center rounded-full border-[5px] bg-white"
                                style="border-color: {{ $circleColor }}"
                                role="img"
                                aria-label="Skor prioritas {{ number_format($skor, 2, ',', '.') }} dari 100, kategori {{ $prioritasLabel }}">

                                <div class="text-center">
                                    <p class="text-3xl font-bold text-slate-900">
                                        {{ number_format($skor, 1, ',', '.') }}
                                    </p>

                                    <p class="mt-1 text-xs font-bold uppercase tracking-wide"
                                       style="color: {{ $circleColor }}">
                                        {{ $prioritasLabel }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <p class="mb-4 text-center text-xs text-slate-500">
                            Skala skor 0–100
                        </p>

                        <dl class="divide-y divide-slate-200 text-sm">
                            <div class="flex items-center justify-between gap-3 py-3">
                                <dt class="text-slate-600">Keparahan</dt>
                                <dd class="font-semibold tabular-nums text-slate-800">
                                    {{ number_format((float) $laporan->skor_keparahan, 2, ',', '.') }}
                                </dd>
                            </div>

                            <div class="flex items-center justify-between gap-3 py-3">
                                <dt class="text-slate-600">Pelapor</dt>
                                <dd class="font-semibold tabular-nums text-slate-800">
                                    {{ number_format((float) $laporan->skor_pelapor, 2, ',', '.') }}
                                </dd>
                            </div>

                            <div class="flex items-center justify-between gap-3 py-3">
                                <dt class="text-slate-600">Fasilitas</dt>
                                <dd class="font-semibold tabular-nums text-slate-800">
                                    {{ number_format((float) $laporan->skor_fasilitas, 2, ',', '.') }}
                                </dd>
                            </div>
                        </dl>
                    @else
                        <div class="my-5 rounded-xl border border-amber-200 bg-amber-50 p-4">
                            <p class="text-sm font-semibold text-amber-900">
                                Skor belum tersedia untuk ditampilkan.
                            </p>

                            <p class="mt-1 text-xs leading-5 text-amber-800">
                                Skor prioritas diproses setelah laporan lolos verifikasi sesuai alur SmartPath.
                            </p>
                        </div>
                    @endif
                </section>

                {{-- Informasi pelapor --}}
                <section
                    class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6"
                    aria-labelledby="reporter-heading">

                    <h2 id="reporter-heading"
                        class="mb-4 flex items-center gap-3 text-sm font-bold uppercase tracking-wide text-slate-800">

                        <i class="fa-regular fa-user text-emerald-700"
                           aria-hidden="true"></i>

                        Pelapor
                    </h2>

                    <div class="flex items-center gap-3">
                        <div
                            class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full border border-emerald-100 bg-emerald-50 text-sm font-bold text-emerald-800"
                            aria-hidden="true">

                            {{ strtoupper(mb_substr($laporan->pelapor?->nama_lengkap ?? 'SP', 0, 2)) }}
                        </div>

                        <div class="min-w-0">
                            <p class="break-words text-sm font-semibold text-slate-800">
                                {{ $laporan->pelapor?->nama_lengkap ?? 'Pengguna SmartPath' }}
                            </p>

                            <p class="mt-1 flex items-center gap-2 text-xs leading-5 text-slate-500">
                                <i class="fa-regular fa-calendar" aria-hidden="true"></i>
                                {{ $laporan->created_at?->format('d M Y, H:i') }}
                            </p>
                        </div>
                    </div>
                </section>

                {{-- Fasilitas terdekat --}}
                <section
                    class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6"
                    aria-labelledby="facility-heading">

                    <h2 id="facility-heading"
                        class="mb-4 flex items-center gap-3 text-sm font-bold uppercase tracking-wide text-slate-800">

                        <i class="fa-solid fa-building text-emerald-700"
                           aria-hidden="true"></i>

                        Fasilitas Terdekat
                    </h2>

                    @if ($laporan->fasilitasTerdekat)
                        <div class="rounded-xl border border-slate-200 p-4">
                            <div class="flex items-start gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-emerald-50">
                                    <i class="fa-solid fa-location-dot text-emerald-700"
                                       aria-hidden="true"></i>
                                </div>

                                <div class="min-w-0 flex-1">
                                    <p class="break-words text-sm font-semibold text-slate-800">
                                        {{ $laporan->fasilitasTerdekat->nama }}
                                    </p>

                                    <p class="mt-1 text-xs text-slate-500">
                                        {{ str($laporan->fasilitasTerdekat->jenis)->replace('_', ' ')->title() }}
                                    </p>

                                    @if ($laporan->fasilitasTerdekat->alamat)
                                        <p class="mt-2 text-sm leading-5 text-slate-600">
                                            {{ $laporan->fasilitasTerdekat->alamat }}
                                        </p>
                                    @endif

                                    @if ($laporan->jarak_fasilitas_meter !== null)
                                        @php
                                            $jarakFasilitas = (float) $laporan->jarak_fasilitas_meter;
                                        @endphp

                                        <p class="mt-3 flex items-center gap-2 text-xs text-slate-600">
                                            <i class="fa-solid fa-route text-emerald-700"
                                               aria-hidden="true"></i>

                                            <span>
                                                Jarak garis lurus:
                                                <strong class="font-semibold text-slate-800">
                                                    @if ($jarakFasilitas >= 1000)
                                                        {{ number_format($jarakFasilitas / 1000, 2, ',', '.') }} km
                                                    @else
                                                        {{ number_format($jarakFasilitas, 0, ',', '.') }} meter
                                                    @endif
                                                </strong>
                                            </span>
                                        </p>
                                    @endif

                                    <p class="mt-2 text-xs leading-5 text-slate-500">
                                        Dihitung dari koordinat lokasi laporan berdasarkan fasilitas aktif yang terdaftar di SmartPath.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="rounded-xl border border-amber-200 bg-amber-50 p-4">
                            <div class="flex items-start gap-3">
                                <i class="fa-solid fa-circle-info mt-0.5 text-amber-700"
                                   aria-hidden="true"></i>

                                <div>
                                    <p class="text-sm font-semibold text-amber-900">
                                        Data fasilitas belum tersedia
                                    </p>

                                    <p class="mt-1 text-sm leading-6 text-amber-800">
                                        Belum ada fasilitas publik aktif yang dapat dicocokkan dengan koordinat laporan. Silakan periksa data fasilitas publik yang terdaftar di SmartPath.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                </section>

                
{{-- Indikasi laporan duplikat --}}
@if ($adaIndikasiDuplikasi)
    <section
        class="rounded-2xl border border-amber-200 bg-amber-50 p-5 sm:p-6"
        aria-labelledby="duplicate-heading"
        role="status"
    >
        <h2
            id="duplicate-heading"
            class="flex items-start gap-3 text-sm font-bold text-amber-900"
        >
            <i
                class="fa-solid fa-triangle-exclamation mt-0.5"
                aria-hidden="true"
            ></i>

            <span>Indikasi Laporan Duplikat</span>
        </h2>

        <p class="mt-3 text-sm leading-6 text-amber-900">
            @if ($induk)
                @if ($laporan->status_duplikasi === 'dikonfirmasi')
                    Administrator mengonfirmasi laporan ini melaporkan
                    hambatan yang sama dengan laporan
                    <span class="font-semibold">
                        {{ $induk->kode_laporan }}
                    </span>.
                @else
                    Laporan ini berada dekat dengan laporan
                    <span class="font-semibold">
                        {{ $induk->kode_laporan }}
                    </span>
                    pada kategori yang sama. Administrator akan memeriksa
                    apakah keduanya merupakan hambatan yang sama.
                @endif
            @else
                Sistem mendeteksi indikasi kemungkinan laporan duplikat.
                Informasi ini masih perlu diperiksa oleh administrator.
            @endif
        </p>

        <p class="mt-2 text-xs leading-5 text-amber-800">
            Indikasi deduplikasi bukan pengganti verifikasi administrator.
        </p>
    </section>
@endif

{{-- Aksi laporan --}}


                {{-- Aksi laporan --}}
                @if ($canManage)
                    <section
                        class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6"
                        aria-label="Aksi laporan">

                        <div class="space-y-3">
                            <a
                                href="{{ route('laporan.edit', $laporan) }}"
                                class="inline-flex min-h-12 w-full items-center justify-center gap-2 rounded-xl border border-emerald-700 bg-white px-4 py-3 text-sm font-semibold text-emerald-800 transition hover:bg-emerald-50 focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:ring-offset-2">

                                <i class="fa-regular fa-pen-to-square"
                                   aria-hidden="true"></i>

                                Edit Laporan
                            </a>

                            <form
                                method="POST"
                                action="{{ route('laporan.destroy', $laporan) }}"
                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus laporan ini?');">

                                @csrf
                                @method('DELETE')

                                <button
                                    type="submit"
                                    class="inline-flex min-h-12 w-full items-center justify-center gap-2 rounded-xl border border-red-200 bg-white px-4 py-3 text-sm font-semibold text-red-700 transition hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-600 focus:ring-offset-2">

                                    <i class="fa-regular fa-trash-can"
                                       aria-hidden="true"></i>

                                    Hapus Laporan
                                </button>
                            </form>
                        </div>
                    </section>
                @endif
            </aside>
        </div>
    </div>
</div>

{{-- ==========================================================
     TOMBOL SCREEN READER MELAYANG
========================================================== --}}
<div id="detailScreenReaderContainer"
     aria-label="Fitur pembaca teks halaman">

    <button
        id="detailScreenReaderButton"
        type="button"
        aria-label="Mulai pembacaan teks halaman"
        aria-pressed="false">

        <i class="fa-solid fa-blind text-lg"
           aria-hidden="true"></i>

        <span id="detailScreenReaderLabel">
            Screen Reader
        </span>
    </button>

    <p id="detailSpeechStatus"
       role="status"
       aria-live="polite"></p>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    /*
    |--------------------------------------------------------------------------
    | GALERI FOTO LAPORAN
    |--------------------------------------------------------------------------
    */
    const mainPhoto = document.getElementById('report-main-photo');
    const photoButtons = document.querySelectorAll('.report-photo-button');

    photoButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            if (!mainPhoto) {
                return;
            }

            const url = button.dataset.photoUrl;
            const alt = button.dataset.photoAlt;

            if (!url) {
                return;
            }

            mainPhoto.src = url;
            mainPhoto.alt = alt || 'Foto laporan';

            photoButtons.forEach(function (item) {
                item.classList.remove(
                    'border-emerald-600',
                    'bg-emerald-50',
                    'text-emerald-900',
                    'shadow-sm'
                );

                item.classList.add(
                    'border-slate-200',
                    'bg-white',
                    'text-slate-700'
                );

                item.setAttribute('aria-pressed', 'false');

                const icon = item.querySelector('i');

                if (icon) {
                    icon.classList.remove('text-emerald-700');
                    icon.classList.add('text-slate-400');
                }
            });

            button.classList.remove(
                'border-slate-200',
                'bg-white',
                'text-slate-700'
            );

            button.classList.add(
                'border-emerald-600',
                'bg-emerald-50',
                'text-emerald-900',
                'shadow-sm'
            );

            button.setAttribute('aria-pressed', 'true');

            const activeIcon = button.querySelector('i');

            if (activeIcon) {
                activeIcon.classList.remove('text-slate-400');
                activeIcon.classList.add('text-emerald-700');
            }
        });
    });

    /*
    |--------------------------------------------------------------------------
    | PETA LEAFLET
    |--------------------------------------------------------------------------
    */
    const mapElement = document.getElementById('detail-map');

    if (mapElement && typeof L !== 'undefined') {
        const latitude = @json($koordinatValid ? (float) $laporan->latitude : null);
        const longitude = @json($koordinatValid ? (float) $laporan->longitude : null);
        const reportTitle = @json($laporan->judul);
        const reportStatus = @json($laporan->status_label);

        if (latitude !== null && longitude !== null) {
            const map = L.map(mapElement, {
                center: [latitude, longitude],
                zoom: 16,
                scrollWheelZoom: false
            });

            L.tileLayer(
                'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
                {
                    attribution: '&copy; OpenStreetMap contributors',
                    maxZoom: 19
                }
            ).addTo(map);

            const popupContent = document.createElement('div');
            const popupTitle = document.createElement('strong');
            const popupStatus = document.createElement('span');

            popupTitle.textContent = reportTitle;
            popupStatus.textContent = reportStatus;

            popupContent.appendChild(popupTitle);
            popupContent.appendChild(document.createElement('br'));
            popupContent.appendChild(popupStatus);

            L.marker([latitude, longitude])
                .addTo(map)
                .bindPopup(popupContent)
                .openPopup();

            window.setTimeout(function () {
                map.invalidateSize();
            }, 150);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | SCREEN READER
    |--------------------------------------------------------------------------
    */
    const readerContainer = document.getElementById(
        'detailScreenReaderContainer'
    );

    const readerButton = document.getElementById(
        'detailScreenReaderButton'
    );

    const readerLabel = document.getElementById(
        'detailScreenReaderLabel'
    );

    const speechStatus = document.getElementById(
        'detailSpeechStatus'
    );

    const detailContent = document.getElementById(
        'detail-laporan-content'
    );

    if (!readerContainer || !readerButton || !readerLabel || !detailContent) {
        return;
    }

    let readingPage = false;
    let isDragging = false;
    let hasMoved = false;
    let startX = 0;
    let startY = 0;
    let initialX = 0;
    let initialY = 0;
    let activePointerId = null;
    let dragResetTimer = null;
    let utterance = null;

    const speechSupported =
        'speechSynthesis' in window &&
        'SpeechSynthesisUtterance' in window;

    function resetReader(message) {
        readingPage = false;
        utterance = null;

        readerButton.setAttribute('aria-pressed', 'false');
        readerLabel.textContent = 'Screen Reader';

        readerButton.setAttribute(
            'aria-label',
            'Mulai pembacaan teks halaman'
        );

        if (speechStatus && message) {
            speechStatus.textContent = message;
        }
    }

    /*
     * Membaca seluruh konten detail laporan.
     */
    function startReading() {
        if (!speechSupported) {
            speechStatus.textContent =
                'Browser tidak mendukung pembacaan suara. ' +
                'Silakan gunakan pembaca layar perangkat.';
            return;
        }

        const pageText = detailContent.innerText
            .replace(/\s+/g, ' ')
            .trim();

        if (!pageText) {
            speechStatus.textContent =
                'Tidak ada teks yang dapat dibacakan.';
            return;
        }

        window.speechSynthesis.cancel();

        utterance = new SpeechSynthesisUtterance(pageText);
        utterance.lang = 'id-ID';
        utterance.rate = 1;
        utterance.pitch = 1;

        utterance.onend = function () {
            resetReader('Pembacaan selesai.');
        };

        utterance.onerror = function (event) {
            if (
                event.error === 'canceled' ||
                event.error === 'interrupted'
            ) {
                return;
            }

            resetReader(
                'Suara tidak dapat diputar. Silakan coba lagi.'
            );
        };

        readingPage = true;

        readerButton.setAttribute('aria-pressed', 'true');
        readerButton.setAttribute(
            'aria-label',
            'Hentikan pembacaan teks halaman'
        );

        readerLabel.textContent = 'Hentikan Bacaan';

        speechStatus.textContent =
            'Detail laporan sedang dibacakan.';

        window.speechSynthesis.speak(utterance);
    }

    /*
     * Klik tombol untuk mulai atau menghentikan bacaan.
     */
    readerButton.addEventListener('click', function () {
        if (hasMoved) {
            return;
        }

        if (!speechSupported) {
            speechStatus.textContent =
                'Browser tidak mendukung pembacaan suara. ' +
                'Silakan gunakan pembaca layar perangkat.';
            return;
        }

        if (
            readingPage ||
            window.speechSynthesis.speaking ||
            window.speechSynthesis.pending
        ) {
            window.speechSynthesis.cancel();
            resetReader('Pembacaan dihentikan.');
            return;
        }

        startReading();
    });

    /*
    |--------------------------------------------------------------------------
    | TOMBOL BISA DIPINDAHKAN DENGAN MOUSE / TOUCH / STYLUS
    |--------------------------------------------------------------------------
    */
    readerContainer.addEventListener('pointerdown', function (e) {
        if (e.pointerType === 'mouse' && e.button !== 0) {
            return;
        }

        if (dragResetTimer) {
            clearTimeout(dragResetTimer);
            dragResetTimer = null;
        }

        isDragging = true;
        hasMoved = false;
        activePointerId = e.pointerId;

        startX = e.clientX;
        startY = e.clientY;

        const rect = readerContainer.getBoundingClientRect();

        initialX = rect.left;
        initialY = rect.top;

        readerContainer.style.left = initialX + 'px';
        readerContainer.style.top = initialY + 'px';
        readerContainer.style.right = 'auto';
        readerContainer.style.bottom = 'auto';

        try {
            readerContainer.setPointerCapture(e.pointerId);
        } catch (error) {
            // Pointer capture tidak tersedia di semua kondisi.
        }
    });

    document.addEventListener('pointermove', function (e) {
        if (!isDragging || e.pointerId !== activePointerId) {
            return;
        }

        const dx = e.clientX - startX;
        const dy = e.clientY - startY;

        if (Math.abs(dx) > 5 || Math.abs(dy) > 5) {
            hasMoved = true;
        }

        if (!hasMoved) {
            return;
        }

        e.preventDefault();

        const maxX = window.innerWidth - readerContainer.offsetWidth;
        const maxY = window.innerHeight - readerContainer.offsetHeight;

        const newX = Math.min(
            Math.max(0, initialX + dx),
            Math.max(0, maxX)
        );

        const newY = Math.min(
            Math.max(0, initialY + dy),
            Math.max(0, maxY)
        );

        readerContainer.style.left = newX + 'px';
        readerContainer.style.top = newY + 'px';
    }, { passive: false });

    function finishDragging(e) {
        if (!isDragging) {
            return;
        }

        if (
            e &&
            e.pointerId !== undefined &&
            e.pointerId !== activePointerId
        ) {
            return;
        }

        isDragging = false;
        activePointerId = null;

        /*
         * Mencegah gerakan drag dianggap sebagai klik tombol.
         */
        dragResetTimer = setTimeout(function () {
            hasMoved = false;
            dragResetTimer = null;
        }, 150);
    }

    document.addEventListener('pointerup', finishDragging);
    document.addEventListener('pointercancel', finishDragging);

    /*
     * Menjaga tombol tetap berada dalam batas layar saat
     * ukuran browser berubah.
     */
    window.addEventListener('resize', function () {
        if (!readerContainer.style.left) {
            return;
        }

        const rect = readerContainer.getBoundingClientRect();

        const maxX = window.innerWidth - readerContainer.offsetWidth;
        const maxY = window.innerHeight - readerContainer.offsetHeight;

        readerContainer.style.left =
            Math.min(Math.max(0, rect.left), Math.max(0, maxX)) + 'px';

        readerContainer.style.top =
            Math.min(Math.max(0, rect.top), Math.max(0, maxY)) + 'px';
    });

    /*
     * Hentikan suara ketika halaman ditinggalkan.
     */
    window.addEventListener('pagehide', function () {
        if (speechSupported) {
            window.speechSynthesis.cancel();
        }
    });
});
</script>
@endpush