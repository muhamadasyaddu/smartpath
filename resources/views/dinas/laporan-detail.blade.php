@extends('layouts.admin')

@section('title', 'Detail Laporan')
@section('page_title', 'Detail Laporan')

@section('content')

<div class="p-6">

    {{-- ==========================================================
    HEADER
    ========================================================== --}}
    <div class="mb-6">

        <a
            href="{{ route('dinas.laporan.index') }}"
            class="inline-flex items-center gap-2
                   text-sm text-slate-500
                   hover:text-slate-700 mb-4"
        >
            <i class="fa-solid fa-arrow-left"></i>
            Kembali ke Laporan
        </a>

        <div class="flex flex-col md:flex-row
                    md:items-center md:justify-between gap-3">

            <div>

                <h1 class="text-2xl font-bold text-slate-800">
                    Detail Laporan
                </h1>

                <p class="text-sm text-slate-500 mt-1">
                    Informasi lengkap laporan aksesibilitas.
                </p>

            </div>


            {{-- STATUS --}}
            @php
                $status = $laporan->status;

                $statusClass = 'bg-slate-50 text-slate-700 border-slate-200';
                $statusLabel = 'Belum Diketahui';

                if ($status === 'menunggu_verifikasi') {
                    $statusClass = 'bg-amber-50 text-amber-700 border-amber-100';
                    $statusLabel = 'Menunggu Verifikasi';
                } elseif ($status === 'diverifikasi') {
                    $statusClass = 'bg-emerald-50 text-emerald-700 border-emerald-100';
                    $statusLabel = 'Terverifikasi';
                } elseif ($status === 'dalam_perbaikan') {
                    $statusClass = 'bg-cyan-50 text-cyan-700 border-cyan-100';
                    $statusLabel = 'Dalam Perbaikan';
                } elseif ($status === 'selesai') {
                    $statusClass = 'bg-emerald-50 text-emerald-700 border-emerald-100';
                    $statusLabel = 'Selesai';
                } elseif ($status === 'ditolak') {
                    $statusClass = 'bg-red-50 text-red-700 border-red-100';
                    $statusLabel = 'Ditolak';
                }
            @endphp

            <span
                class="inline-flex items-center
                       px-3 py-2 rounded-lg border
                       text-xs font-semibold
                       {{ $statusClass }}"
            >
                {{ $statusLabel }}
            </span>

        </div>

    </div>


    {{-- ==========================================================
    INFORMASI UTAMA
    ========================================================== --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">


        {{-- ======================================================
        INFORMASI LAPORAN
        ====================================================== --}}
        <div class="lg:col-span-2
                    bg-white rounded-2xl
                    border border-slate-200
                    shadow-sm p-6">

            <h2 class="text-lg font-semibold text-slate-800 mb-5">
                Informasi Laporan
            </h2>


            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">


                {{-- KODE --}}
                <div>

                    <p class="text-xs text-slate-500 mb-1">
                        Kode Laporan
                    </p>

                    <p class="font-semibold text-slate-800">
                        {{ $laporan->kode_laporan ?? '-' }}
                    </p>

                </div>


                {{-- KATEGORI --}}
                <div>

                    <p class="text-xs text-slate-500 mb-1">
                        Kategori Hambatan
                    </p>

                    <p class="font-semibold text-slate-800">
                        {{ optional($laporan->kategoriHambatan)->nama ?? '-' }}
                    </p>

                </div>


                {{-- JUDUL --}}
                <div class="md:col-span-2">

                    <p class="text-xs text-slate-500 mb-1">
                        Judul Laporan
                    </p>

                    <p class="font-semibold text-slate-800">
                        {{ $laporan->judul ?? '-' }}
                    </p>

                </div>


                {{-- DESKRIPSI --}}
                <div class="md:col-span-2">

                    <p class="text-xs text-slate-500 mb-1">
                        Deskripsi
                    </p>

                    <div
                        class="text-sm leading-6 text-slate-700
                               whitespace-pre-line"
                    >
                        {{ $laporan->deskripsi ?? 'Tidak ada deskripsi.' }}
                    </div>

                </div>


                {{-- LOKASI --}}
                <div class="md:col-span-2">

                    <p class="text-xs text-slate-500 mb-1">
                        Lokasi
                    </p>

                    <p class="text-sm text-slate-700">
                        {{ $laporan->alamat_lengkap ?? 'Lokasi tidak tersedia.' }}
                    </p>

                </div>


                {{-- WILAYAH --}}
                <div>

                    <p class="text-xs text-slate-500 mb-1">
                        Wilayah
                    </p>

                    <p class="font-medium text-slate-800">
                        {{ optional($laporan->wilayah)->nama ?? '-' }}
                    </p>

                </div>


                {{-- TANGGAL --}}
                <div>

                    <p class="text-xs text-slate-500 mb-1">
                        Tanggal Laporan
                    </p>

                    <p class="font-medium text-slate-800">
                        {{
                            $laporan->created_at
                                ? $laporan->created_at->format('d/m/Y H:i')
                                : '-'
                        }}
                    </p>

                </div>

            </div>

        </div>


        {{-- ======================================================
        PRIORITAS
        ====================================================== --}}
        <div
            class="bg-white rounded-2xl
                   border border-slate-200
                   shadow-sm p-6"
        >

            <h2 class="text-lg font-semibold text-slate-800 mb-5">
                Prioritas Laporan
            </h2>


            {{-- SKOR --}}
            <div
                class="rounded-xl
                       bg-red-50
                       border border-red-100
                       p-5 mb-5"
            >

                <p class="text-xs text-red-600 font-medium mb-1">
                    Skor Prioritas
                </p>

                <p class="text-3xl font-bold text-red-700">
                    {{
                        $laporan->skor_prioritas !== null
                            ? number_format(
                                (float) $laporan->skor_prioritas,
                                2
                            )
                            : '-'
                    }}
                </p>

            </div>


            {{-- JUMLAH PELAPOR --}}
            <div
                class="flex items-center
                       justify-between
                       py-3 border-b border-slate-100"
            >

                <span class="text-sm text-slate-500">
                    Jumlah Pelapor
                </span>

                <span class="font-semibold text-slate-800">
                    {{ $laporan->jumlah_pelapor ?? 0 }}
                </span>

            </div>


            {{-- KOORDINAT --}}
            <div
                class="py-3
                       border-b border-slate-100"
            >

                <p class="text-sm text-slate-500 mb-2">
                    Koordinat
                </p>

                <p class="text-xs font-mono text-slate-700">
                    {{ $laporan->latitude ?? '-' }},
                    {{ $laporan->longitude ?? '-' }}
                </p>

            </div>


            {{-- SUMBER KOORDINAT --}}
            <div class="py-3">

                <p class="text-sm text-slate-500 mb-1">
                    Sumber Koordinat
                </p>

                <p class="text-sm font-medium text-slate-800">
                    {{ $laporan->sumber_koordinat ?? '-' }}
                </p>

            </div>

        </div>

    </div>


    {{-- ==========================================================
    FOTO LAPORAN
    ========================================================== --}}
    <div
        class="bg-white rounded-2xl
               border border-slate-200
               shadow-sm p-6 mt-6"
    >

        <div class="flex items-center
                    justify-between mb-5">

            <div>

                <h2 class="text-lg font-semibold text-slate-800">
                    Foto Laporan
                </h2>

                <p class="text-xs text-slate-500 mt-1">
                    Bukti visual yang dilampirkan pada laporan.
                </p>

            </div>

        </div>


        @if(
            isset($laporan->fotoLaporan)
            && $laporan->fotoLaporan->count() > 0
        )

            <div class="grid grid-cols-1
                        sm:grid-cols-2
                        lg:grid-cols-4
                        gap-4">

                @foreach($laporan->fotoLaporan as $foto)

                    <div
                        class="rounded-xl
                               overflow-hidden
                               border border-slate-200
                               bg-slate-50"
                    >

                        <img
                            src="{{ asset('storage/' . $foto->path_file) }}"
                            alt="Foto laporan {{ $laporan->kode_laporan }}"
                            class="w-full h-48
                                   object-cover"
                        >

                    </div>

                @endforeach

            </div>

        @else

            <div
                class="rounded-xl
                       border border-dashed
                       border-slate-300
                       bg-slate-50
                       p-8 text-center"
            >

                <i
                    class="fa-regular fa-image
                           text-3xl text-slate-300 mb-3"
                ></i>

                <p class="text-sm font-medium text-slate-500">
                    Tidak ada foto laporan.
                </p>

            </div>

        @endif

    </div>


    {{-- ==========================================================
    PELAPOR & FASILITAS TERDEKAT
    ========================================================== --}}
    <div class="grid grid-cols-1 lg:grid-cols-2
                gap-6 mt-6">


        {{-- PELAPOR --}}
        <div
            class="bg-white rounded-2xl
                   border border-slate-200
                   shadow-sm p-6"
        >

            <h2 class="text-lg font-semibold text-slate-800 mb-5">
                Informasi Pelapor
            </h2>


            @if($laporan->pelapor)

                <div class="space-y-4">

                    <div>

                        <p class="text-xs text-slate-500 mb-1">
                            Nama
                        </p>

                        <p class="text-sm font-medium text-slate-800">
                            {{ $laporan->pelapor->name ?? '-' }}
                        </p>

                    </div>


                    <div>

                        <p class="text-xs text-slate-500 mb-1">
                            Email
                        </p>

                        <p class="text-sm text-slate-700">
                            {{ $laporan->pelapor->email ?? '-' }}
                        </p>

                    </div>

                </div>

            @else

                <p class="text-sm text-slate-500">
                    Informasi pelapor tidak tersedia.
                </p>

            @endif

        </div>


        {{-- FASILITAS TERDEKAT --}}
        <div
            class="bg-white rounded-2xl
                   border border-slate-200
                   shadow-sm p-6"
        >

            <h2 class="text-lg font-semibold text-slate-800 mb-5">
                Fasilitas Terdekat
            </h2>


            @if(
                isset($laporan->fasilitasTerdekat)
                && $laporan->fasilitasTerdekat->count() > 0
            )

                <div class="space-y-3">

                    @foreach($laporan->fasilitasTerdekat as $fasilitas)

                        <div
                            class="flex items-start gap-3
                                   p-3 rounded-xl
                                   bg-slate-50
                                   border border-slate-100"
                        >

                            <div
                                class="w-9 h-9 rounded-lg
                                       bg-emerald-100
                                       flex items-center
                                       justify-center
                                       shrink-0"
                            >

                                <i
                                    class="fa-solid fa-building
                                           text-emerald-600"
                                ></i>

                            </div>


                            <div>

                                <p class="text-sm
                                          font-semibold
                                          text-slate-800">

                                    {{ $fasilitas->nama ?? '-' }}

                                </p>

                                <p class="text-xs
                                          text-slate-500 mt-1">

                                    {{ $fasilitas->jenis ?? '-' }}

                                </p>

                            </div>

                        </div>

                    @endforeach

                </div>

            @else

                <p class="text-sm text-slate-500">
                    Belum ada data fasilitas terdekat.
                </p>

            @endif

        </div>

    </div>


    {{-- ==========================================================
    TOMBOL AKSI
    ========================================================== --}}
    <div
        class="flex flex-col sm:flex-row
               justify-end gap-3
               mt-6"
    >

        <a
            href="{{ route('dinas.laporan.pdf', $laporan->id) }}"
            class="inline-flex items-center
                   justify-center gap-2
                   px-5 py-2.5
                   rounded-lg
                   border border-slate-300
                   text-sm font-medium
                   text-slate-700
                   hover:bg-slate-50
                   transition"
        >
            <i class="fa-solid fa-file-pdf text-red-500"></i>
            Unduh PDF
        </a>


        <a
            href="{{ route('dinas.laporan.index') }}"
            class="inline-flex items-center
                   justify-center gap-2
                   px-5 py-2.5
                   rounded-lg
                   bg-emerald-600
                   text-white
                   text-sm font-medium
                   hover:bg-emerald-700
                   transition"
        >
            <i class="fa-solid fa-arrow-left"></i>
            Kembali ke Laporan
        </a>

    </div>

</div>

@endsection