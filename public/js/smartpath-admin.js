(() => {
    'use strict';

    const DEFAULT_CENTER = [
        -6.4025,
        106.7942
    ];

    const DEFAULT_ZOOM = 12;

    const dashboard =
        document.getElementById(
            'admin-dashboard'
        );

    const mapElement =
        document.getElementById(
            'admin-map'
        );

    if (!dashboard || !mapElement) {
        return;
    }

    if (typeof window.L === 'undefined') {
        console.error(
            'SmartPath: Leaflet.js belum tersedia.'
        );

        return;
    }

    const dataElement =
        document.getElementById(
            'smartpath-map-data'
        );

    let reports = [];

    if (dataElement) {
        try {
            const parsed =
                JSON.parse(
                    dataElement.textContent || '[]'
                );

            if (Array.isArray(parsed)) {
                reports = parsed;
            }
        } catch (error) {
            console.error(
                'SmartPath: data awal peta tidak valid.',
                error
            );
        }
    }


    const escapeHtml = (value) => {
        return String(value ?? '').replace(
            /[&<>'"]/g,
            (character) => {
                const entities = {
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    "'": '&#039;',
                    '"': '&quot;'
                };

                return entities[character];
            }
        );
    };


    const getPriority = (score) => {
        if (
            score === null ||
            score === undefined ||
            score === ''
        ) {
            return {
                key: 'pending',
                label: 'Belum Dinilai',
                color: '#64748b'
            };
        }

        const numericScore =
            Number(score);

        if (numericScore >= 70) {
            return {
                key: 'high',
                label: 'Tinggi',
                color: '#dc2626'
            };
        }

        if (numericScore >= 40) {
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
    };


    const createMarkerIcon = (report) => {
        const priority =
            getPriority(
                report.skor_prioritas
            );

        let markerClass =
            priority.key;

        if (report.status === 'menunggu_verifikasi') {
            markerClass = 'pending';
        }

        return window.L.divIcon({
            className:
                'smartpath-admin-marker',

            html: `
                <span
                    class="sp-marker-pin ${markerClass}"
                    role="img"
                    aria-label="Lokasi ${escapeHtml(
                        report.judul || 'laporan'
                    )}, ${escapeHtml(
                        priority.label
                    )}"
                >
                    <span class="sp-marker-pin-dot"></span>
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
                -32
            ]
        });
    };


    const createPopup = (report) => {
        const priority =
            getPriority(
                report.skor_prioritas
            );

        const score =
            report.skor_prioritas === null
                ||
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
                        alt=""
                        style="
                            width:100%;
                            height:105px;
                            object-fit:cover;
                            border-radius:8px;
                            margin-bottom:8px;
                        "
                    >
                `
                : '';

        return `
            <article
                style="
                    width:245px;
                    font-family:Inter,system-ui,sans-serif;
                "
            >

                ${photo}

                <div
                    style="
                        color:#0f172a;
                        font-size:13px;
                        font-weight:700;
                        line-height:1.4;
                    "
                >
                    ${escapeHtml(
                        report.judul || 'Laporan Hambatan'
                    )}
                </div>

                <div
                    style="
                        margin-top:4px;
                        color:#059669;
                        font-size:10px;
                        font-weight:600;
                    "
                >
                    ${escapeHtml(
                        report.kategori || 'Kategori tidak tersedia'
                    )}
                </div>

                <div
                    style="
                        margin-top:7px;
                        color:#64748b;
                        font-size:10px;
                        line-height:1.5;
                    "
                >
                    ${escapeHtml(
                        report.alamat
                        ||
                        'Alamat tidak tersedia'
                    )}
                </div>

                <div
                    style="
                        display:flex;
                        align-items:center;
                        gap:6px;
                        flex-wrap:wrap;
                        margin-top:8px;
                    "
                >

                    <span
                        style="
                            display:inline-flex;
                            padding:4px 7px;
                            border-radius:6px;
                            background:${priority.color}15;
                            color:${priority.color};
                            border:1px solid ${priority.color}35;
                            font-size:10px;
                            font-weight:700;
                        "
                    >
                        ${escapeHtml(
                            priority.label
                        )}
                    </span>

                    <span
                        style="
                            color:#64748b;
                            font-size:10px;
                        "
                    >
                        Skor ${escapeHtml(score)}
                    </span>

                </div>

                <div
                    style="
                        margin-top:7px;
                        color:#64748b;
                        font-size:10px;
                    "
                >
                    ${escapeHtml(
                        report.status_label
                        ||
                        report.status
                        ||
                        'Status tidak tersedia'
                    )}

                    ·

                    ${escapeHtml(
                        report.jumlah_pelapor || 1
                    )}
                    pelapor
                </div>

            </article>
        `;
    };


    const map =
        window.L
            .map(
                mapElement,
                {
                    zoomControl: true,
                    attributionControl: true,
                    scrollWheelZoom: true
                }
            )
            .setView(
                DEFAULT_CENTER,
                DEFAULT_ZOOM
            );


    window.L
        .tileLayer(
            'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
            {
                maxZoom: 19,
                attribution:
                    '&copy; OpenStreetMap contributors'
            }
        )
        .addTo(map);


    const markerLayer =
        window.L
            .layerGroup()
            .addTo(map);


    const renderMarkers = (
        fitToData = false
    ) => {

        markerLayer.clearLayers();

        const categoryFilter =
            document.getElementById(
                'map-category-filter'
            );

        const periodFilter =
            document.getElementById(
                'map-period-filter'
            );

        const categoryId =
            categoryFilter?.value || '';

        const period =
            Number(
                periodFilter?.value || 0
            );

        const cutoff =
            period > 0
                ? Date.now()
                    -
                    (
                        period
                        *
                        24
                        *
                        60
                        *
                        60
                        *
                        1000
                    )
                : 0;

        const visibleReports =
            reports.filter(
                (report) => {

                    if (
                        categoryId
                        &&
                        String(
                            report.kategori_id
                        )
                        !==
                        String(categoryId)
                    ) {
                        return false;
                    }

                    if (
                        cutoff
                        &&
                        report.created_at
                        &&
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


        visibleReports.forEach(
            (report) => {

                const marker =
                    window.L.marker(
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
                                report.judul,
                            alt:
                                `Lokasi hambatan ${report.judul}`
                        }
                    );

                marker.bindPopup(
                    createPopup(report),
                    {
                        maxWidth: 320,
                        minWidth: 220
                    }
                );

                markerLayer.addLayer(
                    marker
                );
            }
        );


        if (
            fitToData
            &&
            visibleReports.length
        ) {

            const points =
                visibleReports.map(
                    report => [
                        Number(
                            report.latitude
                        ),
                        Number(
                            report.longitude
                        )
                    ]
                );

            if (points.length === 1) {

                map.setView(
                    points[0],
                    16
                );

            } else {

                map.fitBounds(
                    points,
                    {
                        padding: [
                            30,
                            30
                        ],
                        maxZoom: 15
                    }
                );
            }
        }
    };


    const updateKpi = (
        data
    ) => {

        const mappings = {
            'admin-kpi-total':
                data.total,

            'admin-kpi-menunggu':
                data.menunggu,

            'admin-kpi-diverifikasi':
                data.diverifikasi,

            'admin-kpi-kritis':
                data.kritis
        };

        Object.entries(
            mappings
        ).forEach(
            ([id, value]) => {

                const element =
                    document.getElementById(id);

                if (!element) {
                    return;
                }

                element.textContent =
                    Number(
                        value || 0
                    ).toLocaleString(
                        'id-ID'
                    );
            }
        );
    };


    const updateMapSummary = (
        data
    ) => {

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
                Number(
                    data.total || 0
                ).toLocaleString(
                    'id-ID'
                );
        }

        if (area) {
            area.textContent =
                Number(
                    data.area_dipantau || 0
                ).toLocaleString(
                    'id-ID'
                );
        }
    };


    const refreshDashboard = async () => {

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
                            'Accept':
                                'application/json',
                            'X-Requested-With':
                                'XMLHttpRequest'
                        },
                        credentials: 'same-origin',
                        cache: 'no-store'
                    }
                );

            if (!response.ok) {
                throw new Error(
                    `HTTP ${response.status}`
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

                updateMapSummary({
                    total:
                        payload.peta_laporan?.length
                        ??
                        0,

                    area_dipantau:
                        payload.area_dipantau
                });
            }

            if (
                Array.isArray(
                    payload.peta_laporan
                )
            ) {

                reports =
                    payload.peta_laporan;

                renderMarkers(false);
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


    renderMarkers(
        true
    );


    window.setTimeout(
        () => {
            map.invalidateSize();
        },
        200
    );


    window.setInterval(
        refreshDashboard,
        60000
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

})();