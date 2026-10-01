@extends('layouts.admin')

@section('title', 'Rencana Perbaikan')

@section('content')

@php

    $formatRupiah =
        function ($value) {

            return 'Rp ' .
                number_format(
                    (float) $value,
                    0,
                    ',',
                    '.'
                );

        };

@endphp


<div class="space-y-6">


    {{-- ==========================================================
         HEADER
    =========================================================== --}}
    <div
        class="flex flex-col gap-4
               lg:flex-row
               lg:items-end
               lg:justify-between"
    >

        <div>

            <div
                class="mb-2 flex items-center gap-2
                       text-xs font-medium
                       text-slate-500"
            >

                <span>
                    Pemerintah
                </span>

                <span aria-hidden="true">
                    /
                </span>

                <span class="text-emerald-700">
                    Rencana Perbaikan
                </span>

            </div>


            <h1
                class="text-2xl font-bold
                       tracking-tight
                       text-slate-900"
            >
                Rencana Perbaikan
            </h1>


            <p
                class="mt-1 max-w-3xl
                       text-sm leading-6
                       text-slate-500"
            >
                Kelola rencana penanganan berdasarkan laporan
                terverifikasi dan skor prioritas SmartPath.
            </p>

        </div>


        <a
            href="{{ route('dinas.peta') }}"
            class="inline-flex w-fit
                   items-center gap-2
                   rounded-lg
                   border border-slate-200
                   bg-white
                   px-4 py-2.5
                   text-xs font-semibold
                   text-slate-700
                   shadow-sm
                   transition
                   hover:bg-slate-50
                   focus:outline-none
                   focus:ring-2
                   focus:ring-emerald-500/30"
        >

            <i
                class="fa-solid fa-map-location-dot
                       text-emerald-700"
                aria-hidden="true"
            ></i>

            Buka Peta Infrastruktur

        </a>

    </div>


    {{-- ==========================================================
         STATISTIK
    =========================================================== --}}
    <section
        class="grid grid-cols-1
               gap-4
               sm:grid-cols-2
               xl:grid-cols-5"
        aria-label="Ringkasan rencana perbaikan"
    >

        @foreach([

            [
                'label' => 'Perlu Ditindaklanjuti',
                'value' => $totalLaporan,
                'note' => 'laporan terverifikasi',
                'icon' => 'fa-list-check',
                'box' => 'bg-slate-100',
                'iconColor' => 'text-slate-700'
            ],

            [
                'label' => 'Belum Ada Rencana',
                'value' => $belumAdaRencana,
                'note' => 'menunggu penjadwalan',
                'icon' => 'fa-clipboard-question',
                'box' => 'bg-amber-50',
                'iconColor' => 'text-amber-600'
            ],

            [
                'label' => 'Dalam Perbaikan',
                'value' => $dalamPerbaikan,
                'note' => 'sedang ditangani',
                'icon' => 'fa-screwdriver-wrench',
                'box' => 'bg-blue-50',
                'iconColor' => 'text-blue-600'
            ],

            [
                'label' => 'Terlambat',
                'value' => $terlambat,
                'note' => 'melewati target',
                'icon' => 'fa-clock',
                'box' => 'bg-red-50',
                'iconColor' => 'text-red-600'
            ],

            [
                'label' => 'Selesai',
                'value' => $selesai,
                'note' => 'penanganan selesai',
                'icon' => 'fa-circle-check',
                'box' => 'bg-emerald-50',
                'iconColor' => 'text-emerald-600'
            ]

        ] as $card)

            <div
                class="rounded-xl
                       border border-slate-200
                       bg-white
                       p-4
                       shadow-sm"
            >

                <div
                    class="flex items-start
                           justify-between gap-3"
                >

                    <div>

                        <p
                            class="text-xs
                                   font-medium
                                   text-slate-500"
                        >
                            {{ $card['label'] }}
                        </p>

                        <p
                            class="mt-2
                                   text-2xl font-bold
                                   text-slate-900"
                        >
                            {{ $card['value'] }}
                        </p>

                        <p
                            class="mt-1
                                   text-[11px]
                                   text-slate-500"
                        >
                            {{ $card['note'] }}
                        </p>

                    </div>


                    <span
                        class="flex h-9 w-9
                               shrink-0
                               items-center
                               justify-center
                               rounded-lg
                               {{ $card['box'] }}
                               {{ $card['iconColor'] }}"
                    >

                        <i
                            class="fa-solid
                                   {{ $card['icon'] }}"
                            aria-hidden="true"
                        ></i>

                    </span>

                </div>

            </div>

        @endforeach

    </section>


    {{-- ==========================================================
         TABLE
    =========================================================== --}}
    <div
        class="rounded-xl
               border border-slate-200
               bg-white
               shadow-sm"
    >

        <div
            class="border-b
                   border-slate-100
                   px-5 py-5"
        >

            <div
                class="flex flex-col gap-4
                       xl:flex-row
                       xl:items-end
                       xl:justify-between"
            >

                <div>

                    <h2
                        class="text-sm
                               font-bold
                               text-slate-900"
                    >
                        Antrian Perbaikan Infrastruktur
                    </h2>

                    <p
                        class="mt-1
                               text-xs
                               leading-5
                               text-slate-500"
                    >
                        Urutan utama mengikuti skor prioritas
                        WSM yang telah dihitung sistem.
                    </p>

                </div>


                {{-- FILTER --}}
                <form
                    method="GET"
                    action="{{ route('dinas.rencana-perbaikan.index') }}"
                    class="grid grid-cols-1
                           gap-2
                           sm:grid-cols-[minmax(220px,1fr)_190px_auto]"
                >

                    <label
                        class="sr-only"
                        for="q"
                    >
                        Cari laporan
                    </label>

                    <div
                        class="relative"
                    >

                        <i
                            class="fa-solid
                                   fa-magnifying-glass
                                   pointer-events-none
                                   absolute
                                   left-3 top-1/2
                                   -translate-y-1/2
                                   text-xs
                                   text-slate-400"
                            aria-hidden="true"
                        ></i>

                        <input
                            id="q"
                            name="q"
                            value="{{ $search }}"
                            type="search"
                            maxlength="100"
                            placeholder="Cari kode, judul, kategori…"
                            class="w-full
                                   rounded-lg
                                   border border-slate-200
                                   bg-white
                                   py-2.5 pl-9 pr-3
                                   text-xs
                                   text-slate-700
                                   outline-none
                                   transition
                                   focus:border-emerald-500
                                   focus:ring-2
                                   focus:ring-emerald-500/20"
                        >

                    </div>


                    <label
                        class="sr-only"
                        for="status"
                    >
                        Filter status
                    </label>

                    <select
                        id="status"
                        name="status"
                        class="rounded-lg
                               border border-slate-200
                               bg-white
                               px-3 py-2.5
                               text-xs
                               text-slate-700
                               outline-none
                               focus:border-emerald-500
                               focus:ring-2
                               focus:ring-emerald-500/20"
                    >

                        <option value="">
                            Semua status
                        </option>

                        <option
                            value="belum_ada_rencana"
                            @selected(
                                $statusFilter ===
                                'belum_ada_rencana'
                            )
                        >
                            Belum Ada Rencana
                        </option>

                        <option
                            value="belum_dimulai"
                            @selected(
                                $statusFilter ===
                                'belum_dimulai'
                            )
                        >
                            Belum Dimulai
                        </option>

                        <option
                            value="dalam_perbaikan"
                            @selected(
                                $statusFilter ===
                                'dalam_perbaikan'
                            )
                        >
                            Dalam Perbaikan
                        </option>

                        <option
                            value="terlambat"
                            @selected(
                                $statusFilter ===
                                'terlambat'
                            )
                        >
                            Terlambat
                        </option>

                        <option
                            value="selesai"
                            @selected(
                                $statusFilter ===
                                'selesai'
                            )
                        >
                            Selesai
                        </option>

                    </select>


                    <div class="flex gap-2">

                        <button
                            type="submit"
                            class="inline-flex
                                   items-center
                                   justify-center
                                   gap-2
                                   rounded-lg
                                   bg-emerald-700
                                   px-4 py-2.5
                                   text-xs
                                   font-semibold
                                   text-white
                                   transition
                                   hover:bg-emerald-800
                                   focus:outline-none
                                   focus:ring-2
                                   focus:ring-emerald-500/30"
                        >

                            <i
                                class="fa-solid fa-filter"
                                aria-hidden="true"
                            ></i>

                            Filter

                        </button>


                        @if(
                            $search !== '' ||
                            $statusFilter
                        )

                            <a
                                href="{{ route('dinas.rencana-perbaikan.index') }}"
                                class="inline-flex
                                       items-center
                                       justify-center
                                       rounded-lg
                                       border border-slate-200
                                       px-3 py-2.5
                                       text-xs
                                       font-semibold
                                       text-slate-600
                                       hover:bg-slate-50"
                            >
                                Reset
                            </a>

                        @endif

                    </div>

                </form>

            </div>

        </div>


        {{-- TABLE --}}
        <div
            class="overflow-x-auto"
        >

            <table
                class="min-w-[1120px]
                       w-full
                       text-left text-xs"
                aria-label="Daftar rencana perbaikan"
            >

                <thead
                    class="border-b
                           border-slate-100
                           bg-slate-50
                           text-[10px]
                           uppercase
                           tracking-wider
                           text-slate-500"
                >

                    <tr>

                        <th
                            class="px-5 py-3
                                   font-semibold"
                        >
                            Laporan
                        </th>

                        <th
                            class="px-5 py-3
                                   font-semibold"
                        >
                            Lokasi
                        </th>

                        <th
                            class="px-5 py-3
                                   text-center
                                   font-semibold"
                        >
                            Prioritas
                        </th>

                        <th
                            class="px-5 py-3
                                   font-semibold"
                        >
                            Rencana & Jadwal
                        </th>

                        <th
                            class="px-5 py-3
                                   font-semibold"
                        >
                            Anggaran
                        </th>

                        <th
                            class="px-5 py-3
                                   font-semibold"
                        >
                            Status
                        </th>

                        <th
                            class="px-5 py-3
                                   text-center
                                   font-semibold"
                        >
                            Aksi
                        </th>

                    </tr>

                </thead>


                <tbody
                    class="divide-y
                           divide-slate-100"
                >

                @forelse($laporan as $item)

                    @php

                        $rencana =
                            $item->rencanaPerbaikan;

                        $score =
                            (float)
                            $item->skor_prioritas;


                        if ($score >= 70) {

                            $priorityLabel =
                                'Tinggi';

                            $priorityClass =
                                'bg-red-50 text-red-700 border-red-100';

                        } elseif ($score >= 40) {

                            $priorityLabel =
                                'Sedang';

                            $priorityClass =
                                'bg-amber-50 text-amber-700 border-amber-100';

                        } else {

                            $priorityLabel =
                                'Rendah';

                            $priorityClass =
                                'bg-emerald-50 text-emerald-700 border-emerald-100';

                        }


                        if (!$rencana) {

                            $statusLabel =
                                'Belum Ada Rencana';

                            $statusClass =
                                'bg-slate-100 text-slate-600 border-slate-200';

                            $statusIcon =
                                'fa-clipboard-question';

                        } elseif (
                            $rencana->terlambat
                        ) {

                            $statusLabel =
                                'Terlambat';

                            $statusClass =
                                'bg-red-50 text-red-700 border-red-100';

                            $statusIcon =
                                'fa-clock';

                        } else {

                            $statusLabel =
                                $rencana->statusLabel;


                            $statusClass =
                                match (
                                    $rencana->status_otomatis
                                ) {

                                    'dalam_perbaikan' =>
                                        'bg-blue-50 text-blue-700 border-blue-100',

                                    'selesai' =>
                                        'bg-emerald-50 text-emerald-700 border-emerald-100',

                                    default =>
                                        'bg-amber-50 text-amber-700 border-amber-100',

                                };


                            $statusIcon =
                                match (
                                    $rencana->status_otomatis
                                ) {

                                    'dalam_perbaikan' =>
                                        'fa-screwdriver-wrench',

                                    'selesai' =>
                                        'fa-circle-check',

                                    default =>
                                        'fa-clock',

                                };

                        }

                    @endphp


                    <tr
                        class="align-top
                               transition
                               hover:bg-slate-50/80"
                    >

                        {{-- LAPORAN --}}
                        <td class="px-5 py-4">

                            <div
                                class="flex
                                       min-w-[250px]
                                       items-start
                                       gap-3"
                            >

                                <span
                                    class="mt-0.5
                                           flex h-9 w-9
                                           shrink-0
                                           items-center
                                           justify-center
                                           rounded-lg
                                           bg-slate-100
                                           text-slate-600"
                                >

                                    <i
                                        class="fa-solid
                                               fa-location-dot"
                                        aria-hidden="true"
                                    ></i>

                                </span>


                                <div
                                    class="min-w-0"
                                >

                                    <p
                                        class="font-mono
                                               text-[10px]
                                               font-bold
                                               text-slate-500"
                                    >
                                        {{ $item->kode_laporan }}
                                    </p>


                                    <p
                                        class="mt-1
                                               font-semibold
                                               leading-5
                                               text-slate-800"
                                    >
                                        {{ $item->judul }}
                                    </p>


                                    <p
                                        class="mt-1
                                               text-[11px]
                                               text-slate-500"
                                    >
                                        {{
                                            $item->kategoriHambatan?->nama
                                            ??
                                            'Kategori tidak tersedia'
                                        }}
                                    </p>

                                </div>

                            </div>

                        </td>


                        {{-- LOKASI --}}
                        <td class="px-5 py-4">

                            <p
                                class="max-w-[210px]
                                       leading-5
                                       text-slate-700"
                            >
                                {{
                                    $item->alamat_lengkap
                                    ?:
                                    'Alamat tidak tersedia'
                                }}
                            </p>


                            <p
                                class="mt-1
                                       text-[11px]
                                       text-slate-400"
                            >
                                {{
                                    $item->wilayah?->nama
                                    ??
                                    'Wilayah tidak tersedia'
                                }}
                            </p>

                        </td>


                        {{-- PRIORITAS --}}
                        <td
                            class="px-5 py-4
                                   text-center"
                        >

                            <span
                                class="inline-flex
                                       items-center
                                       rounded-full
                                       border
                                       px-2.5 py-1
                                       text-[10px]
                                       font-bold
                                       {{ $priorityClass }}"
                            >
                                {{ $priorityLabel }}
                            </span>


                            <p
                                class="mt-1
                                       font-semibold
                                       text-slate-700"
                            >
                                {{ number_format(
                                    $score,
                                    2
                                ) }}
                            </p>

                        </td>


                        {{-- RENCANA --}}
                        <td class="px-5 py-4">

                            @if($rencana)

                                <p
                                    class="max-w-[260px]
                                           font-medium
                                           leading-5
                                           text-slate-800"
                                >
                                    {{
                                        \Illuminate\Support\Str::limit(
                                            $rencana->tindakan,
                                            80
                                        )
                                    }}
                                </p>


                                <div
                                    class="mt-2
                                           space-y-1
                                           text-[11px]
                                           text-slate-500"
                                >

                                    <p>
                                        Mulai:

                                        <span
                                            class="font-medium
                                                   text-slate-700"
                                        >
                                            {{
                                                $rencana
                                                    ->tanggal_mulai
                                                    ?->format(
                                                        'd M Y'
                                                    )
                                                ??
                                                '-'
                                            }}
                                        </span>
                                    </p>


                                    <p>
                                        Target:

                                        <span
                                            class="font-medium
                                            {{
                                                $rencana->terlambat
                                                    ? 'text-red-700'
                                                    : 'text-slate-700'
                                            }}"
                                        >
                                            {{
                                                $rencana
                                                    ->target_selesai
                                                    ?->format(
                                                        'd M Y'
                                                    )
                                                ??
                                                '-'
                                            }}
                                        </span>
                                    </p>


                                    @if(
                                        $rencana->tanggal_selesai
                                    )

                                        <p>

                                            Selesai:

                                            <span
                                                class="font-medium
                                                       text-emerald-700"
                                            >
                                                {{
                                                    $rencana
                                                        ->tanggal_selesai
                                                        ->format(
                                                            'd M Y'
                                                        )
                                                }}
                                            </span>

                                        </p>

                                    @endif

                                </div>

                            @else

                                <p
                                    class="text-slate-400"
                                >
                                    Belum ada rencana
                                    penanganan.
                                </p>

                                <p
                                    class="mt-1
                                           text-[11px]
                                           text-slate-400"
                                >
                                    Laporan tersedia untuk
                                    dibuatkan jadwal.
                                </p>

                            @endif

                        </td>


                        {{-- ANGGARAN --}}
                        <td class="px-5 py-4">

                            @if($rencana)

                                <p
                                    class="font-semibold
                                           text-slate-800"
                                >
                                    {{
                                        $formatRupiah(
                                            $rencana
                                                ->estimasi_anggaran
                                        )
                                    }}
                                </p>


                                <p
                                    class="mt-1
                                           text-[11px]
                                           text-slate-500"
                                >
                                    Realisasi:

                                    {{
                                        $formatRupiah(
                                            $rencana
                                                ->realisasi_anggaran
                                        )
                                    }}
                                </p>


                                @php

                                    $persentaseAnggaran =
                                        $rencana->estimasi_anggaran > 0

                                            ? min(
                                                100,
                                                round(
                                                    (
                                                        (float)
                                                        $rencana
                                                            ->realisasi_anggaran

                                                        /

                                                        (float)
                                                        $rencana
                                                            ->estimasi_anggaran
                                                    )
                                                    * 100
                                                )
                                            )

                                            : 0;

                                @endphp


                                <div
                                    class="mt-2
                                           h-1.5
                                           w-28
                                           overflow-hidden
                                           rounded-full
                                           bg-slate-100"
                                >

                                    <span
                                        class="block
                                               h-full
                                               rounded-full
                                               bg-emerald-600"
                                        style="
                                            width:
                                            {{ $persentaseAnggaran }}%
                                        "
                                    ></span>

                                </div>

                            @else

                                <span
                                    class="text-slate-400"
                                >
                                    Belum ditetapkan
                                </span>

                            @endif

                        </td>


                        {{-- STATUS --}}
                        <td class="px-5 py-4">

                            <span
                                class="inline-flex
                                       items-center
                                       gap-1.5
                                       rounded-full
                                       border
                                       px-2.5 py-1
                                       text-[10px]
                                       font-semibold
                                       {{ $statusClass }}"
                            >

                                <i
                                    class="fa-solid
                                           {{ $statusIcon }}"
                                    aria-hidden="true"
                                ></i>

                                {{ $statusLabel }}

                            </span>

                        </td>


                        {{-- AKSI --}}
                        <td
                            class="px-5 py-4
                                   text-center"
                        >

                            @if($rencana)

                                <a
                                    href="{{
                                        route(
                                            'dinas.rencana-perbaikan.edit',
                                            $rencana->id
                                        )
                                    }}"
                                    class="inline-flex
                                           items-center
                                           gap-2
                                           rounded-lg
                                           bg-emerald-700
                                           px-3 py-2
                                           text-[11px]
                                           font-semibold
                                           text-white
                                           transition
                                           hover:bg-emerald-800
                                           focus:outline-none
                                           focus:ring-2
                                           focus:ring-emerald-500/30"
                                >

                                    <i
                                        class="fa-solid fa-pen"
                                        aria-hidden="true"
                                    ></i>

                                    Edit

                                </a>

                            @else

                                <a
                                    href="{{
                                        route(
                                            'dinas.rencana-perbaikan.create',
                                            $item->id
                                        )
                                    }}"
                                    class="inline-flex
                                           items-center
                                           gap-2
                                           rounded-lg
                                           bg-emerald-600
                                           px-3 py-2
                                           text-[11px]
                                           font-semibold
                                           text-white
                                           transition
                                           hover:bg-emerald-700
                                           focus:outline-none
                                           focus:ring-2
                                           focus:ring-emerald-500/30"
                                >

                                    <i
                                        class="fa-solid fa-plus"
                                        aria-hidden="true"
                                    ></i>

                                    Buat Rencana

                                </a>

                            @endif

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="7"
                            class="px-5 py-14
                                   text-center"
                        >

                            <div
                                class="mx-auto
                                       flex max-w-sm
                                       flex-col
                                       items-center"
                            >

                                <span
                                    class="flex h-12 w-12
                                           items-center
                                           justify-center
                                           rounded-xl
                                           bg-slate-100
                                           text-slate-400"
                                >

                                    <i
                                        class="fa-regular
                                               fa-folder-open"
                                        aria-hidden="true"
                                    ></i>

                                </span>


                                <p
                                    class="mt-3
                                           text-sm
                                           font-semibold
                                           text-slate-700"
                                >
                                    Tidak ada data yang sesuai
                                </p>


                                <p
                                    class="mt-1
                                           text-xs
                                           leading-5
                                           text-slate-500"
                                >
                                    Ubah kata kunci atau filter
                                    status untuk melihat laporan
                                    lainnya.
                                </p>

                            </div>

                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>


        {{-- PAGINATION --}}
        @if($laporan->hasPages())

            <div
                class="border-t
                       border-slate-100
                       px-5 py-4"
            >

                {{ $laporan->links() }}

            </div>

        @endif

    </div>

</div>

@endsection