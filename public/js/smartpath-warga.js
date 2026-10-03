(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {

        const root = document.getElementById('warga-dashboard');

        if (!root) {
            return;
        }

        const mapElement =
            document.getElementById('warga-map');

        const mapStatus =
            document.getElementById('warga-map-status');

        const nearbyList =
            document.getElementById('warga-nearby-list');

        const nearbyStatus =
            document.getElementById('warga-nearby-status');

        const nearbySummary =
            document.getElementById('warga-nearby-summary');

        const btnLocation =
            document.getElementById('btn-warga-location');

        const btnFacilities =
            document.getElementById('btn-warga-facilities');

        const btnReadPage =
            document.getElementById('btn-read-page');

        const btnReadNearby =
            document.getElementById('btn-read-nearby');


        /*
        |--------------------------------------------------------------------------
        | VALIDASI LEAFLET
        |--------------------------------------------------------------------------
        */

        if (!mapElement || typeof L === 'undefined') {

            setText(
                mapStatus,
                'Peta tidak dapat dimuat. Silakan muat ulang halaman.'
            );

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | KONFIGURASI
        |--------------------------------------------------------------------------
        */

        const CONFIG = {

            laporanUrl:
                root.dataset.laporanUrl,

            fasilitasUrl:
                root.dataset.fasilitasUrl,

            detailTemplate:
                root.dataset.detailTemplate,

            pilotBounds:
                parseJson(
                    root.dataset.pilotBounds,
                    {}
                ),

            pilotCenter:
                parseJson(
                    root.dataset.pilotCenter,
                    {
                        latitude: -6.4025,
                        longitude: 106.7942
                    }
                ),

            nearbyRadius: 50,

            gpsTimeout:
                Number(
                    root.dataset.gpsTimeout || 15000
                ),

            warningAccuracy:
                Number(
                    root.dataset.warningAccuracy || 100
                ),

            manualAccuracy:
                Number(
                    root.dataset.manualAccuracy || 500
                )
        };


        /*
        |--------------------------------------------------------------------------
        | INISIALISASI MAP
        |--------------------------------------------------------------------------
        */

        const map = L.map(
            mapElement,
            {
                center: [
                    Number(
                        CONFIG.pilotCenter.latitude
                    ),
                    Number(
                        CONFIG.pilotCenter.longitude
                    )
                ],

                zoom: 13,

                zoomControl: true,

                attributionControl: true
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


        /*
        |--------------------------------------------------------------------------
        | LAYER
        |--------------------------------------------------------------------------
        */

        const laporanLayer =
            L.layerGroup().addTo(map);

        const fasilitasLayer =
            L.layerGroup();


        /*
        |--------------------------------------------------------------------------
        | STATE
        |--------------------------------------------------------------------------
        */

        let laporanData = [];

        let fasilitasData = [];

        let userMarker = null;

        let userAccuracyCircle = null;

        let currentPosition = null;

        let watchId = null;

        let nearbyReports = [];

        let lastNearbySignature = '';


        /*
        |--------------------------------------------------------------------------
        | HELPER
        |--------------------------------------------------------------------------
        */

        function parseJson(value, fallback) {

            if (!value) {
                return fallback;
            }

            try {

                return JSON.parse(value);

            } catch (error) {

                console.error(
                    'SmartPath: data konfigurasi tidak valid.',
                    error
                );

                return fallback;
            }
        }


        function setText(element, text) {

            if (element) {
                element.textContent = text;
            }
        }


        function escapeHtml(value) {

            const element =
                document.createElement('div');

            element.textContent =
                value == null
                    ? ''
                    : String(value);

            return element.innerHTML;
        }


        /*
        |--------------------------------------------------------------------------
        | VALIDASI AREA DEPOK
        |--------------------------------------------------------------------------
        */

        function isInsidePilotArea(
            latitude,
            longitude
        ) {

            const bounds =
                CONFIG.pilotBounds;

            return (
                Number.isFinite(
                    Number(bounds.min_latitude)
                )

                && Number.isFinite(
                    Number(bounds.max_latitude)
                )

                && Number.isFinite(
                    Number(bounds.min_longitude)
                )

                && Number.isFinite(
                    Number(bounds.max_longitude)
                )

                && latitude >=
                    Number(bounds.min_latitude)

                && latitude <=
                    Number(bounds.max_latitude)

                && longitude >=
                    Number(bounds.min_longitude)

                && longitude <=
                    Number(bounds.max_longitude)
            );
        }


        /*
        |--------------------------------------------------------------------------
        | FORMAT JARAK
        |--------------------------------------------------------------------------
        */

        function formatDistance(meters) {

            if (!Number.isFinite(meters)) {

                return 'Jarak tidak tersedia';
            }

            if (meters < 1) {

                return 'kurang dari 1 meter';
            }

            if (meters >= 1000) {

                return (
                    meters / 1000
                ).toFixed(2) + ' kilometer';
            }

            return Math.round(meters) + ' meter';
        }


        /*
        |--------------------------------------------------------------------------
        | HAVERSINE
        |--------------------------------------------------------------------------
        */

        function calculateDistance(
            lat1,
            lon1,
            lat2,
            lon2
        ) {

            const earthRadius = 6371000;

            const p1 =
                lat1 * Math.PI / 180;

            const p2 =
                lat2 * Math.PI / 180;

            const dp =
                (lat2 - lat1) * Math.PI / 180;

            const dl =
                (lon2 - lon1) * Math.PI / 180;

            const a =
                Math.sin(dp / 2) ** 2

                + Math.cos(p1)
                * Math.cos(p2)
                * Math.sin(dl / 2) ** 2;

            return (
                earthRadius
                * 2
                * Math.atan2(
                    Math.sqrt(a),
                    Math.sqrt(1 - a)
                )
            );
        }


        /*
        |--------------------------------------------------------------------------
        | PRIORITAS
        |--------------------------------------------------------------------------
        */

        function priorityMeta(priority) {

            const value =
                String(
                    priority || ''
                ).toLowerCase();


            if (value === 'tinggi') {

                return {
                    className: 'is-high',
                    label: 'Prioritas Tinggi'
                };
            }


            if (value === 'sedang') {

                return {
                    className: 'is-medium',
                    label: 'Prioritas Sedang'
                };
            }


            if (value === 'rendah') {

                return {
                    className: 'is-low',
                    label: 'Prioritas Rendah'
                };
            }


            return {
                className: 'is-unknown',
                label: 'Belum Dinilai'
            };
        }


        /*
        |--------------------------------------------------------------------------
        | ICON LAPORAN
        |--------------------------------------------------------------------------
        */

        function markerIcon(priority) {

            const meta =
                priorityMeta(priority);


            const colors = {

                'is-high':
                    '#dc2626',

                'is-medium':
                    '#d97706',

                'is-low':
                    '#16a34a',

                'is-unknown':
                    '#64748b'
            };


            const color =
                colors[
                    meta.className
                ] || colors['is-unknown'];


            return L.divIcon({

                className:
                    'warga-report-marker',

                html:
                    '<span class="warga-report-marker__dot" style="background:' +
                    color +
                    '"></span>',

                iconSize:
                    [22, 22],

                iconAnchor:
                    [11, 11],

                popupAnchor:
                    [0, -11]
            });
        }


        /*
        |--------------------------------------------------------------------------
        | ICON FASILITAS
        |--------------------------------------------------------------------------
        */

        function facilityIcon() {

            return L.divIcon({

                className:
                    'warga-facility-marker',

                html:
                    '<span class="warga-facility-marker__dot">' +
                    '<i class="fa-solid fa-building" aria-hidden="true"></i>' +
                    '</span>',

                iconSize:
                    [28, 28],

                iconAnchor:
                    [14, 14]
            });
        }


        /*
        |--------------------------------------------------------------------------
        | RENDER LAPORAN
        |--------------------------------------------------------------------------
        */

        function renderReports(data) {

            laporanLayer.clearLayers();


            data.forEach(function (item) {

                const lat =
                    Number(item.latitude);

                const lng =
                    Number(item.longitude);


                if (
                    !Number.isFinite(lat)
                    ||
                    !Number.isFinite(lng)
                ) {
                    return;
                }


                const priority =
                    priorityMeta(
                        item.tingkat_prioritas
                    );


                const detailUrl =
                    CONFIG.detailTemplate
                        ? CONFIG.detailTemplate.replace(
                            '__REPORT_ID__',
                            encodeURIComponent(item.id)
                        )
                        : '#';


                const popup = `

                    <article
                        class="warga-popup"
                        aria-label="${escapeHtml(
                            item.judul ||
                            'Laporan aksesibilitas'
                        )}"
                    >

                        <h3>
                            ${escapeHtml(
                                item.judul ||
                                'Laporan aksesibilitas'
                            )}
                        </h3>

                        <p>
                            ${escapeHtml(
                                item.kategori ||
                                'Kategori tidak tersedia'
                            )}
                        </p>

                        <p>
                            ${escapeHtml(
                                item.alamat_lengkap ||
                                'Alamat tidak tersedia'
                            )}
                        </p>

                        <div class="warga-popup__meta">

                            <span>
                                ${escapeHtml(
                                    priority.label
                                )}
                            </span>

                            <span>
                                ${escapeHtml(
                                    item.status_label ||
                                    item.status ||
                                    'Terverifikasi'
                                )}
                            </span>

                        </div>

                        <a href="${detailUrl}">
                            Lihat detail laporan
                        </a>

                    </article>
                `;


                L.marker(
                    [lat, lng],
                    {
                        icon:
                            markerIcon(
                                item.tingkat_prioritas
                            )
                    }
                )
                .bindPopup(
                    popup,
                    {
                        maxWidth: 320
                    }
                )
                .addTo(
                    laporanLayer
                );
            });
        }


        /*
        |--------------------------------------------------------------------------
        | RENDER FASILITAS
        |--------------------------------------------------------------------------
        */

        function renderFacilities(data) {

            fasilitasLayer.clearLayers();


            data.forEach(function (item) {

                const lat =
                    Number(item.latitude);

                const lng =
                    Number(item.longitude);


                if (
                    !Number.isFinite(lat)
                    ||
                    !Number.isFinite(lng)
                ) {
                    return;
                }


                const popup = `

                    <article
                        class="warga-popup"
                        aria-label="${escapeHtml(
                            item.nama ||
                            'Fasilitas publik'
                        )}"
                    >

                        <h3>
                            ${escapeHtml(
                                item.nama ||
                                'Fasilitas publik'
                            )}
                        </h3>

                        <p>
                            ${escapeHtml(
                                item.jenis_label ||
                                item.jenis ||
                                'Fasilitas publik'
                            )}
                        </p>

                        <p>
                            ${escapeHtml(
                                item.alamat ||
                                'Alamat tidak tersedia'
                            )}
                        </p>

                    </article>
                `;


                L.marker(
                    [lat, lng],
                    {
                        icon:
                            facilityIcon()
                    }
                )
                .bindPopup(
                    popup,
                    {
                        maxWidth: 300
                    }
                )
                .addTo(
                    fasilitasLayer
                );
            });
        }


        /*
        |--------------------------------------------------------------------------
        | RENDER NEARBY
        |--------------------------------------------------------------------------
        */

        function renderNearby(
            reports,
            announce
        ) {

            nearbyReports =
                reports.slice();


            if (!nearbyList) {
                return;
            }


            if (!reports.length) {

                nearbyList.innerHTML = `

                    <div
                        class="warga-nearby-empty"
                        role="status"
                    >

                        <span
                            class="warga-nearby-empty__icon"
                            aria-hidden="true"
                        >
                            <i class="fa-solid fa-circle-check"></i>
                        </span>

                        <div>

                            <h3>
                                Tidak ada hambatan terverifikasi
                                dalam radius 50 meter
                            </h3>

                            <p>
                                Belum ada laporan aksesibilitas
                                terverifikasi yang terdeteksi di
                                sekitar lokasi Anda.
                            </p>

                        </div>

                    </div>
                `;


                setText(
                    nearbySummary,
                    '0 hambatan dalam radius 50 meter'
                );


                if (btnReadNearby) {

                    btnReadNearby.disabled =
                        false;
                }


                if (announce) {

                    announceNearby(
                        'Tidak ditemukan hambatan aksesibilitas terverifikasi dalam radius 50 meter dari lokasi Anda.'
                    );
                }

                return;
            }


            nearbyList.innerHTML =
                reports
                    .map(function (item, index) {

                        const priority =
                            priorityMeta(
                                item.tingkat_prioritas
                            );


                        const title =
                            item.judul ||
                            'Hambatan aksesibilitas';


                        const category =
                            item.kategori ||
                            'Kategori tidak tersedia';


                        const address =
                            item.alamat_lengkap ||
                            'Alamat tidak tersedia';


                        const distance =
                            formatDistance(
                                item.distance
                            );


                        return `

                            <article
                                class="warga-nearby-item"
                                tabindex="0"
                                aria-labelledby="warga-nearby-title-${index}"
                            >

                                <div
                                    class="warga-nearby-item__icon"
                                    aria-hidden="true"
                                >
                                    <i class="fa-solid fa-triangle-exclamation"></i>
                                </div>


                                <div class="warga-nearby-item__body">

                                    <div class="warga-nearby-item__top">

                                        <h3
                                            id="warga-nearby-title-${index}"
                                        >
                                            ${escapeHtml(title)}
                                        </h3>


                                        <span
                                            class="warga-priority-badge ${priority.className}"
                                        >
                                            ${escapeHtml(
                                                priority.label
                                            )}
                                        </span>

                                    </div>


                                    <p class="warga-nearby-item__category">
                                        Kategori:
                                        ${escapeHtml(category)}
                                    </p>


                                    <p class="warga-nearby-item__address">
                                        ${escapeHtml(address)}
                                    </p>


                                    <p class="warga-nearby-item__distance">

                                        <i
                                            class="fa-solid fa-location-dot"
                                            aria-hidden="true"
                                        ></i>

                                        ${escapeHtml(distance)}
                                        dari lokasi Anda

                                    </p>

                                </div>

                            </article>
                        `;
                    })
                    .join('');


            setText(
                nearbySummary,
                reports.length +
                ' hambatan dalam radius 50 meter'
            );


            if (btnReadNearby) {

                btnReadNearby.disabled =
                    false;
            }


            if (announce) {

                const nearest =
                    reports[0];


                announceNearby(

                    'Ditemukan ' +
                    reports.length +
                    ' hambatan aksesibilitas terverifikasi dalam radius 50 meter. ' +

                    'Hambatan terdekat adalah ' +

                    (
                        nearest.judul ||
                        'hambatan aksesibilitas'
                    ) +

                    ', berjarak ' +

                    formatDistance(
                        nearest.distance
                    ) +

                    ' dari lokasi Anda.'
                );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | LIVE REGION
        |--------------------------------------------------------------------------
        */

        function announceNearby(message) {

            if (nearbyStatus) {

                nearbyStatus.textContent =
                    message;
            }
        }


        /*
        |--------------------------------------------------------------------------
        | UPDATE NEARBY
        |--------------------------------------------------------------------------
        */

        function updateNearby(
            latitude,
            longitude,
            announce
        ) {

            const reports =
                laporanData

                    .map(function (item) {

                        const lat =
                            Number(
                                item.latitude
                            );

                        const lng =
                            Number(
                                item.longitude
                            );


                        if (
                            !Number.isFinite(lat)
                            ||
                            !Number.isFinite(lng)
                        ) {
                            return null;
                        }


                        return {
                            ...item,

                            distance:
                                calculateDistance(
                                    latitude,
                                    longitude,
                                    lat,
                                    lng
                                )
                        };
                    })

                    .filter(Boolean)

                    .filter(function (item) {

                        return (
                            item.distance
                            <=
                            CONFIG.nearbyRadius
                        );
                    })

                    .sort(function (a, b) {

                        return (
                            a.distance
                            -
                            b.distance
                        );
                    });


            const signature =
                reports
                    .map(function (item) {

                        return (
                            item.id +
                            ':' +
                            Math.round(
                                item.distance / 5
                            ) * 5
                        );
                    })
                    .join('|');


            const changed =
                signature !==
                lastNearbySignature;


            if (
                changed
                ||
                !lastNearbySignature
            ) {

                renderNearby(
                    reports,
                    announce
                );
            }


            lastNearbySignature =
                signature;
        }


        /*
        |--------------------------------------------------------------------------
        | UPDATE POSISI USER
        |--------------------------------------------------------------------------
        */

        function updateUserPosition(
            position,
            announce
        ) {

            const latitude =
                Number(
                    position.coords.latitude
                );

            const longitude =
                Number(
                    position.coords.longitude
                );

            const accuracy =
                Number(
                    position.coords.accuracy || 0
                );


            const hadPreviousPosition =
                Boolean(
                    currentPosition
                );


            currentPosition = {

                latitude,

                longitude,

                accuracy
            };


            if (userMarker) {

                map.removeLayer(
                    userMarker
                );
            }


            if (userAccuracyCircle) {

                map.removeLayer(
                    userAccuracyCircle
                );
            }


            userAccuracyCircle =
                L.circle(
                    [
                        latitude,
                        longitude
                    ],
                    {
                        radius:
                            Math.max(
                                accuracy,
                                1
                            ),

                        color:
                            '#059669',

                        fillColor:
                            '#10b981',

                        fillOpacity:
                            0.10,

                        weight:
                            2
                    }
                )
                .addTo(map);


            userMarker =
                L.circleMarker(
                    [
                        latitude,
                        longitude
                    ],
                    {
                        radius:
                            8,

                        color:
                            '#ffffff',

                        weight:
                            3,

                        fillColor:
                            '#059669',

                        fillOpacity:
                            1
                    }
                )
                .addTo(map)
                .bindPopup(
                    'Lokasi Anda'
                );


            if (!hadPreviousPosition) {

                map.setView(
                    [
                        latitude,
                        longitude
                    ],
                    16
                );
            }


            /*
            |--------------------------------------------------------------------------
            | AREA PILOT
            |--------------------------------------------------------------------------
            */

            if (
                !isInsidePilotArea(
                    latitude,
                    longitude
                )
            ) {

                setText(
                    mapStatus,
                    'Lokasi terdeteksi di luar area uji coba Kota Depok. Nearby tidak dijalankan.'
                );


                announceNearby(
                    'Lokasi Anda berada di luar area uji coba Kota Depok. Data Nearby tidak ditampilkan.'
                );


                if (nearbyList) {

                    nearbyList.innerHTML = `

                        <div
                            class="warga-nearby-empty"
                            role="alert"
                        >

                            <span
                                class="warga-nearby-empty__icon is-warning"
                                aria-hidden="true"
                            >
                                <i class="fa-solid fa-location-dot"></i>
                            </span>

                            <div>

                                <h3>
                                    Lokasi berada di luar area uji coba
                                </h3>

                                <p>
                                    SmartPath pada tahap MVP berfokus
                                    pada Kota Depok. Gunakan lokasi
                                    di dalam area uji coba untuk
                                    melihat hambatan terdekat.
                                </p>

                            </div>

                        </div>
                    `;
                }


                if (btnReadNearby) {

                    btnReadNearby.disabled =
                        true;
                }


                lastNearbySignature =
                    '';


                setText(
                    nearbySummary,
                    'Di luar area uji coba'
                );


                return;
            }


            /*
            |--------------------------------------------------------------------------
            | AKURASI
            |--------------------------------------------------------------------------
            */

            let accuracyMessage =
                'Akurasi GPS sekitar ' +
                Math.round(
                    accuracy
                ) +
                ' meter.';


            if (
                accuracy >
                CONFIG.manualAccuracy
            ) {

                accuracyMessage +=
                    ' Akurasi rendah; periksa lokasi secara manual sebelum mengandalkan hasil Nearby.';

            } else if (
                accuracy >
                CONFIG.warningAccuracy
            ) {

                accuracyMessage +=
                    ' Akurasi cukup terbatas.';
            }


            setText(
                mapStatus,
                'Lokasi Anda ditemukan. ' +
                accuracyMessage
            );


            updateNearby(
                latitude,
                longitude,
                announce
            );
        }


        /*
        |--------------------------------------------------------------------------
        | ERROR GPS
        |--------------------------------------------------------------------------
        */

        function locationError(error) {

            let message =
                'Lokasi tidak dapat diperoleh.';


            if (
                error &&
                error.code === 1
            ) {

                message =
                    'Izin lokasi ditolak. Izinkan akses lokasi pada browser untuk menggunakan Nearby.';

            } else if (
                error &&
                error.code === 2
            ) {

                message =
                    'Lokasi tidak tersedia. Pastikan layanan lokasi perangkat aktif.';

            } else if (
                error &&
                error.code === 3
            ) {

                message =
                    'Waktu mendapatkan lokasi habis. Silakan coba lagi.';
            }


            setText(
                mapStatus,
                message
            );


            announceNearby(
                message
            );


            if (btnLocation) {

                btnLocation.disabled =
                    false;

                btnLocation.setAttribute(
                    'aria-busy',
                    'false'
                );

                btnLocation.innerHTML =
                    '<i class="fa-solid fa-location-crosshairs" aria-hidden="true"></i>' +
                    '<span>Gunakan Lokasi Saya</span>';
            }
        }


        /*
        |--------------------------------------------------------------------------
        | GPS TRACKING
        |--------------------------------------------------------------------------
        */

        function startLocationTracking() {

            if (
                !navigator.geolocation
            ) {

                locationError({
                    code: 2
                });

                return;
            }


            if (
                watchId !== null
            ) {

                return;
            }


            btnLocation.disabled =
                true;


            btnLocation.setAttribute(
                'aria-busy',
                'true'
            );


            btnLocation.innerHTML =
                '<i class="fa-solid fa-spinner fa-spin" aria-hidden="true"></i>' +
                '<span>Mencari lokasi...</span>';


            setText(
                mapStatus,
                'Sedang mendeteksi lokasi Anda...'
            );


            announceNearby(
                'Sedang mengakses lokasi perangkat Anda.'
            );


            navigator.geolocation.getCurrentPosition(

                function (position) {

                    updateUserPosition(
                        position,
                        true
                    );


                    btnLocation.disabled =
                        false;


                    btnLocation.setAttribute(
                        'aria-busy',
                        'false'
                    );


                    btnLocation.innerHTML =
                        '<i class="fa-solid fa-location-crosshairs" aria-hidden="true"></i>' +
                        '<span>Perbarui Lokasi</span>';


                    watchId =
                        navigator.geolocation.watchPosition(

                            function (nextPosition) {

                                updateUserPosition(
                                    nextPosition,
                                    true
                                );
                            },


                            function (error) {

                                console.error(
                                    'SmartPath GPS watch error:',
                                    error
                                );


                                setText(
                                    mapStatus,
                                    'Pembaruan lokasi berhenti. ' +
                                    (
                                        error.code === 1
                                            ? 'Izin lokasi ditolak.'
                                            : 'Lokasi tidak dapat diperbarui.'
                                    )
                                );
                            },


                            {
                                enableHighAccuracy:
                                    true,

                                timeout:
                                    CONFIG.gpsTimeout,

                                maximumAge:
                                    5000
                            }
                        );
                },


                locationError,


                {
                    enableHighAccuracy:
                        true,

                    timeout:
                        CONFIG.gpsTimeout,

                    maximumAge:
                        0
                }
            );
        }


        /*
        |--------------------------------------------------------------------------
        | STOP GPS
        |--------------------------------------------------------------------------
        */

        function stopLocationTracking() {

            if (
                watchId !== null
                &&
                navigator.geolocation
            ) {

                navigator.geolocation.clearWatch(
                    watchId
                );

                watchId =
                    null;
            }
        }


        /*
        |--------------------------------------------------------------------------
        | LOAD DATA PETA
        |--------------------------------------------------------------------------
        */

        function loadMapData() {

            const laporanRequest =

                fetch(
                    CONFIG.laporanUrl
                )
                .then(function (response) {

                    if (!response.ok) {

                        throw new Error(
                            'Data laporan tidak dapat dimuat.'
                        );
                    }

                    return response.json();
                });


            const fasilitasRequest =

                fetch(
                    CONFIG.fasilitasUrl
                )
                .then(function (response) {

                    if (!response.ok) {

                        throw new Error(
                            'Data fasilitas tidak dapat dimuat.'
                        );
                    }

                    return response.json();
                });


            Promise.allSettled(
                [
                    laporanRequest,
                    fasilitasRequest
                ]
            )
            .then(function (results) {

                const laporanResult =
                    results[0];

                const fasilitasResult =
                    results[1];


                if (
                    laporanResult.status ===
                    'fulfilled'
                ) {

                    laporanData =
                        Array.isArray(
                            laporanResult.value
                        )
                            ? laporanResult.value
                            : [];


                    renderReports(
                        laporanData
                    );


                    setText(
                        mapStatus,
                        'Peta aksesibilitas siap digunakan. Data yang ditampilkan berasal dari laporan terverifikasi.'
                    );

                } else {

                    console.error(
                        'SmartPath laporan:',
                        laporanResult.reason
                    );


                    setText(
                        mapStatus,
                        'Data laporan belum dapat dimuat.'
                    );
                }


                if (
                    fasilitasResult.status ===
                    'fulfilled'
                ) {

                    fasilitasData =
                        Array.isArray(
                            fasilitasResult.value
                        )
                            ? fasilitasResult.value
                            : [];


                    renderFacilities(
                        fasilitasData
                    );

                } else {

                    console.error(
                        'SmartPath fasilitas:',
                        fasilitasResult.reason
                    );
                }
            });
        }


        /*
        |--------------------------------------------------------------------------
        | VOICE
        |--------------------------------------------------------------------------
        */

        function chooseIndonesianVoice() {

            if (
                !('speechSynthesis' in window)
            ) {

                return null;
            }


            const voices =
                window
                    .speechSynthesis
                    .getVoices();


            return (

                voices.find(
                    function (voice) {

                        return /^id(-|_)?ID$/i.test(
                            voice.lang
                        );
                    }
                )

                ||

                voices.find(
                    function (voice) {

                        return /^id/i.test(
                            voice.lang
                        );
                    }
                )

                ||

                null
            );
        }


        function speak(
            text,
            button,
            defaultLabel
        ) {

            if (
                !('speechSynthesis' in window)
            ) {

                setText(
                    mapStatus,
                    'Browser Anda tidak mendukung fitur Dengar Panduan.'
                );

                return;
            }


            if (
                window.speechSynthesis.speaking
            ) {

                window.speechSynthesis.cancel();


                if (button) {

                    const span =
                        button.querySelector(
                            'span'
                        );


                    if (span) {

                        span.textContent =
                            defaultLabel;
                    }


                    button.setAttribute(
                        'aria-pressed',
                        'false'
                    );
                }


                return;
            }


            const utterance =
                new SpeechSynthesisUtterance(
                    text
                );


            utterance.lang =
                'id-ID';

            utterance.rate =
                0.9;

            utterance.pitch =
                1;

            utterance.volume =
                1;


            const voice =
                chooseIndonesianVoice();


            if (voice) {

                utterance.voice =
                    voice;
            }


            if (button) {

                const span =
                    button.querySelector(
                        'span'
                    );


                if (span) {

                    span.textContent =
                        'Berhenti';
                }


                button.setAttribute(
                    'aria-pressed',
                    'true'
                );
            }


            utterance.onend =
                function () {

                    if (button) {

                        const span =
                            button.querySelector(
                                'span'
                            );


                        if (span) {

                            span.textContent =
                                defaultLabel;
                        }


                        button.setAttribute(
                            'aria-pressed',
                            'false'
                        );
                    }
                };


            utterance.onerror =
                function () {

                    if (button) {

                        const span =
                            button.querySelector(
                                'span'
                            );


                        if (span) {

                            span.textContent =
                                defaultLabel;
                        }


                        button.setAttribute(
                            'aria-pressed',
                            'false'
                        );
                    }


                    setText(
                        mapStatus,
                        'Fitur Dengar Panduan tidak dapat dijalankan pada browser ini.'
                    );
                };


            window.speechSynthesis.cancel();

            window.speechSynthesis.speak(
                utterance
            );
        }


        /*
        |--------------------------------------------------------------------------
        | NARASI DASHBOARD
        |--------------------------------------------------------------------------
        */

        function dashboardNarration() {

            const name =
                root.dataset.userName ||
                'Warga';


            const count =
                root.dataset.reportCount ||
                '0';


            return (

                'Selamat datang di Dashboard Warga SmartPath, ' +

                name +

                '. Di halaman ini Anda dapat membuat laporan hambatan aksesibilitas, melihat ' +

                count +

                ' laporan yang Anda buat, melihat peta aksesibilitas, dan mengetahui hambatan terverifikasi di sekitar lokasi Anda. ' +

                'Fitur Nearby menampilkan hambatan dalam radius 50 meter setelah Anda mengizinkan akses lokasi. ' +

                'Gunakan tombol Navigasi Aktif untuk membuka halaman navigasi. ' +

                'Mode Eksplorasi Pasif digunakan untuk mengetahui hambatan terdekat, sedangkan Navigasi Aktif tersedia pada halaman navigasi.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | NARASI NEARBY
        |--------------------------------------------------------------------------
        */

        function nearbyNarration() {

            if (
                !nearbyReports.length
            ) {

                return (
                    'Tidak ada hambatan aksesibilitas terverifikasi dalam radius 50 meter dari lokasi Anda.'
                );
            }


            return nearbyReports

                .map(
                    function (item, index) {

                        const priority =
                            priorityMeta(
                                item.tingkat_prioritas
                            );


                        return (

                            'Hambatan ' +

                            (index + 1) +

                            ': ' +

                            (
                                item.judul ||
                                'hambatan aksesibilitas'
                            ) +

                            '. Kategori ' +

                            (
                                item.kategori ||
                                'tidak tersedia'
                            ) +

                            '. ' +

                            priority.label +

                            '. Berjarak ' +

                            formatDistance(
                                item.distance
                            ) +

                            ' dari lokasi Anda.'
                        );
                    }
                )

                .join(' ');
        }


        /*
        |--------------------------------------------------------------------------
        | EVENT
        |--------------------------------------------------------------------------
        */

        if (btnLocation) {

            btnLocation.addEventListener(
                'click',
                startLocationTracking
            );
        }


        if (btnFacilities) {

            btnFacilities.addEventListener(
                'click',
                function () {

                    const active =
                        map.hasLayer(
                            fasilitasLayer
                        );


                    if (active) {

                        map.removeLayer(
                            fasilitasLayer
                        );


                        btnFacilities.setAttribute(
                            'aria-pressed',
                            'false'
                        );


                        btnFacilities.classList.remove(
                            'is-active'
                        );


                        setText(
                            mapStatus,
                            'Lapisan fasilitas publik disembunyikan.'
                        );

                    } else {

                        fasilitasLayer.addTo(
                            map
                        );


                        btnFacilities.setAttribute(
                            'aria-pressed',
                            'true'
                        );


                        btnFacilities.classList.add(
                            'is-active'
                        );


                        setText(
                            mapStatus,
                            'Lapisan fasilitas publik ditampilkan.'
                        );
                    }
                }
            );
        }


        if (btnReadPage) {

            btnReadPage.addEventListener(
                'click',
                function () {

                    speak(
                        dashboardNarration(),
                        btnReadPage,
                        'Dengar Panduan'
                    );
                }
            );
        }


        if (btnReadNearby) {

            btnReadNearby.addEventListener(
                'click',
                function () {

                    speak(
                        nearbyNarration(),
                        btnReadNearby,
                        'Dengar Hambatan Terdekat'
                    );
                }
            );
        }


        /*
        |--------------------------------------------------------------------------
        | RESPONSIVE MAP
        |--------------------------------------------------------------------------
        */

        window.addEventListener(
            'resize',
            function () {

                window.setTimeout(
                    function () {

                        map.invalidateSize();

                    },
                    150
                );
            }
        );


        window.setTimeout(
            function () {

                map.invalidateSize();

            },
            250
        );


        /*
        |--------------------------------------------------------------------------
        | CLEANUP
        |--------------------------------------------------------------------------
        */

        window.addEventListener(
            'beforeunload',
            stopLocationTracking
        );


        /*
        |--------------------------------------------------------------------------
        | START
        |--------------------------------------------------------------------------
        */

        loadMapData();

    });
})();