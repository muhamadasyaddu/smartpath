@extends('layouts.guest')

@section('title', 'Daftar Akun')

@section('content')
<div class="min-h-screen w-full bg-[#f3faf7] flex items-end sm:items-center justify-center p-0 sm:p-4">

    <div class="relative w-full max-w-lg overflow-hidden bg-white sm:rounded-[2rem] sm:shadow-[0_20px_60px_rgba(15,23,42,0.12)]">

        {{-- ==========================================================
             ACCESSIBILITY ILLUSTRATION
        =========================================================== --}}
        <div class="relative h-[230px] sm:h-[400px] overflow-hidden bg-emerald-50">
            <img
                src="{{ asset('foto-disabilitas.png') }}"
                alt="Ilustrasi kota ramah disabilitas dan aksesibel"
                class="absolute inset-0 h-full w-full object-cover object-bottom"
            >
            <div class="absolute inset-x-0 bottom-0 h-24 bg-gradient-to-t from-white/90 to-transparent" aria-hidden="true"></div>
        </div>

        {{-- ==========================================================
             REGISTER CARD
        =========================================================== --}}
        <div class="relative z-20 -mt-10 rounded-t-[2rem] bg-white px-6 pb-8 pt-6 shadow-[0_-10px_30px_rgba(15,23,42,0.06)] sm:px-8 sm:pt-7">

            {{-- BRAND & ACCESSIBILITY CONTROLS --}}
            <div class="mb-5 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-emerald-600 text-white shadow-[0_8px_20px_rgba(5,150,105,0.22)]" aria-hidden="true">
                        <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 2.5 20 6v5.5c0 5.1-3.4 8.6-8 10-4.6-1.4-8-4.9-8-10V6l8-3.5Z"/>
                            <path d="m8.2 12 2.3 2.3 5.3-5.3"/>
                        </svg>
                    </div>
                    <div>
                        <span class="text-xl font-extrabold tracking-tight text-slate-900 block">
                            Smart<span class="text-emerald-600">Path</span>
                        </span>
                        <p class="text-[11px] font-medium text-slate-400">
                            Platform Pelaporan Aksesibilitas
                        </p>
                    </div>
                </div>

                {{-- TOMBOL NARRATOR / READ FORM --}}
                <button
                    type="button"
                    id="btnReadPage"
                    tabindex="0"
                    class="flex items-center gap-1.5 rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs font-bold text-emerald-700 transition hover:bg-emerald-100 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                    aria-label="Bacakan panduan dan isi formulir pendaftaran"
                >
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon>
                        <path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"></path>
                    </svg>
                    <span>Dengar Panduan</span>
                </button>
            </div>

            {{-- HEADER --}}
            <header class="mb-5">
                <h1 class="text-[26px] font-extrabold leading-tight tracking-tight text-slate-900" id="formHeaderTitle">
                    Buat Akun
                </h1>
                <p class="mt-1 max-w-[340px] text-sm leading-6 text-slate-500" id="formHeaderDesc">
                    Daftarkan akun Anda untuk mulai melaporkan hambatan aksesibilitas di sekitar Anda.
                </p>
            </header>

            {{-- GENERAL ERROR ALERT --}}
            @if ($errors->any())
                <div class="mb-5 flex items-start gap-3 rounded-2xl border border-red-200 bg-red-50/80 p-4 text-red-800" role="alert" aria-live="assertive">
                    <svg class="h-5 w-5 shrink-0 text-red-500 mt-0.5" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                        <path d="M12 9v4m0 4h.01M10.3 3.8 2.7 17a2 2 0 0 0 1.7 3h15.2a2 2 0 0 0 1.7-3L13.7 3.8a2 2 0 0 0-3.4 0Z"/>
                    </svg>
                    <div class="text-xs leading-5">
                        <strong class="font-bold">Periksa kembali data Anda:</strong>
                        <ul class="mt-1 list-disc list-inside space-y-0.5">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            {{-- REGISTER FORM --}}
            <form method="POST" action="{{ route('register.post') }}" id="registerForm" novalidate class="space-y-4">
                @csrf

                {{-- NAMA LENGKAP --}}
                <div>
                    <label for="nama_lengkap" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700">
                        Nama Lengkap <span class="text-red-500" aria-hidden="true">*</span>
                    </label>
                    <div class="relative flex items-center">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4" aria-hidden="true">
                            <svg class="h-5 w-5 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 21a8 8 0 0 0-16 0"/>
                                <circle cx="12" cy="7" r="4"/>
                            </svg>
                        </div>
                        <input
                            type="text"
                            id="nama_lengkap"
                            name="nama_lengkap"
                            value="{{ old('nama_lengkap') }}"
                            placeholder="Masukkan nama lengkap"
                            autocomplete="name"
                            maxlength="150"
                            required
                            aria-required="true"
                            autofocus
                            @error('nama_lengkap') 
                                aria-invalid="true" 
                                aria-describedby="nama_lengkap-error"
                            @enderror
                            class="h-14 w-full rounded-2xl border border-slate-200 bg-white pl-12 pr-14 text-sm text-slate-900 shadow-[0_2px_8px_rgba(15,23,42,0.03)] outline-none transition-all duration-200 placeholder:text-slate-400 hover:border-slate-300 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 @error('nama_lengkap') border-red-500 focus:border-red-500 focus:ring-red-500/10 @enderror"
                        >
                        <button
                            type="button"
                            tabindex="0"
                            class="btn-voice-input absolute right-2 flex h-10 w-10 items-center justify-center rounded-xl text-slate-400 hover:bg-slate-100 hover:text-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                            data-target="nama_lengkap"
                            aria-label="Isi nama lengkap dengan suara"
                            title="Isi nama lengkap dengan suara"
                        >
                            <svg class="h-5 w-5 mic-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3z"></path>
                                <path d="M19 10v2a7 7 0 0 1-14 0v-2"></path>
                                <line x1="12" y1="19" x2="12" y2="22"></line>
                            </svg>
                        </button>
                    </div>
                    @error('nama_lengkap')
                        <p class="mt-1.5 text-xs font-semibold text-red-600" id="nama_lengkap-error" role="alert">{{ $message }}</p>
                    @enderror
                </div>

                {{-- EMAIL --}}
                <div>
                    <label for="email" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700">
                        Email <span class="text-red-500" aria-hidden="true">*</span>
                    </label>
                    <div class="relative flex items-center">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4" aria-hidden="true">
                            <svg class="h-5 w-5 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="5" width="18" height="14" rx="2"/>
                                <path d="m3 7 9 6 9-6"/>
                            </svg>
                        </div>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="nama@email.com"
                            autocomplete="email"
                            inputmode="email"
                            maxlength="150"
                            required
                            aria-required="true"
                            @error('email') 
                                aria-invalid="true" 
                                aria-describedby="email-error"
                            @enderror
                            class="h-14 w-full rounded-2xl border border-slate-200 bg-white pl-12 pr-14 text-sm text-slate-900 shadow-[0_2px_8px_rgba(15,23,42,0.03)] outline-none transition-all duration-200 placeholder:text-slate-400 hover:border-slate-300 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 @error('email') border-red-500 focus:border-red-500 focus:ring-red-500/10 @enderror"
                        >
                        <button
                            type="button"
                            tabindex="0"
                            class="btn-voice-input absolute right-2 flex h-10 w-10 items-center justify-center rounded-xl text-slate-400 hover:bg-slate-100 hover:text-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                            data-target="email"
                            aria-label="Isi email dengan suara"
                            title="Isi email dengan suara"
                        >
                            <svg class="h-5 w-5 mic-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3z"></path>
                                <path d="M19 10v2a7 7 0 0 1-14 0v-2"></path>
                                <line x1="12" y1="19" x2="12" y2="22"></line>
                            </svg>
                        </button>
                    </div>
                    @error('email')
                        <p class="mt-1.5 text-xs font-semibold text-red-600" id="email-error" role="alert">{{ $message }}</p>
                    @enderror
                </div>

                {{-- KATA SANDI --}}
                <div>
                    <label for="kata_sandi" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700">
                        Kata Sandi <span class="text-red-500" aria-hidden="true">*</span>
                    </label>
                    <div class="relative flex items-center">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4" aria-hidden="true">
                            <svg class="h-5 w-5 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="4" y="10" width="16" height="11" rx="2"/>
                                <path d="M8 10V7a4 4 0 0 1 8 0v3"/>
                            </svg>
                        </div>
                        <input
                            type="password"
                            id="kata_sandi"
                            name="kata_sandi"
                            placeholder="Masukkan kata sandi"
                            autocomplete="new-password"
                            minlength="8"
                            maxlength="72"
                            required
                            aria-required="true"
                            aria-describedby="password-hint passwordRequirementError @error('kata_sandi') kata_sandi-error @enderror"
                            @error('kata_sandi') aria-invalid="true" @enderror
                            class="h-14 w-full rounded-2xl border border-slate-200 bg-white pl-12 pr-24 text-sm text-slate-900 shadow-[0_2px_8px_rgba(15,23,42,0.03)] outline-none transition-all duration-200 placeholder:text-slate-400 hover:border-slate-300 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 @error('kata_sandi') border-red-500 focus:border-red-500 focus:ring-red-500/10 @enderror"
                        >
                        <div class="absolute right-2 flex items-center gap-1">
                            {{-- Voice Input --}}
                            <button
                                type="button"
                                tabindex="0"
                                class="btn-voice-input flex h-10 w-10 items-center justify-center rounded-xl text-slate-400 hover:bg-slate-100 hover:text-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                                data-target="kata_sandi"
                                aria-label="Isi kata sandi dengan suara"
                                title="Isi kata sandi dengan suara"
                            >
                                <svg class="h-5 w-5 mic-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3z"></path>
                                    <path d="M19 10v2a7 7 0 0 1-14 0v-2"></path>
                                    <line x1="12" y1="19" x2="12" y2="22"></line>
                                </svg>
                            </button>
                            {{-- Toggle Password --}}
                            <button
                                type="button"
                                tabindex="0"
                                class="flex h-10 w-10 items-center justify-center rounded-xl text-slate-400 hover:bg-slate-100 hover:text-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                                data-password-toggle="kata_sandi"
                                aria-label="Tampilkan kata sandi"
                                aria-pressed="false"
                            >
                                <svg class="h-5 w-5 eye-open" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"/>
                                    <circle cx="12" cy="12" r="2.5"/>
                                </svg>
                                <svg class="h-5 w-5 eye-closed hidden" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="m3 3 18 18"/>
                                    <path d="M10.6 6.2A10.8 10.8 0 0 1 12 6c6 0 9.5 6 9.5 6a16 16 0 0 1-3.2 3.9"/>
                                    <path d="M6.3 6.4C3.9 8.2 2.5 12 2.5 12s3.5 6 9.5 6c1.3 0 2.5-.3 3.6-.8"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Strength Indicator --}}
                    <div class="mt-2">
                        <div class="flex h-1.5 gap-1" id="strengthBars" aria-hidden="true">
                            <span class="flex-1 rounded-full bg-slate-200 transition-all"></span>
                            <span class="flex-1 rounded-full bg-slate-200 transition-all"></span>
                            <span class="flex-1 rounded-full bg-slate-200 transition-all"></span>
                            <span class="flex-1 rounded-full bg-slate-200 transition-all"></span>
                        </div>
                        <p class="strength-text mt-1.5 text-xs text-slate-500" id="password-hint" aria-live="polite">
                            Min. 8 karakter, kombinasi huruf & angka.
                        </p>
                    </div>

                    @error('kata_sandi')
                        <p class="mt-1.5 text-xs font-semibold text-red-600" id="kata_sandi-error" role="alert">{{ $message }}</p>
                    @enderror

                    <p class="mt-1.5 hidden text-xs font-semibold text-red-600" id="passwordRequirementError" role="alert" aria-live="assertive">
                        ✗ Kata sandi harus mengandung kombinasi huruf dan angka.
                    </p>
                </div>

                {{-- KONFIRMASI KATA SANDI --}}
                <div>
                    <label for="kata_sandi_confirmation" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700">
                        Konfirmasi Kata Sandi <span class="text-red-500" aria-hidden="true">*</span>
                    </label>
                    <div class="relative flex items-center">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4" aria-hidden="true">
                            <svg class="h-5 w-5 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                            </svg>
                        </div>
                        <input
                            type="password"
                            id="kata_sandi_confirmation"
                            name="kata_sandi_confirmation"
                            placeholder="Masukkan ulang kata sandi"
                            autocomplete="new-password"
                            minlength="8"
                            maxlength="72"
                            required
                            aria-required="true"
                            aria-describedby="passwordMatch"
                            class="h-14 w-full rounded-2xl border border-slate-200 bg-white pl-12 pr-24 text-sm text-slate-900 shadow-[0_2px_8px_rgba(15,23,42,0.03)] outline-none transition-all duration-200 placeholder:text-slate-400 hover:border-slate-300 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10"
                        >
                        <div class="absolute right-2 flex items-center gap-1">
                            {{-- Voice Input --}}
                            <button
                                type="button"
                                tabindex="0"
                                class="btn-voice-input flex h-10 w-10 items-center justify-center rounded-xl text-slate-400 hover:bg-slate-100 hover:text-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                                data-target="kata_sandi_confirmation"
                                aria-label="Isi konfirmasi kata sandi dengan suara"
                                title="Isi konfirmasi kata sandi dengan suara"
                            >
                                <svg class="h-5 w-5 mic-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3z"></path>
                                    <path d="M19 10v2a7 7 0 0 1-14 0v-2"></path>
                                    <line x1="12" y1="19" x2="12" y2="22"></line>
                                </svg>
                            </button>
                            {{-- Toggle Password --}}
                            <button
                                type="button"
                                tabindex="0"
                                class="flex h-10 w-10 items-center justify-center rounded-xl text-slate-400 hover:bg-slate-100 hover:text-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                                data-password-toggle="kata_sandi_confirmation"
                                aria-label="Tampilkan konfirmasi kata sandi"
                                aria-pressed="false"
                            >
                                <svg class="h-5 w-5 eye-open" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"/>
                                    <circle cx="12" cy="12" r="2.5"/>
                                </svg>
                                <svg class="h-5 w-5 eye-closed hidden" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="m3 3 18 18"/>
                                    <path d="M10.6 6.2A10.8 10.8 0 0 1 12 6c6 0 9.5 6 9.5 6a16 16 0 0 1-3.2 3.9"/>
                                    <path d="M6.3 6.4C3.9 8.2 2.5 12 2.5 12s3.5 6 9.5 6c1.3 0 2.5-.3 3.6-.8"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <p class="password-match mt-1.5 text-xs font-semibold" id="passwordMatch" aria-live="polite"></p>
                </div>

                {{-- NOMOR HP (OPSIONAL) --}}
                <div>
                    <div class="mb-1.5 flex items-center justify-between">
                        <label for="nomor_hp" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                            Nomor HP
                        </label>
                        <span class="text-[11px] font-medium text-slate-400">Opsional</span>
                    </div>
                    <div class="relative flex items-center">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4" aria-hidden="true">
                            <svg class="h-5 w-5 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="6" y="2.5" width="12" height="19" rx="2"/>
                                <path d="M10 18.5h4"/>
                            </svg>
                        </div>
                        <input
                            type="tel"
                            id="nomor_hp"
                            name="nomor_hp"
                            value="{{ old('nomor_hp') }}"
                            placeholder="08xxxxxxxxxx"
                            autocomplete="tel"
                            maxlength="20"
                            inputmode="tel"
                            @error('nomor_hp') 
                                aria-invalid="true" 
                                aria-describedby="nomor_hp-error"
                            @enderror
                            class="h-14 w-full rounded-2xl border border-slate-200 bg-white pl-12 pr-14 text-sm text-slate-900 shadow-[0_2px_8px_rgba(15,23,42,0.03)] outline-none transition-all duration-200 placeholder:text-slate-400 hover:border-slate-300 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 @error('nomor_hp') border-red-500 focus:border-red-500 focus:ring-red-500/10 @enderror"
                        >
                        <button
                            type="button"
                            tabindex="0"
                            class="btn-voice-input absolute right-2 flex h-10 w-10 items-center justify-center rounded-xl text-slate-400 hover:bg-slate-100 hover:text-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                            data-target="nomor_hp"
                            aria-label="Isi nomor hp dengan suara"
                            title="Isi nomor hp dengan suara"
                        >
                            <svg class="h-5 w-5 mic-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3z"></path>
                                <path d="M19 10v2a7 7 0 0 1-14 0v-2"></path>
                                <line x1="12" y1="19" x2="12" y2="22"></line>
                            </svg>
                        </button>
                    </div>
                    @error('nomor_hp')
                        <p class="mt-1.5 text-xs font-semibold text-red-600" id="nomor_hp-error" role="alert">{{ $message }}</p>
                    @enderror
                </div>

                {{-- AGREEMENT --}}
                <div class="pt-1">
                    <label class="flex cursor-pointer items-start gap-3 text-xs leading-5 text-slate-600">
                        <input
                            type="checkbox"
                            name="agreement"
                            value="1"
                            required
                            aria-required="true"
                            tabindex="0"
                            class="mt-0.5 h-4 w-4 shrink-0 rounded border-slate-300 text-emerald-600 focus:ring-2 focus:ring-emerald-500/20 focus:ring-offset-0"
                        >
                        <span>Saya menyetujui penggunaan data saya untuk keperluan pelaporan dan pengelolaan layanan SmartPath.</span>
                    </label>
                </div>

                {{-- SUBMIT BUTTON --}}
                <div class="pt-2">
                    <button
                        type="submit"
                        id="registerSubmit"
                        tabindex="0"
                        class="group flex h-14 w-full items-center justify-center gap-2 rounded-2xl bg-emerald-600 px-5 text-sm font-bold text-white shadow-[0_8px_20px_rgba(5,150,105,0.20)] transition-all duration-200 hover:bg-emerald-700 hover:shadow-[0_10px_25px_rgba(5,150,105,0.25)] active:scale-[0.985] focus:outline-none focus:ring-4 focus:ring-emerald-500/20"
                    >
                        <span class="submit-text">Buat Akun</span>
                        <span class="submit-loading hidden items-center gap-2" aria-hidden="true">
                            <span class="inline-block h-5 w-5 animate-spin rounded-full border-2 border-white/30 border-t-white"></span>
                            <span>Membuat akun...</span>
                        </span>
                        <svg class="h-5 w-5 transition-transform duration-200 group-hover:translate-x-1" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 12h14"/>
                            <path d="m13 6 6 6-6 6"/>
                        </svg>
                    </button>
                </div>
            </form>

            {{-- LOGIN LINK --}}
            <div class="mt-6 text-center">
                <p class="text-sm text-slate-500">
                    Sudah memiliki akun?
                    <a href="{{ route('login') }}" tabindex="0" class="ml-1 font-bold text-emerald-600 transition hover:text-emerald-700 focus:outline-none focus:underline">
                        Masuk di sini
                    </a>
                </p>
            </div>

            {{-- FOOTER / SECURITY INFO --}}
            <footer class="mt-6 border-t border-slate-100 pt-5 text-center">
                <div class="flex items-center justify-center gap-1.5 text-xs text-slate-400">
                    <svg class="h-4 w-4 text-emerald-500" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                        <path d="M12 2.5 20 6v5.5c0 5.1-3.4 8.6-8 10-4.6-1.4-8-4.9-8-10V6l8-3.5Z"/>
                        <path d="m8.5 12 2.2 2.2 4.8-4.8"/>
                    </svg>
                    <span>Data Anda dilindungi dan diproses secara aman.</span>
                </div>
                <p class="mt-1 text-[11px] font-medium text-slate-400">
                    © {{ date('Y') }} SmartPath. All rights reserved.
                </p>
            </footer>

        </div>

    </div>

