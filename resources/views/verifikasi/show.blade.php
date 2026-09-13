@extends('layouts.admin')

@section('title', 'Verifikasi - ' . $laporan->kode_laporan)
@section('page_title', 'Verifikasi Laporan')

@push('styles')
<style>
    #verifikasi-map {
        height: 320px;
        border-radius: 0.75rem;
        z-index: 1;
    }
</style>
@endpush

@section('content')
<div class="space-y-6">

    <nav class="text-sm text-slate-400" aria-label="Breadcrumb">
        <ol class="flex items-center gap-2">
            <li>
                <a href="{{ route('admin.verifikasi.index') }}" class="hover:text-emerald-700 transition-colors">
                    Verifikasi
                </a>
            </li>
            <li aria-hidden="true">/</li>
            <li class="text-slate-700 font-medium" aria-current="page">
                {{ $laporan->kode_laporan }}
            </li>
        </ol>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="lg:col-span-2 space-y-6">

            {{-- Detail laporan --}}
            <section class="bg-white rounded-xl border border-slate-200 p-6">
                <div class="flex items-start justify-between gap-3 mb-4">
                    <div>
                        <span class="text-xs font-mono text-slate-400">
                            {{ $laporan->kode_laporan }}
                        </span>

                        <h2 class="text-lg font-bold text-slate-900 mt-1">
                            {{ $laporan->judul }}
                        </h2>
                    </div>

                    <span class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-semibold {{ $laporan->warna }} whitespace-nowrap">
                        {{ $laporan->status_label }}
                    </span>
                </div>

                <div class="flex flex-wrap items-center gap-2 mb-4">
                    @if($laporan->kategoriHambatan)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-100 text-sm text-slate-700">
                            <span
                                class="w-2.5 h-2.5 rounded-full"
                                style="background-color: {{ $laporan->kategoriHambatan->warna_penanda ?? '#64748b' }}"
                                aria-hidden="true"
                            ></span>
                            {{ $laporan->kategoriHambatan->nama }}
                        </span>
                    @endif

                    <span class="text-xs text-slate-400">
                        {{ $laporan->created_at->locale('id')->translatedFormat('d M Y H:i') }}
                    </span>
                </div>

                <p class="text-slate-700 leading-relaxed">
                    {{ $laporan->deskripsi }}
                </p>

                <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                    <div class="rounded-lg bg-slate-50 border border-slate-100 p-3">
                        <p class="text-xs text-slate-400 mb-1">Pelapor</p>
                        <p class="font-medium text-slate-700">
                            {{ $laporan->pelapor->nama_lengkap ?? '-' }}
                        </p>
                    </div>

                    <div class="rounded-lg bg-slate-50 border border-slate-100 p-3">
                        <p class="text-xs text-slate-400 mb-1">Sumber Koordinat</p>
                        <p class="font-medium text-slate-700">
                            {{ $laporan->sumber_koordinat === 'gps_otomatis' ? 'GPS Otomatis' : 'Penandaan Manual' }}
                        </p>
                    </div>
                </div>
            </section>

            {{-- Foto --}}
            <section class="bg-white rounded-xl border border-slate-200 p-6">
                <h3 class="font-semibold text-slate-900 mb-4">
                    Bukti Foto
                </h3>

                @if($laporan->fotoLaporan->count())
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        @foreach($laporan->fotoLaporan as $foto)
                            <div class="relative overflow-hidden rounded-lg border border-slate-200">
                                <img
                                    src="{{ $foto->url }}"
                                    alt="Foto laporan {{ $loop->iteration }}"
                                    class="w-full h-40 object-cover"
                                    loading="lazy"
                                >

                                @if($foto->adalah_utama)
                                    <span class="absolute top-1.5 left-1.5 px-1.5 py-0.5 bg-emerald-600 text-white text-[10px] font-bold rounded-md">
                                        Utama
                                    </span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-red-600">
                        Tidak ada foto yang tersimpan.
                    </p>
                @endif
            </section>

            {{-- Lokasi --}}
            <section class="bg-white rounded-xl border border-slate-200 p-6">
                <h3 class="font-semibold text-slate-900 mb-4">
                    Lokasi Hambatan
                </h3>

                <div
                    id="verifikasi-map"
                    role="application"
                    aria-label="Peta lokasi laporan"
                ></div>

                <div class="mt-3 space-y-1">
                    <p class="text-sm text-slate-600">
                        {{ $laporan->alamat_lengkap ?: 'Alamat tidak tersedia.' }}
                    </p>

                    <p class="text-xs text-slate-400 font-mono">
                        {{ $laporan->latitude }}, {{ $laporan->longitude }}
                    </p>
                </div>
            </section>

            {{-- Indikasi duplikasi --}}
            @if($laporan->laporanInduk)
                <section class="bg-amber-50 rounded-xl border border-amber-200 p-6">
                    <h3 class="font-semibold text-amber-800 mb-2">
                        Laporan Terhubung
                    </h3>

                    <p class="text-sm text-amber-700 mb-2">
                        Sistem mendeteksi laporan ini berada pada kategori yang sama
                        dan radius deduplikasi 50 meter dari laporan induk.
                    </p>

                    <a
                        href="{{ route('admin.verifikasi.show', $laporan->laporanInduk) }}"
                        class="inline-flex items-center gap-1 text-sm font-medium text-amber-800 hover:text-amber-900"
                    >
                        {{ $laporan->laporanInduk->kode_laporan }}
                        — {{ $laporan->laporanInduk->judul }}
                    </a>
                </section>
            @endif

            {{-- Riwayat verifikasi --}}
            @if($laporan->verifikasi->count())
                <section class="bg-white rounded-xl border border-slate-200 p-6">
                    <h3 class="font-semibold text-slate-900 mb-4">
                        Riwayat Verifikasi
                    </h3>

                    <div class="space-y-4">
                        @foreach($laporan->verifikasi as $verifikasi)
                            <div class="flex gap-3 p-3 rounded-lg bg-slate-50">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold {{ $verifikasi->warna }} shrink-0">
                                    {{ $verifikasi->keputusan_label }}
                                </span>

                                <div>
                                    <p class="text-sm text-slate-700">
                                        {{ $verifikasi->catatan_admin ?: 'Tidak ada catatan.' }}
                                    </p>

                                    <p class="text-xs text-slate-400 mt-1">
                                        {{ $verifikasi->admin->nama_lengkap ?? 'Sistem' }}
                                        •
                                        {{ $verifikasi->created_at->format('d M Y H:i') }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif
        </div>

        {{-- Sidebar --}}
        <aside class="space-y-6">

            {{-- SLA --}}
            <section class="bg-white rounded-xl border border-slate-200 p-6">
                <h3 class="font-semibold text-slate-900 mb-4 text-sm uppercase tracking-wider">
                    SLA Verifikasi
                </h3>

                @if($laporan->status === 'menunggu_verifikasi')
                    @php
                        $slaExpired = $laporan->created_at->lt(now()->subHours(48));
                        $elapsedHours = (int) $laporan->created_at->diffInHours(now());
                    @endphp

                    @if($slaExpired)
                        <div class="rounded-xl bg-red-50 border border-red-200 p-4">
                            <p class="text-sm font-semibold text-red-800">
                                Melewati target 2×24 jam
                            </p>
                            <p class="text-xs text-red-600 mt-1">
                                Laporan telah menunggu sekitar {{ $elapsedHours }} jam.
                            </p>
                        </div>
                    @else
                        <div class="rounded-xl bg-emerald-50 border border-emerald-200 p-4">
                            <p class="text-sm font-semibold text-emerald-800">
                                Masih dalam SLA
                            </p>
                            <p class="text-xs text-emerald-700 mt-1">
                                Menunggu sekitar {{ $elapsedHours }} jam.
                            </p>
                        </div>
                    @endif
                @else
                    <p class="text-sm text-slate-500">
                        Laporan sudah diproses.
                    </p>
                @endif
            </section>

            {{-- Prioritas --}}
            <section class="bg-white rounded-xl border border-slate-200 p-6">
                <h3 class="font-semibold text-slate-900 mb-4 text-sm uppercase tracking-wider">
                    Skor Prioritas
                </h3>

                @if($laporan->skor_prioritas !== null)
                    <div class="text-center mb-4">
                        <p class="text-3xl font-bold text-slate-900">
                            {{ number_format((float) $laporan->skor_prioritas, 2) }}
                        </p>

                        <p class="text-xs text-slate-400 uppercase mt-1">
                            {{ $laporan->tingkat_prioritas }}
                        </p>
                    </div>
                @else
                    <p class="text-sm text-slate-400 text-center py-4">
                        Skor akan dihitung setelah laporan induk terverifikasi.
                    </p>
                @endif

                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Jumlah Pelapor</span>
                        <span class="font-medium">{{ $laporan->jumlah_pelapor }}</span>
                    </div>

                    @if($laporan->skor_prioritas !== null)
                        <div class="flex justify-between">
                            <span class="text-slate-500">Keparahan</span>
                            <span class="font-medium">{{ number_format((float) $laporan->skor_keparahan, 2) }}</span>
                        </div>

                        <div class="flex justify-between">
                            <span class="text-slate-500">Pelapor</span>
                            <span class="font-medium">{{ number_format((float) $laporan->skor_pelapor, 2) }}</span>
                        </div>

                        <div class="flex justify-between">
                            <span class="text-slate-500">Fasilitas</span>
                            <span class="font-medium">{{ number_format((float) $laporan->skor_fasilitas, 2) }}</span>
                        </div>
                    @endif
                </div>
            </section>

            {{-- Pelapor --}}
            <section class="bg-white rounded-xl border border-slate-200 p-6">
                <h3 class="font-semibold text-slate-900 mb-3 text-sm uppercase tracking-wider">
                    Pelapor
                </h3>

                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-emerald-100 rounded-full flex items-center justify-center text-emerald-700 font-semibold text-sm">
                        {{ strtoupper(substr($laporan->pelapor->nama_lengkap ?? 'NA', 0, 2)) }}
                    </div>

                    <div>
                        <p class="text-sm font-medium text-slate-700">
                            {{ $laporan->pelapor->nama_lengkap ?? '-' }}
                        </p>

                        <p class="text-xs text-slate-400">
                            {{ $laporan->pelapor->email ?? '-' }}
                        </p>
                    </div>
                </div>
            </section>

            {{-- Aksi --}}
            @if($laporan->status === 'menunggu_verifikasi')
                <section class="bg-white rounded-xl border border-slate-200 p-6">
                    <h3 class="font-semibold text-slate-900 mb-4">
                        Keputusan Verifikasi
                    </h3>

                    <form
                        id="verifikasi-form"
                        method="POST"
                        action=""
                        class="space-y-4"
                    >
                        @csrf

                        <div>
                            <label
                                for="catatan_admin"
                                class="block text-sm font-medium text-slate-700 mb-1.5"
                            >
                                Catatan Admin
                            </label>

                            <textarea
                                id="catatan_admin"
                                name="catatan_admin"
                                rows="4"
                                maxlength="1000"
                                class="w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm"
                                placeholder="Untuk penolakan, tuliskan alasan yang jelas dan dapat dipahami pelapor."
                            >{{ old('catatan_admin') }}</textarea>

                            @error('catatan_admin')
                                <p class="mt-1 text-sm text-red-600" role="alert">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label
                                for="kategori_koreksi"
                                class="block text-sm font-medium text-slate-700 mb-1.5"
                            >
                                Koreksi Kategori (opsional)
                            </label>

                            <select
                                id="kategori_koreksi"
                                name="kategori_koreksi"
                                class="w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm"
                            >
                                <option value="">
                                    Tidak ada koreksi
                                </option>

                                @foreach(\App\Models\KategoriHambatan::aktif()->urutTampil()->get() as $kategori)
                                    <option
                                        value="{{ $kategori->id }}"
                                        {{ old('kategori_koreksi') == $kategori->id ? 'selected' : '' }}
                                    >
                                        {{ $kategori->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-2 pt-2">
                            <button
                                type="button"
                                onclick="submitVerification('{{ route('admin.verifikasi.approve', $laporan) }}', false)"
                                class="w-full inline-flex items-center justify-center gap-2 bg-emerald-600 text-white font-medium px-4 py-2.5 rounded-xl hover:bg-emerald-700 transition-colors text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2"
                            >
                                Setujui Laporan
                            </button>

                            <button
                                type="button"
                                onclick="submitVerification('{{ route('admin.verifikasi.reject', $laporan) }}', true)"
                                class="w-full inline-flex items-center justify-center gap-2 bg-white text-red-600 font-medium px-4 py-2.5 rounded-xl border border-red-200 hover:bg-red-50 transition-colors text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                            >
                                Tolak Laporan
                            </button>
                        </div>
                    </form>
                </section>
            @endif
        </aside>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const map = L.map('verifikasi-map', {
        center: [
            {{ (float) $laporan->latitude }},
            {{ (float) $laporan->longitude }}
        ],
        zoom: 16
    });

    L.tileLayer(
        'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
        {
            attribution: '&copy; OpenStreetMap',
            maxZoom: 19
        }
    ).addTo(map);

    L.marker([
        {{ (float) $laporan->latitude }},
        {{ (float) $laporan->longitude }}
    ])
    .addTo(map)
    .bindPopup(
        @json($laporan->judul)
    )
    .openPopup();

    setTimeout(() => map.invalidateSize(), 300);
});

function submitVerification(action, isReject) {
    const form = document.getElementById('verifikasi-form');

    if (!form) {
        return;
    }

    if (isReject) {
        const note = document
            .getElementById('catatan_admin')
            .value
            .trim();

        if (!note) {
            alert('Alasan penolakan wajib diisi.');
            document.getElementById('catatan_admin').focus();
            return;
        }

        if (!confirm('Tolak laporan ini? Alasan penolakan akan disimpan dan dapat dilihat pelapor.')) {
            return;
        }
    } else if (!confirm('Setujui laporan ini? Laporan akan menjadi terverifikasi dan diproses ke tahap prioritas.')) {
        return;
    }

    form.action = action;
    form.submit();
}
</script>
@endpush
