document.addEventListener('DOMContentLoaded', function () {
    'use strict';

    const form =
        document.getElementById('laporan-form');

    const mapElement =
        document.getElementById('location-map');

    const latInput =
        document.getElementById('latitude');

    const lngInput =
        document.getElementById('longitude');

    const addressInput =
        document.getElementById('alamat_lengkap');

    const sourceInput =
        document.getElementById('sumber_koordinat');

    const locationStatus =
        document.getElementById('location-status');

    const locateButton =
        document.getElementById('btn-locate');

    const dropZone =
        document.getElementById('drop-zone');

    const fotoInput =
        document.getElementById('foto-input');

    const preview =
        document.getElementById('foto-preview');

    const submitButton =
        document.getElementById('submit-laporan');

    const descriptionInput =
        document.getElementById('deskripsi');

    const descriptionCounter =
        document.getElementById('deskripsi-counter');


    /*
    |--------------------------------------------------------------------------
    | CATATAN ARSITEKTUR
    |--------------------------------------------------------------------------
    |
    | Kecamatan / wilayah_id TIDAK lagi dikelola oleh frontend.
    |
    | Browser hanya bertugas:
    |
    | 1. mendapatkan latitude;
    | 2. mendapatkan longitude;
    | 3. memungkinkan user menentukan titik melalui peta;
    | 4. mengisi alamat berdasarkan koordinat;
    | 5. mengirim data laporan.
    |
    | Penentuan wilayah_id dilakukan oleh backend berdasarkan koordinat.
    |
    | Jangan menambahkan kembali:
    |
    | - wilayahSelect
    | - getNearestWilayah()
    | - validateWilayahLocation()
    | - haversine()
    |
    | karena Kecamatan bukan bagian dari UI Create Laporan.
    |
    */


    /*
    |--------------------------------------------------------------------------
    | Validasi element halaman
    |--------------------------------------------------------------------------
    */

    if (
        !form ||
        !mapElement ||
        !latInput ||
        !lngInput ||
        !sourceInput ||
        !locationStatus ||
        !locateButton ||
        !dropZone ||
        !fotoInput ||
        !preview
    ) {
        console.error(
            'SmartPath: elemen halaman laporan tidak lengkap.'
        );

        return;
    }


    /*
    |--------------------------------------------------------------------------
    | Validasi Leaflet
    |--------------------------------------------------------------------------
    */

    if (typeof L === 'undefined') {

        locationStatus.textContent =
            'Peta gagal dimuat. Silakan muat ulang halaman atau pilih lokasi secara manual setelah koneksi tersedia.';

        locationStatus.className =
            'sp-location-status is-error';

        return;
    }


    /*
    |--------------------------------------------------------------------------
    | Konfigurasi
    |--------------------------------------------------------------------------
    */

    const DEFAULT_CENTER = [
        -6.4025,
        106.8197
    ];

    const DEFAULT_ZOOM = 14;

    const MAX_PHOTO_BYTES =
        5 * 1024 * 1024;

    const MAX_PHOTO =
        Math.max(
            1,
            Math.min(
                Number(
                    fotoInput.dataset.maxFoto || 5
                ),
                10
            )
        );


    /*
     * Akurasi GPS tidak disimpan ke database.
     *
     * Nilai ini hanya digunakan untuk membantu user
     * menentukan apakah titik GPS cukup presisi.
     */

    const LOCATION_WARNING_ACCURACY = 100;

    const LOCATION_MANUAL_RECOMMENDED = 50;

    const GPS_WATCH_TIMEOUT = 15000;


        /*
    |--------------------------------------------------------------------------
    | Wilayah Pilot SmartPath
    |--------------------------------------------------------------------------
    |
    | MVP SmartPath berfokus pada Kota Depok.
    |
    | Geographic envelope digunakan sebagai sanity check.
    | Ini bukan polygon batas administrasi final.
    |
    */

    let PILOT_BOUNDS = null;

    try {
        PILOT_BOUNDS =
            JSON.parse(
                mapElement.dataset.pilotBounds || '{}'
            );
    } catch (error) {
        console.error(
            'SmartPath: konfigurasi batas wilayah tidak valid.',
            error
        );
    }

    const PILOT_CENTER = (() => {
        try {
            return JSON.parse(
                mapElement.dataset.pilotCenter || '{}'
            );
        } catch (error) {
            return {
                latitude: -6.4025,
                longitude: 106.7942
            };
        }
    })();

    function isInsidePilotArea(
        latitude,
        longitude
    ) {
        if (!PILOT_BOUNDS) {
            return false;
        }

        return (
            latitude >=
                Number(PILOT_BOUNDS.min_latitude)

            && latitude <=
                Number(PILOT_BOUNDS.max_latitude)

            && longitude >=
                Number(PILOT_BOUNDS.min_longitude)

            && longitude <=
                Number(PILOT_BOUNDS.max_longitude)
        );
    }

    function resetToPilotArea() {
        clearAccuracyCircle();

        if (marker) {
            map.removeLayer(marker);
            marker = null;
        }

        latInput.value = '';
        lngInput.value = '';

        setSource('manual');

        map.setView(
            [
                Number(PILOT_CENTER.latitude),
                Number(PILOT_CENTER.longitude)
            ],
            DEFAULT_ZOOM,
            {
                animate: true
            }
        );
    }


    /*
    |--------------------------------------------------------------------------
    | State
    |--------------------------------------------------------------------------
    */

    let marker = null;

    let accuracyCircle = null;

    let bestGpsPosition = null;

    let gpsWatchId = null;

    let gpsWatchTimer = null;

    let geocodeController = null;

    let geocodeTimer = null;

    let selectedFiles = [];

    let addressWasAutoFilled = false;

    let gpsFinished = false;

    let isSubmitting = false;


    /*
    |--------------------------------------------------------------------------
    | Map
    |--------------------------------------------------------------------------
    */

    const map =
        L.map(
            mapElement,
            {
                center: DEFAULT_CENTER,
                zoom: DEFAULT_ZOOM,
                zoomControl: true,
                attributionControl: true
            }
        );


    L.tileLayer(
        'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
        {
            attribution:
                '&copy; OpenStreetMap contributors',

            maxZoom: 19,

            detectRetina: true
        }
    ).addTo(map);


    /*
    |--------------------------------------------------------------------------
    | Status lokasi
    |--------------------------------------------------------------------------
    */

    function setLocationStatus(
        message,
        type = 'neutral'
    ) {

        locationStatus.textContent =
            message;

        locationStatus.className =
            `sp-location-status is-${type}`;
    }


    /*
    |--------------------------------------------------------------------------
    | Accuracy circle
    |--------------------------------------------------------------------------
    */

    function clearAccuracyCircle() {

        if (accuracyCircle) {

            map.removeLayer(
                accuracyCircle
            );

            accuracyCircle =
                null;
        }
    }


    function renderAccuracyCircle(
        latitude,
        longitude,
        accuracy
    ) {

        clearAccuracyCircle();


        if (
            !Number.isFinite(
                accuracy
            ) ||
            accuracy <= 0 ||
            accuracy > 1000
        ) {
            return;
        }


        accuracyCircle =
            L.circle(
                [
                    latitude,
                    longitude
                ],
                {
                    radius: accuracy,

                    color: '#059669',

                    weight: 1,

                    fillColor: '#10b981',

                    fillOpacity: 0.08,

                    interactive: false
                }
            ).addTo(map);
    }


    /*
    |--------------------------------------------------------------------------
    | Source koordinat
    |--------------------------------------------------------------------------
    */

    function setSource(
        source
    ) {

        sourceInput.value =
            source;
    }


    /*
    |--------------------------------------------------------------------------
    | Reverse geocoding
    |--------------------------------------------------------------------------
    |
    | Reverse geocoding hanya digunakan untuk membantu mengisi alamat.
    |
    | Koordinat tetap menjadi sumber utama lokasi laporan.
    |
    */

    function scheduleReverseGeocode(
        latitude,
        longitude
    ) {

        if (!addressInput) {
            return;
        }


        window.clearTimeout(
            geocodeTimer
        );


        geocodeTimer =
            window.setTimeout(
                async function () {

                    if (geocodeController) {

                        geocodeController.abort();
                    }


                    geocodeController =
                        new AbortController();


                    try {

                        const url =
                            new URL(
                                'https://nominatim.openstreetmap.org/reverse'
                            );


                        url.searchParams.set(
                            'lat',
                            latitude.toFixed(7)
                        );


                        url.searchParams.set(
                            'lon',
                            longitude.toFixed(7)
                        );


                        url.searchParams.set(
                            'format',
                            'jsonv2'
                        );


                        url.searchParams.set(
                            'accept-language',
                            'id'
                        );


                        url.searchParams.set(
                            'zoom',
                            '18'
                        );


                        const response =
                            await fetch(
                                url.toString(),
                                {
                                    method: 'GET',

                                    headers: {
                                        Accept:
                                            'application/json'
                                    },

                                    signal:
                                        geocodeController.signal
                                }
                            );


                        if (!response.ok) {

                            throw new Error(
                                'Reverse geocoding failed.'
                            );
                        }


                        const data =
                            await response.json();


                        /*
                         * Jangan menimpa alamat manual user.
                         */

                        if (
                            data.display_name &&
                            (
                                !addressInput.value.trim()
                                ||
                                addressWasAutoFilled
                            )
                        ) {

                            addressInput.value =
                                data.display_name;

                            addressWasAutoFilled =
                                true;
                        }

                    } catch (error) {

                        /*
                         * Reverse geocoding adalah fitur tambahan.
                         *
                         * Jika gagal, laporan tetap dapat dibuat.
                         */

                        if (
                            error.name !==
                            'AbortError'
                        ) {
                            // Sengaja tidak menampilkan error.
                        }
                    }

                },
                650
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Marker
    |--------------------------------------------------------------------------
    */

    function placeMarker(
        latitude,
        longitude,
        source = 'manual',
        zoom = 17,
        accuracy = null
    ) {

        if (
            !Number.isFinite(
                latitude
            ) ||
            !Number.isFinite(
                longitude
            )
        ) {
            return false;
        }


        if (marker) {

            map.removeLayer(
                marker
            );
        }


        marker =
            L.marker(
                [
                    latitude,
                    longitude
                ],
                {
                    draggable: true,

                    title:
                        source ===
                        'gps_otomatis'
                            ? 'Lokasi GPS perangkat'
                            : 'Lokasi laporan'
                }
            ).addTo(map);


        /*
         * Accessibility marker.
         */

        if (marker._icon) {

            marker._icon.setAttribute(
                'role',
                'img'
            );

            marker._icon.setAttribute(
                'aria-label',
                source ===
                'gps_otomatis'
                    ? 'Marker lokasi GPS perangkat'
                    : 'Marker lokasi laporan'
            );
        }


        latInput.value =
            latitude.toFixed(7);


        lngInput.value =
            longitude.toFixed(7);


        setSource(
            source
        );


        map.setView(
            [
                latitude,
                longitude
            ],
            zoom,
            {
                animate: true
            }
        );


        renderAccuracyCircle(
            latitude,
            longitude,
            accuracy
        );


        scheduleReverseGeocode(
            latitude,
            longitude
        );


        /*
         * Marker dapat digeser manual.
         */

        marker.off(
            'dragend'
        );


        marker.on(
            'dragend',
            function (event) {

                const position =
                    event.target.getLatLng();

                        if (
                    !isInsidePilotArea(
                        position.lat,
                        position.lng
                    )
                ) {
                    /*
                     * Kembalikan marker ke posisi sebelumnya.
                     */
                    const previousLat =
                        Number.parseFloat(
                            latInput.value
                        );

                    const previousLng =
                        Number.parseFloat(
                            lngInput.value
                        );

                    if (
                        Number.isFinite(previousLat) &&
                        Number.isFinite(previousLng)
                    ) {
                        event.target.setLatLng([
                            previousLat,
                            previousLng
                        ]);
                    }

                    setLocationStatus(
                        'Marker tidak dapat dipindahkan ke luar wilayah uji coba Kota Depok.',
                        'warning'
                    );

                    return;
                }


                latInput.value =
                    position.lat.toFixed(7);


                lngInput.value =
                    position.lng.toFixed(7);


                /*
                 * Setelah marker digeser manual,
                 * GPS watch dihentikan agar hasil GPS berikutnya
                 * tidak mengambil kembali posisi marker.
                 */

                stopGpsWatch();


                locateButton.disabled =
                    false;


                gpsFinished =
                    true;


                bestGpsPosition =
                    null;


                setSource(
                    'manual'
                );


                clearAccuracyCircle();


                scheduleReverseGeocode(
                    position.lat,
                    position.lng
                );


                setLocationStatus(
                    'Lokasi diubah secara manual. Pastikan marker berada tepat di lokasi hambatan.',
                    'success'
                );
            }
        );


        return true;
    }


    /*
    |--------------------------------------------------------------------------
    | Format akurasi
    |--------------------------------------------------------------------------
    */

    function formatAccuracy(
        accuracy
    ) {

        if (
            !Number.isFinite(
                accuracy
            )
        ) {
            return 'akurasi tidak tersedia';
        }


        return `akurasi sekitar ${Math.round(accuracy)} meter`;
    }


    /*
    |--------------------------------------------------------------------------
    | Stop GPS watch
    |--------------------------------------------------------------------------
    */

    function stopGpsWatch() {

        if (
            gpsWatchId !== null
        ) {

            navigator.geolocation.clearWatch(
                gpsWatchId
            );

            gpsWatchId =
                null;
        }


        if (
            gpsWatchTimer !== null
        ) {

            window.clearTimeout(
                gpsWatchTimer
            );

            gpsWatchTimer =
                null;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Finish GPS
    |--------------------------------------------------------------------------
    */

    function finishGps(
        bestPosition
    ) {

        if (gpsFinished) {
            return;
        }


        gpsFinished =
            true;


        stopGpsWatch();


        locateButton.disabled =
            false;


        if (!bestPosition) {

            setLocationStatus(
                'Lokasi perangkat belum dapat diperoleh. Pastikan Location Services Windows aktif atau pilih titik manual pada peta.',
                'error'
            );

            return;
        }


        const latitude =
            bestPosition.coords.latitude;

        const longitude =
            bestPosition.coords.longitude;

                if (
            !isInsidePilotArea(
                Number(latitude),
                Number(longitude)
            )
        ) {
            resetToPilotArea();

            setLocationStatus(
                'Lokasi perangkat terdeteksi di luar wilayah uji coba Kota Depok. Sistem tidak menyimpan lokasi tersebut. Pilih titik laporan secara manual pada peta.',
                'warning'
            );

            return;
        }

        const accuracy =
            Number.isFinite(
                bestPosition.coords.accuracy
            )
                ? bestPosition.coords.accuracy
                : null;


        placeMarker(
            latitude,
            longitude,
            'gps_otomatis',
            17,
            accuracy
        );


        /*
         * Akurasi > 500 meter tidak langsung ditolak.
         *
         * User masih dapat menggeser marker secara manual.
         */

        if (
            Number.isFinite(
                accuracy
            ) &&
            accuracy >
                LOCATION_MANUAL_RECOMMENDED
        ) {

            setLocationStatus(
                `Lokasi perangkat terdeteksi, tetapi ${formatAccuracy(accuracy)}. Untuk laporan yang presisi, geser marker ke titik hambatan atau gunakan perangkat dengan Location Services aktif.`,
                'warning'
            );

            return;
        }


        if (
            Number.isFinite(
                accuracy
            ) &&
            accuracy >
                LOCATION_WARNING_ACCURACY
        ) {

            setLocationStatus(
                `Lokasi GPS berhasil diperoleh dengan ${formatAccuracy(accuracy)}. Periksa posisi marker sebelum mengirim laporan.`,
                'warning'
            );

            return;
        }


        setLocationStatus(
            Number.isFinite(
                accuracy
            )
                ? `Lokasi GPS berhasil diperoleh dengan ${formatAccuracy(accuracy)}.`
                : 'Lokasi GPS berhasil diperoleh secara otomatis.',
            'success'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Gunakan lokasi perangkat
    |--------------------------------------------------------------------------
    */

    function useCurrentLocation() {

        if (
            !navigator.geolocation
        ) {

            setLocationStatus(
                'Browser tidak mendukung Geolocation API. Pilih lokasi secara manual pada peta.',
                'error'
            );

            return;
        }


        stopGpsWatch();


        bestGpsPosition =
            null;


        gpsFinished =
            false;


        locateButton.disabled =
            true;


        setLocationStatus(
            'Meminta izin lokasi perangkat dan mencari posisi paling akurat...',
            'neutral'
        );


        const options = {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 0
        };


        let singleRequestFinished =
            false;


        /*
         * Setiap hasil lokasi dibandingkan.
         *
         * Hasil dengan accuracy terkecil yang dipertahankan.
         */

        const acceptPosition =
            function (position) {

                if (
                    gpsFinished ||
                    !position ||
                    !position.coords
                ) {
                    return;
                }


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
                        position.coords.accuracy
                    );


                if (
                    !Number.isFinite(
                        latitude
                    ) ||
                    !Number.isFinite(
                        longitude
                    )
                ) {
                    return;
                }

                            /*
                 * Jangan menerima hasil lokasi yang secara jelas
                 * berada di luar wilayah pilot SmartPath.
                 *
                 * Ini penting terutama pada desktop tanpa GPS
                 * yang dapat menerima estimasi berbasis IP/Wi-Fi.
                 */
                if (
                    !isInsidePilotArea(
                        latitude,
                        longitude
                    )
                ) {
                    setLocationStatus(
                        'Lokasi perangkat terdeteksi di luar wilayah uji coba Kota Depok. Sistem tidak menggunakan koordinat tersebut. Anda dapat memilih titik laporan secara manual pada peta.',
                        'warning'
                    );

                    return;
                }




                if (!bestGpsPosition) {

                    bestGpsPosition =
                        position;

                } else {

                    const currentBest =
                        Number(
                            bestGpsPosition
                                .coords
                                .accuracy
                        );


                    if (
                        !Number.isFinite(
                            currentBest
                        ) ||
                        (
                            Number.isFinite(
                                accuracy
                            ) &&
                            accuracy <
                                currentBest
                        )
                    ) {

                        bestGpsPosition =
                            position;
                    }
                }


                /*
                 * Jika sudah sangat bagus,
                 * jangan tunggu timeout.
                 */

                if (
                    Number.isFinite(
                        accuracy
                    ) &&
                    accuracy <= 50
                ) {

                    finishGps(
                        bestGpsPosition
                    );

                    return;
                }


                setLocationStatus(
                    `Posisi ditemukan (${formatAccuracy(accuracy)}). Sedang mencari hasil yang lebih presisi...`,
                    'neutral'
                );
            };


        const handleError =
            function (error) {

                singleRequestFinished =
                    true;


                if (
                    error.code ===
                    error.PERMISSION_DENIED
                ) {

                    stopGpsWatch();

                    locateButton.disabled =
                        false;


                    setLocationStatus(
                        'Izin lokasi ditolak. Izinkan Location untuk situs ini dan coba lagi.',
                        'error'
                    );

                    return;
                }


                if (
                    error.code ===
                    error.POSITION_UNAVAILABLE
                ) {

                    setLocationStatus(
                        'Posisi perangkat belum tersedia. Sistem sedang mencoba sumber lokasi lain dari perangkat.',
                        'warning'
                    );

                } else if (
                    error.code ===
                    error.TIMEOUT
                ) {

                    setLocationStatus(
                        'Pencarian lokasi awal melewati batas waktu. Sistem tetap mencoba mendapatkan posisi terbaik.',
                        'warning'
                    );
                }
            };


        /*
         * Request pertama.
         */

        navigator.geolocation.getCurrentPosition(
            function (position) {

                acceptPosition(
                    position
                );

                singleRequestFinished =
                    true;
            },

            handleError,

            options
        );


        /*
         * Request berkelanjutan.
         *
         * Browser dapat memperoleh fix pertama
         * yang belum stabil kemudian memberikan
         * fix yang lebih akurat beberapa detik kemudian.
         */

        gpsWatchId =
            navigator.geolocation.watchPosition(

                acceptPosition,

                function () {
                    /*
                     * Jangan langsung menghentikan watch.
                     * Hasil berikutnya masih dapat tersedia.
                     */
                },

                {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                }
            );


        /*
         * Batas total pencarian.
         */

        gpsWatchTimer =
            window.setTimeout(
                function () {

                    if (gpsFinished) {
                        return;
                    }


                    if (
                        bestGpsPosition
                    ) {

                        finishGps(
                            bestGpsPosition
                        );

                        return;
                    }


                    stopGpsWatch();

                    locateButton.disabled =
                        false;


                    setLocationStatus(
                        singleRequestFinished
                            ?
                            'Lokasi perangkat belum tersedia. Pilih titik manual pada peta agar laporan tetap dapat dibuat.'
                            :
                            'Pengambilan lokasi terlalu lama. Pastikan Location Services aktif lalu coba lagi.',
                        'error'
                    );

                },
                GPS_WATCH_TIMEOUT
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Klik peta = lokasi manual
    |--------------------------------------------------------------------------
    */

    map.on(
        'click',
        function (event) {

            /*
             * Jika user memilih lokasi manual,
             * hentikan proses GPS agar tidak menimpa
             * titik yang baru dipilih.
             */

                    if (
                !isInsidePilotArea(
                    event.latlng.lat,
                    event.latlng.lng
                )
            ) {
                setLocationStatus(
                    'Titik yang dipilih berada di luar wilayah uji coba Kota Depok. Silakan pilih titik di dalam area Depok.',
                    'warning'
                );

                return;
            }

            stopGpsWatch();


            locateButton.disabled =
                false;


            gpsFinished =
                true;


            bestGpsPosition =
                null;


            placeMarker(
                event.latlng.lat,
                event.latlng.lng,
                'manual',
                17,
                null
            );


            setLocationStatus(
                'Lokasi dipilih secara manual. Pastikan marker berada tepat di lokasi hambatan.',
                'success'
            );
        }
    );


    /*
    |--------------------------------------------------------------------------
    | Tombol GPS
    |--------------------------------------------------------------------------
    */

    locateButton.addEventListener(
        'click',
        useCurrentLocation
    );


    /*
    |--------------------------------------------------------------------------
    | Alamat manual
    |--------------------------------------------------------------------------
    */

    if (addressInput) {

        addressInput.addEventListener(
            'input',
            function () {

                /*
                 * Begitu user mengetik sendiri,
                 * reverse geocoder tidak boleh menimpa.
                 */

                addressWasAutoFilled =
                    false;
            }
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Restore koordinat setelah validation error
    |--------------------------------------------------------------------------
    */

    const oldLatitude =
        Number.parseFloat(
            latInput.value
        );

    const oldLongitude =
        Number.parseFloat(
            lngInput.value
        );


    if (
    Number.isFinite(oldLatitude) &&
    Number.isFinite(oldLongitude) &&
    isInsidePilotArea(
        oldLatitude,
        oldLongitude
    )
    ) {

        placeMarker(
            oldLatitude,
            oldLongitude,
            sourceInput.value ||
                'manual',
            17,
            null
        );

    } else {

        /*
         * Proposal menjelaskan bahwa lokasi
         * dapat diperoleh otomatis menggunakan
         * Geolocation API.
         */

        useCurrentLocation();
    }


    /*
    |--------------------------------------------------------------------------
    | Leaflet resize
    |--------------------------------------------------------------------------
    */

    window.setTimeout(
        function () {

            map.invalidateSize();

        },
        250
    );


    /*
    |--------------------------------------------------------------------------
    | Counter deskripsi
    |--------------------------------------------------------------------------
    */

    function updateDescriptionCounter() {

        if (
            !descriptionInput ||
            !descriptionCounter
        ) {
            return;
        }


        const length =
            descriptionInput.value.length;


        descriptionCounter.textContent =
            `${length}/5000 karakter`;


        descriptionCounter.classList.toggle(
            'text-amber-600',
            length >= 4500
        );


        descriptionCounter.classList.toggle(
            'text-red-600',
            length >= 4900
        );
    }


    if (descriptionInput) {

        descriptionInput.addEventListener(
            'input',
            updateDescriptionCounter
        );


        updateDescriptionCounter();
    }


    /*
    |--------------------------------------------------------------------------
    | Upload foto
    |--------------------------------------------------------------------------
    */

    function syncInputFiles() {

        if (
            typeof DataTransfer ===
            'undefined'
        ) {
            return;
        }


        const dataTransfer =
            new DataTransfer();


        selectedFiles.forEach(
            function (file) {

                dataTransfer.items.add(
                    file
                );
            }
        );


        fotoInput.files =
            dataTransfer.files;
    }


    function renderPreviews() {

        preview.innerHTML =
            '';


        selectedFiles.forEach(
            function (
                file,
                index
            ) {

                const wrapper =
                    document.createElement(
                        'div'
                    );


                wrapper.className =
                    'photo-preview-item group';


                const image =
                    document.createElement(
                        'img'
                    );


                image.className =
                    'w-full h-24 object-cover rounded-xl border border-slate-200';


                image.alt =
                    `Preview foto ${index + 1}`;


                const removeButton =
                    document.createElement(
                        'button'
                    );


                removeButton.type =
                    'button';


                removeButton.className =
                    'photo-remove-button';


                removeButton.setAttribute(
                    'aria-label',
                    `Hapus foto ${index + 1}`
                );


                removeButton.textContent =
                    '×';


                removeButton.addEventListener(
                    'click',
                    function () {

                        selectedFiles.splice(
                            index,
                            1
                        );


                        syncInputFiles();

                        renderPreviews();
                    }
                );


                wrapper.appendChild(
                    image
                );


                wrapper.appendChild(
                    removeButton
                );


                preview.appendChild(
                    wrapper
                );


                const reader =
                    new FileReader();


                reader.onload =
                    function (
                        event
                    ) {

                        image.src =
                            event.target.result;
                    };


                reader.readAsDataURL(
                    file
                );
            }
        );
    }


    function addFiles(
        fileList
    ) {

        const incoming =
            Array.from(
                fileList || []
            );


        const allowedTypes = [
            'image/jpeg',
            'image/png',
            'image/webp'
        ];


        for (
            const file of incoming
        ) {

            if (
                selectedFiles.length >=
                MAX_PHOTO
            ) {

                alert(
                    `Maksimal ${MAX_PHOTO} foto dapat diunggah.`
                );

                break;
            }


            if (
                !allowedTypes.includes(
                    file.type
                )
            ) {

                alert(
                    `File "${file.name}" bukan JPG, PNG, atau WEBP yang valid.`
                );

                continue;
            }


            if (
                file.size >
                MAX_PHOTO_BYTES
            ) {

                alert(
                    `Ukuran file "${file.name}" melebihi batas 5MB.`
                );

                continue;
            }


            const duplicate =
                selectedFiles.some(
                    function (existing) {

                        return (
                            existing.name ===
                                file.name &&

                            existing.size ===
                                file.size &&

                            existing.lastModified ===
                                file.lastModified
                        );
                    }
                );


            if (!duplicate) {

                selectedFiles.push(
                    file
                );
            }
        }


        syncInputFiles();

        renderPreviews();
    }


    /*
    |--------------------------------------------------------------------------
    | Photo UI
    |--------------------------------------------------------------------------
    */

    dropZone.addEventListener(
        'click',
        function (event) {

            if (
                event.target.closest(
                    'button'
                )
            ) {
                return;
            }


            fotoInput.click();
        }
    );


    dropZone.addEventListener(
        'keydown',
        function (event) {

            if (
                event.key === 'Enter' ||
                event.key === ' '
            ) {

                event.preventDefault();

                fotoInput.click();
            }
        }
    );


    dropZone.addEventListener(
        'dragover',
        function (event) {

            event.preventDefault();

            dropZone.classList.add(
                'is-dragging'
            );
        }
    );


    dropZone.addEventListener(
        'dragleave',
        function () {

            dropZone.classList.remove(
                'is-dragging'
            );
        }
    );


    dropZone.addEventListener(
        'drop',
        function (event) {

            event.preventDefault();

            dropZone.classList.remove(
                'is-dragging'
            );


            addFiles(
                event.dataTransfer.files
            );
        }
    );


    fotoInput.addEventListener(
        'change',
        function () {

            addFiles(
                this.files
            );
        }
    );


    /*
    |--------------------------------------------------------------------------
    | Submit validation
    |--------------------------------------------------------------------------
    */

    form.addEventListener(
        'submit',
        function (event) {

            /*
             * Cegah double submit.
             */

            if (isSubmitting) {

                event.preventDefault();

                return;
            }


            syncInputFiles();


            const latitude =
                Number.parseFloat(
                    latInput.value
                );


            const longitude =
                Number.parseFloat(
                    lngInput.value
                );


            /*
             * Lokasi wajib ada.
             */

            if (
                !Number.isFinite(
                    latitude
                ) ||
                !Number.isFinite(
                    longitude
                )
            ) {

                event.preventDefault();


                setLocationStatus(
                    'Lokasi laporan belum ditentukan. Gunakan GPS atau pilih titik pada peta.',
                    'error'
                );


                alert(
                    'Lokasi laporan belum ditentukan. Gunakan GPS atau pilih titik pada peta.'
                );


                return;
            }


            /*
             * GPS dengan akurasi >500m tidak langsung
             * dianggap cukup presisi.
             *
             * User masih dapat menggeser marker.
             */

            if (
                sourceInput.value ===
                    'gps_otomatis' &&

                bestGpsPosition &&

                Number.isFinite(
                    bestGpsPosition.coords.accuracy
                ) &&

                bestGpsPosition.coords.accuracy >
                    LOCATION_MANUAL_RECOMMENDED
            ) {

                event.preventDefault();


                setLocationStatus(
                    'Akurasi lokasi perangkat terlalu rendah untuk dikirim sebagai titik GPS. Geser marker ke lokasi hambatan atau aktifkan Location Services perangkat.',
                    'error'
                );


                alert(
                    'Akurasi lokasi perangkat masih terlalu rendah. Silakan geser marker ke lokasi hambatan secara manual sebelum mengirim laporan.'
                );


                return;
            }


            /*
             * PENTING:
             *
             * Tidak ada lagi:
             *
             * validateWilayahLocation()
             * wilayahSelect.focus()
             * validasi Kecamatan di browser
             *
             * Backend yang menentukan wilayah_id berdasarkan
             * latitude dan longitude.
             */


            /*
             * Foto wajib.
             */

            if (
                !fotoInput.files.length
            ) {

                event.preventDefault();


                alert(
                    'Minimal satu foto harus diunggah.'
                );


                return;
            }


            /*
             * Hindari double submission.
             */

            isSubmitting =
                true;


            if (submitButton) {

                submitButton.disabled =
                    true;


                submitButton.classList.add(
                    'opacity-60',
                    'cursor-not-allowed'
                );
            }

        }
    );
});