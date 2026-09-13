@extends('layouts.app')

@section('title', 'Buat Laporan Baru')

@push('styles')
<style>
    #location-map {
        height: 320px;
        border-radius: 0.75rem;
        z-index: 1;
    }

    .photo-preview-item {
        position: relative;
    }
</style>
@endpush

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <nav class="text-sm text-slate-400 mb-4" aria-label="Breadcrumb">
            <ol class="flex items-center gap-2">
                <li>
                    <a href="{{ route('laporan.index') }}" class="hover:text-emerald-700 transition-colors">
                        Laporan
                    </a>
                </li>
                <li aria-hidden="true">/</li>
                <li class="text-slate-700 font-medium" aria-current="page">
                    Buat Baru
                </li>
            </ol>
        </nav>

        <h1 class="text-2xl font-bold text-slate-900">
            Buat Laporan Hambatan
        </h1>

        <p class="text-sm text-slate-500 mt-1">
            Laporkan hambatan aksesibilitas infrastruktur publik yang Anda temui.
        </p>
    </div>

    <form
        id="laporan-form"
        method="POST"
        action="{{ route('laporan.store') }}"
        enctype="multipart/form-data"
        novalidate
    >
        @csrf

        <input
            type="hidden"
            id="sumber_koordinat"
            name="sumber_koordinat"
            value="{{ old('sumber_koordinat', 'gps_otomatis') }}"
        >

        <div class="space-y-6">

            @if($errors->any())
                <div
                    class="bg-red-50 border border-red-200 text-red-800 rounded-xl p-4"
                    role="alert"
                    aria-live="polite"
                >
                    <h3 class="font-semibold mb-1">
                        Mohon perbaiki kesalahan berikut:
                    </h3>

                    <ul class="text-sm list-disc list-inside space-y-0.5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Kategori --}}
            <section class="bg-white rounded-xl border border-slate-200 p-6">
                <h2 class="font-semibold text-slate-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                    Kategori Hambatan
                </h2>

                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                    @foreach($kategoriHambatan as $kategori)
                        <label class="relative cursor-pointer">
                            <input
                                type="radio"
                                name="kategori_hambatan_id"
                                value="{{ $kategori->id }}"
                                class="peer sr-only"
                                aria-label="{{ $kategori->nama }}"
                                {{ old('kategori_hambatan_id') == $kategori->id ? 'checked' : '' }}
                            >

                            <div class="border-2 rounded-xl p-3 text-center transition-all peer-checked:border-emerald-500 peer-checked:bg-emerald-50 peer-focus:ring-2 peer-focus:ring-emerald-500 peer-focus:ring-offset-2 hover:border-slate-300 border-slate-200">
                                <span
                                    class="w-3 h-3 rounded-full inline-block mb-1.5"
                                    style="background-color: {{ $kategori->warna_penanda ?? '#64748b' }}"
                                    aria-hidden="true"
                                ></span>

                                <p class="text-xs font-medium text-slate-700">
                                    {{ $kategori->nama }}
                                </p>
                            </div>
                        </label>
                    @endforeach
                </div>

                @error('kategori_hambatan_id')
                    <p class="mt-2 text-sm text-red-600" role="alert">
                        {{ $message }}
                    </p>
                @enderror
            </section>

            {{-- Detail --}}
            <section class="bg-white rounded-xl border border-slate-200 p-6">
                <h2 class="font-semibold text-slate-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Detail Laporan
                </h2>

                <div class="space-y-4">
                    <div>
                        <label for="judul" class="block text-sm font-medium text-slate-700 mb-1.5">
                            Judul Laporan
                            <span class="text-red-500" aria-hidden="true">*</span>
                        </label>

                        <input
                            type="text"
                            id="judul"
                            name="judul"
                            value="{{ old('judul') }}"
                            maxlength="200"
                            class="w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm @error('judul') border-red-300 @enderror"
                            placeholder="Contoh: Trotoar rusak di depan fasilitas publik"
                            required
                            aria-required="true"
                        >

                        @error('judul')
                            <p class="mt-1 text-sm text-red-600" role="alert">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label for="deskripsi" class="block text-sm font-medium text-slate-700 mb-1.5">
                            Deskripsi
                            <span class="text-red-500" aria-hidden="true">*</span>
                        </label>

                        <textarea
                            id="deskripsi"
                            name="deskripsi"
                            rows="4"
                            maxlength="5000"
                            class="w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm @error('deskripsi') border-red-300 @enderror"
                            placeholder="Jelaskan kondisi hambatan yang Anda temui..."
                            required
                            aria-required="true"
                        >{{ old('deskripsi') }}</textarea>

                        @error('deskripsi')
                            <p class="mt-1 text-sm text-red-600" role="alert">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
            </section>

            {{-- Lokasi --}}
            <section class="bg-white rounded-xl border border-slate-200 p-6">
                <h2 class="font-semibold text-slate-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Lokasi Hambatan
                </h2>

                <p class="text-sm text-slate-500 mb-2">
                    Lokasi akan dicoba diambil otomatis dari GPS perangkat. Jika tidak tersedia,
                    Anda dapat memilih titik secara manual pada peta.
                </p>

                <p
                    id="location-status"
                    class="text-xs text-slate-500 mb-4"
                    role="status"
                    aria-live="polite"
                >
                    Menyiapkan lokasi...
                </p>

                <div
                    id="location-map"
                    class="mb-4"
                    role="application"
                    aria-label="Peta pemilih lokasi. Klik peta atau geser marker untuk mengubah lokasi."
                ></div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="latitude" class="block text-sm font-medium text-slate-700 mb-1.5">
                            Latitude
                            <span class="text-red-500" aria-hidden="true">*</span>
                        </label>

                        <input
                            type="text"
                            id="latitude"
                            name="latitude"
                            value="{{ old('latitude') }}"
                            class="w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm font-mono @error('latitude') border-red-300 @enderror"
                            placeholder="-6.4025"
                            required
                            aria-required="true"
                            readonly
                        >

                        @error('latitude')
                            <p class="mt-1 text-sm text-red-600" role="alert">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label for="longitude" class="block text-sm font-medium text-slate-700 mb-1.5">
                            Longitude
                            <span class="text-red-500" aria-hidden="true">*</span>
                        </label>

                        <input
                            type="text"
                            id="longitude"
                            name="longitude"
                            value="{{ old('longitude') }}"
                            class="w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm font-mono @error('longitude') border-red-300 @enderror"
                            placeholder="106.8197"
                            required
                            aria-required="true"
                            readonly
                        >

                        @error('longitude')
                            <p class="mt-1 text-sm text-red-600" role="alert">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                <div class="mt-4">
                    <label for="alamat_lengkap" class="block text-sm font-medium text-slate-700 mb-1.5">
                        Alamat / Keterangan Lokasi
                    </label>

                    <input
                        type="text"
                        id="alamat_lengkap"
                        name="alamat_lengkap"
                        value="{{ old('alamat_lengkap') }}"
                        maxlength="255"
                        class="w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm @error('alamat_lengkap') border-red-300 @enderror"
                        placeholder="Contoh: Jl. Margonda Raya, Depok"
                    >

                    @error('alamat_lengkap')
                        <p class="mt-1 text-sm text-red-600" role="alert">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <button
                    type="button"
                    id="btn-locate"
                    class="mt-3 inline-flex items-center gap-2 text-sm font-medium text-emerald-700 hover:text-emerald-800 transition-colors focus:outline-none focus:ring-2 focus:ring-emerald-500 rounded"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Gunakan Lokasi Saya Saat Ini
                </button>
            </section>

            {{-- Wilayah --}}
            <section class="bg-white rounded-xl border border-slate-200 p-6">
                <h2 class="font-semibold text-slate-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    Wilayah
                </h2>

                <label for="wilayah_id" class="block text-sm font-medium text-slate-700 mb-1.5">
                    Kecamatan
                    <span class="text-red-500" aria-hidden="true">*</span>
                </label>

                <select
                    id="wilayah_id"
                    name="wilayah_id"
                    class="w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm @error('wilayah_id') border-red-300 @enderror"
                    required
                    aria-required="true"
                >
                    <option value="">Pilih Kecamatan</option>

                    @foreach($wilayahList as $wilayah)
                        <option
                            value="{{ $wilayah->id }}"
                            {{ old('wilayah_id') == $wilayah->id ? 'selected' : '' }}
                        >
                            {{ $wilayah->nama }}
                        </option>
                    @endforeach
                </select>

                @error('wilayah_id')
                    <p class="mt-1 text-sm text-red-600" role="alert">
                        {{ $message }}
                    </p>
                @enderror
            </section>

            {{-- Foto --}}
            <section class="bg-white rounded-xl border border-slate-200 p-6">
                <h2 class="font-semibold text-slate-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 00-2 2z"/>
                    </svg>
                    Foto Hambatan
                </h2>

                <p class="text-sm text-slate-500 mb-3">
                    Unggah foto sebagai bukti kondisi hambatan.
                    Maksimal {{ $maxFoto }} foto, 5MB per foto.
                </p>

                <div
                    id="drop-zone"
                    class="border-2 border-dashed border-slate-300 rounded-xl p-6 text-center hover:border-emerald-400 transition-colors cursor-pointer"
                    role="button"
                    tabindex="0"
                    aria-label="Pilih atau seret foto ke sini"
                >
                    <svg class="w-10 h-10 text-slate-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 0115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>

                    <p class="text-sm text-slate-600 font-medium">
                        Klik untuk memilih foto atau seret ke sini
                    </p>

                    <p class="text-xs text-slate-400 mt-1">
                        JPG, PNG, WEBP • Maks. 5MB per foto
                    </p>

                    <input
                        type="file"
                        id="foto-input"
                        name="foto[]"
                        multiple
                        accept="image/jpeg,image/png,image/webp"
                        class="hidden"
                        aria-label="Pilih foto laporan"
                    >
                </div>

                <div
                    id="foto-preview"
                    class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3 mt-4"
                    aria-live="polite"
                ></div>

                @error('foto')
                    <p class="mt-2 text-sm text-red-600" role="alert">
                        {{ $message }}
                    </p>
                @enderror

                @foreach($errors->get('foto.*') as $messages)
                    @foreach($messages as $message)
                        <p class="mt-2 text-sm text-red-600" role="alert">
                            {{ $message }}
                        </p>
                    @endforeach
                @endforeach
            </section>

            <div class="flex items-center justify-between pt-2">
                <a
                    href="{{ route('laporan.index') }}"
                    class="text-sm font-medium text-slate-500 hover:text-slate-700 transition-colors focus:outline-none focus:ring-2 focus:ring-emerald-500 rounded"
                >
                    Batal
                </a>

                <button
                    type="submit"
                    id="submit-laporan"
                    class="inline-flex items-center gap-2 bg-emerald-600 text-white font-semibold px-8 py-3 rounded-xl hover:bg-emerald-700 transition-colors focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 shadow-sm disabled:opacity-60 disabled:cursor-not-allowed"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9 2zm0 0v-8"/>
                    </svg>
                    Kirim Laporan
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const defaultCenter = [-6.4025, 106.8197];
    const map = L.map('location-map', {
        center: defaultCenter,
        zoom: 14
    });

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap',
        maxZoom: 19
    }).addTo(map);

    setTimeout(() => map.invalidateSize(), 300);

    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');
    const addressInput = document.getElementById('alamat_lengkap');
    const sourceInput = document.getElementById('sumber_koordinat');
    const locationStatus = document.getElementById('location-status');
    const locateButton = document.getElementById('btn-locate');

    let marker = null;

    function setSource(source) {
        sourceInput.value = source;
    }

    function reverseGeocode(lat, lng) {
        /*
         * Reverse geocoding hanya membantu mengisi alamat.
         * Kegagalan layanan ini tidak menggagalkan pelaporan karena
         * koordinat GPS tetap menjadi sumber lokasi utama.
         */
        fetch(
            `https://nominatim.openstreetmap.org/reverse?lat=${encodeURIComponent(lat)}&lon=${encodeURIComponent(lng)}&format=json&accept-language=id`,
            {
                headers: {
                    'Accept': 'application/json'
                }
            }
        )
        .then(response => {
            if (!response.ok) {
                throw new Error('Reverse geocoding gagal.');
            }

            return response.json();
        })
        .then(data => {
            if (data.display_name && !addressInput.value) {
                addressInput.value = data.display_name;
            }
        })
        .catch(() => {
            // Alamat bersifat tambahan; jangan blokir pengiriman laporan.
        });
    }

    function placeMarker(lat, lng, source = 'manual', zoom = 16) {
        if (!Number.isFinite(lat) || !Number.isFinite(lng)) {
            return;
        }

        if (marker) {
            map.removeLayer(marker);
        }

        marker = L.marker([lat, lng], {
            draggable: true
        }).addTo(map);

        latInput.value = lat.toFixed(7);
        lngInput.value = lng.toFixed(7);
        setSource(source);

        map.setView([lat, lng], zoom);

        marker.on('dragend', function (event) {
            const position = event.target.getLatLng();

            latInput.value = position.lat.toFixed(7);
            lngInput.value = position.lng.toFixed(7);
            setSource('manual');

            locationStatus.textContent =
                'Lokasi diubah secara manual melalui marker peta.';

            reverseGeocode(position.lat, position.lng);
        });

        reverseGeocode(lat, lng);
    }

    function useCurrentLocation() {
        if (!('geolocation' in navigator)) {
            locationStatus.textContent =
                'Perangkat/browser tidak mendukung Geolocation API. Pilih lokasi secara manual pada peta.';
            return;
        }

        locationStatus.textContent =
            'Meminta izin lokasi dan mengambil koordinat GPS...';

        navigator.geolocation.getCurrentPosition(
            function (position) {
                placeMarker(
                    position.coords.latitude,
                    position.coords.longitude,
                    'gps_otomatis'
                );

                locationStatus.textContent =
                    'Lokasi GPS berhasil diperoleh secara otomatis.';
            },
            function (error) {
                let message =
                    'GPS tidak dapat digunakan. Silakan pilih lokasi secara manual pada peta.';

                if (error.code === error.PERMISSION_DENIED) {
                    message =
                        'Izin lokasi ditolak. Silakan pilih lokasi secara manual pada peta atau izinkan akses lokasi.';
                }

                locationStatus.textContent = message;
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 30000
            }
        );
    }

    map.on('click', function (event) {
        placeMarker(
            event.latlng.lat,
            event.latlng.lng,
            'manual'
        );

        locationStatus.textContent =
            'Lokasi dipilih secara manual pada peta.';
    });

    locateButton.addEventListener('click', useCurrentLocation);

    /*
     * Jika validasi server mengembalikan koordinat lama,
     * pertahankan titik tersebut.
     */
    const oldLat = parseFloat(latInput.value);
    const oldLng = parseFloat(lngInput.value);

    if (
        Number.isFinite(oldLat)
        && Number.isFinite(oldLng)
    ) {
        placeMarker(
            oldLat,
            oldLng,
            sourceInput.value || 'manual'
        );

        locationStatus.textContent =
            sourceInput.value === 'gps_otomatis'
                ? 'Lokasi GPS sebelumnya dipertahankan.'
                : 'Lokasi manual sebelumnya dipertahankan.';
    } else {
        /*
         * Sesuai spesifikasi proposal, GPS dicoba otomatis.
         * Pengguna tetap memiliki fallback manual.
         */
        useCurrentLocation();
    }

    // ==========================================
    // Upload foto
    // ==========================================
    const dropZone = document.getElementById('drop-zone');
    const fotoInput = document.getElementById('foto-input');
    const preview = document.getElementById('foto-preview');
    const maxFoto = {{ (int) $maxFoto }};

    let selectedFiles = [];

    function syncInputFiles() {
        const dataTransfer = new DataTransfer();

        selectedFiles.forEach(file => {
            dataTransfer.items.add(file);
        });

        fotoInput.files = dataTransfer.files;
    }

    function renderPreviews() {
        preview.innerHTML = '';

        selectedFiles.forEach((file, index) => {
            const wrapper = document.createElement('div');
            wrapper.className = 'photo-preview-item group';

            const image = document.createElement('img');
            image.className =
                'w-full h-24 object-cover rounded-lg border border-slate-200';
            image.alt = `Preview foto ${index + 1}`;

            const removeButton = document.createElement('button');
            removeButton.type = 'button';
            removeButton.className =
                'absolute -top-1.5 -right-1.5 w-6 h-6 bg-red-600 text-white rounded-full text-xs flex items-center justify-center opacity-0 group-hover:opacity-100 focus:opacity-100 transition-opacity';
            removeButton.setAttribute(
                'aria-label',
                `Hapus foto ${index + 1}`
            );
            removeButton.textContent = '×';

            removeButton.addEventListener('click', function () {
                selectedFiles.splice(index, 1);
                syncInputFiles();
                renderPreviews();
            });

            wrapper.appendChild(image);
            wrapper.appendChild(removeButton);
            preview.appendChild(wrapper);

            const reader = new FileReader();

            reader.onload = function (event) {
                image.src = event.target.result;
            };

            reader.readAsDataURL(file);
        });
    }

    function addFiles(fileList) {
        const incoming = Array.from(fileList);

        for (const file of incoming) {
            if (selectedFiles.length >= maxFoto) {
                break;
            }

            if (![
                'image/jpeg',
                'image/png',
                'image/webp'
            ].includes(file.type)) {
                continue;
            }

            if (file.size > 5 * 1024 * 1024) {
                alert(
                    `Ukuran file "${file.name}" melebihi batas 5MB.`
                );
                continue;
            }

            const duplicate = selectedFiles.some(existing =>
                existing.name === file.name
                && existing.size === file.size
                && existing.lastModified === file.lastModified
            );

            if (!duplicate) {
                selectedFiles.push(file);
            }
        }

        syncInputFiles();
        renderPreviews();
    }

    dropZone.addEventListener('click', function () {
        fotoInput.click();
    });

    dropZone.addEventListener('keydown', function (event) {
        if (event.key === 'Enter' || event.key === ' ') {
            event.preventDefault();
            fotoInput.click();
        }
    });

    dropZone.addEventListener('dragover', function (event) {
        event.preventDefault();
        dropZone.classList.add(
            'border-emerald-400',
            'bg-emerald-50'
        );
    });

    dropZone.addEventListener('dragleave', function () {
        dropZone.classList.remove(
            'border-emerald-400',
            'bg-emerald-50'
        );
    });

    dropZone.addEventListener('drop', function (event) {
        event.preventDefault();

        dropZone.classList.remove(
            'border-emerald-400',
            'bg-emerald-50'
        );

        addFiles(event.dataTransfer.files);
    });

    fotoInput.addEventListener('change', function () {
        addFiles(this.files);
    });

    document.getElementById('laporan-form')
        .addEventListener('submit', function (event) {
            syncInputFiles();

            if (!fotoInput.files.length) {
                event.preventDefault();
                alert('Minimal satu foto harus diunggah.');
                return;
            }

            if (!latInput.value || !lngInput.value) {
                event.preventDefault();

                alert(
                    'Lokasi laporan belum ditentukan. Gunakan GPS atau pilih titik pada peta.'
                );
            }
        });
});
</script>
@endpush