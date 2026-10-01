@extends('layouts.admin')

@section('title', 'Rencana Perbaikan')

@section('content')

<div class="p-6">

    {{-- ==========================================================
         HEADER
    =========================================================== --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-800">
            Rencana Perbaikan
        </h1>

        <p class="text-sm text-slate-500 mt-1">
            Daftar laporan terverifikasi yang menjadi bahan pertimbangan
            dalam perencanaan perbaikan infrastruktur.
        </p>
    </div>


    {{-- ==========================================================
         STATISTIK
    =========================================================== --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5 mb-6">

        {{-- Total Rencana --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500">
                        Total Rencana
                    </p>

                    <h2 class="text-2xl font-bold text-slate-800 mt-1">
                        {{ $totalRencana }}
                    </h2>
                </div>

                <div class="w-11 h-11 rounded-xl bg-emerald-50 flex items-center justify-center">
                    <i
                        class="fa-solid fa-clipboard-list text-emerald-500 text-lg"
                        aria-hidden="true"
                    ></i>
                </div>
            </div>
        </div>


        {{-- Belum Dimulai --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500">
                        Belum Dimulai
                    </p>

                    <h2 class="text-2xl font-bold text-slate-800 mt-1">
                        {{ $belumDimulai }}
                    </h2>
                </div>

                <div class="w-11 h-11 rounded-xl bg-amber-50 flex items-center justify-center">
                    <i
                        class="fa-solid fa-clock text-amber-500 text-lg"
                        aria-hidden="true"
                    ></i>
                </div>
            </div>
        </div>


        {{-- Dalam Perbaikan --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500">
                        Dalam Perbaikan
                    </p>

                    <h2 class="text-2xl font-bold text-slate-800 mt-1">
                        {{ $dalamPerbaikan }}
                    </h2>
                </div>

                <div class="w-11 h-11 rounded-xl bg-blue-50 flex items-center justify-center">
                    <i
                        class="fa-solid fa-screwdriver-wrench text-blue-500 text-lg"
                        aria-hidden="true"
                    ></i>
                </div>
            </div>
        </div>


        {{-- Selesai --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500">
                        Selesai
                    </p>

                    <h2 class="text-2xl font-bold text-slate-800 mt-1">
                        {{ $selesai }}
                    </h2>
                </div>

                <div class="w-11 h-11 rounded-xl bg-emerald-50 flex items-center justify-center">
                    <i
                        class="fa-solid fa-circle-check text-emerald-500 text-lg"
                        aria-hidden="true"
                    ></i>
                </div>
            </div>
        </div>

    </div>


    {{-- ==========================================================
         DAFTAR LAPORAN
    =========================================================== --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

        {{-- Header --}}
        <div class="px-6 py-5 border-b border-slate-200">

            <h2 class="text-lg font-semibold text-slate-800">
                Daftar Laporan dan Rencana Perbaikan
            </h2>

            <p class="text-sm text-slate-500 mt-1">
                Laporan yang telah diverifikasi dan memiliki skor prioritas.
            </p>


            {{-- Filter Status --}}
            <form
                method="GET"
                action="{{ route('dinas.rencana-perbaikan.index') }}"
                class="mt-4 flex flex-col sm:flex-row sm:items-center gap-3"
            >

                <div class="w-full sm:w-64">

                    <label
                        for="status"
                        class="sr-only"
                    >
                        Filter status
                    </label>

                    <select
                        id="status"
                        name="status"
                        onchange="this.form.submit()"
                        class="w-full px-4 py-3
                               border border-slate-200
                               rounded-xl
                               bg-white
                               text-sm text-slate-700
                               focus:outline-none
                               focus:ring-2
                               focus:ring-emerald-500
                               focus:border-emerald-500"
                    >

                        <option value="">
                            Semua Status
                        </option>

                        <option
                            value="belum_ada_rencana"
                            {{ request('status') === 'belum_ada_rencana' ? 'selected' : '' }}
                        >
                            Belum Ada Rencana
                        </option>

                        <option
                            value="belum_dimulai"
                            {{ request('status') === 'belum_dimulai' ? 'selected' : '' }}
                        >
                            Belum Dimulai
                        </option>

                        <option
                            value="dalam_perbaikan"
                            {{ request('status') === 'dalam_perbaikan' ? 'selected' : '' }}
                        >
                            Dalam Perbaikan
                        </option>

                        <option
                            value="terlambat"
                            {{ request('status') === 'terlambat' ? 'selected' : '' }}
                        >
                            Terlambat
                        </option>

                        <option
                            value="selesai"
                            {{ request('status') === 'selesai' ? 'selected' : '' }}
                        >
                            Selesai
                        </option>

                    </select>

                </div>


                {{-- Reset Filter --}}
                @if(request('status'))

                    <a
                        href="{{ route('dinas.rencana-perbaikan.index') }}"
                        class="inline-flex items-center justify-center gap-2
                               px-4 py-3
                               rounded-xl
                               border border-slate-200
                               bg-white
                               text-sm font-medium
                               text-slate-600
                               hover:bg-slate-50
                               transition"
                    >
                        <i
                            class="fa-solid fa-rotate-left"
                            aria-hidden="true"
                        ></i>

                        Reset
                    </a>

                @endif

            </form>

        </div>


        {{-- ======================================================
             TABEL
        ======================================================= --}}
        <div class="overflow-x-auto">

            <table class="w-full text-sm">

                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>

                        <th class="px-5 py-4 text-left font-semibold text-slate-600">
                            No
                        </th>

                        <th class="px-5 py-4 text-left font-semibold text-slate-600">
                            Kode
                        </th>

                        <th class="px-5 py-4 text-left font-semibold text-slate-600">
                            Laporan
                        </th>

                        <th class="px-5 py-4 text-left font-semibold text-slate-600">
                            Kategori
                        </th>

                        <th class="px-5 py-4 text-left font-semibold text-slate-600">
                            Prioritas
                        </th>

                        <th class="px-5 py-4 text-left font-semibold text-slate-600">
                            Jadwal Perbaikan
                        </th>

                        <th class="px-5 py-4 text-left font-semibold text-slate-600">
                            Status Rencana
                        </th>

                        <th class="px-5 py-4 text-center font-semibold text-slate-600">
                            Aksi
                        </th>

                    </tr>
                </thead>


                <tbody class="divide-y divide-slate-100">

                    @forelse($laporan as $index => $item)

                        @php
                            $skor = (float) $item->skor_prioritas;
                            $rencana = $item->rencanaPerbaikan;

                            $status = $rencana->status_otomatis ?? null;
                            $terlambat = $rencana->terlambat ?? false;

                            if ($skor >= 70) {
                                $labelPrioritas = 'Tinggi';
                                $prioritasClass = 'bg-red-50 text-red-600';
                            } elseif ($skor >= 40) {
                                $labelPrioritas = 'Sedang';
                                $prioritasClass = 'bg-amber-50 text-amber-600';
                            } else {
                                $labelPrioritas = 'Rendah';
                                $prioritasClass = 'bg-emerald-50 text-emerald-600';
                            }

                            /*
                            |--------------------------------------------------
                            | Status tampilan
                            |--------------------------------------------------
                            | Terlambat hanya indikator tampilan.
                            | Status database tetap dalam_perbaikan.
                            */
                            if (!$rencana) {
                                $statusLabel = 'Belum Ada Rencana';
                                $statusClass = 'bg-slate-100 text-slate-600';
                                $statusIcon = 'fa-regular fa-folder-open';
                            } elseif ($terlambat) {
                                $statusLabel = 'Terlambat';
                                $statusClass = 'bg-red-50 text-red-600';
                                $statusIcon = 'fa-solid fa-triangle-exclamation';
                            } elseif ($status === 'belum_dimulai') {
                                $statusLabel = 'Belum Dimulai';
                                $statusClass = 'bg-amber-50 text-amber-600';
                                $statusIcon = 'fa-solid fa-clock';
                            } elseif ($status === 'dalam_perbaikan') {
                                $statusLabel = 'Dalam Perbaikan';
                                $statusClass = 'bg-blue-50 text-blue-600';
                                $statusIcon = 'fa-solid fa-screwdriver-wrench';
                            } elseif ($status === 'selesai') {
                                $statusLabel = 'Selesai';
                                $statusClass = 'bg-emerald-50 text-emerald-600';
                                $statusIcon = 'fa-solid fa-circle-check';
                            } else {
                                $statusLabel = '-';
                                $statusClass = 'bg-slate-100 text-slate-500';
                                $statusIcon = 'fa-solid fa-minus';
                            }
                        @endphp


                        <tr class="hover:bg-slate-50 transition align-top">

                            {{-- No --}}
                            <td class="px-5 py-5 text-slate-700">
                                {{ $laporan->firstItem() + $index }}
                            </td>


                            {{-- Kode --}}
                            <td class="px-5 py-5">
                                <span class="font-medium text-slate-800 whitespace-nowrap">
                                    {{ $item->kode_laporan }}
                                </span>
                            </td>


                            {{-- Laporan --}}
                            <td class="px-5 py-5">
                                <div class="max-w-sm">

                                    <p class="font-medium text-slate-800">
                                        {{ $item->judul }}
                                    </p>

                                    <p class="text-xs text-slate-500 mt-1">
                                        {{ $item->alamat_lengkap ?? 'Lokasi tidak tersedia' }}
                                    </p>

                                </div>
                            </td>


                            {{-- Kategori --}}
                            <td class="px-5 py-5 text-slate-700">
                                {{ $item->kategoriHambatan?->nama ?? '-' }}
                            </td>


                            {{-- Prioritas --}}
                            <td class="px-5 py-5">

                                <span
                                    class="inline-flex items-center px-2.5 py-1
                                           rounded-full text-xs font-semibold
                                           {{ $prioritasClass }}"
                                >
                                    {{ $labelPrioritas }}
                                </span>

                                <p class="text-xs text-slate-500 mt-1">
                                    Skor:
                                    {{ number_format($skor, 2) }}
                                </p>

                            </td>


                            {{-- Jadwal Perbaikan --}}
                            <td class="px-5 py-5">

                                @if($rencana)

                                    <div class="space-y-1 text-xs">

                                        <div>
                                            <span class="text-slate-500">
                                                Mulai:
                                            </span>

                                            <span class="font-medium text-slate-700">
                                                {{ $rencana->tanggal_mulai
                                                    ? $rencana->tanggal_mulai->format('d M Y')
                                                    : '-' }}
                                            </span>
                                        </div>

                                        <div>
                                            <span class="text-slate-500">
                                                Target:
                                            </span>

                                            <span class="font-medium text-slate-700">
                                                {{ $rencana->target_selesai
                                                    ? $rencana->target_selesai->format('d M Y')
                                                    : '-' }}
                                            </span>
                                        </div>

                                        <div>
                                            <span class="text-slate-500">
                                                Selesai:
                                            </span>

                                            <span class="font-medium text-slate-700">
                                                {{ $rencana->tanggal_selesai
                                                    ? $rencana->tanggal_selesai->format('d M Y')
                                                    : '-' }}
                                            </span>
                                        </div>

                                    </div>

                                @else

                                    <span class="text-slate-400">
                                        Belum ada jadwal
                                    </span>

                                @endif

                            </td>


                            {{-- Status Rencana --}}
                            <td class="px-5 py-5">

                                <span
                                    class="inline-flex items-center gap-1.5
                                           px-2.5 py-1
                                           rounded-full
                                           text-xs font-medium
                                           {{ $statusClass }}"
                                >
                                    <i
                                        class="{{ $statusIcon }}"
                                        aria-hidden="true"
                                    ></i>

                                    {{ $statusLabel }}
                                </span>

                            </td>


                            {{-- Aksi --}}
                            <td class="px-5 py-5">

                                <div class="flex items-center justify-center">

                                    @if($rencana)

                                        <a
                                            href="{{ route(
                                                'dinas.rencana-perbaikan.edit',
                                                $rencana->id
                                            ) }}"
                                            class="inline-flex items-center gap-2
                                                   px-3 py-2
                                                   rounded-lg
                                                   bg-emerald-700
                                                   text-white
                                                   text-xs font-medium
                                                   hover:bg-emerald-800
                                                   transition"
                                        >
                                            <i
                                                class="fa-solid fa-pen"
                                                aria-hidden="true"
                                            ></i>

                                            Edit
                                        </a>

                                    @else

                                        <a
                                            href="{{ route(
                                                'dinas.rencana-perbaikan.create',
                                                $item->id
                                            ) }}"
                                            class="inline-flex items-center gap-2
                                                   px-3 py-2
                                                   rounded-lg
                                                   bg-emerald-600
                                                   text-white
                                                   text-xs font-medium
                                                   hover:bg-emerald-700
                                                   transition"
                                        >
                                            <i
                                                class="fa-solid fa-plus"
                                                aria-hidden="true"
                                            ></i>

                                            Buat Rencana
                                        </a>

                                    @endif

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="8"
                                class="px-5 py-12 text-center text-slate-500"
                            >

                                <div class="flex flex-col items-center">

                                    <i
                                        class="fa-regular fa-folder-open
                                               text-4xl text-slate-300 mb-3"
                                        aria-hidden="true"
                                    ></i>

                                    <p class="font-medium">
                                        Belum ada laporan yang sesuai.
                                    </p>

                                    <p class="text-xs mt-1">
                                        Coba ubah filter status atau tunggu
                                        hingga tersedia laporan terverifikasi.
                                    </p>

                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- ==========================================================
             PAGINATION
        =========================================================== --}}
        @if($laporan->hasPages())

            <div class="px-5 py-4 border-t border-slate-200">
                {{ $laporan->links() }}
            </div>

        @endif

    </div>

</div>

@endsection