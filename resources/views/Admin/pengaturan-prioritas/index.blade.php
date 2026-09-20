@extends('layouts.admin')

@section('title', 'Pengaturan Prioritas')

@section('content')

<div class="max-w-7xl mx-auto space-y-6">

    {{-- HEADER --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">

        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-emerald-700">
                SmartPath Administration
            </p>

            <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">
                Pengaturan Prioritas
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Kelola bobot penilaian dan parameter radius yang digunakan SmartPath.
            </p>
        </div>

        <a
            href="{{ route('admin.pengaturan-prioritas.create') }}"
            class="inline-flex items-center justify-center rounded-xl bg-emerald-700 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2"
        >
            Tambah Konfigurasi
        </a>

    </div>


    {{-- GRID --}}
    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">

        {{-- TABLE --}}
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white xl:col-span-2">

            <div class="flex flex-col gap-3 border-b border-slate-100 px-6 py-4 sm:flex-row sm:items-center sm:justify-between">

                <div>
                    <h2 class="font-semibold text-slate-900">
                        Riwayat Konfigurasi
                    </h2>

                    <p class="mt-1 text-xs text-slate-500">
                        Hanya satu konfigurasi yang boleh aktif pada satu waktu.
                    </p>
                </div>

                <form
                    method="POST"
                    action="{{ route('admin.pengaturan-prioritas.recalculate') }}"
                    data-confirm="Hitung ulang skor seluruh laporan terverifikasi sekarang?"
                >
                    @csrf

                    <button
                        type="submit"
                        class="inline-flex items-center justify-center rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                    >
                        Hitung Ulang
                    </button>
                </form>

            </div>


            <div class="overflow-x-auto">

                <table class="min-w-full text-sm">

                    <thead class="border-b border-slate-200 bg-slate-50">

                        <tr class="text-left text-xs uppercase tracking-wide text-slate-500">

                            <th class="px-6 py-3">
                                Konfigurasi
                            </th>

                            <th class="px-6 py-3">
                                Bobot
                            </th>

                            <th class="px-6 py-3">
                                Radius
                            </th>

                            <th class="px-6 py-3">
                                Status
                            </th>

                            <th class="px-6 py-3 text-right">
                                Aksi
                            </th>

                        </tr>

                    </thead>


                    <tbody class="divide-y divide-slate-100">

                        @forelse($pengaturanPrioritas as $item)

                            <tr class="transition hover:bg-slate-50/70">

                                <td class="px-6 py-4 align-top">

                                    <div class="font-semibold text-slate-900">
                                        {{ $item->label }}
                                    </div>

                                    <div class="mt-1 text-xs text-slate-500">
                                        {{ $item->catatan ?: 'Tanpa catatan.' }}
                                    </div>

                                    <div class="mt-2 text-[11px] text-slate-400">

                                        Dibuat
                                        {{ $item->created_at?->format('d M Y H:i') }}

                                        oleh

                                        {{ $item->dibuatOleh?->nama_lengkap
                                            ?? $item->dibuatOleh?->nama
                                            ?? $item->dibuatOleh?->name
                                            ?? '-' }}

                                    </div>

                                </td>


                                <td class="whitespace-nowrap px-6 py-4">

                                    <div class="text-xs text-slate-600">
                                        Keparahan:
                                        <strong>
                                            {{ number_format((float) $item->bobot_keparahan, 2) }}
                                        </strong>
                                    </div>

                                    <div class="text-xs text-slate-600">
                                        Pelapor:
                                        <strong>
                                            {{ number_format((float) $item->bobot_pelapor, 2) }}
                                        </strong>
                                    </div>

                                    <div class="text-xs text-slate-600">
                                        Fasilitas:
                                        <strong>
                                            {{ number_format((float) $item->bobot_fasilitas, 2) }}
                                        </strong>
                                    </div>

                                </td>


                                <td class="whitespace-nowrap px-6 py-4 text-xs text-slate-600">

                                    Deduplikasi:
                                    <strong>
                                        {{ $item->radius_deduplikasi_m }} m
                                    </strong>

                                    <br>

                                    Fasilitas:
                                    <strong>
                                        {{ $item->radius_fasilitas_m }} m
                                    </strong>

                                </td>


                                <td class="px-6 py-4">

                                    @if($item->adalah_aktif)

                                        <span class="inline-flex rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">
                                            Aktif
                                        </span>

                                    @else

                                        <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-500">
                                            Nonaktif
                                        </span>

                                    @endif

                                </td>


                                <td class="whitespace-nowrap px-6 py-4 text-right">

                                    <a
                                        href="{{ route('admin.pengaturan-prioritas.edit', $item) }}"
                                        class="text-xs font-semibold text-emerald-700 transition hover:text-emerald-800"
                                    >
                                        Edit
                                    </a>


                                    @unless($item->adalah_aktif)

                                        <form
                                            class="inline"
                                            method="POST"
                                            action="{{ route('admin.pengaturan-prioritas.activate', $item) }}"
                                            data-confirm="Aktifkan konfigurasi ini?"
                                        >
                                            @csrf

                                            <button
                                                type="submit"
                                                class="ml-3 text-xs font-semibold text-teal-700 hover:text-teal-800"
                                            >
                                                Aktifkan
                                            </button>
                                        </form>


                                        <form
                                            class="inline"
                                            method="POST"
                                            action="{{ route('admin.pengaturan-prioritas.destroy', $item) }}"
                                            data-confirm="Hapus konfigurasi ini?"
                                        >
                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="ml-3 text-xs font-semibold text-red-600 hover:text-red-700"
                                            >
                                                Hapus
                                            </button>

                                        </form>

                                    @endunless

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td
                                    colspan="5"
                                    class="px-6 py-12 text-center text-slate-400"
                                >
                                    Belum ada konfigurasi prioritas.
                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>


            @if($pengaturanPrioritas->hasPages())

                <div class="border-t border-slate-100 px-6 py-4">
                    {{ $pengaturanPrioritas->links('pagination::tailwind') }}
                </div>

            @endif

        </section>


        {{-- ACTIVE CONFIG --}}
        <aside class="h-fit rounded-2xl border border-slate-200 bg-white p-6">

            <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700">
                Konfigurasi Aktif
            </p>

            @if($aktif)

                <h2 class="mt-2 text-lg font-bold text-slate-900">
                    {{ $aktif->label }}
                </h2>


                <div class="mt-5 grid grid-cols-3 gap-2">

                    <div class="rounded-xl bg-emerald-50 p-3">
                        <div class="text-[11px] text-emerald-700">
                            Keparahan
                        </div>

                        <div class="text-xl font-bold text-slate-900">
                            {{ number_format((float) $aktif->bobot_keparahan * 100, 0) }}%
                        </div>
                    </div>


                    <div class="rounded-xl bg-teal-50 p-3">
                        <div class="text-[11px] text-teal-700">
                            Pelapor
                        </div>

                        <div class="text-xl font-bold text-slate-900">
                            {{ number_format((float) $aktif->bobot_pelapor * 100, 0) }}%
                        </div>
                    </div>


                    <div class="rounded-xl bg-slate-50 p-3">
                        <div class="text-[11px] text-slate-600">
                            Fasilitas
                        </div>

                        <div class="text-xl font-bold text-slate-900">
                            {{ number_format((float) $aktif->bobot_fasilitas * 100, 0) }}%
                        </div>
                    </div>

                </div>


                <div class="mt-5 space-y-2 text-sm text-slate-600">

                    <div class="flex justify-between gap-4">
                        <span>Radius deduplikasi</span>
                        <strong>{{ $aktif->radius_deduplikasi_m }} m</strong>
                    </div>

                    <div class="flex justify-between gap-4">
                        <span>Radius fasilitas</span>
                        <strong>{{ $aktif->radius_fasilitas_m }} m</strong>
                    </div>

                </div>

            @else

                <div class="mt-3 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-700">
                    Belum ada konfigurasi prioritas aktif.
                </div>

            @endif

        </aside>

    </div>

</div>

@endsection