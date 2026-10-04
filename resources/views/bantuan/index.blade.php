@extends('layouts.admin')

@section('title', 'Bantuan SmartPath')
@section('page_title', 'Bantuan')

@section('content')

<div class="space-y-6">

    {{-- HEADER --}}
    <section>

        <div class="flex items-center gap-2 text-xs text-slate-500">
            <span>Administrator</span>
            <span aria-hidden="true">/</span>
            <span class="font-medium text-emerald-700">
                Bantuan
            </span>
        </div>

        <div class="mt-2 flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">

            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-950">
                    Pusat Bantuan SmartPath
                </h1>

                <p class="mt-1 max-w-2xl text-sm leading-6 text-slate-500">
                    Panduan singkat untuk membantu administrator mengelola
                    laporan, verifikasi, prioritas, peta, dan aktivitas sistem.
                </p>
            </div>

            <div
                class="inline-flex w-fit items-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs font-semibold text-emerald-700"
            >
                <i class="fa-solid fa-circle-check" aria-hidden="true"></i>
                SmartPath Operational Guide
            </div>

        </div>

    </section>


    {{-- RINGKASAN --}}
    <section
        class="grid grid-cols-1 gap-4 sm:grid-cols-3"
        aria-label="Ringkasan bantuan"
    >

        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">

            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-50 text-emerald-700">
                <i class="fa-solid fa-shield-halved" aria-hidden="true"></i>
            </div>

            <h2 class="mt-4 text-sm font-semibold text-slate-900">
                Human-in-the-loop
            </h2>

            <p class="mt-1 text-xs leading-5 text-slate-500">
                Laporan tidak langsung menjadi data publik.
                Administrator melakukan pemeriksaan sebelum publikasi.
            </p>

        </div>


        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">

            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-teal-50 text-teal-700">
                <i class="fa-solid fa-chart-column" aria-hidden="true"></i>
            </div>

            <h2 class="mt-4 text-sm font-semibold text-slate-900">
                WSM Prioritas
            </h2>

            <p class="mt-1 text-xs leading-5 text-slate-500">
                Skor dihitung setelah verifikasi berdasarkan keparahan,
                pelapor unik, dan kedekatan fasilitas vital.
            </p>

        </div>


        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">

            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-slate-100 text-slate-700">
                <i class="fa-solid fa-map-location-dot" aria-hidden="true"></i>
            </div>

            <h2 class="mt-4 text-sm font-semibold text-slate-900">
                Peta Aksesibilitas
            </h2>

            <p class="mt-1 text-xs leading-5 text-slate-500">
                Titik terverifikasi ditampilkan menggunakan Leaflet.js
                dan OpenStreetMap.
            </p>

        </div>

    </section>


    {{-- PANDUAN --}}
    <section
        class="grid grid-cols-1 gap-6 xl:grid-cols-2"
    >

        <article class="rounded-xl border border-slate-200 bg-white shadow-sm">

            <div class="border-b border-slate-100 px-5 py-4">
                <h2 class="text-sm font-bold text-slate-900">
                    Alur Pengelolaan Laporan
                </h2>

                <p class="mt-1 text-xs text-slate-500">
                    Urutan proses utama SmartPath.
                </p>
            </div>

            <div class="divide-y divide-slate-100">

                @foreach([
                    [
                        'no' => '01',
                        'title' => 'Laporan masuk',
                        'text' => 'Laporan warga tersimpan sebagai menunggu verifikasi dan belum dipublikasikan.'
                    ],
                    [
                        'no' => '02',
                        'title' => 'Pemeriksaan bukti',
                        'text' => 'Periksa kategori, deskripsi, foto, koordinat, dan indikasi duplikasi.'
                    ],
                    [
                        'no' => '03',
                        'title' => 'Verifikasi',
                        'text' => 'Laporan yang valid disetujui. Laporan tidak relevan dapat ditolak dengan alasan.'
                    ],
                    [
                        'no' => '04',
                        'title' => 'Perhitungan prioritas',
                        'text' => 'Laporan terverifikasi diproses menggunakan Weighted Sum Model.'
                    ],
                    [
                        'no' => '05',
                        'title' => 'Peta dan tindak lanjut',
                        'text' => 'Data terverifikasi tampil pada peta dan dapat menjadi dasar rencana perbaikan.'
                    ]
                ] as $step)

                    <div class="flex gap-4 px-5 py-4">

                        <span
                            class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-[10px] font-bold text-slate-600"
                        >
                            {{ $step['no'] }}
                        </span>

                        <div>
                            <h3 class="text-sm font-semibold text-slate-900">
                                {{ $step['title'] }}
                            </h3>

                            <p class="mt-1 text-xs leading-5 text-slate-500">
                                {{ $step['text'] }}
                            </p>
                        </div>

                    </div>

                @endforeach

            </div>

        </article>


        <article class="rounded-xl border border-slate-200 bg-white shadow-sm">

            <div class="border-b border-slate-100 px-5 py-4">
                <h2 class="text-sm font-bold text-slate-900">
                    Indikator Prioritas
                </h2>

                <p class="mt-1 text-xs text-slate-500">
                    Interpretasi skor pada peta SmartPath.
                </p>
            </div>

            <div class="space-y-3 p-5">

                <div class="rounded-xl border border-red-100 bg-red-50 p-4">
                    <div class="flex items-center gap-3">
                        <span class="h-3 w-3 rounded-full bg-red-600"></span>
                        <div>
                            <p class="text-sm font-semibold text-red-800">
                                Prioritas Tinggi
                            </p>
                            <p class="text-xs text-red-700">
                                Skor ≥ 70
                            </p>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl border border-amber-100 bg-amber-50 p-4">
                    <div class="flex items-center gap-3">
                        <span class="h-3 w-3 rounded-full bg-amber-500"></span>
                        <div>
                            <p class="text-sm font-semibold text-amber-800">
                                Prioritas Sedang
                            </p>
                            <p class="text-xs text-amber-700">
                                Skor 40–69
                            </p>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl border border-emerald-100 bg-emerald-50 p-4">
                    <div class="flex items-center gap-3">
                        <span class="h-3 w-3 rounded-full bg-emerald-600"></span>
                        <div>
                            <p class="text-sm font-semibold text-emerald-800">
                                Prioritas Rendah
                            </p>
                            <p class="text-xs text-emerald-700">
                                Skor &lt; 40
                            </p>
                        </div>
                    </div>
                </div>

            </div>

        </article>

    </section>


    {{-- FAQ --}}
    <section class="rounded-xl border border-slate-200 bg-white shadow-sm">

        <div class="border-b border-slate-100 px-5 py-4">

            <h2 class="text-sm font-bold text-slate-900">
                Pertanyaan Umum
            </h2>

            <p class="mt-1 text-xs text-slate-500">
                Referensi operasional administrator.
            </p>

        </div>


        <div class="divide-y divide-slate-100">

            <details class="group px-5 py-4">
                <summary class="cursor-pointer list-none text-sm font-semibold text-slate-800">
                    Mengapa laporan belum muncul pada peta publik?
                </summary>

                <p class="mt-2 max-w-3xl text-xs leading-5 text-slate-500">
                    Karena laporan harus melewati verifikasi terlebih dahulu.
                    Laporan yang masih menunggu verifikasi tidak dipublikasikan
                    pada peta publik.
                </p>
            </details>


            <details class="group px-5 py-4">
                <summary class="cursor-pointer list-none text-sm font-semibold text-slate-800">
                    Mengapa dua laporan mempunyai satu titik pada peta?
                </summary>

                <p class="mt-2 max-w-3xl text-xs leading-5 text-slate-500">
                    SmartPath melakukan deduplikasi berdasarkan kategori dan
                    radius 50 meter. Laporan sejenis yang masih aktif dapat
                    terhubung sebagai laporan anak terhadap laporan induk.
                </p>
            </details>


            <details class="group px-5 py-4">
                <summary class="cursor-pointer list-none text-sm font-semibold text-slate-800">
                    Kapan skor prioritas dihitung?
                </summary>

                <p class="mt-2 max-w-3xl text-xs leading-5 text-slate-500">
                    Skor prioritas dihitung setelah laporan lolos verifikasi.
                    Komponen MVP menggunakan bobot keparahan 0,40,
                    pelapor unik 0,35, dan kedekatan fasilitas publik vital 0,25.
                </p>
            </details>


            <details class="group px-5 py-4">
                <summary class="cursor-pointer list-none text-sm font-semibold text-slate-800">
                    Apa standar waktu verifikasi?
                </summary>

                <p class="mt-2 max-w-3xl text-xs leading-5 text-slate-500">
                    Proposal menetapkan target verifikasi maksimal 2×24 jam
                    sejak laporan masuk.
                </p>
            </details>


            <details class="group px-5 py-4">
                <summary class="cursor-pointer list-none text-sm font-semibold text-slate-800">
                    Apakah peta menggunakan Google Maps?
                </summary>

                <p class="mt-2 max-w-3xl text-xs leading-5 text-slate-500">
                    Tidak. SmartPath menggunakan Leaflet.js dengan OpenStreetMap
                    sebagai basemap sesuai rancangan teknologi proposal.
                </p>
            </details>

        </div>

    </section>

</div>

@endsection