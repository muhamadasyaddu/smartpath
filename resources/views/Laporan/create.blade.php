@extends('layouts.app')

@section('title', 'Buat Laporan Baru')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/laporan-create.css') }}">
@endpush

@section('content')
<div class="laporan-create-page max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">

    {{-- Breadcrumb --}}
    <nav class="text-sm text-slate-500 mb-4" aria-label="Breadcrumb">
        <ol class="flex items-center gap-2 flex-wrap">
            <li>
                <a
                    href="{{ route('laporan.index') }}"
                    class="hover:text-emerald-700 transition-colors"
                >
                    Laporan
                </a>
            </li>

            <li aria-hidden="true">›</li>

            <li
                class="text-slate-900 font-medium"
                aria-current="page"
            >
                Buat Laporan
            </li>
        </ol>
    </nav>

    {{-- Page heading --}}
    <header class="mb-6 sm:mb-7">
        <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-950">
            Buat Laporan Hambatan
        </h1>

        <p class="text-sm sm:text-base text-slate-500 mt-1.5">
            Laporkan hambatan aksesibilitas infrastruktur publik yang Anda temui.
        </p>
    </header>

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

        @if($errors->any())
            <div
                class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-red-800"
                role="alert"
                aria-live="polite"
            >
                <h2 class="font-semibold mb-1">
                    Mohon perbaiki kesalahan berikut:
                </h2>

                <ul class="text-sm list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="space-y-5 sm:space-y-6">

            {{-- 1. Kategori Hambatan --}}
            <section
                class="sp-report-card"
                aria-labelledby="kategori-heading"
            >
                <div class="sp-section-heading">
                    <span class="sp-section-number">1.</span>

                    <h2 id="kategori-heading">
                        Kategori Hambatan
                    </h2>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">

                    @foreach($kategoriHambatan as $kategori)

                        <label class="sp-category-option">

                            <input
                                type="radio"
                                name="kategori_hambatan_id"
                                value="{{ $kategori->id }}"
                                class="peer sr-only"
                                aria-label="{{ $kategori->nama }}"
                                {{ old('kategori_hambatan_id') == $kategori->id ? 'checked' : '' }}
                            >

                            <span class="sp-category-card">

                                <span
                                    class="sp-category-dot"
                                    style="--category-color: {{ $kategori->warna_penanda ?? '#64748b' }}"
                                    aria-hidden="true"
                                ></span>

                                <span class="sp-category-label">
                                    {{ $kategori->nama }}
                                </span>

                            </span>

                        </label>

                    @endforeach

                </div>

                @error('kategori_hambatan_id')
                    <p
                        class="sp-field-error"
                        role="alert"
                    >
                        {{ $message }}
                    </p>
                @enderror
            </section>


            {{-- 2. Detail Laporan --}}
            <section
                class="sp-report-card"
                aria-labelledby="detail-heading"
            >

                <div class="sp-section-heading">
                    <span class="sp-section-number">2.</span>

                    <h2 id="detail-heading">
                        Detail Laporan
                    </h2>
                </div>

                <div class="space-y-4">

                    <div>

                        <label
                            for="judul"
                            class="sp-label"
                        >
                            Judul Laporan
                            <span
                                class="text-red-500"
                                aria-hidden="true"
                            >
                                *
                            </span>
                        </label>

                        <input
                            type="text"
                            id="judul"
                            name="judul"
                            value="{{ old('judul') }}"
                            maxlength="200"
                            class="sp-input @error('judul') border-red-400 @enderror"
                            placeholder="Contoh: Trotoar rusak di depan fasilitas publik"
                            required
                            aria-required="true"
                        >

                        @error('judul')
                            <p
                                class="sp-field-error"
                                role="alert"
                            >
                                {{ $message }}
                            </p>
                        @enderror

                    </div>


                    <div>

                        <label
                            for="deskripsi"
                            class="sp-label"
                        >
                            Deskripsi
                            <span
                                class="text-red-500"
                                aria-hidden="true"
                            >
                                *
                            </span>
                        </label>

                        <textarea
                            id="deskripsi"
                            name="deskripsi"
                            rows="5"
                            maxlength="5000"
                            class="sp-input sp-textarea @error('deskripsi') border-red-400 @enderror"
                            placeholder="Jelaskan secara detail hambatan yang Anda temui..."
                            required
                            aria-required="true"
                        >{{ old('deskripsi') }}</textarea>

                        <div class="flex justify-end mt-1">
                            <span
                                id="deskripsi-counter"
                                class="text-xs text-slate-400"
                            >
                                0/5000 karakter
                            </span>
                        </div>

                        @error('deskripsi')
                            <p
                                class="sp-field-error"
                                role="alert"
                            >
                                {{ $message }}
                            </p>
                        @enderror

                    </div>

                </div>

            </section>


            {{-- 3. Lokasi Hambatan --}}
            <section
                class="sp-report-card"
                aria-labelledby="lokasi-heading"
            >

                <div class="sp-section-heading">
                    <span class="sp-section-number">3.</span>

                    <h2 id="lokasi-heading">
                        Lokasi Hambatan
                    </h2>
                </div>

                <p class="text-sm text-slate-500 mb-2">
                    Gunakan lokasi perangkat untuk mendapatkan titik otomatis.
                    Anda tetap dapat menggeser marker atau memilih titik lain
                    pada peta secara manual.
                </p>

                <p
                    id="location-status"
                    class="sp-location-status mb-4"
                    role="status"
                    aria-live="polite"
                >
                    Menyiapkan lokasi perangkat...
                </p>


                <div
                    id="location-map"
                    class="sp-map mb-4"
                    role="region"
                    aria-label="Peta pemilih lokasi. Klik peta atau geser marker untuk menentukan titik laporan."
                ></div>


                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                    <div>

                        <label
                            for="latitude"
                            class="sp-label"
                        >
                            Latitude
                            <span
                                class="text-red-500"
                                aria-hidden="true"
                            >
                                *
                            </span>
                        </label>

                        <input
                            type="text"
                            id="latitude"
                            name="latitude"
                            value="{{ old('latitude') }}"
                            class="sp-input sp-coordinate"
                            placeholder="-6.4025000"
                            inputmode="decimal"
                            readonly
                            required
                            aria-required="true"
                        >

                        @error('latitude')
                            <p
                                class="sp-field-error"
                                role="alert"
                            >
                                {{ $message }}
                            </p>
                        @enderror

                    </div>


                    <div>

                        <label
                            for="longitude"
                            class="sp-label"
                        >
                            Longitude
                            <span
                                class="text-red-500"
                                aria-hidden="true"
                            >
                                *
                            </span>
                        </label>

                        <input
                            type="text"
                            id="longitude"
                            name="longitude"
                            value="{{ old('longitude') }}"
                            class="sp-input sp-coordinate"
                            placeholder="106.8197000"
                            inputmode="decimal"
                            readonly
                            required
                            aria-required="true"
                        >

                        @error('longitude')
                            <p
                                class="sp-field-error"
                                role="alert"
                            >
                                {{ $message }}
                            </p>
                        @enderror

                    </div>

                </div>


                <div class="mt-4">

                    <label
                        for="alamat_lengkap"
                        class="sp-label"
                    >
                        Alamat Lengkap
                    </label>

                    <input
                        type="text"
                        id="alamat_lengkap"
                        name="alamat_lengkap"
                        value="{{ old('alamat_lengkap') }}"
                        maxlength="255"
                        class="sp-input @error('alamat_lengkap') border-red-400 @enderror"
                        placeholder="Contoh: Jl. Margonda Raya, Depok"
                        autocomplete="street-address"
                    >

                    @error('alamat_lengkap')
                        <p
                            class="sp-field-error"
                            role="alert"
                        >
                            {{ $message }}
                        </p>
                    @enderror

                </div>


                {{-- Kecamatan tetap dipertahankan karena backend membutuhkannya --}}
                <div class="mt-4 rounded-xl border border-slate-200 bg-slate-50 p-4">

                    <label
                        for="wilayah_id"
                        class="sp-label mb-1.5"
                    >
                        Kecamatan
                        <span
                            class="text-red-500"
                            aria-hidden="true"
                        >
                            *
                        </span>
                    </label>

                    <p
                        id="wilayah-help"
                        class="text-xs leading-5 text-slate-500 mb-2"
                    >
                        Sistem memilih kecamatan terdekat berdasarkan titik laporan.
                        Pilihan tetap dapat disesuaikan jika marker digeser secara manual.
                    </p>

                    <select
                        id="wilayah_id"
                        name="wilayah_id"
                        class="sp-input"
                        required
                        aria-required="true"
                        aria-describedby="wilayah-help"
                    >

                        <option value="">
                            Pilih Kecamatan
                        </option>

                        @foreach($wilayahList as $wilayah)

                            <option
                                value="{{ $wilayah->id }}"
                                data-latitude="{{ $wilayah->latitude }}"
                                data-longitude="{{ $wilayah->longitude }}"
                                {{ old('wilayah_id') == $wilayah->id ? 'selected' : '' }}
                            >
                                {{ $wilayah->nama }}
                            </option>

                        @endforeach

                    </select>

                    @error('wilayah_id')
                        <p
                            class="sp-field-error"
                            role="alert"
                        >
                            {{ $message }}
                        </p>
                    @enderror

                </div>


                <button
                    type="button"
                    id="btn-locate"
                    class="sp-location-button mt-4"
                >

                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        aria-hidden="true"
                    >
                        <circle
                            cx="12"
                            cy="12"
                            r="9"
                            stroke-width="1.8"
                        ></circle>

                        <circle
                            cx="12"
                            cy="12"
                            r="3"
                            stroke-width="1.8"
                        ></circle>

                        <path
                            d="M12 3V1.5M12 22.5V21M3 12H1.5M22.5 12H21"
                            stroke-width="1.8"
                            stroke-linecap="round"
                        ></path>
                    </svg>

                    <span>
                        Gunakan Lokasi Saya Saat Ini
                    </span>

                </button>

            </section>


            {{-- 4. Prioritas --}}
            <section
                class="sp-report-card"
                aria-labelledby="prioritas-heading"
            >

                <div class="sp-section-heading">

                    <span class="sp-section-number">
                        4.
                    </span>

                    <h2 id="prioritas-heading">
                        Prioritas
                    </h2>

                </div>


                {{--

                    PENTING:

                    Prioritas tidak dikirim sebagai input database.

                    Proposal SmartPath menetapkan bahwa prioritas
                    dihitung otomatis menggunakan WSM setelah verifikasi.

                    Karena itu dropdown hanya menjadi representasi
                    UI seperti mock-up proposal dan dibuat non-editable.

                --}}

                <label
                    for="prioritas-preview"
                    class="sp-label"
                >
                    Prioritas
                    <span
                        class="text-red-500"
                        aria-hidden="true"
                    >
                        *
                    </span>
                </label>

                <select
                    id="prioritas-preview"
                    class="sp-input sp-priority-preview"
                    disabled
                    aria-describedby="prioritas-help"
                >

                    <option>
                        Ditentukan otomatis oleh sistem
                    </option>

                </select>

                <p
                    id="prioritas-help"
                    class="text-xs leading-5 text-slate-500 mt-2"
                >
                    Setelah laporan diverifikasi, SmartPath menghitung prioritas
                    berdasarkan keparahan hambatan, jumlah pelapor unik,
                    dan kedekatan fasilitas publik vital.
                </p>

            </section>


            {{-- 5. Foto Hambatan --}}
            <section
                class="sp-report-card"
                aria-labelledby="foto-heading"
            >

                <div class="sp-section-heading">

                    <span class="sp-section-number">
                        5.
                    </span>

                    <h2 id="foto-heading">
                        Foto Hambatan
                    </h2>

                </div>


                <p class="text-sm text-slate-500 mb-3">
                    Unggah foto untuk mendukung laporan Anda
                    (maks. {{ $maxFoto }} foto).
                    Maksimal 5MB per foto.
                </p>


                <div
                    id="drop-zone"
                    class="sp-drop-zone"
                    role="button"
                    tabindex="0"
                    aria-controls="foto-input"
                    aria-label="Pilih atau seret foto ke sini"
                >

                    <svg
                        class="sp-upload-icon"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        aria-hidden="true"
                    >
                        <path d="M12 16V4"></path>
                        <path d="M7 9l5-5 5 5"></path>
                        <path d="M5 20h14a2 2 0 0 0 2-2v-3"></path>
                        <path d="M3 15v3a2 2 0 0 0 2 2"></path>
                    </svg>

                    <p class="text-sm text-slate-700 font-semibold">
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
                        class="sr-only"
                        data-max-foto="{{ (int) $maxFoto }}"
                        aria-label="Pilih foto laporan"
                    >

                </div>


                <div
                    id="foto-preview"
                    class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3 mt-4"
                    aria-live="polite"
                ></div>


                @error('foto')
                    <p
                        class="sp-field-error mt-3"
                        role="alert"
                    >
                        {{ $message }}
                    </p>
                @enderror


                @foreach($errors->get('foto.*') as $messages)

                    @foreach($messages as $message)

                        <p
                            class="sp-field-error mt-3"
                            role="alert"
                        >
                            {{ $message }}
                        </p>

                    @endforeach

                @endforeach

            </section>


            {{-- Actions --}}
            <div class="flex items-center justify-between gap-4 pt-1 pb-2">

                <a
                    href="{{ route('laporan.index') }}"
                    class="sp-cancel-button"
                >
                    ← Batal
                </a>


                <button
                    type="submit"
                    id="submit-laporan"
                    class="sp-submit-button"
                >

                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        aria-hidden="true"
                    >
                        <path d="M22 2L11 13"></path>
                        <path d="M22 2l-7 20-4-9-9-4 20-7z"></path>
                    </svg>

                    <span>
                        Kirim Laporan
                    </span>

                </button>

            </div>

        </div>

    </form>

</div>
@endsection


@push('scripts')
    <script
        src="{{ asset('js/laporan-create.js') }}"
        defer
    ></script>
@endpush