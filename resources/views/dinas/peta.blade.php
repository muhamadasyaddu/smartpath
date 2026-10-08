@extends('layouts.admin')

@section('title', 'Peta Infrastruktur')

@section('content')

<div
    class="space-y-6"
    id="dinas-map-page"
>

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
                       text-xs font-medium text-slate-500"
            >
                <span>Pemerintah</span>

                <span aria-hidden="true">
                    /
                </span>

                <span class="text-emerald-700">
                    Peta Infrastruktur
                </span>
            </div>

            <h1
                class="text-2xl font-bold tracking-tight text-slate-900"
            >
                Peta Infrastruktur
            </h1>

            <p
                class="mt-1 max-w-3xl
                       text-sm leading-6 text-slate-500"
            >
                Pantau sebaran hambatan aksesibilitas yang telah
                diverifikasi dan fasilitas publik vital sebagai dasar
                perencanaan penanganan.
            </p>

        </div>


        <div class="flex items-center gap-2">

            <span
                id="map-last-updated"
                class="text-xs text-slate-400"
                aria-live="polite"
            >
                Memuat data…
            </span>

            <button
                type="button"
                id="map-refresh"
                class="inline-flex items-center gap-2
                       rounded-lg
                       border border-slate-200
                       bg-white
                       px-3 py-2
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
                    class="fa-solid fa-rotate"
                    aria-hidden="true"
                ></i>

                Perbarui

            </button>

        </div>

    </div>


    {{-- ==========================================================
         STATISTIK
    =========================================================== --}}
    <div
        class="grid grid-cols-1
               gap-4
               sm:grid-cols-3"
    >

        {{-- TOTAL --}}
        <div
            class="rounded-xl
                   border border-slate-200
                   bg-white
                   p-4
                   shadow-sm"
        >

            <div
                class="flex items-center justify-between"
            >

                <span
                    class="text-xs font-medium text-slate-500"
                >
                    Total titik terlihat
                </span>

                <span
                    class="flex h-9 w-9
                           items-center justify-center
                           rounded-lg
                           bg-emerald-50
                           text-emerald-700"
                >
                    <i
                        class="fa-solid fa-location-dot"
                        aria-hidden="true"
                    ></i>
                </span>

            </div>

            <p
                id="summary-total"
                class="mt-3 text-2xl font-bold text-slate-900"
            >
                0
            </p>

            <p class="mt-1 text-xs text-slate-500">
                Laporan terverifikasi
            </p>

        </div>


        {{-- PRIORITAS --}}
        <div
            class="rounded-xl
                   border border-red-100
                   bg-white
                   p-4
                   shadow-sm"
        >

            <div
                class="flex items-center justify-between"
            >

                <span
                    class="text-xs font-medium text-slate-500"
                >
                    Prioritas tinggi
                </span>

                <span
                    class="flex h-9 w-9
                           items-center justify-center
                           rounded-lg
                           bg-red-50
                           text-red-600"
                >
                    <i
                        class="fa-solid fa-triangle-exclamation"
                        aria-hidden="true"
                    ></i>
                </span>

            </div>

            <p
                id="summary-high"
                class="mt-3 text-2xl font-bold text-slate-900"
            >
                0
            </p>

            <p class="mt-1 text-xs text-slate-500">
                Skor ≥ 70
            </p>

        </div>


        {{-- FASILITAS --}}
        <div
            class="rounded-xl
                   border border-teal-100
                   bg-white
                   p-4
                   shadow-sm"
        >

            <div
                class="flex items-center justify-between"
            >

                <span
                    class="text-xs font-medium text-slate-500"
                >
                    Fasilitas publik
                </span>

                <span
                    class="flex h-9 w-9
                           items-center justify-center
                           rounded-lg
                           bg-teal-50
                           text-teal-700"
                >
                    <i
                        class="fa-solid fa-building"
                        aria-hidden="true"
                    ></i>
                </span>

            </div>

            <p
                id="summary-facilities"
                class="mt-3 text-2xl font-bold text-slate-900"
            >
                0
            </p>

            <p class="mt-1 text-xs text-slate-500">
                Data fasilitas aktif
            </p>

        </div>

    </div>


    {{-- ==========================================================
         PETA + FILTER
    =========================================================== --}}
    <div
        class="grid grid-cols-1
               gap-4
               xl:grid-cols-[280px_minmax(0,1fr)]"
    >

        {{-- ======================================================
             FILTER
        ======================================================= --}}
        <aside
            class="rounded-xl
                   border border-slate-200
                   bg-white
                   shadow-sm"
            aria-label="Filter peta"
        >

            <div
                class="border-b border-slate-100
                       p-5"
            >

                <div
                    class="flex items-start
                           justify-between gap-3"
                >

                    <div>

                        <h2
                            class="text-sm font-bold
                                   text-slate-900"
                        >
                            Filter Peta
                        </h2>

                        <p
                            class="mt-1
                                   text-xs leading-5
                                   text-slate-500"
                        >
                            Sesuaikan data yang ditampilkan
                            pada peta.
                        </p>

                    </div>

                    <button
                        type="button"
                        id="map-reset"
                        class="text-xs font-semibold
                               text-emerald-700
                               hover:text-emerald-800"
                    >
                        Reset
                    </button>

                </div>

            </div>


            <div class="space-y-6 p-5">

                {{-- STATUS --}}
                <fieldset>

                    <legend
                        class="mb-3
                               text-xs font-bold
                               uppercase tracking-wide
                               text-slate-500"
                    >
                        Status laporan
                    </legend>

                    <div class="space-y-2.5">

                        @foreach([
                            'diverifikasi' => 'Diverifikasi',
                            'dalam_perbaikan' => 'Dalam Perbaikan',
                            'selesai' => 'Selesai',
                        ] as $status => $label)

                            <label
                                class="flex cursor-pointer
                                       items-center gap-3
                                       text-sm text-slate-700"
                            >

                                <input
                                    type="checkbox"
                                    class="filter-status
                                           h-4 w-4
                                           rounded
                                           border-slate-300
                                           text-emerald-600
                                           focus:ring-emerald-500"
                                    value="{{ $status }}"
                                    checked
                                >

                                <span
                                    class="h-2.5 w-2.5 rounded-full
                                    {{
                                        $status === 'diverifikasi'
                                            ? 'bg-blue-500'
                                            : (
                                                $status === 'dalam_perbaikan'
                                                    ? 'bg-amber-500'
                                                    : 'bg-emerald-500'
                                            )
                                    }}"
                                ></span>

                                <span>
                                    {{ $label }}
                                </span>

                            </label>

                        @endforeach

                    </div>

                </fieldset>


                {{-- KATEGORI --}}
                <fieldset>

                    <legend
                        class="mb-3
                               text-xs font-bold
                               uppercase tracking-wide
                               text-slate-500"
                    >
                        Kategori hambatan
                    </legend>

                    <div
                        class="max-h-56
                               space-y-2.5
                               overflow-y-auto
                               pr-1"
                    >

                        @foreach($kategoriHambatan as $kategori)

                            <label
                                class="flex cursor-pointer
                                       items-center gap-3
                                       text-sm text-slate-700"
                            >

                                <input
                                    type="checkbox"
                                    class="filter-kategori
                                           h-4 w-4
                                           rounded
                                           border-slate-300
                                           text-emerald-600
                                           focus:ring-emerald-500"
                                    value="{{ $kategori->id }}"
                                    checked
                                >

                                <span
                                    class="h-2.5 w-2.5
                                           rounded-full"
                                    style="
                                        background-color:
                                        {{ $kategori->warna_penanda ?: '#64748b' }}
                                    "
                                ></span>

                                <span>
                                    {{ $kategori->nama }}
                                </span>

                            </label>

                        @endforeach

                    </div>

                </fieldset>


                {{-- PRIORITAS --}}
                <fieldset>

                    <legend
                        class="mb-3
                               text-xs font-bold
                               uppercase tracking-wide
                               text-slate-500"
                    >
                        Prioritas
                    </legend>

                    <div class="space-y-2.5">

                        @foreach([
                            'high' => 'Tinggi · ≥ 70',
                            'medium' => 'Sedang · 40–69',
                            'low' => 'Rendah · < 40'
                        ] as $key => $label)

                            <label
                                class="flex cursor-pointer
                                       items-center gap-3
                                       text-sm text-slate-700"
                            >

                                <input
                                    type="checkbox"
                                    class="filter-priority
                                           h-4 w-4
                                           rounded
                                           border-slate-300
                                           text-emerald-600
                                           focus:ring-emerald-500"
                                    value="{{ $key }}"
                                    checked
                                >

                                <span
                                    class="h-2.5 w-2.5
                                           rounded-full
                                    {{
                                        $key === 'high'
                                            ? 'bg-red-600'
                                            : (
                                                $key === 'medium'
                                                    ? 'bg-amber-500'
                                                    : 'bg-green-600'
                                            )
                                    }}"
                                ></span>

                                <span>
                                    {{ $label }}
                                </span>

                            </label>

                        @endforeach

                    </div>

                </fieldset>


                {{-- FASILITAS --}}
                <div
                    class="border-t
                           border-slate-100
                           pt-5"
                >

                    <label
                        class="flex cursor-pointer
                               items-start gap-3
                               text-sm text-slate-700"
                    >

                        <input
                            type="checkbox"
                            id="toggle-fasilitas"
                            class="mt-0.5
                                   h-4 w-4
                                   rounded
                                   border-slate-300
                                   text-teal-600
                                   focus:ring-teal-500"
                        >

                        <span>

                            <span
                                class="block font-semibold
                                       text-slate-800"
                            >
                                Tampilkan fasilitas publik
                            </span>

                            <span
                                class="mt-0.5 block
                                       text-xs leading-5
                                       text-slate-500"
                            >
                                Rumah sakit, sekolah, halte,
                                kantor pemerintah, dan fasilitas
                                vital lainnya.
                            </span>

                        </span>

                    </label>

                </div>

            </div>

        </aside>


        {{-- ======================================================
             MAP
        ======================================================= --}}
        <section
            class="min-w-0
                   overflow-hidden
                   rounded-xl
                   border border-slate-200
                   bg-white
                   shadow-sm"
            aria-labelledby="map-title"
        >

            <div
                class="flex flex-col gap-2
                       border-b border-slate-100
                       px-5 py-4
                       sm:flex-row
                       sm:items-center
                       sm:justify-between"
            >

                <div>

                    <h2
                        id="map-title"
                        class="text-sm font-bold
                               text-slate-900"
                    >
                        Sebaran Hambatan Aksesibilitas
                    </h2>

                    <p
                        class="mt-1 text-xs
                               text-slate-500"
                    >
                        Leaflet.js + OpenStreetMap · Kota Depok
                    </p>

                </div>

                <div
                    id="map-status"
                    class="text-xs text-slate-500"
                    role="status"
                    aria-live="polite"
                >
                    Memuat peta…
                </div>

            </div>


            <div
                id="dinas-map"
                class="h-[560px] w-full bg-slate-100"
            ></div>


            {{-- LEGEND --}}
            <div
                class="flex flex-wrap
                       items-center
                       gap-x-5 gap-y-2
                       border-t
                       border-slate-100
                       px-5 py-3
                       text-xs text-slate-600"
                aria-label="Legenda prioritas"
            >

                <span
                    class="font-semibold
                           text-slate-700"
                >
                    Legenda:
                </span>

                <span
                    class="inline-flex
                           items-center gap-2"
                >
                    <span
                        class="h-2.5 w-2.5
                               rounded-full bg-red-600"
                    ></span>

                    Tinggi (≥70)
                </span>

                <span
                    class="inline-flex
                           items-center gap-2"
                >
                    <span
                        class="h-2.5 w-2.5
                               rounded-full bg-amber-500"
                    ></span>

                    Sedang (40–69)
                </span>

                <span
                    class="inline-flex
                           items-center gap-2"
                >
                    <span
                        class="h-2.5 w-2.5
                               rounded-full bg-green-600"
                    ></span>

                    Rendah (&lt;40)
                </span>

                <span
                    class="inline-flex
                           items-center gap-2"
                >
                    <span
                        class="h-2.5 w-2.5
                               rounded-full bg-teal-600"
                    ></span>

                    Fasilitas publik
                </span>

            </div>

        </section>

    </div>


    {{-- ==========================================================
         ACCESSIBLE TEXT LIST
    =========================================================== --}}
    <section
        class="rounded-xl
               border border-slate-200
               bg-white
               shadow-sm"
        aria-labelledby="map-list-title"
    >

        <div
            class="flex flex-col gap-1
                   border-b border-slate-100
                   px-5 py-4
                   sm:flex-row
                   sm:items-center
                   sm:justify-between"
        >

            <div>

                <h2
                    id="map-list-title"
                    class="text-sm font-bold
                           text-slate-900"
                >
                    Daftar Titik Terlihat
                </h2>

                <p
                    class="mt-1
                           text-xs leading-5
                           text-slate-500"
                >
                    Alternatif informasi berbasis teks
                    untuk membantu aksesibilitas dan audit data.
                </p>

            </div>

            <span
                id="map-list-count"
                class="text-xs font-semibold
                       text-slate-500"
            >
                0 titik
            </span>

        </div>


        <div
            id="map-list"
            class="divide-y divide-slate-100"
        ></div>

    </section>

