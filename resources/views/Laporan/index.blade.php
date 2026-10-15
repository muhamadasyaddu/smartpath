@extends('layouts.app')

@section('title', 'Laporan Saya - SmartPath')

@push('styles')
<style>
    #homeScreenReaderButtonContainer {
        touch-action: none;
        user-select: none;
        -webkit-user-select: none;
        cursor: move;
        max-width: calc(100vw - 16px);
    }

    #homeScreenReaderButton {
        touch-action: none;
    }

    #homeSpeechStatus {
        position: absolute;
        width: 1px;
        height: 1px;
        padding: 0;
        margin: -1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        white-space: nowrap;
        border: 0;
    }

    @media (max-width: 640px) {
        #homeScreenReaderButtonContainer {
            right: 12px;
            bottom: 12px;
        }

        #homeScreenReaderButton {
            padding: 10px 14px;
        }
    }
</style>
@endpush

@section('content')
@include('partials.nav-public')

<main id="laporan-saya-main"
    class="max-w-7xl mx-auto w-full px-4 py-8 sm:px-6 lg:px-8">

    {{-- Notifikasi sukses --}}
    @if (session('sukses'))
        <div
            class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800 flex items-center gap-2"
            role="alert"
            aria-live="polite">

            <i data-lucide="check-circle"
                class="w-5 h-5 text-emerald-600"
                aria-hidden="true"></i>

            <span>{{ session('sukses') }}</span>
        </div>
    @endif

    {{-- Notifikasi error --}}
    @if (session('galat'))
        <div
            class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-800 flex items-center gap-2"
            role="alert"
            aria-live="polite">

            <i data-lucide="alert-circle"
                class="w-5 h-5 text-red-600"
                aria-hidden="true"></i>

            <span>{{ session('galat') }}</span>
        </div>
    @endif

    {{-- Header halaman --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">
                Laporan Saya
            </h1>

            <p class="text-sm text-slate-500 mt-1">
                Daftar laporan hambatan yang Anda buat.
            </p>
        </div>

        <a
            href="{{ route('laporan.create') }}"
            class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">

            <i data-lucide="plus"
                class="w-4 h-4"
                aria-hidden="true"></i>

            Buat Laporan Baru
        </a>
    </div>

    {{-- Filter status --}}
    @php
        $currentStatus = request('status');

        $filters = [
            [
                'key' => null,
                'label' => 'Semua',
                'icon' => 'list-filter',
                'count' => $statusCounts['semua'] ?? 0,
            ],
            [
                'key' => 'menunggu_verifikasi',
                'label' => 'Menunggu',
                'icon' => 'clock',
                'count' => $statusCounts['menunggu_verifikasi'] ?? 0,
            ],
            [
                'key' => 'diverifikasi',
                'label' => 'Diverifikasi',
                'icon' => 'check-circle-2',
                'count' => $statusCounts['diverifikasi'] ?? 0,
            ],
            [
                'key' => 'dalam_perbaikan',
                'label' => 'Perbaikan',
                'icon' => 'wrench',
                'count' => $statusCounts['dalam_perbaikan'] ?? 0,
            ],
            [
                'key' => 'selesai',
                'label' => 'Selesai',
                'icon' => 'circle-check',
                'count' => $statusCounts['selesai'] ?? 0,
            ],
            [
                'key' => 'ditolak',
                'label' => 'Ditolak',
                'icon' => 'circle-x',
                'count' => $statusCounts['ditolak'] ?? 0,
            ],
        ];
    @endphp

    <nav
        class="flex items-center gap-2 overflow-x-auto pb-2 mb-6 text-sm"
        aria-label="Filter status laporan">

        @foreach ($filters as $filter)
            @php
                $isActive = $currentStatus === $filter['key']
                    || (!$currentStatus && $filter['key'] === null);

                $url = $filter['key']
                    ? request()->fullUrlWithQuery([
                        'status' => $filter['key'],
                        'page' => null,
                    ])
                    : request()->fullUrlWithQuery([
                        'status' => null,
                        'page' => null,
                    ]);
            @endphp

            <a
                href="{{ $url }}"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-full border shrink-0 transition
                    {{ $isActive
                        ? 'border-slate-900 bg-white text-slate-900 shadow-sm'
                        : 'border-slate-200 bg-white text-slate-600 hover:border-slate-300' }}"
                @if ($isActive) aria-current="page" @endif>

                <i data-lucide="{{ $filter['icon'] }}"
                    class="w-4 h-4"
                    aria-hidden="true"></i>

                {{ $filter['label'] }}

                <span class="bg-slate-100 text-slate-700 text-xs px-2 py-0.5 rounded-full font-bold">
                    {{ $filter['count'] }}
                </span>
            </a>
        @endforeach
    </nav>

    {{-- Daftar laporan --}}
    <div class="space-y-4">

        @forelse ($laporan as $item)
            <article
                class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4 p-4 rounded-2xl border border-slate-200 bg-white shadow-sm hover:border-slate-300 transition">

                <div class="flex flex-col sm:flex-row items-start gap-4 w-full lg:w-auto flex-1">

                    {{-- Foto laporan --}}
                    <div
                        class="w-full sm:w-36 h-28 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-center shrink-0 overflow-hidden">

                        @if ($item->fotoUtama)
                            <img
                                src="{{ $item->fotoUtama->url }}"
                                alt="Foto utama {{ $item->judul }}"
                                class="w-full h-full object-cover"
                                loading="lazy">
                        @else
                            <i data-lucide="image"
                                class="w-7 h-7 text-slate-300"
                                aria-hidden="true"></i>
                        @endif
                    </div>

                    {{-- Informasi laporan --}}
                    <div class="flex-1 space-y-1.5 min-w-0">

                        <div class="flex flex-wrap items-center gap-2 text-xs">

                            <span class="font-bold text-slate-700">
                                {{ $item->kode_laporan }}
                            </span>

                            <span class="px-2.5 py-0.5 rounded-md {{ $item->warna }} font-medium">
                                {{ $item->status_label }}
                            </span>

                            @if ($item->skor_prioritas !== null)
                                <span
                                    class="px-2.5 py-0.5 rounded-md border border-slate-300 text-slate-700 font-medium flex items-center gap-1">

                                    <i data-lucide="gauge"
                                        class="w-3 h-3 text-amber-500"
                                        aria-hidden="true"></i>

                                    Prioritas {{ $item->tingkat_prioritas }}
                                </span>
                            @endif
                        </div>

                        <h2 class="text-base font-bold text-slate-900 leading-snug">
                            <a
                                href="{{ route('laporan.show', $item) }}"
                                class="hover:text-emerald-600 transition">

                                {{ $item->judul }}
                            </a>
                        </h2>

                        <p class="text-sm text-slate-600 line-clamp-1">
                            {{ $item->deskripsi }}
                        </p>

                        <div class="flex flex-wrap items-center gap-4 text-xs text-slate-500 pt-1">

                            <span class="flex items-center gap-1">
                                <i data-lucide="tag"
                                    class="w-3.5 h-3.5"
                                    aria-hidden="true"></i>

                                {{ $item->kategoriHambatan?->nama ?? '-' }}
                            </span>

                            <span class="flex items-center gap-1">
                                <i data-lucide="map-pin"
                                    class="w-3.5 h-3.5"
                                    aria-hidden="true"></i>

                                {{ $item->wilayah?->nama ?? ($item->alamat_lengkap ?? 'Lokasi tersedia') }}
                            </span>

                            <span class="flex items-center gap-1">
                                <i data-lucide="calendar"
                                    class="w-3.5 h-3.5"
                                    aria-hidden="true"></i>

                                {{ $item->created_at->locale('id')->translatedFormat('d M Y') }}
                            </span>

                            @if ($item->jumlah_pelapor > 1)
                                <span class="flex items-center gap-1">
                                    <i data-lucide="users"
                                        class="w-3.5 h-3.5"
                                        aria-hidden="true"></i>

                                    {{ $item->jumlah_pelapor }} pelapor
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Skor prioritas --}}
                <div class="self-end lg:self-center shrink-0">
                    <div
                        class="w-24 h-16 rounded-xl border border-slate-300 flex flex-col items-center justify-center p-2 text-center bg-white">

                        @if ($item->skor_prioritas !== null)
                            <span class="text-xl font-extrabold text-slate-900 leading-none">
                                {{ number_format((float) $item->skor_prioritas, 1) }}
                            </span>

                            <span class="text-[9px] font-bold tracking-wider text-slate-500 uppercase mt-1">
                                PRIORITAS
                            </span>
                        @else
                            <span class="text-xs font-semibold text-slate-400">
                                Belum dinilai
                            </span>
                        @endif
                    </div>
                </div>
            </article>

        @empty
            <div
                class="text-center py-12 bg-slate-50 rounded-2xl border border-dashed border-slate-200">

                <i data-lucide="folder-open"
                    class="w-10 h-10 text-slate-400 mx-auto mb-2"
                    aria-hidden="true"></i>

                <p class="text-slate-600 font-medium">
                    Belum ada laporan pada filter ini.
                </p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if ($laporan->hasPages())
        <div class="mt-6">
            {{ $laporan->links('pagination::tailwind') }}
        </div>
    @endif

