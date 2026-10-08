(() => {
    'use strict';

    const dashboard =
        document.getElementById(
            'admin-dashboard'
        );

    const mapElement =
        document.getElementById(
            'admin-map'
        );

    const dataElement =
        document.getElementById(
            'smartpath-map-data'
        );

    if (
        !dashboard ||
        !mapElement ||
        typeof window.L === 'undefined'
    ) {
        return;
    }

    const DEFAULT_CENTER = [
        -6.4025,
        106.7942
    ];

    const DEFAULT_ZOOM = 12;

    let reports = [];

async function loadHomeReports() {

    try {

        const response =
            await fetch(
                '{{ route("peta.data") }}',
                {
                    headers: {
                        'Accept':
                            'application/json'
                    }
                }
            );

        if (!response.ok) {
            throw new Error(
                'Gagal mengambil data laporan.'
            );
        }

        reports =
            await response.json();

        renderHomeMarkers();

    } catch (error) {

        console.error(
            'SmartPath Home Map:',
            error
        );

    }
}


    const escapeHtml = (value) => {

        return String(
            value ?? ''
        ).replace(
            /[&<>'"]/g,
            (character) => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                "'": '&#039;',
                '"': '&quot;'
            })[character]
        );

    };


    const priority = (score) => {

        if (
            score === null ||
            score === undefined ||
            score === ''
        ) {
            return {
                key: 'pending',
                label: 'Belum Dinilai',
                color: '#64748B'
            };
        }

        const value =
            Number(score);

        if (value >= 70) {

            return {
                key: 'high',
                label: 'Tinggi',
                color: '#DC2626'
            };

        }

        if (value >= 40) {

            return {
                key: 'medium',
                label: 'Sedang',
                color: '#D97706'
            };

        }

        return {
            key: 'low',
            label: 'Rendah',
            color: '#16A34A'
        };

    };


    const formatNumber =
        (value) =>
            Number(value || 0)
                .toLocaleString('id-ID');


    const createMarkerIcon =
        (report) => {

            const level =
                priority(
                    report.skor_prioritas
                );

            const label =
                `Lokasi ${
                    report.judul ||
                    'laporan hambatan'
                }, prioritas ${
                    level.label
                }`;

            return L.divIcon({

                className:
                    'smartpath-admin-marker',

                html: `
                    <span
                        class="sp-marker-pin ${level.key}"
                        role="img"
                        aria-label="${escapeHtml(label)}"
                    >
                        <span aria-hidden="true"></span>
                    </span>
                `,

                iconSize: [
                    30,
                    38
                ],

                iconAnchor: [
                    15,
                    37
                ],

                popupAnchor: [
                    0,
                    -34
                ]

            });

        };


    const createPopup =
        (report) => {

            const level =
                priority(
                    report.skor_prioritas
                );

            const score =
                report.skor_prioritas === null ||
                report.skor_prioritas === undefined

                    ? 'Belum dinilai'

                    : Number(
                        report.skor_prioritas
                    ).toFixed(2);


            const photo =
                report.foto_utama

                    ? `
                        <img
                            src="${escapeHtml(
                                report.foto_utama
                            )}"
                            alt="Foto hambatan: ${escapeHtml(
                                report.judul
                            )}"
                            class="sp-map-popup-photo"
                        >
                    `

                    : '';


            const distance =
                report.jarak_fasilitas_meter !== null &&
                report.jarak_fasilitas_meter !== undefined

                    ? `
                        <div class="sp-map-popup-detail">
                            Fasilitas terdekat:
                            ${formatNumber(
                                Math.round(
                                    Number(
                                        report.jarak_fasilitas_meter
                                    )
                                )
                            )} m
                        </div>
                    `

                    : '';


            const detail =
                report.detail_url

                    ? `
                        <a
                            href="${escapeHtml(
                                report.detail_url
                            )}"
                            class="sp-map-popup-link"
                        >
                            Lihat detail laporan →
                        </a>
                    `

                    : '';


            return `
                <article
                    class="sp-map-popup"
                    aria-label="Informasi laporan ${escapeHtml(
                        report.judul
                    )}"
                >

                    ${photo}

                    <div class="sp-map-popup-title">
                        ${escapeHtml(
                            report.judul ||
                            'Laporan Hambatan'
                        )}
                    </div>

                    <div class="sp-map-popup-category">
                        ${escapeHtml(
                            report.kategori ||
                            'Kategori tidak tersedia'
                        )}
                    </div>

                    <div class="sp-map-popup-address">
                        ${escapeHtml(
                            report.alamat ||
                            report.wilayah ||
                            'Alamat tidak tersedia'
                        )}
                    </div>

                    <div class="sp-map-popup-meta">

                        <span
                            class="sp-map-popup-badge"
                            style="
                                color:${level.color};
                                border-color:${level.color}55;
                                background:${level.color}12;
                            "
                        >
                            ${escapeHtml(
                                level.label
                            )}
                        </span>

                        <span class="sp-map-popup-score">
                            Skor ${escapeHtml(score)}
                        </span>

                    </div>

                    <div class="sp-map-popup-detail">
                        Status:
                        ${escapeHtml(
                            report.status_label ||
                            report.status ||
                            'Tidak tersedia'
                        )}
                    </div>

                    <div class="sp-map-popup-detail">
                        Pelapor:
                        ${formatNumber(
                            report.jumlah_pelapor ||
                            1
                        )}
                        orang
                    </div>

                    ${distance}

                    ${detail}

                </article>
            `;

        };


    const map =
        L.map(
            mapElement,
            {
                zoomControl: true,
                attributionControl: true,
                scrollWheelZoom: true,
                keyboard: true
            }
        ).setView(
            DEFAULT_CENTER,
            DEFAULT_ZOOM
        );


    L.tileLayer(
        'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
        {
            maxZoom: 19,
            attribution:
                '&copy; OpenStreetMap contributors'
        }
    ).addTo(map);


    const markerLayer =
        L.layerGroup()
            .addTo(map);


    const visibleReports =
        () => {

            const category =
                document
                    .getElementById(
                        'map-category-filter'
                    )
                    ?.value || '';

            const period =
                Number(
                    document
                        .getElementById(
                            'map-period-filter'
                        )
                        ?.value || 0
                );

            const cutoff =
                period > 0
                    ? Date.now()
                        -
                        (
                            period
                            *
                            86400000
                        )
                    : 0;


            return reports.filter(
                (report) => {

                    if (
                        category &&
                        String(
                            report.kategori_id
                        ) !== String(
                            category
                        )
                    ) {
                        return false;
                    }


                    if (
                        cutoff &&
                        report.created_at &&
                        Date.parse(
                            report.created_at
                        ) < cutoff
                    ) {
                        return false;
                    }


                    return (
                        Number.isFinite(
                            Number(
                                report.latitude
                            )
                        )
                        &&
                        Number.isFinite(
                            Number(
                                report.longitude
                            )
                        )
                    );

                }
            );

        };


    const updateMapCount =
        (count) => {

            const element =
                document.getElementById(
                    'admin-map-total'
                );

            if (element) {

                element.textContent =
                    formatNumber(count);

            }

        };


    const renderMarkers =
        (fitToData = false) => {

            markerLayer.clearLayers();

            const visible =
                visibleReports();


            visible.forEach(
                (report) => {

                    const marker =
                        L.marker(
                            [
                                Number(
                                    report.latitude
                                ),
                                Number(
                                    report.longitude
                                )
                            ],
                            {
                                icon:
                                    createMarkerIcon(
                                        report
                                    ),

                                title:
                                    report.judul ||
                                    'Laporan hambatan',

                                alt:
                                    `Lokasi hambatan ${
                                        report.judul ||
                                        'laporan'
                                    }`,

                                keyboard:
                                    true
                            }
                        );


                    marker.bindPopup(
                        createPopup(report),
                        {
                            maxWidth: 310,
                            minWidth: 225,
                            closeButton: true,
                            autoPan: true
                        }
                    );


                    markerLayer.addLayer(
                        marker
                    );

                    const markerElement = marker.getElement();
                    if (markerElement) {
                        const score = Number(report.skor_prioritas);
                        const priority = Number.isFinite(score)
                            ? (score >= 70 ? 'tinggi' : score >= 40 ? 'sedang' : 'rendah')
                            : 'belum dinilai';
                        markerElement.setAttribute(
                            'aria-label',
                            `Laporan ${report.judul || 'hambatan aksesibilitas'}. Prioritas ${priority}.`
                        );
                    }

                }
            );


            updateMapCount(
                visible.length
            );


            if (
                fitToData &&
                visible.length
            ) {

                const points =
                    visible.map(
                        (report) => [
                            Number(
                                report.latitude
                            ),
                            Number(
                                report.longitude
                            )
                        ]
                    );


                if (
                    points.length === 1
                ) {

                    map.setView(
                        points[0],
                        16
                    );

                } else {

                    map.fitBounds(
                        points,
                        {
                            padding: [
                                25,
                                25
                            ],
                            maxZoom: 15
                        }
                    );

                }

            }

        };


    const updateKpi =
        (stats) => {

            const mappings = {

                'admin-kpi-total':
                    stats.total,

                'admin-kpi-menunggu':
                    stats.menunggu,

                'admin-kpi-diverifikasi':
                    stats.diverifikasi,

                'admin-kpi-kritis':
                    stats.kritis

            };


            Object.entries(
                mappings
            ).forEach(
                ([id, value]) => {

                    const element =
                        document.getElementById(
                            id
                        );

                    if (element) {

                        element.textContent =
                            formatNumber(
                                value
                            );

                    }

                }
            );

        };


    const updateMapSummary =
        (payload) => {

            const total =
                document.getElementById(
                    'admin-map-total'
                );

            const area =
                document.getElementById(
                    'admin-map-area'
                );


            if (total) {

                total.textContent =
                    formatNumber(
                        payload.total ??
                        reports.length
                    );

            }


            if (area) {

                area.textContent =
                    formatNumber(
                        payload.area_dipantau ??
                        0
                    );

            }

        };


    const refreshDashboard =
        async () => {

            const liveUrl =
                dashboard.dataset.liveUrl;

            if (!liveUrl) {
                return;
            }


            try {

                const response =
                    await fetch(
                        liveUrl,
                        {
                            method: 'GET',

                            headers: {
                                Accept:
                                    'application/json',

                                'X-Requested-With':
                                    'XMLHttpRequest'
                            },

                            credentials:
                                'same-origin',

                            cache:
                                'no-store'
                        }
                    );


                if (!response.ok) {

                    throw new Error(
                        `HTTP ${
                            response.status
                        }`
                    );

                }


                const payload =
                    await response.json();


                if (
                    payload.statistik
                ) {

                    updateKpi(
                        payload.statistik
                    );

                }


                if (
                    Array.isArray(
                        payload.peta_laporan
                    )
                ) {

                    reports =
                        payload.peta_laporan;


                    updateMapSummary({

                        total:
                            reports.length,

                        area_dipantau:
                            payload.area_dipantau

                    });


                    renderMarkers(
                        false
                    );

                }

            } catch (error) {

                console.warn(
                    'SmartPath: refresh dashboard gagal.',
                    error
                );

            }

        };


    document
        .getElementById(
            'map-category-filter'
        )
        ?.addEventListener(
            'change',
            () => renderMarkers(false)
        );


    document
        .getElementById(
            'map-period-filter'
        )
        ?.addEventListener(
            'change',
            () => renderMarkers(false)
        );


    document
        .getElementById(
            'map-recenter'
        )
        ?.addEventListener(
            'click',
            () => {

                map.flyTo(
                    DEFAULT_CENTER,
                    DEFAULT_ZOOM,
                    {
                        duration: 0.5
                    }
                );

            }
        );


    renderMarkers(true);


    window.setTimeout(
        () => map.invalidateSize(),
        250
    );


    /*
     * Sesuai proposal:
     * dashboard diperbarui otomatis setiap 60 detik
     * menggunakan AJAX polling tanpa reload halaman.
     */
    window.setInterval(
        refreshDashboard,
        60000
    );


    window.addEventListener(
        'resize',
        () => map.invalidateSize(),
        {
            passive: true
        }
    );

})();