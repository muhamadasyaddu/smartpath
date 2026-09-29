@extends('layouts.admin')

@section('title', 'Kinerja & Anggaran')
@section('page_title', 'Kinerja & Anggaran')

@section('content')

<div class="space-y-6">

    {{-- ==========================================================
    HEADER
    ========================================================== --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">

        <div>
            <h1 class="text-xl font-bold text-slate-900">
                Kinerja & Anggaran
            </h1>

            <p class="text-sm text-slate-500 mt-1">
                Pemantauan perkembangan rencana perbaikan dan realisasi anggaran.
            </p>
        </div>

        <div class="inline-flex items-center gap-2 px-3 py-2 rounded-lg
                    bg-slate-50 border border-slate-200 text-xs text-slate-500">

            <i class="fa-solid fa-circle-info text-emerald-600"></i>

            Data simulasi prototype

        </div>

    </div>


    {{-- ==========================================================
    1. RINGKASAN KINERJA
    ========================================================== --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">

        {{-- TOTAL RENCANA --}}
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">

            <div class="flex items-center justify-between mb-3">

                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">

                    <i class="fa-solid fa-list-check text-blue-600"></i>

                </div>

                <span class="text-xs font-semibold text-slate-400">
                    Total
                </span>

            </div>

            <p class="text-2xl font-bold text-slate-900">
                {{ $totalRencana ?? 0 }}
            </p>

            <p class="text-xs text-slate-500 mt-1">
                Total rencana
            </p>

        </div>


        {{-- BELUM DIMULAI --}}
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">

            <div class="flex items-center justify-between mb-3">

                <div class="w-10 h-10 bg-slate-100 rounded-lg flex items-center justify-center">

                    <i class="fa-solid fa-clock text-slate-600"></i>

                </div>

                <span class="text-xs font-semibold text-slate-500">
                    Belum Dimulai
                </span>

            </div>

            <p class="text-2xl font-bold text-slate-900">
                {{ $belumDimulai ?? 0 }}
            </p>

            <p class="text-xs text-slate-500 mt-1">
                Belum dilaksanakan
            </p>

        </div>


        {{-- DALAM PERBAIKAN --}}
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">

            <div class="flex items-center justify-between mb-3">

                <div class="w-10 h-10 bg-cyan-100 rounded-lg flex items-center justify-center">

                    <i class="fa-solid fa-screwdriver-wrench text-cyan-600"></i>

                </div>

                <span class="text-xs font-semibold text-cyan-600">
                    Proses
                </span>

            </div>

            <p class="text-2xl font-bold text-slate-900">
                {{ $dalamPerbaikan ?? 0 }}
            </p>

            <p class="text-xs text-slate-500 mt-1">
                Dalam perbaikan
            </p>

        </div>


        {{-- SELESAI --}}
        <div class="bg-white rounded-xl border border-emerald-200 p-5 shadow-sm">

            <div class="flex items-center justify-between mb-3">

                <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">

                    <i class="fa-solid fa-circle-check text-emerald-600"></i>

                </div>

                <span class="text-xs font-semibold text-emerald-600">
                    Selesai
                </span>

            </div>

            <p class="text-2xl font-bold text-slate-900">
                {{ $selesai ?? 0 }}
            </p>

            <p class="text-xs text-slate-500 mt-1">
                Rencana selesai
            </p>

        </div>


        {{-- TERLAMBAT --}}
        <div class="bg-white rounded-xl border border-red-200 p-5 shadow-sm">

            <div class="flex items-center justify-between mb-3">

                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">

                    <i class="fa-solid fa-triangle-exclamation text-red-600"></i>

                </div>

                <span class="text-xs font-semibold text-red-600">
                    Terlambat
                </span>

            </div>

            <p class="text-2xl font-bold text-slate-900">
                {{ $terlambat ?? 0 }}
            </p>

            <p class="text-xs text-slate-500 mt-1">
                Melewati target
            </p>

        </div>

    </div>


    {{-- ==========================================================
    2. RINGKASAN ANGGARAN
    ========================================================== --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

        {{-- ESTIMASI --}}
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">

            <div class="flex items-center gap-3">

                <div class="w-10 h-10 rounded-lg bg-blue-100
                            flex items-center justify-center">

                    <i class="fa-solid fa-wallet text-blue-600"></i>

                </div>

                <div>

                    <p class="text-xs text-slate-500">
                        Total Estimasi
                    </p>

                    <p class="text-lg font-bold text-slate-900">
                        Rp {{ number_format($totalEstimasi ?? 0, 0, ',', '.') }}
                    </p>

                </div>

            </div>

        </div>


        {{-- REALISASI --}}
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">

            <div class="flex items-center gap-3">

                <div class="w-10 h-10 rounded-lg bg-emerald-100
                            flex items-center justify-center">

                    <i class="fa-solid fa-money-bill-wave text-emerald-600"></i>

                </div>

                <div>

                    <p class="text-xs text-slate-500">
                        Total Realisasi
                    </p>

                    <p class="text-lg font-bold text-slate-900">
                        Rp {{ number_format($totalRealisasi ?? 0, 0, ',', '.') }}
                    </p>

                </div>

            </div>

        </div>


        {{-- SISA --}}
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">

            <div class="flex items-center gap-3">

                <div class="w-10 h-10 rounded-lg bg-amber-100
                            flex items-center justify-center">

                    <i class="fa-solid fa-coins text-amber-600"></i>

                </div>

                <div>

                    <p class="text-xs text-slate-500">
                        Sisa Anggaran
                    </p>

                    <p class="text-lg font-bold text-slate-900">
                        Rp {{ number_format($sisaAnggaran ?? 0, 0, ',', '.') }}
                    </p>

                </div>

            </div>

        </div>

    </div>


   
    {{-- ==========================================================
    4. REKAP PER KATEGORI
    ========================================================== --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">

        <div class="px-5 py-4 border-b border-slate-200">

            <h2 class="font-semibold text-slate-900">
                Rekap Anggaran per Kategori
            </h2>

            <p class="text-xs text-slate-500 mt-1">
                Ringkasan rencana perbaikan berdasarkan kategori hambatan.
            </p>

        </div>


        <div class="overflow-x-auto">

            <table class="w-full text-sm text-left">

                <thead class="bg-slate-50 border-b border-slate-200">

                    <tr>

                        <th class="px-5 py-3 text-xs font-semibold
                                   text-slate-500 uppercase">
                            Kategori
                        </th>

                        <th class="px-5 py-3 text-xs font-semibold
                                   text-slate-500 uppercase text-center">
                            Jumlah Rencana
                        </th>

                        <th class="px-5 py-3 text-xs font-semibold
                                   text-slate-500 uppercase text-right">
                            Estimasi
                        </th>

                        <th class="px-5 py-3 text-xs font-semibold
                                   text-slate-500 uppercase text-right">
                            Realisasi
                        </th>

                        <th class="px-5 py-3 text-xs font-semibold
                                   text-slate-500 uppercase text-center">
                            Progress
                        </th>

                    </tr>

                </thead>


                <tbody class="divide-y divide-slate-100">

                    @forelse($rekapKategori ?? [] as $kategori)

                        <tr class="hover:bg-slate-50 transition-colors">

                            <td class="px-5 py-4">

                                <span class="font-semibold text-slate-800">
                                    {{ $kategori->nama_kategori }}
                                </span>

                            </td>


                            <td class="px-5 py-4 text-center">

                                <span class="inline-flex items-center justify-center
                                             min-w-[32px] px-2 py-1
                                             rounded-md bg-slate-100
                                             text-xs font-semibold text-slate-700">

                                    {{ $kategori->jumlah_rencana }}

                                </span>

                            </td>


                            <td class="px-5 py-4 text-right
                                       font-medium text-slate-700">

                                Rp {{ number_format(
                                    $kategori->estimasi,
                                    0,
                                    ',',
                                    '.'
                                ) }}

                            </td>


                            <td class="px-5 py-4 text-right
                                       font-semibold text-slate-900">

                                Rp {{ number_format(
                                    $kategori->realisasi,
                                    0,
                                    ',',
                                    '.'
                                ) }}

                            </td>


                            <td class="px-5 py-4">

                                <div class="min-w-[120px]">

                                    <div class="flex items-center justify-between
                                                text-[11px] mb-1">

                                        <span class="text-slate-400">
                                            Realisasi
                                        </span>

                                        <span class="font-bold text-slate-700">
                                            {{ $kategori->persentase }}%
                                        </span>

                                    </div>

                                    <div class="w-full h-2 bg-slate-100
                                                rounded-full overflow-hidden">

                                        <div
                                            class="h-2 bg-emerald-600
                                                   rounded-full"
                                            style="width: {{ min(100, $kategori->persentase) }}%"
                                        ></div>

                                    </div>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="5"
                                class="px-5 py-8 text-center text-slate-400"
                            >

                                Belum ada data anggaran.

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>


    {{-- ==========================================================
    5. FILTER RENCANA PERBAIKAN
    ========================================================== --}}
    <div class="bg-white rounded-xl border border-slate-200
                shadow-sm overflow-hidden">

        <div class="px-5 py-4 border-b border-slate-200
                    flex flex-col md:flex-row md:items-center
                    md:justify-between gap-3">

            <div>

                <h2 class="font-semibold text-slate-900">
                    Detail Kinerja & Anggaran
                </h2>

                <p class="text-xs text-slate-500 mt-1">
                    Daftar rencana perbaikan yang tercatat pada sistem.
                </p>

            </div>


            {{-- FILTER --}}
            <form
                method="GET"
                action="{{ route('dinas.kinerja-anggaran.index') }}"
                class="flex items-center gap-2"
            >

                <label
                    for="status"
                    class="text-xs font-medium text-slate-500"
                >
                    Status
                </label>

                <select
                    id="status"
                    name="status"
                    onchange="this.form.submit()"
                    class="rounded-lg border border-slate-300
                           bg-white px-3 py-2 text-sm
                           text-slate-700
                           focus:border-emerald-500
                           focus:ring-emerald-500"
                >

                    <option
                        value="semua"
                        {{ $filter === 'semua' ? 'selected' : '' }}
                    >
                        Semua Status
                    </option>

                    <option
                        value="belum_dimulai"
                        {{ $filter === 'belum_dimulai' ? 'selected' : '' }}
                    >
                        Belum Dimulai
                    </option>

                    <option
                        value="dalam_perbaikan"
                        {{ $filter === 'dalam_perbaikan' ? 'selected' : '' }}
                    >
                        Dalam Perbaikan
                    </option>

                    <option
                        value="terlambat"
                        {{ $filter === 'terlambat' ? 'selected' : '' }}
                    >
                        Terlambat
                    </option>

                    <option
                        value="selesai"
                        {{ $filter === 'selesai' ? 'selected' : '' }}
                    >
                        Selesai
                    </option>

                </select>

            </form>

        </div>


        {{-- TABEL --}}
        <div class="overflow-x-auto">

            <table class="w-full text-sm text-left">

                <thead class="bg-slate-50 border-b border-slate-200">

                    <tr>

                        <th class="px-5 py-3 text-xs font-semibold text-slate-500">
                            Kode
                        </th>

                        <th class="px-5 py-3 text-xs font-semibold text-slate-500">
                            Kategori
                        </th>

                        <th class="px-5 py-3 text-xs font-semibold text-slate-500">
                            Prioritas
                        </th>

                        <th class="px-5 py-3 text-xs font-semibold text-slate-500">
                            Status
                        </th>

                        <th class="px-5 py-3 text-xs font-semibold text-slate-500">
                            Target
                        </th>

                        <th class="px-5 py-3 text-xs font-semibold
                                   text-slate-500 text-right">
                            Estimasi
                        </th>

                        <th class="px-5 py-3 text-xs font-semibold
                                   text-slate-500 text-right">
                            Realisasi
                        </th>

                    </tr>

                </thead>


                <tbody class="divide-y divide-slate-100">

                    @forelse($rencanaTampil ?? [] as $rencana)

                        @php

                            $status = $rencana->status_otomatis ?? 'belum_dimulai';

                            $badgeClass = match ($status) {

                                'selesai' =>
                                    'bg-emerald-50 text-emerald-700 border border-emerald-100',

                                'dalam_perbaikan' =>
                                    'bg-cyan-50 text-cyan-700 border border-cyan-100',

                                default =>
                                    'bg-slate-50 text-slate-600 border border-slate-200',

                            };

                            $statusLabel = match ($status) {

                                'selesai' =>
                                    'Selesai',

                                'dalam_perbaikan' =>
                                    'Dalam Perbaikan',

                                default =>
                                    'Belum Dimulai',

                            };

                        @endphp


                        <tr class="hover:bg-slate-50 transition-colors">


                            {{-- KODE --}}
                            <td class="px-5 py-4">

                                <div class="font-mono font-bold text-slate-700">
                                    {{ $rencana->laporan->kode_laporan ?? '-' }}
                                </div>

                                <div class="text-[11px] text-slate-400 mt-1">
                                    {{ Str::limit(
                                        $rencana->laporan->judul ?? '-',
                                        35
                                    ) }}
                                </div>

                            </td>


                            {{-- KATEGORI --}}
                            <td class="px-5 py-4">

                                <span class="text-slate-700 font-medium">

                                    {{
                                        $rencana->laporan
                                            ->kategoriHambatan
                                            ->nama
                                            ?? 'Kategori Lainnya'
                                    }}

                                </span>

                            </td>


                            {{-- PRIORITAS --}}
                            <td class="px-5 py-4">

                                @if(
                                    $rencana->prioritas_label === 'Tinggi'
                                )

                                    <span class="inline-flex items-center
                                                 px-2 py-1 rounded-md
                                                 bg-red-50 text-red-700
                                                 border border-red-100
                                                 text-xs font-semibold">

                                        Tinggi

                                    </span>

                                @elseif(
                                    $rencana->prioritas_label === 'Sedang'
                                )

                                    <span class="inline-flex items-center
                                                 px-2 py-1 rounded-md
                                                 bg-amber-50 text-amber-700
                                                 border border-amber-100
                                                 text-xs font-semibold">

                                        Sedang

                                    </span>

                                @else

                                    <span class="inline-flex items-center
                                                 px-2 py-1 rounded-md
                                                 bg-slate-50 text-slate-600
                                                 border border-slate-200
                                                 text-xs font-semibold">

                                        Rendah

                                    </span>

                                @endif

                            </td>


                            {{-- STATUS --}}
                            <td class="px-5 py-4">

                                @if($rencana->terlambat)

                                    <span class="inline-flex items-center
                                                 gap-1.5 px-2 py-1
                                                 rounded-md bg-red-50
                                                 text-red-700
                                                 border border-red-100
                                                 text-xs font-semibold">

                                        <i class="fa-solid fa-triangle-exclamation"></i>

                                        Terlambat

                                    </span>

                                @else

                                    <span class="inline-flex items-center
                                                 px-2 py-1 rounded-md
                                                 text-xs font-semibold
                                                 {{ $badgeClass }}">

                                        {{ $statusLabel }}

                                    </span>

                                @endif

                            </td>


                            {{-- TARGET --}}
                            <td class="px-5 py-4 text-slate-600 whitespace-nowrap">

                                {{
                                    $rencana->target_selesai
                                        ? $rencana->target_selesai->format('d M Y')
                                        : '-'
                                }}

                            </td>


                            {{-- ESTIMASI --}}
                            <td class="px-5 py-4 text-right
                                       text-slate-700 whitespace-nowrap">

                                Rp {{ number_format(
                                    $rencana->estimasi_anggaran ?? 0,
                                    0,
                                    ',',
                                    '.'
                                ) }}

                            </td>


                            {{-- REALISASI --}}
                            <td class="px-5 py-4 text-right
                                       font-semibold text-slate-900
                                       whitespace-nowrap">

                                Rp {{ number_format(
                                    $rencana->realisasi_anggaran ?? 0,
                                    0,
                                    ',',
                                    '.'
                                ) }}

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="7"
                                class="px-5 py-10 text-center"
                            >

                                <div class="flex flex-col items-center">

                                    <div class="w-12 h-12 rounded-full
                                                bg-slate-100 flex items-center
                                                justify-center mb-3">

                                        <i class="fa-solid fa-chart-line
                                                  text-slate-400"></i>

                                    </div>

                                    <p class="text-sm font-semibold
                                              text-slate-600">

                                        Belum ada data

                                    </p>

                                    <p class="text-xs text-slate-400 mt-1">

                                        Belum terdapat rencana perbaikan
                                        yang sesuai dengan filter.

                                    </p>

                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- ======================================================
        PAGINATION MANUAL
        ====================================================== --}}
        @if(($totalData ?? 0) > ($perPage ?? 10))

            @php

                $totalHalaman = (int) ceil(
                    ($totalData ?? 0) / ($perPage ?? 10)
                );

            @endphp

            <div class="px-5 py-4 border-t border-slate-200
                        flex flex-col sm:flex-row
                        sm:items-center sm:justify-between gap-3">

                <p class="text-xs text-slate-500">

                    Menampilkan
                    {{ (($page - 1) * $perPage) + 1 }}
                    –
                    {{ min($page * $perPage, $totalData) }}
                    dari
                    {{ $totalData }}
                    data

                </p>


                <div class="flex items-center gap-1">

                    @if($page > 1)

                        <a
                            href="{{ route(
                                'dinas.kinerja-anggaran.index',
                                [
                                    'status' => $filter,
                                    'page' => $page - 1,
                                ]
                            ) }}"
                            class="inline-flex items-center justify-center
                                   w-9 h-9 rounded-lg
                                   border border-slate-200
                                   text-slate-600
                                   hover:bg-slate-50"
                        >

                            <i class="fa-solid fa-chevron-left text-xs"></i>

                        </a>

                    @endif


                    @for($i = 1; $i <= $totalHalaman; $i++)

                        <a
                            href="{{ route(
                                'dinas.kinerja-anggaran.index',
                                [
                                    'status' => $filter,
                                    'page' => $i,
                                ]
                            ) }}"
                            class="inline-flex items-center justify-center
                                   w-9 h-9 rounded-lg border
                                   text-xs font-semibold
                                   {{ $page == $i
                                        ? 'bg-emerald-600 border-emerald-600 text-white'
                                        : 'border-slate-200 text-slate-600 hover:bg-slate-50'
                                   }}"
                        >

                            {{ $i }}

                        </a>

                    @endfor


                    @if($page < $totalHalaman)

                        <a
                            href="{{ route(
                                'dinas.kinerja-anggaran.index',
                                [
                                    'status' => $filter,
                                    'page' => $page + 1,
                                ]
                            ) }}"
                            class="inline-flex items-center justify-center
                                   w-9 h-9 rounded-lg
                                   border border-slate-200
                                   text-slate-600
                                   hover:bg-slate-50"
                        >

                            <i class="fa-solid fa-chevron-right text-xs"></i>

                        </a>

                    @endif

                </div>

            </div>

        @endif

    </div>

</div>

@endsection