</main>

{{-- Tombol Screen Reader melayang --}}
<div
    id="homeScreenReaderButtonContainer"
    class="fixed bottom-5 right-5 z-[9998] flex flex-wrap justify-end gap-2"
    aria-label="Fitur aksesibilitas suara">

    <button
        id="homeScreenReaderButton"
        type="button"
        class="inline-flex items-center gap-2 rounded-full border border-emerald-700 bg-emerald-700 px-4 py-3 text-sm font-semibold text-white shadow-lg transition hover:bg-emerald-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2"
        aria-label="Mulai pembacaan teks halaman"
        aria-pressed="false">

        <i class="fa-solid fa-blind text-lg shrink-0" aria-hidden="true"></i>

        <span id="homeScreenReaderLabel">
            Screen Reader
        </span>
    </button>

    <p
        id="homeSpeechStatus"
        role="status"
        aria-live="polite"></p>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById(
        'homeScreenReaderButtonContainer'
    );

    const readerButton = document.getElementById(
        'homeScreenReaderButton'
    );

    const readerLabel = document.getElementById(
        'homeScreenReaderLabel'
    );

    const speechStatus = document.getElementById(
        'homeSpeechStatus'
    );

    const main = document.getElementById('laporan-saya-main');

    if (!container || !readerButton || !readerLabel || !main) {
        return;
    }

    /* =========================================
       DRAG AND DROP TOMBOL SCREEN READER
    ========================================= */
    let isDragging = false;
    let hasMoved = false;
    let startX = 0;
    let startY = 0;
    let initialX = 0;
    let initialY = 0;
    let activePointerId = null;

    function dragStart(e) {
        if (e.target.closest('button') &&
            e.target.closest('button') !== readerButton) {
            return;
        }

        if (e.pointerType === 'mouse' && e.button !== 0) {
            return;
        }

        isDragging = true;
        hasMoved = false;
        activePointerId = e.pointerId;

        startX = e.clientX;
        startY = e.clientY;

        const rect = container.getBoundingClientRect();

        initialX = rect.left;
        initialY = rect.top;

        container.style.left = initialX + 'px';
        container.style.top = initialY + 'px';
        container.style.right = 'auto';
        container.style.bottom = 'auto';

        if (container.setPointerCapture) {
            try {
                container.setPointerCapture(e.pointerId);
            } catch (error) {
                // Pointer capture tidak selalu tersedia.
            }
        }
    }

    function dragMove(e) {
        if (!isDragging || e.pointerId !== activePointerId) {
            return;
        }

        const dx = e.clientX - startX;
        const dy = e.clientY - startY;

        if (Math.abs(dx) > 4 || Math.abs(dy) > 4) {
            hasMoved = true;
        }

        if (!hasMoved) {
            return;
        }

        e.preventDefault();

        const maxX = window.innerWidth - container.offsetWidth;
        const maxY = window.innerHeight - container.offsetHeight;

        const newX = Math.min(
            Math.max(0, initialX + dx),
            Math.max(0, maxX)
        );

        const newY = Math.min(
            Math.max(0, initialY + dy),
            Math.max(0, maxY)
        );

        container.style.left = newX + 'px';
        container.style.top = newY + 'px';
    }

    function dragEnd(e) {
        if (!isDragging) {
            return;
        }

        if (
            e &&
            e.pointerId !== undefined &&
            activePointerId !== null &&
            e.pointerId !== activePointerId
        ) {
            return;
        }

        isDragging = false;
        activePointerId = null;

        /*
         * Reset hasMoved setelah event klik selesai,
         * supaya tombol tetap bisa digunakan setelah dipindahkan.
         */
        setTimeout(function () {
            hasMoved = false;
        }, 0);
    }

    container.addEventListener('pointerdown', dragStart);
    document.addEventListener('pointermove', dragMove, {
        passive: false
    });
    document.addEventListener('pointerup', dragEnd);
    document.addEventListener('pointercancel', dragEnd);

    /* =========================================
       SCREEN READER / PEMBACA TEKS
    ========================================= */
    if (!('speechSynthesis' in window) ||
        !('SpeechSynthesisUtterance' in window)) {

        readerButton.addEventListener('click', function () {
            if (speechStatus) {
                speechStatus.textContent =
                    'Browser ini belum mendukung pembacaan suara. Silakan gunakan fitur pembaca layar perangkat.';
            }
        });

        return;
    }

    let readingPage = false;

    function resetReader(message) {
        readingPage = false;

        readerButton.setAttribute('aria-pressed', 'false');
        readerLabel.textContent = 'Screen Reader';

        if (speechStatus && message) {
            speechStatus.textContent = message;
        }
    }

    function speakText(text) {
        window.speechSynthesis.cancel();

        const utterance = new SpeechSynthesisUtterance(text);

        utterance.lang = 'id-ID';
        utterance.rate = 1;
        utterance.pitch = 1;

        utterance.onend = function () {
            resetReader('Pembacaan selesai.');
        };

        utterance.onerror = function (event) {
            if (event.error === 'canceled' ||
                event.error === 'interrupted') {
                return;
            }

            resetReader('Suara tidak dapat diputar. Silakan coba lagi.');
        };

        window.speechSynthesis.speak(utterance);
    }

    readerButton.addEventListener('click', function () {
        if (hasMoved) {
            return;
        }

        if (window.speechSynthesis.speaking || readingPage) {
            window.speechSynthesis.cancel();

            resetReader('Pembacaan dihentikan.');
            return;
        }

        const pageText = main.innerText
            .replace(/\s+/g, ' ')
            .trim();

        if (!pageText) {
            if (speechStatus) {
                speechStatus.textContent =
                    'Tidak ada teks yang dapat dibacakan.';
            }
            return;
        }

        readingPage = true;

        readerButton.setAttribute('aria-pressed', 'true');
        readerLabel.textContent = 'Hentikan Bacaan';

        if (speechStatus) {
            speechStatus.textContent =
                'Konten halaman Laporan Saya sedang dibacakan.';
        }

        speakText(pageText);
    });

    /* Hentikan suara saat pengguna meninggalkan halaman */
    window.addEventListener('pagehide', function () {
        window.speechSynthesis.cancel();
    });
});
</script>
@endpush
