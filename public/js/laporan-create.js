document.addEventListener('DOMContentLoaded', function () {
    'use strict';

    /*
    |--------------------------------------------------------------------------
    | Element references
    |--------------------------------------------------------------------------
    */

    const form = document.getElementById('laporan-form');
    const mapElement = document.getElementById('location-map');

    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');
    const addressInput = document.getElementById('alamat_lengkap');
    const sourceInput = document.getElementById('sumber_koordinat');

    const locationStatus =
        document.getElementById('location-status');

    const locateButton =
        document.getElementById('btn-locate');

    const wilayahSelect =
        document.getElementById('wilayah_id');

    const dropZone =
        document.getElementById('drop-zone');

    const fotoInput =
        document.getElementById('foto-input');

    const preview =
        document.getElementById('foto-preview');

    const submitButton =
        document.getElementById('submit-laporan');

    if (
        !form ||
        !mapElement ||
        !latInput ||
        !lngInput ||
        !sourceInput ||
        !locationStatus ||
        !locateButton ||
        !wilayahSelect ||
        !dropZone ||
        !fotoInput ||
        !preview
    ) {
        console.error(
            'SmartPath: elemen form laporan tidak lengkap.'
        );

        return;
    }

    /*
    |--------------------------------------------------------------------------
    | Configuration
    |--------------------------------------------------------------------------
    */

    const defaultCenter = [-6.4025, 106.8197];

    const maxFoto = Number(
        fotoInput.dataset.maxFoto || 5
    );

    const maxFotoBytes =
        5 * 1024 * 1024;

    /*
    |--------------------------------------------------------------------------
    | Map
    |--------------------------------------------------------------------------
    */

    if (typeof L === 'undefined') {
        locationStatus.textContent =
            'Peta gagal dimuat. Silakan muat ulang halaman.';

        locationStatus.classList.add(
            'smartpath-location-status',
            'is-error'
        );

        return;
    }

    const map = L.map('location-map', {
        center: defaultCenter,
        zoom: 14,
        zoomControl: true
    });

    L.tileLayer(
        'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
        {
            attribution:
                '&copy; OpenStreetMap contributors',
            maxZoom: 19
        }
    ).addTo(map);

    let marker = null;

    /*
    |--------------------------------------------------------------------------
    | Location status
    |--------------------------------------------------------------------------
    */

    function setLocationStatus(
        message,
        type = 'neutral'
    ) {
        locationStatus.textContent = message;

        locationStatus.classList.remove(
            'is-success',
            'is-warning',
            'is-error',
            'is-neutral'
        );

        locationStatus.classList.add(
            'smartpath-location-status',
            `is-${type}`
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Haversine
    |--------------------------------------------------------------------------
    */

    function haversine(
        lat1,
        lng1,
        lat2,
        lng2
    ) {
        const earthRadius = 6371000;

        const toRad = value =>
            value * Math.PI / 180;

        const dLat =
            toRad(lat2 - lat1);

        const dLng =
            toRad(lng2 - lng1);

        const a =
            Math.sin(dLat / 2) ** 2
            +
            Math.cos(toRad(lat1))
            *
            Math.cos(toRad(lat2))
            *
            Math.sin(dLng / 2) ** 2;

        const safeA =
            Math.min(
                1,
                Math.max(0, a)
            );

        return (
            earthRadius *
            2 *
            Math.atan2(
                Math.sqrt(safeA),
                Math.sqrt(1 - safeA)
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Find nearest Kecamatan
    |--------------------------------------------------------------------------
    */

    function getNearestWilayah(
        latitude,
        longitude
    ) {
        let nearest = null;
        let nearestDistance = Infinity;

        Array.from(
            wilayahSelect.options
        ).forEach(function (option) {
            if (!option.value) {
                return;
            }

            const optionLatitude =
                Number.parseFloat(
                    option.dataset.latitude
                );

            const optionLongitude =
                Number.parseFloat(
                    option.dataset.longitude
                );

            if (
                !Number.isFinite(optionLatitude) ||
                !Number.isFinite(optionLongitude)
            ) {
                return;
            }

            const distance =
                haversine(
                    latitude,
                    longitude,
                    optionLatitude,
                    optionLongitude
                );

            if (
                distance < nearestDistance
            ) {
                nearestDistance = distance;

                nearest = {
                    option: option,
                    distance: distance
                };
            }
        });

        return nearest;
    }

    /*
    |--------------------------------------------------------------------------
    | Validate Kecamatan against GPS/map coordinate
    |--------------------------------------------------------------------------
    |
    | MVP uses Kecamatan reference points.
    | This is not a polygon boundary calculation.
    |
    */

    function validateWilayahLocation(
        autoSelect = false,
        showStatus = true
    ) {
        const latitude =
            Number.parseFloat(
                latInput.value
            );

        const longitude =
            Number.parseFloat(
                lngInput.value
            );

        if (
            !Number.isFinite(latitude) ||
            !Number.isFinite(longitude)
        ) {
            if (showStatus) {
                setLocationStatus(
                    'Lokasi belum ditentukan.',
                    'neutral'
                );
            }

            return false;
        }

        const nearest =
            getNearestWilayah(
                latitude,
                longitude
            );

        if (!nearest) {
            if (showStatus) {
                setLocationStatus(
                    'Data referensi kecamatan belum tersedia.',
                    'error'
                );
            }

            return false;
        }

        if (
            autoSelect &&
            !wilayahSelect.value
        ) {
            wilayahSelect.value =
                nearest.option.value;
        }

        const selected =
            wilayahSelect.options[
                wilayahSelect.selectedIndex
            ];

        if (
            !selected ||
            !selected.value
        ) {
            if (showStatus) {
                setLocationStatus(
                    `Lokasi terdeteksi di sekitar Kecamatan ${nearest.option.textContent.trim()}. Silakan pilih kecamatan.`,
                    'warning'
                );
            }

            return false;
        }

        const selectedLatitude =
            Number.parseFloat(
                selected.dataset.latitude
            );

        const selectedLongitude =
            Number.parseFloat(
                selected.dataset.longitude
            );

        if (
            !Number.isFinite(selectedLatitude) ||
            !Number.isFinite(selectedLongitude)
        ) {
            if (showStatus) {
                setLocationStatus(
                    'Kecamatan yang dipilih belum memiliki titik referensi lokasi.',
                    'error'
                );
            }

            return false;
        }

        const selectedDistance =
            haversine(
                latitude,
                longitude,
                selectedLatitude,
                selectedLongitude
            );

        /*
        |--------------------------------------------------------------------------
        | Tolerance
        |--------------------------------------------------------------------------
        |
        | Minimum 1.5 km digunakan untuk menghindari false rejection
        | akibat titik referensi yang bukan polygon batas administratif.
        |
        */

        const tolerance =
            Math.max(
                1500,
                nearest.distance * 1.25
            );

        const mismatch =
            nearest.option.value !== selected.value &&
            selectedDistance > tolerance;

        if (mismatch) {
            if (showStatus) {
                setLocationStatus(
                    `Lokasi lebih dekat ke Kecamatan ${nearest.option.textContent.trim()}. Pilihan "${selected.textContent.trim()}" tidak sesuai dengan titik laporan.`,
                    'error'
                );
            }

            return false;
        }

        if (showStatus) {
            setLocationStatus(
                `Lokasi konsisten dengan Kecamatan ${selected.textContent.trim()}.`,
                'success'
            );
        }

        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | Reverse geocoding
    |--------------------------------------------------------------------------
    */

    function reverseGeocode(
        latitude,
        longitude
    ) {
        if (!addressInput) {
            return;
        }

        fetch(
            `https://nominatim.openstreetmap.org/reverse?lat=${encodeURIComponent(latitude)}&lon=${encodeURIComponent(longitude)}&format=json&accept-language=id`,
            {
                method: 'GET',
                headers: {
                    Accept: 'application/json'
                }
            }
        )
            .then(function (response) {
                if (!response.ok) {
                    throw new Error(
                        'Reverse geocoding failed.'
                    );
                }

                return response.json();
            })
            .then(function (data) {
                if (
                    data.display_name &&
                    !addressInput.value.trim()
                ) {
                    addressInput.value =
                        data.display_name;
                }
            })
            .catch(function () {
                /*
                |--------------------------------------------------------------------------
                | Reverse geocoding is supplementary.
                | GPS coordinates remain authoritative.
                |--------------------------------------------------------------------------
                */
            });
    }

    /*
    |--------------------------------------------------------------------------
    | Marker
    |--------------------------------------------------------------------------
    */

    function setSource(source) {
        sourceInput.value = source;
    }

    function placeMarker(
        latitude,
        longitude,
        source = 'manual',
        zoom = 17,
        autoSelectWilayah = true
    ) {
        if (
            !Number.isFinite(latitude) ||
            !Number.isFinite(longitude)
        ) {
            return false;
        }

        if (marker) {
            map.removeLayer(marker);
        }

        marker = L.marker(
            [latitude, longitude],
            {
                draggable: true
            }
        ).addTo(map);

        latInput.value =
            latitude.toFixed(7);

        lngInput.value =
            longitude.toFixed(7);

        setSource(source);

        map.setView(
            [latitude, longitude],
            zoom,
            {
                animate: true
            }
        );

        validateWilayahLocation(
            autoSelectWilayah,
            true
        );

        reverseGeocode(
            latitude,
            longitude
        );

        marker.on(
            'dragend',
            function (event) {
                const position =
                    event.target.getLatLng();

                latInput.value =
                    position.lat.toFixed(7);

                lngInput.value =
                    position.lng.toFixed(7);

                setSource('manual');

                validateWilayahLocation(
                    false,
                    true
                );

                reverseGeocode(
                    position.lat,
                    position.lng
                );
            }
        );

        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | Current location
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

        locateButton.disabled = true;

        setLocationStatus(
            'Meminta izin lokasi dan mengambil koordinat GPS...',
            'neutral'
        );

        navigator.geolocation.getCurrentPosition(
            function (position) {
                const latitude =
                    position.coords.latitude;

                const longitude =
                    position.coords.longitude;

                const accuracy =
                    Number.isFinite(
                        position.coords.accuracy
                    )
                        ? Math.round(
                            position.coords.accuracy
                        )
                        : null;

                placeMarker(
                    latitude,
                    longitude,
                    'gps_otomatis',
                    17,
                    true
                );

                const validWilayah =
                    validateWilayahLocation(
                        false,
                        false
                    );

                if (validWilayah) {
                    setLocationStatus(
                        accuracy
                            ? `Lokasi GPS berhasil diperoleh. Akurasi sekitar ${accuracy} meter.`
                            : 'Lokasi GPS berhasil diperoleh secara otomatis.',
                        'success'
                    );
                }

                locateButton.disabled = false;
            },
            function (error) {
                let message =
                    'GPS tidak dapat digunakan. Pilih lokasi secara manual pada peta.';

                if (
                    error.code ===
                    error.PERMISSION_DENIED
                ) {
                    message =
                        'Izin lokasi ditolak. Izinkan akses lokasi pada browser lalu tekan tombol ini lagi.';
                } else if (
                    error.code ===
                    error.POSITION_UNAVAILABLE
                ) {
                    message =
                        'Posisi GPS tidak tersedia. Pastikan layanan lokasi perangkat aktif.';
                } else if (
                    error.code ===
                    error.TIMEOUT
                ) {
                    message =
                        'Pengambilan GPS terlalu lama. Coba tekan tombol lokasi lagi.';
                }

                setLocationStatus(
                    message,
                    'error'
                );

                locateButton.disabled = false;
            },
            {
                enableHighAccuracy: true,
                timeout: 15000,
                maximumAge: 0
            }
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Map interaction
    |--------------------------------------------------------------------------
    */

    map.on(
        'click',
        function (event) {
            placeMarker(
                event.latlng.lat,
                event.latlng.lng,
                'manual',
                17,
                true
            );

            const valid =
                validateWilayahLocation(
                    false,
                    false
                );

            if (!valid) {
                return;
            }

            setLocationStatus(
                'Lokasi dipilih secara manual pada peta.',
                'success'
            );
        }
    );

    wilayahSelect.addEventListener(
        'change',
        function () {
            validateWilayahLocation(
                false,
                true
            );
        }
    );

    locateButton.addEventListener(
        'click',
        function () {
            useCurrentLocation();
        }
    );

    /*
    |--------------------------------------------------------------------------
    | Restore previous coordinates after validation failure
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
        Number.isFinite(oldLongitude)
    ) {
        placeMarker(
            oldLatitude,
            oldLongitude,
            sourceInput.value ||
                'manual',
            17,
            false
        );

        validateWilayahLocation(
            false,
            true
        );
    } else {
        /*
        |--------------------------------------------------------------------------
        | Proposal requirement:
        | GPS is attempted automatically when opening the form.
        |--------------------------------------------------------------------------
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
    | Photo upload
    |--------------------------------------------------------------------------
    */

    let selectedFiles = [];

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
                dataTransfer.items.add(file);
            }
        );

        fotoInput.files =
            dataTransfer.files;
    }

    function renderPreviews() {
        preview.innerHTML = '';

        selectedFiles.forEach(
            function (file, index) {
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
                    'w-full h-24 object-cover rounded-lg border border-slate-200';

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
                    function (event) {
                        image.src =
                            event.target.result;
                    };

                reader.readAsDataURL(
                    file
                );
            }
        );
    }

    function addFiles(fileList) {
        const incoming =
            Array.from(fileList || []);

        for (
            const file of incoming
        ) {
            if (
                selectedFiles.length >=
                maxFoto
            ) {
                alert(
                    `Maksimal ${maxFoto} foto dapat diunggah.`
                );

                break;
            }

            const allowedTypes = [
                'image/jpeg',
                'image/png',
                'image/webp'
            ];

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
                maxFotoBytes
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
        function () {
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
            syncInputFiles();

            const latitude =
                Number.parseFloat(
                    latInput.value
                );

            const longitude =
                Number.parseFloat(
                    lngInput.value
                );

            if (
                !Number.isFinite(latitude) ||
                !Number.isFinite(longitude)
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

            const wilayahValid =
                validateWilayahLocation(
                    false,
                    true
                );

            if (!wilayahValid) {
                event.preventDefault();

                alert(
                    'Kecamatan yang dipilih tidak sesuai dengan titik lokasi laporan. Silakan pilih kecamatan yang sesuai dengan marker GPS/peta.'
                );

                wilayahSelect.focus();

                return;
            }

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
            |--------------------------------------------------------------------------
            | Prevent accidental double submission
            |--------------------------------------------------------------------------
            */

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