</div>

@endsection


{{-- ==============================================================
     LEAFLET CSS
=============================================================== --}}
@push('styles')

<link
    rel="stylesheet"
    href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
>

<style>

    #dinas-map
    .leaflet-control-attribution {
        font-size: 12px;
    }

    .dinas-report-marker,
    .dinas-facility-marker {
        background: transparent;
        border: 0;
    }

    .dinas-report-marker span {
        display: block;
        width: 18px;
        height: 18px;
        border-radius: 999px 999px 999px 2px;
        transform: rotate(-45deg);
        border: 2px solid #fff;
        box-shadow:
            0 2px 7px rgba(15, 23, 42, .28);
    }

    .dinas-report-marker.high span {
        background: #dc2626;
    }

    .dinas-report-marker.medium span {
        background: #d97706;
    }

    .dinas-report-marker.low span {
        background: #16a34a;
    }

    .dinas-facility-marker span {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 26px;
        height: 26px;
        border-radius: 8px;
        background: #0d9488;
        color: #fff;
        border: 2px solid #fff;
        box-shadow:
            0 2px 7px rgba(15, 23, 42, .24);
        font-size: 12px;
    }

    .dinas-map-popup {
        font-family: Inter, sans-serif;
        min-width: 220px;
    }

</style>

@endpush


