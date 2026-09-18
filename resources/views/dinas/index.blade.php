@extends('layouts.admin')

@section('title', 'Laporan SmartPath')

@section('content')

<div class="p-6">

    {{-- Header --}}
    <div class="mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">
                Laporan Aksesibilitas
            </h1>

            <p class="text-sm text-slate-500 mt-1">
                Daftar laporan aksesibilitas yang masuk ke SmartPath.
            </p>
        </div>
    </div>


    {{-- Card Tabel --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

        <div class="overflow-x-auto">

            <table class="w-full text-sm">

                {{-- Header Tabel --}}
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>

                        <th class="px-5 py-4 text-left">
                            No
                        </th>

                        <th class="px-5 py-4 text-left">
                            Kode
                        </th>

                        <th class="px-5 py-4 text-left">
                            Judul
                        </th>

                        <th class="px-5 py-4 text-left">
                            Kategori
                        </th>

                        <th class="px-5 py-4 text-left">
                            Status
                        </th>

                        <th class="px-5 py-4 text-left">
                            Prioritas
                        </th>

                        <th class="px-5 py-4 text-left">
                            Pelapor
                        </th>

                        <th class="px-5 py-4 text-left">
                            Tanggal
                        </th>

                        <th class="px-5 py-4 text-center">
                            Aksi
                        </th>

                    </tr>
                </thead>


                {{-- Isi Tabel --}}
                <tbody class="divide-y divide-slate-100">

                    @forelse($laporan as $index => $item)

                        <tr class="hover:bg-slate-50 transition">

                            {{-- No --}}
                            <td class="px-5 py-4 text-slate-700">
                                {{ $laporan->firstItem() + $index }}
                            </td>


                            {{-- Kode --}}
                            <td class="px-5 py-4 font-medium text-slate-800 whitespace-nowrap">
                                {{ $item->kode_laporan }}
                            </td>


                            {{-- Judul --}}
                            <td class="px-5 py-4 text-slate-700">
                                {{ $item->judul }}
                            </td>


                            {{-- Kategori --}}
                            <td class="px-5 py-4 text-slate-700">
                                {{ $item->kategoriHambatan?->nama ?? '-' }}
                            </td>


                            {{-- Status --}}
                            <td class="px-5 py-4 text-slate-700">
                                {{ $item->status_label }}
                            </td>


                            {{-- Prioritas --}}
                            <td class="px-5 py-4 text-slate-700">
                                {{ $item->skor_prioritas !== null
                                    ? number_format((float) $item->skor_prioritas, 2)
                                    : '-' }}
                            </td>


                            {{-- Jumlah Pelapor --}}
                            <td class="px-5 py-4 text-slate-700 text-center">
                                {{ $item->jumlah_pelapor }}
                            </td>


                            {{-- Tanggal --}}
                            <td class="px-5 py-4 whitespace-nowrap text-slate-700">
                                {{ $item->created_at?->format('d/m/Y') }}
                            </td>


                            {{-- Aksi --}}
                            <td class="px-5 py-4">

                                <div class="flex items-center justify-center gap-2">

                                    {{-- PDF --}}
                                    <a href="{{ route('dinas.laporan.pdf', $item) }}"
                                       class="inline-flex items-center gap-2 px-3 py-2
                                              rounded-lg border border-slate-200
                                              bg-white text-sm font-medium
                                              text-slate-700
                                              hover:bg-red-50 hover:border-red-200
                                              transition"
                                       title="Download PDF">

                                        <i class="fa-solid fa-file-pdf text-red-500"></i>
                                        <span>PDF</span>

                                    </a>


                                    {{-- Excel --}}
                                    <a href="{{ route('dinas.laporan.unduh') }}"
                                       class="inline-flex items-center gap-2 px-3 py-2
                                              rounded-lg border border-slate-200
                                              bg-white text-sm font-medium
                                              text-slate-700
                                              hover:bg-emerald-50 hover:border-emerald-200
                                              transition"
                                       title="Download Excel">

                                        <i class="fa-solid fa-file-excel text-green-600"></i>
                                        <span>Excel</span>

                                    </a>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="9"
                                class="px-5 py-12 text-center text-slate-500">

                                <div class="flex flex-col items-center justify-center">

                                    <i class="fa-regular fa-folder-open text-4xl
                                              text-slate-300 mb-3"></i>

                                    <p>
                                        Belum ada data laporan.
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