</div>
@endsection

@push('scripts')
<script>
    (function() {
        // =========================================================
        // 1. DUKUNGAN KEYBOARD NAVIGASI (TAB, SPACEBAR, ENTER)
        // =========================================================
        document.querySelectorAll('button[type="button"], [data-password-toggle], .btn-voice-input').forEach(element => {
            element.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ' || e.keyCode === 32 || e.keyCode === 13) {
                    e.preventDefault();
                    this.click();
                }
            });
        });

        // =========================================================
        // 2. TOGGLE PASSWORD VISIBILITY
        // =========================================================
        document.querySelectorAll('[data-password-toggle]').forEach(btn => {
            btn.addEventListener('click', function() {
                const inputId = this.getAttribute('data-password-toggle');
                const input = document.getElementById(inputId);
                if (!input) return;

                const isPassword = input.type === 'password';
                input.type = isPassword ? 'text' : 'password';

                this.setAttribute('aria-pressed', isPassword ? 'true' : 'false');
                const labelName = inputId === 'kata_sandi' ? 'kata sandi' : 'konfirmasi kata sandi';
                this.setAttribute('aria-label', isPassword ? `Sembunyikan ${labelName}` : `Tampilkan ${labelName}`);

                this.querySelector('.eye-open').classList.toggle('hidden', !isPassword);
                this.querySelector('.eye-closed').classList.toggle('hidden', isPassword);
            });
        });

        // Validation & Password Strength
        const pwdInput = document.getElementById('kata_sandi');
        const confirmInput = document.getElementById('kata_sandi_confirmation');
        const bars = document.querySelectorAll('#strengthBars span');
        const strengthText = document.querySelector('.strength-text');
        const matchMsg = document.getElementById('passwordMatch');
        const requirementError = document.getElementById('passwordRequirementError');
        const form = document.getElementById('registerForm');
        const submitBtn = document.getElementById('registerSubmit');

        function validatePasswordCombination(password) {
            return /[A-Za-z]/.test(password) && /\d/.test(password);
        }

        function getStrength(score) {
            if (score <= 1) return { level: 1, label: 'Lemah', color: '#ef4444' };
            if (score === 2) return { level: 2, label: 'Sedang', color: '#f59e0b' };
            if (score === 3) return { level: 3, label: 'Kuat', color: '#10b981' };
            if (score >= 4) return { level: 4, label: 'Sangat kuat', color: '#059669' };
            return { level: 0, label: '', color: '#e2e8f0' };
        }

        pwdInput.addEventListener('input', function() {
            const pwd = this.value;
            let score = 0;

            if (pwd.length >= 8) score++;
            if (pwd.length >= 12) score++;
            if (/[a-z]/.test(pwd) && /[A-Z]/.test(pwd)) score++;
            if (/\d/.test(pwd) && /[^a-zA-Z0-9]/.test(pwd)) score++;
            score = Math.min(score, 4);

            const strength = getStrength(score);
            bars.forEach((bar, idx) => {
                bar.style.backgroundColor = (idx < strength.level) ? strength.color : '#e2e8f0';
            });

            if (pwd.length === 0) {
                strengthText.textContent = 'Min. 8 karakter, kombinasi huruf & angka.';
                strengthText.className = 'strength-text mt-1.5 text-xs text-slate-500';
            } else {
                strengthText.textContent = `Kekuatan kata sandi: ${strength.label}`;
                strengthText.className = 'strength-text mt-1.5 text-xs font-semibold text-slate-700';
            }

            if (validatePasswordCombination(pwd)) {
                requirementError.classList.add('hidden');
                pwdInput.setAttribute('aria-invalid', 'false');
            }
        });

        function checkMatch() {
            const pwd = pwdInput.value;
            const confirm = confirmInput.value;

            if (confirm.length === 0) {
                matchMsg.textContent = '';
                confirmInput.removeAttribute('aria-invalid');
                return;
            }

            if (pwd === confirm) {
                matchMsg.textContent = '✓ Kata sandi cocok.';
                matchMsg.className = 'password-match mt-1.5 text-xs font-semibold text-emerald-600';
                confirmInput.setAttribute('aria-invalid', 'false');
            } else {
                matchMsg.textContent = '✗ Kata sandi tidak cocok.';
                matchMsg.className = 'password-match mt-1.5 text-xs font-semibold text-red-600';
                confirmInput.setAttribute('aria-invalid', 'true');
            }
        }

        pwdInput.addEventListener('input', checkMatch);
        confirmInput.addEventListener('input', checkMatch);

        form.addEventListener('submit', function(e) {
            const pwd = pwdInput.value;
            const confirm = confirmInput.value;

            if (!validatePasswordCombination(pwd)) {
                e.preventDefault();
                requirementError.classList.remove('hidden');
                pwdInput.setAttribute('aria-invalid', 'true');
                pwdInput.focus();
                speakText("Kata sandi harus mengandung kombinasi huruf dan angka.");
                return;
            } else {
                requirementError.classList.add('hidden');
            }

            if (pwd !== confirm) {
                e.preventDefault();
                confirmInput.setAttribute('aria-invalid', 'true');
                confirmInput.focus();
                speakText("Kata sandi dan konfirmasi kata sandi tidak cocok.");
                return;
            }

            submitBtn.classList.add('pointer-events-none', 'opacity-90');
            submitBtn.querySelector('.submit-text').classList.add('hidden');
            submitBtn.querySelector('.submit-loading').classList.remove('hidden');
            submitBtn.querySelector('.submit-loading').classList.add('flex');
            submitBtn.disabled = true;
        });

        // =========================================================
        // 3. TEXT-TO-SPEECH (NARATOR PEMBACA PANDUAN)
        // =========================================================
        function speakText(text) {
            if ('speechSynthesis' in window) {
                window.speechSynthesis.cancel();
                const utterance = new SpeechSynthesisUtterance(text);
                utterance.lang = 'id-ID';
                utterance.rate = 1;
                window.speechSynthesis.speak(utterance);
            }
        }

        const btnReadPage = document.getElementById('btnReadPage');
        if (btnReadPage) {
            btnReadPage.addEventListener('click', function() {
                const title = document.getElementById('formHeaderTitle').innerText;
                const desc = document.getElementById('formHeaderDesc').innerText;
                speakText(`${title}. ${desc}. Anda bisa berpindah antar isian menggunakan tombol Tab, dan menekan Spasi atau Enter pada tombol mikrofon untuk berbicara.`);
            });
        }

        // =========================================================
        // 4. VOICE-TO-TEXT (PERBAIKAN: TANPA SUARA BANTUAN DI AWAL)
        // =========================================================
        const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;

        if (SpeechRecognition) {
            document.querySelectorAll('.btn-voice-input').forEach(btn => {
                btn.addEventListener('click', function() {
                    const targetId = this.getAttribute('data-target');
                    const targetInput = document.getElementById(targetId);
                    if (!targetInput) return;

                    // Hentikan suara narator lain jika sedang berjalan agar tidak mengganggu mikrofon
                    if ('speechSynthesis' in window) {
                        window.speechSynthesis.cancel();
                    }

                    const recognition = new SpeechRecognition();
                    recognition.lang = 'id-ID';
                    recognition.interimResults = false;

                    const micIcon = this.querySelector('.mic-icon');
                    const fieldName = targetId.replace('_', ' ');

                    // 1. Saat rekaman dimulai: HANYA ubah warna ikon menjadi merah kedip (TANPA SUARA KOMPUTER)
                    recognition.onstart = function() {
                        micIcon.classList.add('text-red-500', 'animate-pulse');
                    };

                    // 2. Saat mendengarkan suara Anda
                    recognition.onresult = function(event) {
                        let transcript = event.results[0][0].transcript.trim();

                        // Format otomatis sesuai jenis input
                        if (targetId === 'email') {
                            transcript = transcript.toLowerCase()
                                .replace(/\s+at\s+/g, '@')
                                .replace(/\s+et\s+/g, '@')
                                .replace(/\s+dot\s+/g, '.')
                                .replace(/\s+titik\s+/g, '.')
                                .replace(/\s+/g, '');
                        } else if (targetId === 'nomor_hp') {
                            transcript = transcript.replace(/\D/g, '');
                        }

                        targetInput.value = transcript;
                        targetInput.dispatchEvent(new Event('input'));
                        targetInput.focus();

                        // Opsional: Komputer membacakan ulang HANYA SETELAH Anda selesai berbicara
                        speakText(`${fieldName} terisi: ${transcript}`);
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
    })();
</script>
@endpush