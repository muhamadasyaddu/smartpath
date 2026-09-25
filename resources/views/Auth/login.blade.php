@extends('layouts.guest')

@section('title', 'Masuk - SmartPath')

@section('content')

<div class="flex min-h-screen w-full items-end justify-center bg-[#f3faf7] p-0 sm:items-center sm:p-4">

    <div class="relative w-full max-w-lg overflow-hidden bg-white sm:rounded-[2rem] sm:shadow-[0_20px_60px_rgba(15,23,42,0.12)]">

        {{-- ==========================================================
             ACCESSIBILITY ILLUSTRATION
        =========================================================== --}}
        <div class="relative h-[230px] overflow-hidden bg-emerald-50 sm:h-[400px]">
            <img
                src="{{ asset('foto-disabilitas.png') }}"
                alt="Ilustrasi kota yang aksesibel dengan pengguna kursi roda dan pejalan kaki"
                class="absolute inset-0 h-full w-full object-cover object-bottom"
            >
            <div class="absolute inset-x-0 bottom-0 h-32 bg-gradient-to-t from-white/80 to-transparent"></div>
        </div>

        {{-- ==========================================================
             LOGIN CARD
        =========================================================== --}}
        <div class="relative z-20 -mt-10 rounded-t-[2rem] bg-white px-6 pb-8 pt-6 shadow-[0_-10px_30px_rgba(15,23,42,0.06)] sm:px-8 sm:pt-7">

            {{-- HEADER & BRAND --}}
            <div class="mb-6 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-emerald-600 text-white shadow-[0_8px_20px_rgba(5,150,105,0.22)]">
                        <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 5.5L9 3l6 2.5L20 3v15.5L15 21l-6-2.5L4 21V5.5Z"/>
                            <path d="M9 3v15.5"/>
                            <path d="M15 5.5V21"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-xl font-extrabold tracking-tight text-slate-900">
                            Smart<span class="text-emerald-600">Path</span>
                        </div>
                        <p class="text-[11px] font-medium text-slate-400">
                            Pemetaan aksesibilitas
                        </p>
                    </div>
                </div>

                {{-- SCREEN READER BUTTON --}}
                <button
                    type="button"
                    id="btn-read-page"
                    class="flex items-center gap-1.5 rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs font-bold text-emerald-700 hover:bg-emerald-100 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                    title="Dengarkan isi halaman ini"
                    aria-label="Bacakan isi halaman ini"
                >
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/>
                        <path d="M15.54 8.46a5 5 0 0 1 0 7.07"/>
                        <path d="M19.07 4.93a10 10 0 0 1 0 14.14"/>
                    </svg>
                    <span>Bacakan</span>
                </button>
            </div>

            {{-- TITLE --}}
            <div class="mb-7">
                <h2 class="text-[27px] font-extrabold leading-tight tracking-tight text-slate-900">
                    Selamat datang kembali
                </h2>
                <p class="mt-2 max-w-[320px] text-sm leading-6 text-slate-500">
                    Masuk untuk melanjutkan perjalanan menuju kota yang lebih aksesibel.
                </p>
            </div>

            {{-- ERROR ALERT SUMMARY --}}
            @if($errors->has('email') && !$errors->has('kata_sandi'))
                <div class="mb-5 flex items-start gap-3 rounded-2xl border border-red-200 bg-red-50 px-4 py-3.5 text-sm text-red-800" role="alert">
                    <div class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-red-100">
                        <svg class="h-4 w-4 text-red-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="12" y1="8" x2="12" y2="12"/>
                            <line x1="12" y1="16" x2="12.01" y2="16"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-bold">Gagal masuk</p>
                        <p class="mt-0.5 text-xs leading-5 text-red-700">
                            {{ $errors->first('email') }}
                        </p>
                    </div>
                </div>
            @endif

            {{-- FORM LOGIN --}}
            <form method="POST" action="{{ route('login.post') }}" novalidate class="space-y-5">
                @csrf

                {{-- FIELD EMAIL --}}
                <div>
                    <div class="mb-2 flex items-center justify-between">
                        <label for="email" class="text-sm font-bold text-slate-800">
                            Email
                        </label>
                        {{-- Voice Button --}}
                        <button
                            type="button"
                            class="btn-voice flex items-center gap-1 text-xs font-semibold text-emerald-600 hover:text-emerald-700 focus:outline-none"
                            data-target="email"
                            aria-label="Mulai bicara untuk mengisi email"
                        >
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3z"/>
                                <path d="M19 10v2a7 7 0 0 1-14 0v-2"/>
                                <line x1="12" y1="19" x2="12" y2="22"/>
                            </svg>
                            <span>Dikte</span>
                        </button>
                    </div>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                            <svg class="h-5 w-5 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <rect x="3" y="5" width="18" height="14" rx="2"/>
                                <polyline points="3 7 12 13 21 7"/>
                            </svg>
                        </div>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="Masukkan email"
                            autocomplete="email"
                            required
                            aria-describedby="email-error"
                            class="h-14 w-full rounded-2xl border border-slate-200 bg-white pl-12 pr-4 text-sm text-slate-900 shadow-[0_2px_8px_rgba(15,23,42,0.03)] outline-none transition-all duration-200 placeholder:text-slate-400 hover:border-slate-300 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10"
                        >
                    </div>
                    @if($errors->has('email'))
                        <p id="email-error" class="mt-2 text-xs font-semibold text-red-600" role="alert">
                            {{ $errors->first('email') }}
                        </p>
                    @endif
                </div>

                {{-- FIELD KATA SANDI --}}
                <div>
                    <div class="mb-2 flex items-center justify-between">
                        <label for="kata_sandi" class="text-sm font-bold text-slate-800">
                            Kata Sandi
                        </label>
                        {{-- Voice Button --}}
                        <button
                            type="button"
                            class="btn-voice flex items-center gap-1 text-xs font-semibold text-emerald-600 hover:text-emerald-700 focus:outline-none"
                            data-target="kata_sandi"
                            aria-label="Mulai bicara untuk mengisi kata sandi"
                        >
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3z"/>
                                <path d="M19 10v2a7 7 0 0 1-14 0v-2"/>
                                <line x1="12" y1="19" x2="12" y2="22"/>
                            </svg>
                            <span>Dikte</span>
                        </button>
                    </div>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                            <svg class="h-5 w-5 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <rect x="4" y="10" width="16" height="11" rx="2"/>
                                <path d="M8 10V7a4 4 0 0 1 8 0v3"/>
                            </svg>
                        </div>
                        <input
                            type="password"
                            id="kata_sandi"
                            name="kata_sandi"
                            placeholder="Masukkan kata sandi"
                            autocomplete="current-password"
                            required
                            aria-describedby="password-error"
                            class="h-14 w-full rounded-2xl border border-slate-200 bg-white pl-12 pr-14 text-sm text-slate-900 shadow-[0_2px_8px_rgba(15,23,42,0.03)] outline-none transition-all duration-200 placeholder:text-slate-400 hover:border-slate-300 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10"
                        >
                        {{-- Toggle Password --}}
                        <button
                            type="button"
                            id="toggle-password"
                            class="absolute inset-y-0 right-0 flex w-12 items-center justify-center rounded-r-2xl text-slate-400 transition hover:text-emerald-600 focus:outline-none"
                            aria-label="Tampilkan kata sandi"
                        >
                            <svg id="password-eye" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"/>
                                <circle cx="12" cy="12" r="2.5"/>
                            </svg>
                        </button>
                    </div>
                    @if($errors->has('kata_sandi'))
                        <p id="password-error" class="mt-2 text-xs font-semibold text-red-600" role="alert">
                            {{ $errors->first('kata_sandi') }}
                        </p>
                    @endif
                </div>

                {{-- REMEMBER ME & FORGOT PASSWORD --}}
                <div class="flex items-center justify-between pt-0.5">
                    <label for="remember" class="flex cursor-pointer items-center gap-2.5">
                        <input
                            type="checkbox"
                            id="remember"
                            name="remember"
                            value="1"
                            class="h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500"
                        >
                        <span class="text-sm font-medium text-slate-600">
                            Ingat saya
                        </span>
                    </label>

                    <a href="{{ route('password.request') }}" class="text-sm font-bold text-emerald-600 transition hover:text-emerald-700">
                        Lupa kata sandi?
                    </a>
                </div>

                {{-- SUBMIT BUTTON --}}
                <button
                    type="submit"
                    class="group flex h-14 w-full items-center justify-center gap-2 rounded-2xl bg-emerald-600 px-5 text-sm font-bold text-white shadow-[0_8px_20px_rgba(5,150,105,0.20)] transition-all duration-200 hover:bg-emerald-700 hover:shadow-[0_10px_25px_rgba(5,150,105,0.25)] active:scale-[0.985] focus:outline-none focus:ring-4 focus:ring-emerald-500/20"
                >
                    <span>Masuk ke SmartPath</span>
                    <svg class="h-5 w-5 transition-transform duration-200 group-hover:translate-x-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M5 12h14"/>
                        <path d="m13 6 6 6-6 6"/>
                    </svg>
                </button>
            </form>

            {{-- SOCIAL LOGIN --}}
            <div class="relative my-7 flex items-center">
                <div class="h-px flex-1 bg-slate-200"></div>
                <span class="mx-4 whitespace-nowrap text-xs font-medium text-slate-400">
                    atau masuk dengan
                </span>
                <div class="h-px flex-1 bg-slate-200"></div>
            </div>

            <div>
                <a
                    href="{{ route('auth.google') }}"
                    class="flex h-12 w-full items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-white text-sm font-semibold text-slate-700 transition hover:border-slate-300 hover:bg-slate-50 focus:outline-none"
                >
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none">
                        <path d="M21.35 12.2c0-.78-.07-1.53-.22-2.25H12v4.26h5.24a4.48 4.48 0 01-1.94 2.94v2.45h3.15c1.84-1.69 2.9-4.18 2.9-7.4z" fill="#4285F4" />
                        <path d="M12 21.5c2.63 0 4.83-.87 6.44-2.36l-3.15-2.45c-.87.58-1.98.93-3.29.93-2.53 0-4.67-1.71-5.44-4.01H3.3v2.53A9.72 9.72 0 0012 21.5z" fill="#34A853" />
                        <path d="M6.56 13.61A5.84 5.84 0 016.25 12c0-.56.1-1.1.31-1.61V7.86H3.3A9.72 9.72 0 002.25 12c0 1.57.38 3.05 1.05 4.34l3.26-2.73z" fill="#FBBC05" />
                        <path d="M12 6.38c1.43 0 2.71.49 3.72 1.45l2.79-2.79C16.82 3.5 14.63 2.5 12 2.5a9.72 9.72 0 00-8.7 5.36l3.26 2.53C7.33 8.09 9.47 6.38 12 6.38z" fill="#EA4335" />
                    </svg>
                    <span>Google</span>
                </a>
            </div>

            {{-- REGISTER LINK --}}
            <div class="mt-7 text-center">
                <p class="text-sm text-slate-500">
                    Belum punya akun?
                    <a href="{{ route('auth.register') }}" class="ml-1 font-bold text-emerald-600 transition hover:text-emerald-700">
                        Daftar
                    </a>
                </p>
            </div>

            {{-- FOOTER --}}
            <div class="mt-7 border-t border-slate-100 pt-5 text-center">
                <p class="text-[11px] font-medium text-slate-400">
                    SmartPath • Pemetaan Aksesibilitas Infrastruktur
                </p>
            </div>

        </div>

    </div>

