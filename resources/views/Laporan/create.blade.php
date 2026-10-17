@extends('layouts.app')

@section('title', 'Buat Laporan Baru')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/laporan-create.css') }}">
    <style>
        #homeScreenReaderButton { touch-action: manipulation; }
        #homeScreenReaderButton:focus-visible { outline: 3px solid #10b981; outline-offset: 3px; }
        .sp-report-card[data-screen-reader-active="true"],
        .sp-category-card[data-screen-reader-active="true"] { outline: 3px solid #059669; outline-offset: 3px; background-color: #d1fae5; }
        #homeSpeechStatus { position:absolute; width:1px; height:1px; padding:0; margin:-1px; overflow:hidden; clip:rect(0,0,0,0); white-space:nowrap; border:0; }
    </style>
@endpush

@section('content')

<header class="sticky top-0 z-50 border-b border-slate-200 bg-white shadow-sm">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between gap-4">
           <a
                href="{{ auth()->user()->peran === 'administrator' ? route('admin.dashboard') : (auth()->user()->peran === 'dinas' ? route('dinas.dashboard') : route('warga.dashboard')) }}"
                class="flex shrink-0 items-center gap-3 rounded-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-600 focus-visible:ring-offset-2"
                aria-label="SmartPath - Dashboard"
            >
                <img
                    src="{{ asset('logo-smartpath-cropped.png') }}"
                    alt="SmartPath"
                    class="h-10 w-auto object-contain"
                >
            </a>

            <nav
                class="hidden items-center gap-6 md:flex lg:gap-8"
                aria-label="Navigasi utama"
            >
                <a
                    href="{{ auth()->user()->peran === 'administrator' ? route('admin.dashboard') : (auth()->user()->peran === 'dinas' ? route('dinas.dashboard') : route('warga.dashboard')) }}"
                    @class([
                        'rounded-sm py-2 text-sm font-medium transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-600',
                        'text-emerald-700 font-semibold' => request()->routeIs('admin.dashboard', 'dinas.dashboard', 'warga.dashboard'),
                        'text-slate-600 hover:text-emerald-700' => !request()->routeIs('admin.dashboard', 'dinas.dashboard', 'warga.dashboard'),
                    ])
                    @if(request()->routeIs('admin.dashboard', 'dinas.dashboard', 'warga.dashboard')) aria-current="page" @endif
                >
                    Dashboard
                </a>

                <a
                    href="{{ route('peta.index') }}"
                    @class([
                        'rounded-sm py-2 text-sm font-medium transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-600',
                        'text-emerald-700 font-semibold' => request()->routeIs('peta.index', 'peta.data', 'peta.fasilitas'),
                        'text-slate-600 hover:text-emerald-700' => !request()->routeIs('peta.index', 'peta.data', 'peta.fasilitas'),
                    ])
                    @if(request()->routeIs('peta.index', 'peta.data', 'peta.fasilitas')) aria-current="page" @endif
                >
                    Peta
                </a>

                <a
                    href="{{ route('laporan.index') }}"
                    @class([
                        'rounded-sm py-2 text-sm font-medium transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-600',
                        'text-emerald-700 font-semibold' => request()->routeIs('laporan.*'),
                        'text-slate-600 hover:text-emerald-700' => !request()->routeIs('laporan.*'),
                    ])
                    @if(request()->routeIs('laporan.*')) aria-current="page" @endif
                >
                    Laporan
                </a>

                <a
                    href="{{ route('tentang') }}"
                    @class([
                        'rounded-sm py-2 text-sm font-medium transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-600',
                        'text-emerald-700 font-semibold' => request()->routeIs('tentang'),
                        'text-slate-600 hover:text-emerald-700' => !request()->routeIs('tentang'),
                    ])
                    @if(request()->routeIs('tentang')) aria-current="page" @endif
                >
                    Tentang
                </a>
            </nav>

            <a
                href="{{ route('profile.edit') }}"
                class="inline-flex shrink-0 items-center gap-2 rounded-full border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 transition-colors hover:border-emerald-300 hover:bg-emerald-50 hover:text-emerald-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-600 focus-visible:ring-offset-2"
                aria-label="Buka profil pengguna"
                @if(request()->routeIs('profile.edit')) aria-current="page" @endif
            >
                <span class="flex h-7 w-7 items-center justify-center rounded-full bg-emerald-100 text-emerald-800">
                    <svg
                        class="h-4 w-4"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        aria-hidden="true"
                    >
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                </span>

                <span>{{ auth()->user()->nama_lengkap }}</span>
            </a>
        </div>

        <nav
            class="flex items-center justify-between gap-2 overflow-x-auto border-t border-slate-100 py-2 md:hidden"
            aria-label="Navigasi seluler"
        >
            <a
                href="{{ auth()->user()->peran === 'administrator' ? route('admin.dashboard') : (auth()->user()->peran === 'dinas' ? route('dinas.dashboard') : route('warga.dashboard')) }}"
                @class([
                    'whitespace-nowrap rounded-lg px-3 py-2 text-xs font-medium transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-600',
                    'bg-emerald-50 text-emerald-800' => request()->routeIs('admin.dashboard', 'dinas.dashboard', 'warga.dashboard'),
                    'text-slate-600 hover:bg-slate-50' => !request()->routeIs('admin.dashboard', 'dinas.dashboard', 'warga.dashboard'),
                ])
            >
                Dashboard
            </a>

            <a
                href="{{ route('peta.index') }}"
                @class([
                    'whitespace-nowrap rounded-lg px-3 py-2 text-xs font-medium transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-600',
                    'bg-emerald-50 text-emerald-800' => request()->routeIs('peta.index', 'peta.data', 'peta.fasilitas'),
                    'text-slate-600 hover:bg-slate-50' => !request()->routeIs('peta.index', 'peta.data', 'peta.fasilitas'),
                ])
            >
                Peta
            </a>

            <a
                href="{{ route('laporan.index') }}"
                @class([
                    'whitespace-nowrap rounded-lg px-3 py-2 text-xs font-medium transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-600',
                    'bg-emerald-50 text-emerald-800' => request()->routeIs('laporan.*'),
                    'text-slate-600 hover:bg-slate-50' => !request()->routeIs('laporan.*'),
                ])
                @if(request()->routeIs('laporan.*')) aria-current="page" @endif
            >
                Laporan
            </a>

            <a
                href="{{ route('tentang') }}"
                @class([
                    'whitespace-nowrap rounded-lg px-3 py-2 text-xs font-medium transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-600',
                    'bg-emerald-50 text-emerald-800' => request()->routeIs('tentang'),
                    'text-slate-600 hover:bg-slate-50' => !request()->routeIs('tentang'),
                ])
            >
                Tentang
            </a>
        </nav>
    </div>
</header>

<div class="laporan-create-page max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">

    <nav
        class="text-sm text-slate-500 mb-4"
        aria-label="Breadcrumb"
    >
        <ol class="flex items-center gap-2 flex-wrap">
            <li>
                <a
                    href="{{ route('laporan.index') }}"
                    class="hover:text-slate-700 transition-colors"
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

        <input
            type="hidden"
            id="gps_accuracy"
            name="gps_accuracy"
            value="{{ old('gps_accuracy') }}"
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
                class="sp-report-card bg-white border border-slate-200 rounded-2xl p-5 sm:p-6 shadow-sm"
                aria-labelledby="kategori-heading"
            >
                <div class="sp-section-heading mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-slate-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
                        <line x1="7" y1="7" x2="7.01" y2="7"></line>
                    </svg>

                    <h2 id="kategori-heading" class="text-lg font-bold text-slate-950">
                        1. Kategori Hambatan
                    </h2>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                    @foreach($kategoriHambatan as $kategori)
                        <label class="sp-category-option cursor-pointer">
                            <input
                                type="radio"
                                name="kategori_hambatan_id"
                                value="{{ $kategori->id }}"
                                class="peer sr-only"
                                aria-label="{{ $kategori->nama }}"
                                {{ old('kategori_hambatan_id') == $kategori->id ? 'checked' : '' }}
                            >

                            <span class="sp-category-card flex flex-col items-center justify-center p-3.5 rounded-xl border border-slate-200 bg-white text-slate-700 transition-all duration-200 peer-checked:bg-emerald-100 peer-checked:text-emerald-900 peer-checked:border-emerald-400 peer-checked:shadow-md hover:bg-slate-50">
                                <span
                                    class="sp-category-dot w-3 h-3 rounded-full mb-2 border border-slate-300"
                                    style="--category-color: {{ $kategori->warna_penanda ?? '#10b981' }}; background-color: var(--category-color);"
                                    aria-hidden="true"
                                ></span>

                                <span class="sp-category-label text-center text-xs sm:text-sm font-semibold leading-snug">
                                    {{ $kategori->nama }}
                                </span>
                            </span>
                        </label>
                    @endforeach
                </div>

                @error('kategori_hambatan_id')
                    <p class="sp-field-error mt-2 text-sm text-red-600" role="alert">
                        {{ $message }}
                    </p>
                @enderror
            </section>

            {{-- 2. Detail Laporan --}}
            <section
                class="sp-report-card bg-white border border-slate-200 rounded-2xl p-5 sm:p-6 shadow-sm"
                aria-labelledby="detail-heading"
            >
                <div class="sp-section-heading mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-slate-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="16" y1="13" x2="8" y2="13"></line>
                        <line x1="16" y1="17" x2="8" y2="17"></line>
                    </svg>

                    <h2 id="detail-heading" class="text-lg font-bold text-slate-950">
                        2. Detail Laporan
                    </h2>
                </div>

                <div class="space-y-4">
                    {{-- INPUT JUDUL DENGAN TOMBOL VOICE --}}
                    <div>
                        <label for="judul" class="sp-label text-slate-900 font-medium text-sm block mb-1">
                            Judul Laporan <span class="text-red-500" aria-hidden="true">*</span>
                        </label>
                        <div class="relative flex items-center">
                            <input
                                type="text"
                                id="judul"
                                name="judul"
                                value="{{ old('judul') }}"
                                maxlength="200"
                                class="sp-input w-full bg-white rounded-xl border-slate-300 pr-12 focus:border-emerald-500 focus:ring-emerald-500 @error('judul') border-red-400 @enderror"
                                placeholder="Contoh: Trotoar rusak di depan RS"
                                required
                                aria-required="true"
                            >
                            <button
                                type="button"
                                tabindex="0"
                                class="btn-voice-input absolute right-2 flex h-10 w-10 items-center justify-center rounded-xl text-slate-400 hover:bg-slate-100 hover:text-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                                data-target="judul"
                                aria-label="Isi judul laporan dengan suara"
                                title="Bicara untuk mengisi"
                            >
                                <svg class="h-5 w-5 mic-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3z"></path>
                                    <path d="M19 10v2a7 7 0 0 1-14 0v-2"></path>
                                    <line x1="12" y1="19" x2="12" y2="22"></line>
                                </svg>
                            </button>
                        </div>
                        @error('judul')
                            <p class="sp-field-error text-sm text-red-600 mt-1" role="alert">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- TEXTAREA DESKRIPSI DENGAN TOMBOL VOICE --}}
                    <div>
                        <label for="deskripsi" class="sp-label text-slate-900 font-medium text-sm block mb-1">
                            Deskripsi <span class="text-red-500" aria-hidden="true">*</span>
                        </label>
                        <div class="relative">
                            <textarea
                                id="deskripsi"
                                name="deskripsi"
                                rows="5"
                                maxlength="5000"
                                class="sp-input sp-textarea w-full bg-white rounded-xl border-slate-300 pr-12 focus:border-emerald-500 focus:ring-emerald-500 @error('deskripsi') border-red-400 @enderror"
                                placeholder="Jelaskan secara detail hambatan yang Anda temui..."
                                required
                                aria-required="true"
                            >{{ old('deskripsi') }}</textarea>
                            <button
                                type="button"
                                tabindex="0"
                                class="btn-voice-input absolute right-2 top-3 flex h-10 w-10 items-center justify-center rounded-xl text-slate-400 hover:bg-slate-100 hover:text-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                                data-target="deskripsi"
                                aria-label="Isi deskripsi dengan suara"
                                title="Bicara untuk mengisi"
                            >
                                <svg class="h-5 w-5 mic-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3z"></path>
                                    <path d="M19 10v2a7 7 0 0 1-14 0v-2"></path>
                                    <line x1="12" y1="19" x2="12" y2="22"></line>
                                </svg>
                            </button>
                        </div>

                        <div class="flex justify-end mt-1">
                            <span id="deskripsi-counter" class="text-xs text-slate-400">0/5000 karakter</span>
                        </div>
                        @error('deskripsi')
                            <p class="sp-field-error text-sm text-red-600 mt-1" role="alert">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </section>

            {{-- 3. Lokasi Hambatan --}}
            <section
                class="sp-report-card bg-white border border-slate-200 rounded-2xl p-5 sm:p-6 shadow-sm"
                aria-labelledby="lokasi-heading"
            >
                <div class="sp-section-heading mb-2 flex items-center gap-2">
                    <svg class="w-5 h-5 text-slate-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                        <circle cx="12" cy="10" r="3"></circle>
                    </svg>
                    <h2 id="lokasi-heading" class="text-lg font-bold text-slate-950">
                        3. Lokasi Hambatan
                    </h2>
                </div>

                <p class="text-sm text-slate-500 mb-2">Pilih lokasi pada peta atau geser marker untuk menentukan lokasi tepat</p>
                <p id="location-status" class="sp-location-status mb-4 text-xs sm:text-sm" role="status" aria-live="polite">Memeriksa lokasi perangkat...</p>

                <div
                    id="location-map"
                    class="sp-map mb-4 rounded-xl overflow-hidden border border-slate-200"
                    data-pilot-bounds='@json(config("smartpath.pilot.bounds"))'
                    data-pilot-center='@json(config("smartpath.pilot.center"))'
                    data-gps-timeout="{{ config('smartpath.location.watch_timeout_ms', 15000) }}"
                    data-warning-accuracy="{{ config('smartpath.location.warning_accuracy_meters', 100) }}"
                    data-manual-accuracy="{{ config('smartpath.location.manual_recommended_accuracy_meters', 500) }}"
                    role="region"
                    aria-label="Peta pemilih lokasi. Klik peta atau geser marker untuk menentukan titik laporan."
                ></div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="latitude" class="sp-label text-slate-900 font-medium text-sm block mb-1">
                            Latitude <span class="text-red-500" aria-hidden="true">*</span>
                        </label>
                        <input
                            type="text"
                            id="latitude"
                            name="latitude"
                            value="{{ old('latitude') }}"
                            class="sp-input sp-coordinate w-full bg-slate-50 rounded-xl border-slate-300"
                            placeholder="-6.2000"
                            inputmode="decimal"
                            readonly
                            required
                            aria-required="true"
                        >
                        @error('latitude')
                            <p class="sp-field-error text-sm text-red-600 mt-1" role="alert">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="longitude" class="sp-label text-slate-900 font-medium text-sm block mb-1">
                            Longitude <span class="text-red-500" aria-hidden="true">*</span>
                        </label>
                        <input
                            type="text"
                            id="longitude"
                            name="longitude"
                            value="{{ old('longitude') }}"
                            class="sp-input sp-coordinate w-full bg-slate-50 rounded-xl border-slate-300"
                            placeholder="106.8167"
                            inputmode="decimal"
                            readonly
                            required
                            aria-required="true"
                        >
                        @error('longitude')
                            <p class="sp-field-error text-sm text-red-600 mt-1" role="alert">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- ALAMAT LENGKAP DENGAN TOMBOL VOICE --}}
                <div class="mt-4">
                    <label for="alamat_lengkap" class="sp-label text-slate-900 font-medium text-sm block mb-1">
                        Alamat Lengkap
                    </label>
                    <div class="relative flex items-center">
                        <input
                            type="text"
                            id="alamat_lengkap"
                            name="alamat_lengkap"
                            value="{{ old('alamat_lengkap') }}"
                            maxlength="255"
                            class="sp-input w-full bg-white rounded-xl border-slate-300 pr-12 focus:border-emerald-500 focus:ring-emerald-500 @error('alamat_lengkap') border-red-400 @enderror"
                            placeholder="Nama jalan, nomor bangunan, kelurahan, kecamatan, Kota Depok"
                            autocomplete="street-address"
                        >
                        <button
                            type="button"
                            tabindex="0"
                            class="btn-voice-input absolute right-2 flex h-10 w-10 items-center justify-center rounded-xl text-slate-400 hover:bg-slate-100 hover:text-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                            data-target="alamat_lengkap"
                            aria-label="Isi alamat lengkap dengan suara"
                            title="Bicara untuk mengisi"
                        >
                            <svg class="h-5 w-5 mic-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3z"></path>
                                <path d="M19 10v2a7 7 0 0 1-14 0v-2"></path>
                                <line x1="12" y1="19" x2="12" y2="22"></line>
                            </svg>
                        </button>
                    </div>
                    <p class="mt-2 text-xs leading-5 text-slate-500">
                        Alamat diisi otomatis berdasarkan titik GPS melalui OpenStreetMap.
                    </p>
                    @error('alamat_lengkap')
                        <p class="sp-field-error text-sm text-red-600 mt-1" role="alert">{{ $message }}</p>
                    @enderror
                </div>

                <button
                    type="button"
                    id="btn-locate"
                    class="sp-location-button mt-4 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl px-4 py-2.5 transition flex items-center justify-center gap-2 font-medium text-sm"
                >
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                        <circle cx="12" cy="12" r="9" stroke-width="1.8"></circle>
                        <circle cx="12" cy="12" r="3" stroke-width="1.8"></circle>
                        <path d="M12 3V1.5M12 22.5V21M3 12H1.5M22.5 12H21" stroke-width="1.8" stroke-linecap="round"></path>
                    </svg>
                    <span>Gunakan Lokasi Saya Saat Ini</span>
                </button>
            </section>

            {{-- 4. Prioritas --}}
            <section
                class="sp-report-card bg-white border border-slate-200 rounded-2xl p-5 sm:p-6 shadow-sm"
                aria-labelledby="prioritas-heading"
            >
                <div class="sp-section-heading mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-slate-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"></path>
                        <line x1="4" y1="22" x2="4" y2="15"></line>
                    </svg>
                    <h2 id="prioritas-heading" class="text-lg font-bold text-slate-950">
                        4. Prioritas
                    </h2>
                </div>

                <label for="prioritas-preview" class="sp-label text-slate-900 font-medium text-sm block mb-1">
                    Prioritas <span class="text-red-500" aria-hidden="true">*</span>
                </label>
                <select
                    id="prioritas-preview"
                    class="sp-input sp-priority-preview w-full rounded-xl border-slate-300 bg-slate-100"
                    disabled
                >
                    <option>Pilih Prioritas</option>
                </select>
                <p id="prioritas-help" class="text-xs leading-5 text-slate-500 mt-2">
                    Setelah laporan diverifikasi, SmartPath menghitung prioritas berdasarkan keparahan hambatan.
                </p>
            </section>

            {{-- 5. Foto Hambatan --}}
            <section
                class="sp-report-card bg-white border border-slate-200 rounded-2xl p-5 sm:p-6 shadow-sm"
                aria-labelledby="foto-heading"
            >
                <div class="sp-section-heading mb-2 flex items-center gap-2">
                    <svg class="w-5 h-5 text-slate-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path>
                        <circle cx="12" cy="13" r="4"></circle>
                    </svg>
                    <h2 id="foto-heading" class="text-lg font-bold text-slate-950">
                        5. Foto Hambatan
                    </h2>
                </div>

                <p class="text-sm text-slate-500 mb-3">
                    Unggah foto untuk mendukung laporan Anda (maks. {{ $maxFoto ?? 5 }} foto). Maksimal 5MB per foto.
                </p>

                <div
                    id="drop-zone"
                    class="sp-drop-zone border-2 border-dashed border-slate-300 hover:border-emerald-500 bg-white rounded-xl p-6 text-center cursor-pointer transition-all"
                    role="button"
                    tabindex="0"
                    aria-controls="foto-input"
                    aria-label="Pilih atau seret foto ke sini"
                >
                    <svg class="sp-upload-icon w-8 h-8 mx-auto text-slate-400 mb-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                        <path d="M12 16V4"></path>
                        <path d="M7 9l5-5 5 5"></path>
                        <path d="M5 20h14a2 2 0 0 0 2-2v-3"></path>
                        <path d="M3 15v3a2 2 0 0 0 2 2"></path>
                    </svg>
                    <p class="text-sm text-slate-700 font-semibold">Klik atau seret foto ke sini untuk upload</p>
                    <p class="text-xs text-slate-400 mt-1">JPG, PNG, maksimal 5MB per foto</p>

                    <input
                        type="file"
                        id="foto-input"
                        name="foto[]"
                        multiple
                        accept="image/jpeg,image/png,image/webp"
                        class="sr-only"
                        data-max-foto="{{ (int) ($maxFoto ?? 5) }}"
                        aria-label="Pilih foto laporan"
                    >
                </div>

                <div id="foto-preview" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3 mt-4" aria-live="polite"></div>

                @error('foto')
                    <p class="sp-field-error text-sm text-red-600 mt-3" role="alert">{{ $message }}</p>
                @enderror
            </section>

            {{-- Actions --}}
            <div class="flex items-center justify-between gap-4 pt-1 pb-2">
                <a href="{{ route('laporan.index') }}" class="sp-cancel-button text-slate-600 hover:text-slate-900 font-medium text-sm">
                    ← Batal
                </a>

                <button
                    type="submit"
                    id="submit-laporan"
                    class="sp-submit-button bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-6 py-2.5 rounded-xl transition flex items-center gap-2 shadow-md"
                >
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                        <path d="M22 2L11 13"></path>
                        <path d="M22 2l-7 20-4-9-9-4 20-7z"></path>
                    </svg>
                    <span>Kirim Laporan</span>
                </button>
            </div>

        </div>
    </form>

