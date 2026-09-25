@extends('layouts.app')

@section('title', 'Dashboard Warga - SmartPath')

@section('content')
    @include('partials.nav-public')

    <main class="max-w-7xl mx-auto w-full px-4 py-10 sm:px-6 lg:px-8">
        @if(session('sukses'))
            <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                {{ session('sukses') }}
            </div>
        @endif

        {{-- HEADER & SCREEN READER --}}
        <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wide text-emerald-600">Dashboard Warga</p>
                <h1 class="mt-2 text-3xl font-bold text-slate-900">Halo, {{ $user->nama_lengkap ?? 'Warga' }}</h1>
                <p class="mt-2 text-slate-600">Kelola laporan aksesibilitas dan pantau perkembangannya di sini.</p>
            </div>

            {{-- TOMBOL SCREEN READER (DENGAR PANDUAN) --}}
            <div>
                <button
                    type="button"
                    id="btn-read-dashboard"
                    class="flex items-center gap-2 rounded-2xl border border-emerald-300 bg-emerald-50/50 px-4 py-2.5 text-xs font-bold text-emerald-700 hover:bg-emerald-100 focus:outline-none focus:ring-2 focus:ring-emerald-500 shadow-sm"
                    title="Dengarkan panduan halaman ini"
                    aria-label="Dengarkan panduan halaman ini"
                >
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/>
                        <path d="M15.54 8.46a5 5 0 0 1 0 7.07"/>
                        <path d="M19.07 4.93a10 10 0 0 1 0 14.14"/>
                    </svg>
                    <span>Dengar Panduan</span>
                </button>
            </div>
        </div>

        {{-- GRID UTAMA --}}
        <div class="grid gap-6 md:grid-cols-3">
            <!-- Buat Laporan -->
            <a href="{{ route('laporan.create') }}" class="rounded-2xl bg-emerald-600 p-6 text-white shadow-sm transition hover:bg-emerald-700 flex flex-col justify-between">
                <div>
                    <div class="w-10 h-10 rounded-xl bg-emerald-500/30 flex items-center justify-center mb-4">
                        <i data-lucide="plus-circle" class="w-6 h-6 text-white"></i>
                    </div>
                    <h2 class="text-lg font-bold">Buat Laporan</h2>
                    <p class="mt-2 text-sm text-emerald-50">Laporkan hambatan aksesibilitas di sekitar Anda.</p>
                </div>
            </a>

            <!-- Laporan Saya -->
            <a href="{{ route('laporan.index') }}" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:border-emerald-500 hover:shadow-md flex flex-col justify-between group">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600">
                            <i data-lucide="file-text" class="w-6 h-6"></i>
                        </div>
                        <span class="text-3xl font-bold text-slate-900">{{ $jumlahLaporan ?? 0 }}</span>
                    </div>
                    <h2 class="text-lg font-bold text-slate-900 group-hover:text-emerald-600 transition">Laporan Saya</h2>
                    <p class="mt-2 text-sm text-slate-600">Lihat dan kelola seluruh laporan yang pernah Anda buat.</p>
                </div>
            </a>

            <!-- Peta Aksesibilitas -->
            <a href="{{ route('peta.index') }}" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:border-emerald-500 hover:shadow-md flex flex-col justify-between group">
                <div>
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600 mb-4">
                        <i data-lucide="map-pin" class="w-6 h-6"></i>
                    </div>
                    <h2 class="text-lg font-bold text-slate-900 group-hover:text-emerald-600 transition">Peta Aksesibilitas</h2>
                    <p class="mt-2 text-sm text-slate-600">Jelajahi laporan dan fasilitas publik disabilitas.</p>
                </div>
            </a>
        </div>
    </main>

    {{-- SCRIPT DENGAR PANDUAN --}}
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const btnReadDashboard = document.getElementById('btn-read-dashboard');

        if ('speechSynthesis' in window && btnReadDashboard) {
            let voices = [];
            function loadVoices() {
                voices = window.speechSynthesis.getVoices();
            }
            loadVoices();
            if (window.speechSynthesis.onvoiceschanged !== undefined) {
                window.speechSynthesis.onvoiceschanged = loadVoices;
            }

            btnReadDashboard.addEventListener('click', function () {
                if (window.speechSynthesis.speaking) {
                    window.speechSynthesis.cancel();
                    btnReadDashboard.querySelector('span').innerText = 'Dengar Panduan';
                    return;
                }

                const textToRead = `Selamat datang di Dashboard Warga SmartPath, {{ $user->nama_lengkap ?? 'Warga' }}. Di sini Anda dapat membuat laporan hambatan aksesibilitas baru, melihat total {{ $jumlahLaporan ?? 0 }} laporan Anda, atau melihat peta aksesibilitas fasilitas publik.`;
                const utterance = new SpeechSynthesisUtterance(textToRead);

                const idVoice = voices.find(v => v.lang.includes('id') || v.lang.includes('ID'));
                if (idVoice) {
                    utterance.voice = idVoice;
                }
                utterance.lang = 'id-ID';
                utterance.rate = 0.9;

                utterance.onstart = function () {
                    btnReadDashboard.querySelector('span').innerText = 'Berhenti';
                };

                utterance.onend = function () {
                    btnReadDashboard.querySelector('span').innerText = 'Dengar Panduan';
                };

                utterance.onerror = function () {
                    btnReadDashboard.querySelector('span').innerText = 'Dengar Panduan';
                };

                window.speechSynthesis.speak(utterance);
            });
        } else if (btnReadDashboard) {
            btnReadDashboard.style.display = 'none';
        }
    });
    </script>
@endsection