</div>

{{-- SCRIPT ACCESSIBILITY (SAMA DENGAN REGISTER) --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    
    // ==========================================
    // 1. TOGGLE PASSWORD
    // ==========================================
    const toggleButton = document.getElementById('toggle-password');
    const passwordInput = document.getElementById('kata_sandi');
    const eyeIcon = document.getElementById('password-eye');

    if (toggleButton && passwordInput && eyeIcon) {
        toggleButton.addEventListener('click', function () {
            const isPassword = passwordInput.type === 'password';
            passwordInput.type = isPassword ? 'text' : 'password';

            if (isPassword) {
                eyeIcon.innerHTML = `
                    <path d="M3 3l18 18"/>
                    <path d="M10.6 10.6a2 2 0 0 0 2.8 2.8"/>
                    <path d="M9.9 4.2A10.8 10.8 0 0 1 12 4c6.5 0 10 8 10 8a17.8 17.8 0 0 1-3.1 4.3"/>
                    <path d="M6.6 6.6C3.8 8.4 2 12 2 12s3.5 8 10 8c1.4 0 2.7-.3 3.9-.9"/>
                `;
            } else {
                eyeIcon.innerHTML = `
                    <path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"/>
                    <circle cx="12" cy="12" r="2.5"/>
                `;
            }
        });
    }

    // ==========================================
    // 2. VOICE TO TEXT (SPEECH RECOGNITION)
    // ==========================================
    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;

    if (SpeechRecognition) {
        const voiceButtons = document.querySelectorAll('.btn-voice');

        voiceButtons.forEach(button => {
            const recognition = new SpeechRecognition();
            recognition.lang = 'id-ID';
            recognition.continuous = false;
            recognition.interimResults = false;

            button.addEventListener('click', function () {
                const targetId = this.getAttribute('data-target');
                const targetInput = document.getElementById(targetId);

                recognition.start();
                this.classList.add('animate-pulse', 'text-red-600');

                recognition.onresult = function (event) {
                    const transcript = event.results[0][0].transcript;
                    // Hapus spasi agar cocok untuk email/password
                    targetInput.value = transcript.replace(/\s+/g, '').toLowerCase();
                };

                recognition.onspeechend = function () {
                    recognition.stop();
                    button.classList.remove('animate-pulse', 'text-red-600');
                };

                recognition.onerror = function () {
                    button.classList.remove('animate-pulse', 'text-red-600');
                    alert('Suara tidak terdeteksi atau mikrofon tidak diizinkan.');
                };
            });
        });
    } else {
        document.querySelectorAll('.btn-voice').forEach(btn => btn.style.display = 'none');
    }

    // ==========================================
    // 3. SCREEN READER (TEXT TO SPEECH FIX)
    // ==========================================
    const btnReadPage = document.getElementById('btn-read-page');

    if ('speechSynthesis' in window && btnReadPage) {
        
        // Memuat daftar suara browser
        let voices = [];
        function loadVoices() {
            voices = window.speechSynthesis.getVoices();
        }
        loadVoices();
        if (window.speechSynthesis.onvoiceschanged !== undefined) {
            window.speechSynthesis.onvoiceschanged = loadVoices;
        }

        btnReadPage.addEventListener('click', function () {
            if (window.speechSynthesis.speaking) {
                window.speechSynthesis.cancel();
                btnReadPage.querySelector('span').innerText = 'Bacakan';
                return;
            }

            const pageText = `Selamat datang kembali di SmartPath. Masuk untuk melanjutkan perjalanan menuju kota yang lebih aksesibel. Silakan masukkan Email dan Kata Sandi Anda.`;
            const utterance = new SpeechSynthesisUtterance(pageText);

            // Cari suara Bahasa Indonesia, jika tidak ada gunakan default
            const idVoice = voices.find(v => v.lang.includes('id') || v.lang.includes('ID'));
            if (idVoice) {
                utterance.voice = idVoice;
            }
            utterance.lang = 'id-ID';
            utterance.rate = 0.9;

            utterance.onstart = function () {
                btnReadPage.querySelector('span').innerText = 'Berhenti';
            };

            utterance.onend = function () {
                btnReadPage.querySelector('span').innerText = 'Bacakan';
            };

            utterance.onerror = function () {
                btnReadPage.querySelector('span').innerText = 'Bacakan';
            };

            window.speechSynthesis.speak(utterance);
        });
    } else if (btnReadPage) {
        btnReadPage.style.display = 'none';
    }

});
</script>

@endsection