{{-- ==============================================================
     LEAFLET JS + MAP LOGIC
=============================================================== --}}
@push('scripts')

<script
    src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
></script>

<script>

document.addEventListener(
    'DOMContentLoaded',
    () => {

        'use strict';


        const mapElement =
            document.getElementById(
                'dinas-map'
            );


        if (
            !mapElement ||
            typeof window.L === 'undefined'
        ) {
            return;
        }


        const map =
            L.map(
                mapElement,
                {
                    center: [
                        -6.4025,
                        106.7942
                    ],
                    zoom: 12,
                    zoomControl: true
                }
            );


        L.tileLayer(
            'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
            {
                maxZoom: 19,
                attribution:
                    '&copy; OpenStreetMap contributors'
            }
        ).addTo(map);


        const laporanLayer =
            L.layerGroup()
                .addTo(map);


        const fasilitasLayer =
            L.layerGroup();


        let laporanData = [];

        let fasilitasData = [];

        let firstFit = true;


        const el =
            id =>
                document.getElementById(id);


        function escapeHtml(value) {

            return String(
                value ?? ''
            ).replace(
                /[&<>'"]/g,
                character => {

                    const entities = {
                        '&': '&amp;',
                        '<': '&lt;',
                        '>': '&gt;',
                        "'": '&#039;',
                        '"': '&quot;'
                    };

                    return entities[
                        character
                    ];

                }
            );

        }


        function getPriority(score) {

            const value =
                Number(score);


            if (!Number.isFinite(value)) {

                return {
                    key: 'low',
                    label: 'Belum Dinilai',
                    color: '#64748b'
                };

            }


            if (value >= 70) {

                return {
                    key: 'high',
                    label: 'Tinggi',
                    color: '#dc2626'
                };

            }


            if (value >= 40) {

                return {
                    key: 'medium',
                    label: 'Sedang',
                    color: '#d97706'
                };

            }


            return {
                key: 'low',
                label: 'Rendah',
                color: '#16a34a'
            };

        }


        function iconReport(report) {

            const priority =
                getPriority(
                    report.skor_prioritas
                );


            return L.divIcon({

                className:
                    `dinas-report-marker ${priority.key}`,

                html:
                    '<span aria-hidden="true"></span>',

                iconSize: [
                    20,
                    20
                ],

                iconAnchor: [
                    10,
                    19
                ],

                popupAnchor: [
                    0,
                    -18
                ]

            });

        }


        function iconFacility() {

            return L.divIcon({

                className:
                    'dinas-facility-marker',

                html:
                    '<span aria-hidden="true">' +
                    '<i class="fa-solid fa-building"></i>' +
                    '</span>',

                iconSize: [
                    28,
                    28
                ],

                iconAnchor: [
                    14,
                    14
                ]

            });

        }


        function renderFacilities() {

            fasilitasLayer.clearLayers();


            fasilitasData.forEach(
                facility => {

                    const latitude =
                        Number(
                            facility.latitude
                        );


                    const longitude =
                        Number(
                            facility.longitude
                        );


                    if (
                        !Number.isFinite(latitude) ||
                        !Number.isFinite(longitude)
                    ) {
                        return;
                    }


                    const marker =
                        L.marker(
                            [
                                latitude,
                                longitude
                            ],
                            {
                                icon:
                                    iconFacility(),

                                title:
                                    facility.nama,

                                alt:
                                    `Fasilitas publik ${facility.nama}`
                            }
                        );


                    marker.bindPopup(
                        `
                        <div
                            class="dinas-map-popup"
                        >

                            <strong
                                class="text-sm
                                       text-slate-900"
                            >
                                ${escapeHtml(
                                    facility.nama
                                )}
                            </strong>

                            <div
                                class="mt-1
                                       text-xs
                                       font-semibold
                                       text-teal-700"
                            >
                                ${escapeHtml(
                                    facility.jenis_label
                                )}
                            </div>

                            <div
                                class="mt-2
                                       text-xs
                                       leading-5
                                       text-slate-500"
                            >
                                ${escapeHtml(
                                    facility.alamat ||
                                    'Alamat tidak tersedia'
                                )}
                            </div>

                            <div
                                class="mt-2
                                       text-xs
                                       text-slate-500"
                            >
                                Tingkat vital:
                                <strong>
                                    ${escapeHtml(
                                        facility.bobot_vital
                                    )}
                                </strong>
                            </div>

                        </div>
                        `
                    );


                    fasilitasLayer.addLayer(
                        marker
                    );

                    const markerElement =
                        marker.getElement();

                    if (markerElement) {
                        markerElement.setAttribute(
                            'role',
                            'button'
                        );

                        markerElement.setAttribute(
                            'tabindex',
                            '0'
                        );

                        markerElement.setAttribute(
                            'aria-label',
                            `Fasilitas publik ${facility.nama}. ${facility.jenis_label || ''}`
                        );
                    }

                }
            );


            el(
                'summary-facilities'
            ).textContent =
                fasilitasData.length;

        }


        function renderList(items) {

            const list =
                el('map-list');


            el(
                'map-list-count'
            ).textContent =
                `${items.length} titik`;


            if (!items.length) {

                list.innerHTML =
                    `
                    <div
                        class="px-5 py-10
                               text-center
                               text-sm
                               text-slate-500"
                    >
                        Tidak ada titik yang sesuai
                        dengan filter saat ini.
                    </div>
                    `;

                return;

            }


            list.innerHTML =
                items
                    .slice(0, 12)
                    .map(
                        report => {

                            const p =
                                getPriority(
                                    report.skor_prioritas
                                );


                            return `
                            <article
                                class="px-5 py-4
                                       hover:bg-slate-50"
                            >

                                <div
                                    class="flex flex-col
                                           gap-3
                                           sm:flex-row
                                           sm:items-center
                                           sm:justify-between"
                                >

                                    <div
                                        class="min-w-0"
                                    >

                                        <div
                                            class="flex flex-wrap
                                                   items-center
                                                   gap-2"
                                        >

                                            <span
                                                class="font-semibold
                                                       text-sm
                                                       text-slate-900"
                                            >
                                                ${escapeHtml(
                                                    report.kode_laporan ||
                                                    'Laporan'
                                                )}
                                            </span>

                                            <span
                                                class="rounded-full
                                                       px-2 py-0.5
                                                       text-[10px]
                                                       font-semibold"
                                                style="
                                                    color:${p.color};
                                                    background:${p.color}15;
                                                "
                                            >
                                                Prioritas
                                                ${escapeHtml(
                                                    p.label
                                                )}
                                            </span>

                                        </div>


                                        <p
                                            class="mt-1
                                                   truncate
                                                   text-sm
                                                   text-slate-700"
                                        >
                                            ${escapeHtml(
                                                report.judul ||
                                                'Hambatan aksesibilitas'
                                            )}
                                        </p>


                                        <p
                                            class="mt-1
                                                   text-xs
                                                   text-slate-500"
                                        >
                                            ${escapeHtml(
                                                report.kategori ||
                                                'Kategori tidak tersedia'
                                            )}

                                            ·

                                            ${escapeHtml(
                                                report.wilayah ||
                                                'Wilayah tidak tersedia'
                                            )}
                                        </p>

                                    </div>


                                    <div
                                        class="shrink-0
                                               text-left
                                               sm:text-right"
                                    >

                                        <p
                                            class="text-sm
                                                   font-bold
                                                   text-slate-900"
                                        >
                                            ${
                                                report.skor_prioritas !== null &&
                                                report.skor_prioritas !== undefined
                                                    ? Number(
                                                        report.skor_prioritas
                                                    ).toFixed(2)
                                                    : '-'
                                            }
                                        </p>

                                        <p
                                            class="text-[11px]
                                                   text-slate-500"
                                        >
                                            ${escapeHtml(
                                                report.status_label ||
                                                report.status
                                            )}
                                        </p>

                                    </div>

                                </div>

                            </article>
                            `;

                        }
                    )
                    .join('');

        }


        function renderReports() {

            laporanLayer.clearLayers();


            const statuses =
                [
                    ...document.querySelectorAll(
                        '.filter-status:checked'
                    )
                ]
                .map(
                    element =>
                        element.value
                );


            const categories =
                [
                    ...document.querySelectorAll(
                        '.filter-kategori:checked'
                    )
                ]
                .map(
                    element =>
                        String(
                            element.value
                        )
                );


            const priorities =
                [
                    ...document.querySelectorAll(
                        '.filter-priority:checked'
                    )
                ]
                .map(
                    element =>
                        element.value
                );


            const visible =
                laporanData.filter(
                    report => {

                        const priority =
                            getPriority(
                                report.skor_prioritas
                            );


                        return (
                            statuses.includes(
                                report.status
                            )

                            &&

                            (
                                categories.length === 0

                                ||

                                categories.includes(
                                    String(
                                        report.kategori_id
                                    )
                                )
                            )

                            &&

                            priorities.includes(
                                priority.key
                            )
                        );

                    }
                );


            visible.forEach(
                report => {

                    const latitude =
                        Number(
                            report.latitude
                        );


                    const longitude =
                        Number(
                            report.longitude
                        );


                    if (
                        !Number.isFinite(latitude) ||
                        !Number.isFinite(longitude)
                    ) {
                        return;
                    }


                    const marker =
                        L.marker(
                            [
                                latitude,
                                longitude
                            ],
                            {
                                icon:
                                    iconReport(
                                        report
                                    ),

                                title:
                                    report.judul ||
                                    'Hambatan aksesibilitas',

                                alt:
                                    `Lokasi hambatan ${
                                        report.judul || ''
                                    }`
                            }
                        );


                    const priority =
                        getPriority(
                            report.skor_prioritas
                        );


                    marker.bindPopup(
                        `
                        <div
                            class="dinas-map-popup"
                        >

                            <strong
                                class="text-sm
                                       text-slate-900"
                            >
                                ${escapeHtml(
                                    report.judul ||
                                    'Laporan Hambatan'
                                )}
                            </strong>


                            <div
                                class="mt-1
                                       text-xs
                                       font-semibold"
                                style="
                                    color:${priority.color};
                                "
                            >
                                Prioritas
                                ${escapeHtml(
                                    priority.label
                                )}

                                · Skor

                                ${
                                    report.skor_prioritas !== null &&
                                    report.skor_prioritas !== undefined
                                        ? Number(
                                            report.skor_prioritas
                                        ).toFixed(2)
                                        : '-'
                                }
                            </div>


                            <div
                                class="mt-2
                                       text-xs
                                       text-slate-600"
                            >
                                ${escapeHtml(
                                    report.kategori ||
                                    'Kategori tidak tersedia'
                                )}
                            </div>


                            <div
                                class="mt-1
                                       text-xs
                                       leading-5
                                       text-slate-500"
                            >
                                ${escapeHtml(
                                    report.alamat_lengkap ||
                                    'Alamat tidak tersedia'
                                )}
                            </div>


                            <div
                                class="mt-2
                                       text-xs
                                       text-slate-500"
                            >
                                ${escapeHtml(
                                    report.status_label ||
                                    report.status
                                )}

                                ·

                                ${Number(
                                    report.jumlah_pelapor || 1
                                )}

                                pelapor
                            </div>

                        </div>
                        `
                    );


                    laporanLayer.addLayer(
                        marker
                    );

                    const markerElement =
                        marker.getElement();

                    if (markerElement) {
                        markerElement.setAttribute(
                            'role',
                            'button'
                        );

                        markerElement.setAttribute(
                            'tabindex',
                            '0'
                        );

                        markerElement.setAttribute(
                            'aria-label',
                            `Laporan ${report.judul || 'hambatan aksesibilitas'}. Prioritas ${priority.label}.`
                        );
                    }

                }
            );


            const high =
                visible.filter(
                    report =>
                        Number(
                            report.skor_prioritas
                        ) >= 70
                ).length;


            el(
                'summary-total'
            ).textContent =
                visible.length;


            el(
                'summary-high'
            ).textContent =
                high;


            renderList(
                visible
            );


            if (
                firstFit &&
                visible.length
            ) {

                const points =
                    visible
                        .map(
                            report => [
                                Number(
                                    report.latitude
                                ),
                                Number(
                                    report.longitude
                                )
                            ]
                        )
                        .filter(
                            point =>
                                Number.isFinite(
                                    point[0]
                                )
                                &&
                                Number.isFinite(
                                    point[1]
                                )
                        );


                if (
                    points.length === 1
                ) {

                    map.setView(
                        points[0],
                        16
                    );

                } else if (
                    points.length > 1
                ) {

                    map.fitBounds(
                        points,
                        {
                            padding: [
                                28,
                                28
                            ],
                            maxZoom: 15
                        }
                    );

                }


                firstFit = false;

            }

        }


        async function loadData(
            showLoading = true
        ) {

            if (showLoading) {

                el(
                    'map-status'
                ).textContent =
                    'Memuat data…';

            }


            try {

                const [
                    reportsResponse,
                    facilitiesResponse
                ] =
                    await Promise.all([
                        fetch(
                            '{{ route('peta.data') }}',
                            {
                                headers: {
                                    Accept:
                                        'application/json'
                                },
                                cache:
                                    'no-store'
                            }
                        ),

                        fetch(
                            '{{ route('peta.fasilitas') }}',
                            {
                                headers: {
                                    Accept:
                                        'application/json'
                                },
                                cache:
                                    'no-store'
                            }
                        )
                    ]);


                if (
                    !reportsResponse.ok ||
                    !facilitiesResponse.ok
                ) {

                    throw new Error(
                        'Endpoint data peta tidak merespons dengan benar.'
                    );

                }


                const [
                    reports,
                    facilities
                ] =
                    await Promise.all([
                        reportsResponse.json(),
                        facilitiesResponse.json()
                    ]);


                if (
                    !Array.isArray(reports) ||
                    !Array.isArray(facilities)
                ) {

                    throw new Error(
                        'Format data peta tidak valid.'
                    );

                }


                laporanData =
                    reports;


                fasilitasData =
                    facilities;


                renderFacilities();

                renderReports();


                el(
                    'map-status'
                ).textContent =
                    'Data aktif';


                el(
                    'map-last-updated'
                ).textContent =
                    `Diperbarui ${
                        new Intl.DateTimeFormat(
                            'id-ID',
                            {
                                hour: '2-digit',
                                minute: '2-digit'
                            }
                        ).format(
                            new Date()
                        )
                    }`;

            } catch (error) {

                console.error(
                    'SmartPath Dinas Peta:',
                    error
                );


                el(
                    'map-status'
                ).textContent =
                    'Data gagal dimuat';


                el(
                    'map-last-updated'
                ).textContent =
                    'Periksa koneksi aplikasi';


                el(
                    'map-list'
                ).innerHTML =
                    `
                    <div
                        class="px-5 py-10
                               text-center
                               text-sm
                               text-red-600"
                    >
                        Data peta tidak dapat dimuat.
                        Coba tekan Perbarui.
                    </div>
                    `;

            }

        }


        document
            .querySelectorAll(
                '.filter-status, .filter-kategori, .filter-priority'
            )
            .forEach(
                input => {

                    input.addEventListener(
                        'change',
                        renderReports
                    );

                }
            );


        el(
            'toggle-fasilitas'
        ).addEventListener(
            'change',
            event => {

                if (
                    event.target.checked
                ) {

                    fasilitasLayer.addTo(
                        map
                    );

                } else {

                    map.removeLayer(
                        fasilitasLayer
                    );

                }

            }
        );


        el(
            'map-refresh'
        ).addEventListener(
            'click',
            () => {

                loadData(
                    true
                );

            }
        );


        el(
            'map-reset'
        ).addEventListener(
            'click',
            () => {

                document
                    .querySelectorAll(
                        '.filter-status, .filter-kategori, .filter-priority'
                    )
                    .forEach(
                        input => {

                            input.checked =
                                true;

                        }
                    );


                el(
                    'toggle-fasilitas'
                ).checked =
                    false;


                map.removeLayer(
                    fasilitasLayer
                );


                firstFit =
                    false;


                renderReports();

            }
        );


        window.addEventListener(
            'resize',
            () => {

                map.invalidateSize();

            },
            {
                passive: true
            }
        );


        loadData(
            true
        );


        /*
         * Proposal menetapkan pembaruan dashboard
         * secara berkala menggunakan AJAX polling.
         */
        setInterval(
            () => {

                loadData(
                    false
                );

            },
            60000
        );


        setTimeout(
            () => {

                map.invalidateSize();

            },
            250
        );

    }
);

</script>

@endpush