</div>

<!-- ===== AKSESIBILITAS SUARA (SCREEN READER) ===== -->
<div class="fixed bottom-5 right-5 z-[9998] flex flex-wrap justify-end gap-2" aria-label="Fitur aksesibilitas suara">
    <button id="homeScreenReaderButton" type="button"
        class="inline-flex items-center gap-2 rounded-full border border-emerald-700 bg-emerald-700 px-4 py-3 text-sm font-semibold text-white shadow-lg transition hover:bg-emerald-800 focus-visible:outline-white"
        aria-label="Mulai pembaca layar untuk konten halaman" aria-pressed="false">
        <i class="fa-solid fa-blind text-lg shrink-0" aria-hidden="true"></i>
        <span id="homeScreenReaderLabel">Dengar Panduan</span>
    </button>
    <p id="homeSpeechStatus" class="sr-only" role="status" aria-live="polite"></p>
</div>

@endsection

@push('scripts')
    <script src="{{ asset('js/laporan-create.js') }}" defer></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // =========================================================
            // 1. SCREEN READER: BACA CARD DAN PILIHAN KATEGORI
            // =========================================================
            const readerButton = document.getElementById('homeScreenReaderButton');
            const readerLabel = document.getElementById('homeScreenReaderLabel');
            const speechStatus = document.getElementById('homeSpeechStatus');

            if (readerButton && !('speechSynthesis' in window)) {
                readerButton.disabled = true;
                readerButton.title = 'Browser ini tidak mendukung fitur suara.';
                if (speechStatus) speechStatus.textContent = 'Browser tidak mendukung pembacaan suara.';
            } else if (readerButton) {
                let speechRunId = 0;
                let isReading = false;

                const cardSelector = '.laporan-create-page > header, .sp-report-card';

                function getCardText(card) {
                    return card ? (card.innerText || card.textContent || '').replace(/\s+/g, ' ').trim() : '';
                }

                function isVisible(element) {
                    if (!element || !element.isConnected) return false;
                    const style = window.getComputedStyle(element);
                    return style.display !== 'none' && style.visibility !== 'hidden' && element.getClientRects().length > 0;
                }

                function removeHighlight() {
                    document.querySelectorAll('[data-screen-reader-active="true"]').forEach(function (element) {
                        element.removeAttribute('data-screen-reader-active');
                    });
                }

                function updateButton(reading) {
                    isReading = reading;
                    readerButton.setAttribute('aria-pressed', reading ? 'true' : 'false');
                    readerLabel.textContent = reading ? 'Hentikan Bacaan' : 'Dengar Panduan';
                    readerButton.setAttribute('aria-label', reading ? 'Hentikan pembacaan suara' : 'Bacakan bagian formulir');
                }

                function stopReading(message) {
                    speechRunId++;
                    window.speechSynthesis.cancel();
                    updateButton(false);
                    removeHighlight();
                    if (speechStatus && message) speechStatus.textContent = message;
                }

                function speakText(text, highlightedElement, statusText) {
                    if (!text) return;
                    speechRunId++;
                    const thisRunId = speechRunId;
                    window.speechSynthesis.cancel();
                    removeHighlight();

                    if (highlightedElement) highlightedElement.setAttribute('data-screen-reader-active', 'true');
                    updateButton(true);
                    if (speechStatus) speechStatus.textContent = statusText || 'Sedang membacakan bagian yang dipilih.';

                    const utterance = new SpeechSynthesisUtterance(text);
                    utterance.lang = 'id-ID';
                    utterance.rate = 0.85;
                    utterance.pitch = 1;

                    utterance.onend = function () {
                        if (thisRunId !== speechRunId) return;
                        updateButton(false);
                        removeHighlight();
                        if (speechStatus) speechStatus.textContent = 'Selesai membaca bagian ini. Pilih bagian lain untuk mendengarkan lagi.';
                    };

                    utterance.onerror = function (event) {
                        if (thisRunId !== speechRunId) return;
                        updateButton(false);
                        removeHighlight();
                        if (speechStatus) speechStatus.textContent = (event.error === 'canceled' || event.error === 'interrupted')
                            ? 'Pembacaan dihentikan.'
                            : 'Suara tidak dapat diputar. Silakan coba lagi.';
                    };

                    window.speechSynthesis.speak(utterance);
                }

                function speakCard(card) {
                    if (!card || !isVisible(card)) return;
                    const text = getCardText(card);
                    if (!text) return;
                    const heading = card.querySelector('h1, h2, h3, h4');
                    const headingText = heading ? heading.innerText.trim() : 'bagian formulir';
                    speakText(text, card, 'Sedang membacakan ' + headingText + '.');
                }

                // Klik area kosong pada card membacakan isi card tersebut.
                document.querySelectorAll('.sp-report-card').forEach(function (card) {
                    card.setAttribute('tabindex', '0');
                    card.addEventListener('click', function (event) {
                        if (event.target.closest('button, input, textarea, select, option, label, a, [role="button"], #location-map, .leaflet-container, #drop-zone')) return;
                        speakCard(card);
                    });
                    card.addEventListener('keydown', function (event) {
                        if (event.target !== card || (event.key !== 'Enter' && event.key !== ' ')) return;
                        event.preventDefault();
                        speakCard(card);
                    });
                });

                // Klik setiap pilihan kategori akan membacakan nama kategori itu saja.
                document.querySelectorAll('.sp-category-option input[type="radio"]').forEach(function (input) {
                    input.addEventListener('change', function () {
                        if (!this.checked) return;
                        const option = this.closest('.sp-category-option');
                        const categoryCard = option ? option.querySelector('.sp-category-card') : null;
                        const categoryLabel = option ? option.querySelector('.sp-category-label') : null;
                        const categoryName = categoryLabel ? categoryLabel.innerText.trim() : '';
                        if (!categoryCard || !categoryName) return;
                        speakText('Kategori hambatan. ' + categoryName, categoryCard, 'Sedang membacakan kategori hambatan: ' + categoryName + '.');
                    });
                });

                // Tombol melayang membacakan card pertama; tekan lagi untuk berhenti.
                readerButton.addEventListener('click', function () {
                    if (isReading || window.speechSynthesis.speaking) {
                        stopReading('Pembacaan dihentikan.');
                        return;
                    }
                    const cards = Array.from(document.querySelectorAll(cardSelector)).filter(function (card) {
                        return isVisible(card) && getCardText(card).length > 0;
                    });
                    if (!cards.length) {
                        if (speechStatus) speechStatus.textContent = 'Belum ada bagian formulir yang dapat dibacakan.';
                        return;
                    }
                    speakCard(cards[0]);
                });

                document.addEventListener('keydown', function (event) {
                    if (event.key === 'Escape' && isReading) stopReading('Pembacaan dihentikan.');
                });

                window.addEventListener('pagehide', function () {
                    speechRunId++;
                    window.speechSynthesis.cancel();
                });
            }

            // =========================================================
            // 2. VOICE TO TEXT (INPUT SUARA KE MIKROFON)
            // =========================================================
            const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;

            if (SpeechRecognition) {
                document.querySelectorAll('.btn-voice-input').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const targetId = this.getAttribute('data-target');
                        const targetInput = document.getElementById(targetId);
                        if (!targetInput) return;

                        if ('speechSynthesis' in window) {
                            window.speechSynthesis.cancel();
                        }

                        const recognition = new SpeechRecognition();
                        recognition.lang = 'id-ID';
                        recognition.interimResults = false;

                        const micIcon = this.querySelector('.mic-icon');

                        recognition.onstart = function() {
                            micIcon.classList.add('text-red-500', 'animate-pulse');
                        };

                        recognition.onresult = function(event) {
                            let transcript = event.results[0][0].transcript.trim();

                            // Jika input bertipe textarea/input biasa, tambahkan atau ganti teks
                            if (targetInput.tagName.toLowerCase() === 'textarea' && targetInput.value.length > 0) {
                                targetInput.value += ' ' + transcript;
                            } else {
                                targetInput.value = transcript;
                            }

                            targetInput.dispatchEvent(new Event('input'));
                            targetInput.focus();
                        };

                        recognition.onerror = function() {
                            micIcon.classList.remove('text-red-500', 'animate-pulse');
                        };

                        recognition.onend = function() {
                            micIcon.classList.remove('text-red-500', 'animate-pulse');
                        };

                        recognition.start();
                    });
                });
            } else {
                document.querySelectorAll('.btn-voice-input').forEach(btn => btn.style.display = 'none');
            }
        });
    </script>
@endpush