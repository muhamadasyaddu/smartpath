@extends('layouts.app')

@section('content')

{{-- ============================================
     CSS CUSTOM 
============================================ --}}
<style>
    :root {
        --dark-teal: #062a25;
        --dark-green: #004b40;
        --green: #059669;
        --emerald: #10b981;
        --light-green: #d1fae5;
        --soft-bg: #f5fbfa;
        --text: #123d37;
        --muted: #64748b;
        --border: #dbe7e4;
    }

    .sp-forgot-page {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        min-height: 100vh;
        background:
            radial-gradient(circle at 5% 5%, rgba(16, 185, 129, .13), transparent 22%),
            radial-gradient(circle at 95% 20%, rgba(16, 185, 129, .10), transparent 25%),
            linear-gradient(135deg, #f8fcfb 0%, #eff9f6 100%);
        color: var(--text);
        overflow-x: hidden;
        position: relative;
    }

    /* DECORATION */
    .sp-forgot-page .shape {
        position: fixed;
        pointer-events: none;
        z-index: 0;
    }
    .sp-forgot-page .shape-one {
        width: 260px; height: 260px;
        top: -150px; left: -120px;
        border-radius: 50%;
        background: linear-gradient(135deg, #00695c, #10b981);
        opacity: .9;
    }
    .sp-forgot-page .shape-two {
        width: 280px; height: 180px;
        right: -100px; bottom: -70px;
        border-radius: 50% 0 0 0;
        background: linear-gradient(135deg, #34d399, #004b40);
        opacity: .85;
    }

    .sp-forgot-page .page {
        position: relative;
        z-index: 1;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }

    /* HEADER */
    .sp-forgot-page .header {
        width: 100%;
        max-width: 1420px;
        margin: 0 auto;
        padding: 28px 55px 10px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .sp-forgot-page .brand {
        display: flex;
        align-items: center;
        gap: 13px;
        text-decoration: none;
        color: var(--dark-teal);
    }
    .sp-forgot-page .brand-logo {
        width: 58px; height: 58px;
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, var(--dark-green), var(--emerald));
        color: white;
        font-size: 27px;
        box-shadow: 0 10px 25px rgba(5, 150, 105, .18);
    }
    .sp-forgot-page .brand-logo img,
    .sp-forgot-page .card-logo img {
        width: 38px;
        height: 38px;
        object-fit: contain;
        filter: brightness(0) invert(1);
    }
    .sp-forgot-page .brand-name {
        font-size: 30px;
        line-height: 1;
        font-weight: 800;
        letter-spacing: -1.5px;
    }
    .sp-forgot-page .brand-name span { color: var(--emerald); }
    .sp-forgot-page .brand-subtitle {
        margin-top: 5px;
        font-size: 10px;
        color: #52716b;
        font-weight: 600;
    }
    .sp-forgot-page .tagline {
        text-align: right;
        font-weight: 800;
        font-size: 17px;
        line-height: 1.45;
        color: var(--dark-green);
    }
    .sp-forgot-page .tagline::after {
        content: "";
        display: block;
        width: 115px;
        height: 4px;
        margin: 7px 0 0 auto;
        border-radius: 99px;
        background: var(--emerald);
    }

    /* MAIN */
    .sp-forgot-page .main {
        flex: 1;
        width: 100%;
        max-width: 1420px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: 1.05fr .95fr;
        gap: 60px;
        align-items: center;
        padding: 35px 55px 50px;
    }

    /* LEFT */
    .sp-forgot-page .intro { padding: 20px 0 0 50px; }
    .sp-forgot-page .welcome {
        font-size: 28px;
        font-weight: 700;
        color: var(--dark-green);
        margin-bottom: 4px;
    }
    .sp-forgot-page .title {
        font-size: clamp(48px, 5vw, 76px);
        line-height: .95;
        letter-spacing: -4px;
        font-weight: 800;
        color: var(--dark-teal);
    }
    .sp-forgot-page .title span { color: var(--emerald); }
    .sp-forgot-page .description {
        max-width: 580px;
        margin-top: 25px;
        font-size: 17px;
        line-height: 1.7;
        color: #3f625c;
    }

    /* FEATURES */
    .sp-forgot-page .features {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 18px;
        max-width: 650px;
        margin-top: 35px;
    }
    .sp-forgot-page .feature { text-align: center; }
    .sp-forgot-page .feature-icon {
        width: 64px; height: 64px;
        margin: 0 auto 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: rgba(16, 185, 129, .12);
        color: var(--dark-green);
        font-size: 24px;
        border: 1px solid rgba(16, 185, 129, .12);
    }
    .sp-forgot-page .feature h3 {
        font-size: 13px;
        font-weight: 800;
        color: var(--dark-teal);
    }
    .sp-forgot-page .feature p {
        margin-top: 5px;
        font-size: 9px;
        line-height: 1.5;
        color: #64817b;
    }

    /* HERO IMAGE */
    .sp-forgot-page .city {
        position: relative;
        height: 390px;
        margin-top: 20px;
        overflow: hidden;
        border-radius: 0 0 45% 45%;
    }
    .sp-forgot-page .city img {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center 75%;
    }

    /* RESET CARD */
    .sp-forgot-page .reset-wrapper { display: flex; justify-content: center; }
    .sp-forgot-page .reset-card {
        width: min(100%, 510px);
        background: rgba(255, 255, 255, .94);
        border: 1px solid rgba(219, 231, 228, .9);
        border-radius: 22px;
        padding: 42px;
        box-shadow: 0 25px 70px rgba(6, 42, 37, .10), 0 8px 25px rgba(6, 42, 37, .05);
        backdrop-filter: blur(12px);
    }
    .sp-forgot-page .card-logo {
        width: 60px; height: 60px;
        margin: 0 auto 10px;
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 27px;
        background: linear-gradient(135deg, var(--dark-green), var(--emerald));
    }
    .sp-forgot-page .card-brand {
        text-align: center;
        font-size: 26px;
        font-weight: 800;
        color: var(--dark-teal);
    }
    .sp-forgot-page .card-brand span { color: var(--emerald); }
    .sp-forgot-page .card-brand-subtitle {
        text-align: center;
        margin-top: 3px;
        font-size: 10px;
        color: #65827c;
        font-weight: 600;
    }

    .sp-forgot-page .reset-heading {
        display: flex;
        align-items: center;
        gap: 13px;
        margin-top: 34px;
    }
    .sp-forgot-page .lock {
        color: var(--emerald);
        font-size: 24px;
    }
    .sp-forgot-page .reset-heading h1 {
        font-size: 23px;
        font-weight: 800;
        color: var(--dark-teal);
    }
    .sp-forgot-page .helper {
        margin: 12px 0 25px 37px;
        font-size: 13px;
        line-height: 1.6;
        color: var(--muted);
    }

    /* FORM */
    .sp-forgot-page .form-group { margin-bottom: 20px; }
    .sp-forgot-page .form-label {
        display: block;
        margin-bottom: 8px;
        font-size: 13px;
        font-weight: 800;
        color: var(--dark-teal);
    }
    .sp-forgot-page .input-wrapper { position: relative; }
    .sp-forgot-page .input-icon {
        position: absolute;
        left: 17px; top: 50%;
        transform: translateY(-50%);
        color: #0b9d78;
        font-size: 15px;
    }
    .sp-forgot-page .email-input {
        width: 100%;
        height: 54px;
        border: 2px solid #10a879;
        border-radius: 15px;
        padding: 0 18px 0 47px;
        font-family: inherit;
        font-size: 14px;
        outline: none;
        background: white;
        color: var(--dark-teal);
        transition: .2s ease;
    }
    .sp-forgot-page .email-input::placeholder { color: #a5b6b3; }
    .sp-forgot-page .email-input:focus {
        border-color: var(--dark-green);
        box-shadow: 0 0 0 4px rgba(16, 185, 129, .12);
    }
    .sp-forgot-page .email-input.is-error {
        border-color: #ef4444;
        background: #fef2f2;
    }
    .sp-forgot-page .email-input.is-error:focus {
        box-shadow: 0 0 0 4px rgba(239, 68, 68, .15);
    }
    .sp-forgot-page .field-error {
        margin-top: 6px;
        font-size: 12px;
        color: #dc2626;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .sp-forgot-page .submit-btn {
        width: 100%;
        height: 54px;
        border: none;
        border-radius: 15px;
        margin-top: 2px;
        background: linear-gradient(135deg, #059669, #079f79);
        color: white;
        font-family: inherit;
        font-size: 14px;
        font-weight: 800;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        transition: .2s ease;
        box-shadow: 0 10px 22px rgba(5, 150, 105, .18);
    }
    .sp-forgot-page .submit-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 14px 28px rgba(5, 150, 105, .25);
    }
    .sp-forgot-page .submit-btn:focus-visible {
        outline: 3px solid rgba(16, 185, 129, .35);
        outline-offset: 3px;
    }

    .sp-forgot-page .back-login {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 8px;
        margin-top: 32px;
        color: var(--green);
        font-size: 12px;
        font-weight: 800;
        text-decoration: none;
    }
    .sp-forgot-page .back-login:hover {
        color: var(--dark-green);
        text-decoration: underline;
    }

    /* ALERT */
    .sp-forgot-page .alert {
        padding: 13px 15px;
        margin-bottom: 18px;
        border-radius: 12px;
        font-size: 12px;
        line-height: 1.5;
    }
    .sp-forgot-page .alert-success {
        background: #ecfdf5;
        border: 1px solid #a7f3d0;
        color: #065f46;
    }
    .sp-forgot-page .alert-error {
        background: #fff1f2;
        border: 1px solid #fecdd3;
        color: #9f1239;
    }

    /* SUCCESS STATE */
    .sp-forgot-page .success-state { text-align: center; padding: 8px 0; }
    .sp-forgot-page .success-icon {
        width: 64px; height: 64px;
        margin: 0 auto 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: rgba(16, 185, 129, .12);
        color: var(--emerald);
        font-size: 28px;
    }
    .sp-forgot-page .success-state h2 {
        font-size: 20px;
        font-weight: 800;
        color: var(--dark-teal);
        margin-bottom: 6px;
    }
    .sp-forgot-page .success-state p {
        font-size: 13px;
        color: var(--muted);
        line-height: 1.6;
        margin-bottom: 22px;
    }
    .sp-forgot-page .btn-gmail {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        height: 50px;
        border-radius: 15px;
        background: linear-gradient(135deg, #059669, #079f79);
        color: white;
        font-weight: 800;
        font-size: 14px;
        text-decoration: none;
        transition: .2s ease;
        box-shadow: 0 10px 22px rgba(5, 150, 105, .18);
    }
    .sp-forgot-page .btn-gmail:hover {
        transform: translateY(-2px);
        box-shadow: 0 14px 28px rgba(5, 150, 105, .25);
    }
    .sp-forgot-page .resend-text {
        margin-top: 16px;
        font-size: 11px;
        color: var(--muted);
    }
    .sp-forgot-page .resend-text a {
        color: var(--green);
        font-weight: 700;
        text-decoration: underline;
    }

    /* FOOTER */
    .sp-forgot-page .footer {
        background: var(--dark-teal);
        color: white;
        min-height: 78px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 18px 55px;
        font-size: 11px;
    }
    .sp-forgot-page .footer-left {
        display: flex;
        align-items: center;
        gap: 15px;
    }
    .sp-forgot-page .footer-icon {
        width: 38px; height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid rgba(255,255,255,.3);
        border-radius: 10px;
        font-size: 15px;
    }
    .sp-forgot-page .footer-divider {
        width: 1px; height: 35px;
        background: rgba(255,255,255,.3);
    }
    .sp-forgot-page .footer-title { font-weight: 700; }
    .sp-forgot-page .footer-subtitle {
        margin-top: 3px;
        color: #b9d7d1;
    }
    .sp-forgot-page .copyright { color: #b9d7d1; }

    /* RESPONSIVE */
    @media (max-width: 1100px) {
        .sp-forgot-page .main {
            grid-template-columns: 1fr;
            gap: 30px;
        }
        .sp-forgot-page .intro {
            padding-left: 0;
            text-align: center;
        }
        .sp-forgot-page .description {
            margin-left: auto;
            margin-right: auto;
        }
        .sp-forgot-page .features {
            margin-left: auto;
            margin-right: auto;
        }
        .sp-forgot-page .city {
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
        }
        .sp-forgot-page .reset-wrapper { margin-bottom: 25px; }
    }

    @media (max-width: 700px) {
        .sp-forgot-page .header { padding: 20px; }
        .sp-forgot-page .brand-name { font-size: 23px; }
        .sp-forgot-page .brand-logo {
            width: 48px; height: 48px;
            font-size: 21px;
        }
        .sp-forgot-page .tagline { display: none; }
        .sp-forgot-page .main { padding: 25px 20px 35px; }
        .sp-forgot-page .welcome { font-size: 21px; }
        .sp-forgot-page .title {
            font-size: 52px;
            letter-spacing: -3px;
        }
        .sp-forgot-page .description { font-size: 14px; }
        .sp-forgot-page .features {
            grid-template-columns: repeat(2, 1fr);
            gap: 25px 10px;
        }
        .sp-forgot-page .city { height: 290px; }
        .sp-forgot-page .reset-card {
            padding: 28px 22px;
            border-radius: 18px;
        }
        .sp-forgot-page .reset-heading h1 { font-size: 21px; }
        .sp-forgot-page .helper { margin-left: 0; }
        .sp-forgot-page .footer {
            padding: 20px;
            flex-direction: column;
            gap: 15px;
            text-align: center;
        }
        .sp-forgot-page .footer-left { flex-direction: column; }
        .sp-forgot-page .footer-divider { display: none; }
    }

    @media (prefers-reduced-motion: reduce) {
        .sp-forgot-page *,
        .sp-forgot-page *::before,
        .sp-forgot-page *::after {
            transition-duration: .01ms !important;
            animation-duration: .01ms !important;
        }
    }
</style>

{{-- ============================================
     HTML CONTENT
============================================ --}}
<div class="sp-forgot-page">

    <div class="shape shape-one"></div>
    <div class="shape shape-two"></div>

    <div class="page">

        {{-- HEADER --}}
        <header class="header">
            <a href="{{ url('/') }}" class="brand" aria-label="SmartPath">
                <div class="brand-logo">
                    <img src="{{ asset('favicon.png') }}" alt="" aria-hidden="true">
                </div>
                <div>
                    <div class="brand-name">Smart<span>Path</span></div>
                    <div class="brand-subtitle">Sistem Informasi Penanganan Aksesibilitas</div>
                </div>
            </a>

            <div class="tagline">
                Kota Lebih Aksesibel,<br>Untuk Semua
            </div>
        </header>

        {{-- MAIN --}}
        <main class="main">

            {{-- LEFT --}}
            <section class="intro">
                <div class="welcome">Selamat Datang di</div>
                <h2 class="title">Smart<span>Path</span></h2>

                <p class="description">
                    Platform digital untuk melaporkan, memantau,
                    dan menindaklanjuti kondisi fasilitas publik
                    yang belum ramah aksesibilitas di Kota Depok.
                </p>

                {{-- FEATURES --}}
                <div class="features">
                    <div class="feature">
                        <div class="feature-icon"><i class="fa-solid fa-file-circle-plus" aria-hidden="true"></i></div>
                        <h3>Laporkan</h3>
                        <p>Temukan hambatan aksesibilitas di sekitarmu</p>
                    </div>
                    <div class="feature">
                        <div class="feature-icon"><i class="fa-solid fa-location-dot" aria-hidden="true"></i></div>
                        <h3>Pantau</h3>
                        <p>Lihat status penanganan secara real-time</p>
                    </div>
                    <div class="feature">
                        <div class="feature-icon"><i class="fa-solid fa-users" aria-hidden="true"></i></div>
                        <h3>Bersama</h3>
                        <p>Wujudkan kota yang lebih inklusif</p>
                    </div>
                    <div class="feature">
                        <div class="feature-icon"><i class="fa-solid fa-shield-heart" aria-hidden="true"></i></div>
                        <h3>Aksesibel</h3>
                        <p>Untuk semua kalangan</p>
                    </div>
                </div>

                {{-- HERO IMAGE --}}
                <div class="city">
                    <img
                        src="{{ asset('foto-disabilitas.png') }}"
                        alt="Ilustrasi kota aksesibel dengan pengguna kursi roda"
                    >
                </div>
            </section>

            {{-- RIGHT --}}
            <section class="reset-wrapper">
                <div class="reset-card">

                    {{-- CARD BRAND --}}
                    <div class="card-logo">
                        <img src="{{ asset('favicon.png') }}" alt="" aria-hidden="true">
                    </div>
                    <div class="card-brand">Smart<span>Path</span></div>
                    <div class="card-brand-subtitle">Sistem Informasi Penanganan Aksesibilitas</div>

                    @if (session('status'))
                        {{-- ============ SUCCESS STATE ============ --}}
                        <div class="success-state">
                            <div class="success-icon">
                                <i class="fa-solid fa-check" aria-hidden="true"></i>
                            </div>
                            <h2>Cek Email Kamu!</h2>
                            <p>{{ session('status') }}</p>

                            <a href="https://mail.google.com" target="_blank" rel="noopener" class="btn-gmail">
                                <i class="fa-solid fa-envelope" aria-hidden="true"></i>
                                Buka Gmail
                            </a>

                            <p class="resend-text">
                                Tidak menerima email? Cek folder spam atau
                                <a href="{{ route('password.request') }}">coba lagi</a>.
                            </p>
                        </div>
                    @else
                        {{-- ============ FORM STATE ============ --}}
                        <div class="reset-heading">
                            <div class="lock">
                                <i class="fa-solid fa-lock" aria-hidden="true"></i>
                            </div>
                            <h1>Lupa Kata Sandi?</h1>
                        </div>

                        <p class="helper">
                            Masukkan email kamu dan kami akan
                            mengirimkan link untuk mereset kata sandi.
                        </p>

                        {{-- ERROR GLOBAL --}}
                        @if ($errors->any())
                            <div class="alert alert-error" role="alert">
                                {{ $errors->first() }}
                            </div>
                        @endif

                        {{-- FORM --}}
                        <form method="POST" action="{{ route('password.email') }}">
                            @csrf

                            <div class="form-group">
                                <label for="email" class="form-label">Alamat Email</label>
                                <div class="input-wrapper">
                                    <i class="fa-regular fa-envelope input-icon" aria-hidden="true"></i>
                                    <input
                                        id="email"
                                        type="email"
                                        name="email"
                                        value="{{ old('email') }}"
                                        class="email-input @error('email') is-error @enderror"
                                        placeholder="contoh@gmail.com"
                                        autocomplete="email"
                                        required
                                        autofocus
                                        aria-describedby="@error('email') email-error @enderror">

                                    @error('email')
                                        <p id="email-error" class="field-error">
                                            <i class="fa-solid fa-circle-exclamation" aria-hidden="true"></i>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            </div>

                            <button type="submit" class="submit-btn">
                                <i class="fa-regular fa-paper-plane" aria-hidden="true"></i>
                                Kirim Link Reset
                            </button>
                        </form>

                        {{-- BACK --}}
                        <a href="{{ route('login') }}" class="back-login">
                            <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
                            Kembali ke Login
                        </a>
                    @endif

                </div>
            </section>

        </main>

        {{-- FOOTER --}}
        <footer class="footer">
            <div class="footer-left">
                <div class="footer-icon">
                    <i class="fa-solid fa-city" aria-hidden="true"></i>
                </div>
                <div>
                    <div class="footer-title">Pemerintah Kota Depok</div>
                    <div class="footer-subtitle">Bersama Mewujudkan Kota yang Lebih Aksesibel</div>
                </div>
                <div class="footer-divider"></div>
            </div>
            <div class="copyright">
                © {{ date('Y') }} SmartPath. Semua hak cipta dilindungi.
            </div>
        </footer>

    </div>
</div>

@endsection