@extends('layouts.admin')

@section('title', 'Rencana Perbaikan')

@section('content')

<div class="p-6">

    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-800">
            Rencana Perbaikan
        </h1>

        <p class="text-sm text-slate-500 mt-1">
            Daftar laporan aksesibilitas yang menjadi bahan pertimbangan
            dalam perencanaan perbaikan infrastruktur.
        </p>
    </div>


    {{-- Statistik --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-6">

        {{-- Prioritas Tinggi --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <div class="flex items-center justify-between">

                <div>
                    <p class="text-sm text-slate-500">
                        Prioritas Tinggi
                    </p>

                    <h2 class="text-2xl font-bold text-slate-800 mt-1">
                        {{ $prioritasTinggi }}
                    </h2>
                </div>

                <div class="w-11 h-11 rounded-xl bg-red-50
                            flex items-center justify-center">
                    <i class="fa-solid fa-triangle-exclamation
                              text-red-500 text-lg"></i>
                </div>

            </div>
        </div>


        {{-- Dalam Penanganan --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <div class="flex items-center justify-between">

                <div>
                    <p class="text-sm text-slate-500">
                        Dalam Penanganan
                    </p>

                    <h2 class="text-2xl font-bold text-slate-800 mt-1">
                        {{ $dalamPenanganan }}
                    </h2>
                </div>

                <div class="w-11 h-11 rounded-xl bg-blue-50
                            flex items-center justify-center">
                    <i class="fa-solid fa-screwdriver-wrench
                              text-blue-500 text-lg"></i>
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

                <div class="w-11 h-11 rounded-xl bg-emerald-50
                            flex items-center justify-center">
                    <i class="fa-solid fa-circle-check
                              text-emerald-500 text-lg"></i>
                </div>

            </div>
        </div>

    </div>


    {{-- Tabel --}}
    <div class="bg-white rounded-2xl border border-slate-200
                shadow-sm overflow-hidden">

        <div class="px-6 py-5 border-b border-slate-200">
            <h2 class="text-lg font-semibold text-slate-800">
                Daftar Rencana Perbaikan
            </h2>

            <p class="text-sm text-slate-500 mt-1">
                Laporan yang telah diverifikasi dan memiliki skor prioritas.
            </p>
        </div>


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
                            Lokasi / Judul
                        </th>

                        <th class="px-5 py-4 text-left font-semibold text-slate-600">
                            Kategori
                        </th>

                        <th class="px-5 py-4 text-left font-semibold text-slate-600">
                            Prioritas
                        </th>

                        <th class="px-5 py-4 text-left font-semibold text-slate-600">
                            Status
                        </th>

                        <th class="px-5 py-4 text-center font-semibold text-slate-600">
                            Aksi
                        </th>

                    </tr>

                </thead>


                <tbody class="divide-y divide-slate-100">

                    @forelse($laporan as $index => $item)

                        @php
                            $skor = $item->skor_prioritas;

                            if ($skor >= 70) {
                                $labelPrioritas = 'Tinggi';
                            } elseif ($skor >= 40) {
                                $labelPrioritas = 'Sedang';
                            } else {
                                $labelPrioritas = 'Rendah';
                            }
                        @endphp


                        <tr class="hover:bg-slate-50 transition">

                            {{-- No --}}
                            <td class="px-5 py-4 text-slate-700">
                                {{ $laporan->firstItem() + $index }}
                            </td>


                            {{-- Kode --}}
                            <td class="px-5 py-4">

                                <span class="font-medium text-slate-800 whitespace-nowrap">
                                    {{ $item->kode_laporan }}
                                </span>

                            </td>


                            {{-- Judul --}}
                            <td class="px-5 py-4">

                                <div class="max-w-xs">

                                    <p class="font-medium text-slate-800">
                                        {{ $item->judul }}
                                    </p>

                                    <p class="text-xs text-slate-500 mt-1">
                                        {{ $item->alamat_lengkap ?? 'Lokasi tidak tersedia' }}
                                    </p>

                                </div>

                            </td>


                            {{-- Kategori --}}
                            <td class="px-5 py-4 text-slate-700">

                                {{ $item->kategoriHambatan?->nama ?? '-' }}

                            </td>


                            {{-- Prioritas --}}
                            <td class="px-5 py-4">

                                @if($labelPrioritas === 'Tinggi')

                                    <span class="inline-flex items-center
                                                 px-2.5 py-1 rounded-full
                                                 text-xs font-semibold
                                                 bg-red-50 text-red-600">
                                        Tinggi
                                    </span>

                                @elseif($labelPrioritas === 'Sedang')

                                    <span class="inline-flex items-center
                                                 px-2.5 py-1 rounded-full
                                                 text-xs font-semibold
                                                 bg-amber-50 text-amber-600">
                                        Sedang
                                    </span>

                                @else

                                    <span class="inline-flex items-center
                                                 px-2.5 py-1 rounded-full
                                                 text-xs font-semibold
                                                 bg-emerald-50 text-emerald-600">
                                        Rendah
                                    </span>

                                @endif

                                <p class="text-xs text-slate-500 mt-1">
                                    Skor:
                                    {{ number_format((float) $skor, 2) }}
                                </p>

                            </td>


                            {{-- Status --}}
                            <td class="px-5 py-4">

                                @if($item->status === 'diverifikasi')

                                    <span class="inline-flex items-center
                                                 px-2.5 py-1 rounded-full
                                                 text-xs font-medium
                                                 bg-emerald-50 text-emerald-600">
                                        Terverifikasi
                                    </span>

                                @elseif($item->status === 'dalam_perbaikan')

                                    <span class="inline-flex items-center
                                                 px-2.5 py-1 rounded-full
                                                 text-xs font-medium
                                                 bg-blue-50 text-blue-600">
                                        Dalam Perbaikan
                                    </span>

                                @elseif($item->status === 'selesai')

                                    <span class="inline-flex items-center
                                                 px-2.5 py-1 rounded-full
                                                 text-xs font-medium
                                                 bg-slate-100 text-slate-600">
                                        Selesai
                                    </span>

                                @else

                                    <span class="text-slate-600">
                                        {{ $item->status_label }}
                                    </span>

                                @endif

                            </td>


                            {{-- Aksi --}}
                            <td class="px-5 py-4">

                                <div class="flex items-center justify-center">

                                    <a href="{{ route('dinas.laporan.index', $item->id) }}"
                                       class="inline-flex items-center gap-2
                                              px-3 py-2 rounded-lg
                                              border border-slate-200
                                              bg-white text-sm
                                              font-medium text-slate-700
                                              hover:bg-slate-50
                                              transition">

                                        <i class="fa-solid fa-eye text-slate-500"></i>

                                        <span>Detail</span>

                                    </a>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="7"
                                class="px-5 py-12 text-center text-slate-500">

                                <div class="flex flex-col items-center">

                                    <i class="fa-regular fa-folder-open
                                              text-4xl text-slate-300 mb-3">
                                    </i>

                                    <p class="font-medium">
                                        Belum ada rencana perbaikan.
                                    </p>

                                    <p class="text-xs mt-1">
                                        Data akan muncul setelah laporan
                                        memiliki verifikasi dan skor prioritas.
                                    </p>

                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- Pagination --}}
        @if($laporan->hasPages())

            <div class="px-5 py-4 border-t border-slate-200">

                {{ $laporan->links() }}

            </div>

        @endif

    </div>

</div>

@endsection