@extends('layouts.app')

@section('title', 'Atur Kata Sandi Baru - SmartPath')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/auth-reset-password.css') }}">
@endpush

@section('content')
    <div class="sp-password-reset-page">
        <div class="sp-password-reset-page__shape sp-password-reset-page__shape--one" aria-hidden="true"></div>
        <div class="sp-password-reset-page__shape sp-password-reset-page__shape--two" aria-hidden="true"></div>

        <div class="sp-password-reset-page__layout">
            <header class="sp-password-reset-page__header">
                <a href="{{ url('/') }}" class="sp-password-reset-page__brand" aria-label="SmartPath">
                    <span class="sp-password-reset-page__brand-icon" aria-hidden="true">
                        <img src="{{ asset('favicon.png') }}" alt="" aria-hidden="true" class="sp-password-reset-page__logo">
                    </span>
                    <span>
                        <strong>Smart<span>Path</span></strong>
                        <small>Sistem Informasi Penanganan Aksesibilitas</small>
                    </span>
                </a>

                <p class="sp-password-reset-page__tagline">Kota Lebih Aksesibel,<br>Untuk Semua</p>
            </header>

            <main class="sp-password-reset-page__main">
                <section class="sp-password-reset-page__intro" aria-labelledby="reset-intro-title">
                    <p class="sp-password-reset-page__welcome">Selamat Datang di</p>
                    <h1 id="reset-intro-title">Smart<span>Path</span></h1>
                    <p class="sp-password-reset-page__description">
                        Platform digital untuk melaporkan, memantau,
                        dan menindaklanjuti kondisi fasilitas publik
                        yang belum ramah aksesibilitas di Kota Depok.
                    </p>

                    <div class="sp-password-reset-page__features" aria-label="Fitur SmartPath">
                        <div>
                            <span><i class="fa-solid fa-file-circle-plus" aria-hidden="true"></i></span>
                            <strong>Laporkan</strong>
                            <small>Temukan hambatan aksesibilitas di sekitarmu</small>
                        </div>
                        <div>
                            <span><i class="fa-solid fa-location-dot" aria-hidden="true"></i></span>
                            <strong>Pantau</strong>
                            <small>Lihat status penanganan laporan</small>
                        </div>
                        <div>
                            <span><i class="fa-solid fa-users" aria-hidden="true"></i></span>
                            <strong>Bersama</strong>
                            <small>Wujudkan kota yang lebih inklusif</small>
                        </div>
                        <div>
                            <span><i class="fa-solid fa-shield-heart" aria-hidden="true"></i></span>
                            <strong>Aksesibel</strong>
                            <small>Untuk semua kalangan</small>
                        </div>
                    </div>

                    <div class="sp-password-reset-page__image">
                        <img src="{{ asset('foto-disabilitas.png') }}" alt="Suasana kota yang lebih aksesibel untuk semua">
                    </div>
                </section>

                <section class="sp-password-reset-page__card" aria-labelledby="reset-title">
                    <div class="sp-password-reset-page__card-brand">
                        <span class="sp-password-reset-page__card-icon" aria-hidden="true">
                            <img src="{{ asset('favicon.png') }}" alt="" aria-hidden="true" class="sp-password-reset-page__logo">
                        </span>
                        <strong>Smart<span>Path</span></strong>
                        <small>Sistem Informasi Penanganan Aksesibilitas</small>
                    </div>

                    <div class="sp-password-reset-page__heading">
                        <i class="fa-solid fa-lock" aria-hidden="true"></i>
                        <h2 id="reset-title">Atur Kata Sandi Baru</h2>
                    </div>
                    <p class="sp-password-reset-page__helper">
                        Buat kata sandi baru untuk mengamankan akun SmartPath kamu.
                    </p>

                    @if ($errors->has('email'))
                        <div class="sp-password-reset-page__alert" role="alert">
                            <p>{{ $errors->first('email') }}</p>
                            <a href="{{ route('password.request') }}">Minta link reset baru</a>
                        </div>
                    @endif

                    @if (!$errors->has('email'))
                        <form method="POST" action="{{ route('password.update') }}">
                            @csrf
                            <input type="hidden" name="token" value="{{ $token }}">
                            <input type="hidden" id="email" name="email" value="{{ old('email', $email) }}" required>

                            <div class="sp-password-reset-page__field">
                                <label for="password">Kata Sandi Baru</label>
                                <div class="sp-password-reset-page__input-wrap">
                                    <i class="fa-solid fa-lock" aria-hidden="true"></i>
                                    <input
                                        type="password"
                                        id="password"
                                        name="password"
                                        required
                                        autocomplete="new-password"
                                        @error('password') aria-describedby="password-error" @enderror
                                    >
                                    <button
                                        type="button"
                                        data-password-toggle="password"
                                        aria-label="Tampilkan kata sandi baru"
                                        aria-pressed="false"
                                    >
                                        <svg data-password-eye viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                            <path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"/>
                                            <circle cx="12" cy="12" r="2.5"/>
                                        </svg>
                                    </button>
                                </div>
                                @error('password')
                                    <p id="password-error" class="sp-password-reset-page__field-error">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="sp-password-reset-page__field">
                                <label for="password_confirmation">Konfirmasi Kata Sandi Baru</label>
                                <div class="sp-password-reset-page__input-wrap">
                                    <i class="fa-solid fa-lock" aria-hidden="true"></i>
                                    <input
                                        type="password"
                                        id="password_confirmation"
                                        name="password_confirmation"
                                        required
                                        autocomplete="new-password"
                                    >
                                    <button
                                        type="button"
                                        data-password-toggle="password_confirmation"
                                        aria-label="Tampilkan konfirmasi kata sandi"
                                        aria-pressed="false"
                                    >
                                        <svg data-password-eye viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                            <path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"/>
                                            <circle cx="12" cy="12" r="2.5"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <button type="submit" class="sp-password-reset-page__submit">
                                <i class="fa-solid fa-lock" aria-hidden="true"></i>
                                Simpan Kata Sandi Baru
                            </button>
                        </form>
                    @endif

                    <a href="{{ route('login') }}" class="sp-password-reset-page__back">
                        <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
                        Kembali ke Login
                    </a>
                </section>
            </main>
        </div>
    </div>

    <script>
        document.querySelectorAll('[data-password-toggle]').forEach((button) => {
            button.addEventListener('click', () => {
                const input = document.getElementById(button.dataset.passwordToggle);
                const isVisible = input.type === 'text';

                input.type = isVisible ? 'password' : 'text';
                button.setAttribute('aria-pressed', String(!isVisible));
                button.setAttribute(
                    'aria-label',
                    isVisible ? 'Tampilkan kata sandi' : 'Sembunyikan kata sandi'
                );
                button.querySelector('[data-password-eye]').innerHTML = isVisible
                    ? '<path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"/><circle cx="12" cy="12" r="2.5"/>'
                    : '<path d="M3 3l18 18"/><path d="M10.6 10.6a2 2 0 0 0 2.8 2.8"/><path d="M9.9 4.2A10.8 10.8 0 0 1 12 4c6.5 0 10 8 10 8a17.8 17.8 0 0 1-3.1 4.3"/><path d="M6.6 6.6C3.8 8.4 2 12 2 12s3.5 8 10 8c1.4 0 2.7-.3 3.9-.9"/>';
            });
        });
    </script>
@endsection
