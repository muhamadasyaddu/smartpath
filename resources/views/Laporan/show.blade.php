@extends('layouts.app')

@section('title', 'Detail Laporan - ' . $laporan->judul)

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Breadcrumb --}}
    <nav class="text-sm text-slate-400 mb-6" aria-label="Breadcrumb">
        <ol class="flex items-center gap-2">
            <li><a href="{{ route('laporan.index') }}" class="hover:text-emerald-700 transition-colors">Laporan</a></li>
            <li aria-hidden="true">/</li>
            <li class="text-slate-700 font-medium" aria-current="page">{{ $laporan->kode_laporan }}</li>
        </ol>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main content --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Header card --}}
            <div class="bg-white rounded-xl border border-slate-200 p-6">
                <div class="flex items-start justify-between gap-3 mb-4">
                    <div>
                        <span class="text-xs font-mono text-slate-400">{{ $laporan->kode_laporan }}</span>
                        <h1 class="text-xl font-bold text-slate-900 mt-1">{{ $laporan->judul }}</h1>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-semibold {{ $laporan->warna }} whitespace-nowrap">
                        {{ $laporan->status_label }}
                    </span>
                </div>

                {{-- Kategori & Priority --}}
                <div class="flex flex-wrap items-center gap-3 mb-4">
                    @if($laporan->kategoriHambatan)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-100 text-sm text-slate-700">
                            <span class="w-2.5 h-2.5 rounded-full" style="background-color: {{ $laporan->kategoriHambatan->warna_penanda }}" aria-hidden="true"></span>
                            {{ $laporan->kategoriHambatan->nama }}
                        </span>
                    @endif
                    @if($laporan->tingkat_prioritas === 'tinggi')
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-xs font-bold bg-red-100 text-red-700">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M12.395 2.553a1 1 0 00-1.45-.095l-.8.8a1 1 0 00-.095 1.45l.223.223a1 1 0 010 1.414l-2.56 2.56a1 1 0 01-1.414 0l-.223-.223a1 1 0 00-1.45.095l-.8.8a1 1 0 00.095 1.45l7.151 7.151a1 1 0 001.45.095l.8-.8a1 1 0 00.095-1.45l-.223-.223a1 1 0 010-1.414l2.56-2.56a1 1 0 011.414 0l.223.223a1 1 0 001.45-.095l.8-.8a1 1 0 00-.095-1.45l-7.151-7.15z"/></svg>
                            Prioritas Tinggi
                        </span>
                    @endif
                </div>

                <p class="text-slate-700 leading-relaxed">{{ $laporan->deskripsi }}</p>
            </div>

                    @if($laporan->status === 'ditolak')

            @php
                $penolakan = $laporan->verifikasi
                    ->where('keputusan', 'ditolak')
                    ->sortByDesc('created_at')
                    ->first();
            @endphp

            @if($penolakan)

                <section
                    class="rounded-xl border border-red-200 bg-red-50 p-6"
                    role="alert"
                    aria-live="polite"
                >

                    <div class="flex items-start gap-3">

                        <div class="mt-0.5 shrink-0">

                            <svg
                                class="h-5 w-5 text-red-600"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                                aria-hidden="true"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 9v4m0 4h.01M10.29 3.86l-7.4 12.82A2 2 0 004.63 19.7h14.74a2 2 0 001.74-3.02L13.71 3.86a2 2 0 00-3.42 0z"
                                />
                            </svg>

                        </div>


                        <div>

                            <h2 class="font-semibold text-red-800">
                                Alasan Penolakan
                            </h2>

                            <p class="mt-2 text-sm leading-6 text-red-800">
                                {{ $penolakan->catatan_admin }}
                            </p>

                            <p class="mt-3 text-xs text-red-600">

                                Diverifikasi pada
                                {{ $penolakan->created_at?->format('d M Y H:i') }}

                            </p>

                        </div>

                    </div>

                </section>

            @endif

        @endif

            {{-- Photos --}}
            @if($laporan->fotoLaporan->count() > 0)
                <div class="bg-white rounded-xl border border-slate-200 p-6">
                    <h2 class="font-semibold text-slate-900 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Foto Laporan
                    </h2>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        @foreach($laporan->fotoLaporan as $foto)
                            <div class="relative group overflow-hidden rounded-lg">
                                <img src="{{ $foto->url }}" alt="Foto laporan {{ $loop->iteration }}" class="w-full h-40 object-cover group-hover:scale-105 transition-transform duration-300" loading="lazy">
                                @if($foto->adalah_utama)
                                    <span class="absolute top-1.5 left-1.5 px-1.5 py-0.5 bg-emerald-600 text-white text-[10px] font-bold rounded-md">Utama</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Location Map --}}
            <div class="bg-white rounded-xl border border-slate-200 p-6">
                <h2 class="font-semibold text-slate-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Lokasi
                </h2>
                <div id="detail-map" class="w-full h-64 rounded-xl mb-3" role="application" aria-label="Peta lokasi laporan"></div>
                <p class="text-sm text-slate-600 flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                    {{ $laporan->alamat_lengkap }}
                </p>
                <p class="text-xs text-slate-400 font-mono mt-1">{{ $laporan->latitude }}, {{ $laporan->longitude }}</p>
            </div>

            {{-- Status History --}}
            @if($laporan->riwayatStatus->count() > 0)
                <div class="bg-white rounded-xl border border-slate-200 p-6">
                    <h2 class="font-semibold text-slate-900 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Riwayat Status
                    </h2>
                    <div class="space-y-4" role="list" aria-label="Riwayat perubahan status">
                        @foreach($laporan->riwayatStatus->sortByDesc('created_at') as $riwayat)
                            <div class="flex gap-3" role="listitem">
                                <div class="flex flex-col items-center">
                                    <div class="w-3 h-3 rounded-full bg-emerald-500 mt-1.5" aria-hidden="true"></div>
                                    @if(!$loop->last)
                                        <div class="w-0.5 flex-1 bg-slate-200 mt-1" aria-hidden="true"></div>
                                    @endif
                                </div>
                                <div class="flex-1 pb-4">
                                    <p class="text-sm text-slate-700">
                                        <span class="font-medium">{{ $riwayat->status_label_baru ?? $riwayat->status_baru }}</span>
                                        @if($riwayat->status_sebelumnya)
                                            <span class="text-slate-400">dari {{ $riwayat->status_label_sebelumnya ?? $riwayat->status_sebelumnya }}</span>
                                        @endif
                                    </p>
                                    @if($riwayat->keterangan)
                                        <p class="text-sm text-slate-500 mt-0.5">{{ $riwayat->keterangan }}</p>
                                    @endif
                                    <p class="text-xs text-slate-400 mt-1">
                                        {{ $riwayat->diubahOleh->nama_lengkap ?? 'Sistem' }} • {{ $riwayat->created_at->format('d M Y H:i') }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Priority Score --}}
            <div class="bg-white rounded-xl border border-slate-200 p-6">
                <h2 class="font-semibold text-slate-900 mb-4 text-sm uppercase tracking-wider">Skor Prioritas</h2>
                @if($laporan->skor_prioritas)
                    <div class="text-center mb-4">
                        <p class="text-4xl font-bold {{ $laporan->tingkat_prioritas === 'tinggi' ? 'text-red-600' : ($laporan->tingkat_prioritas === 'sedang' ? 'text-amber-600' : 'text-blue-600') }}">
                            {{ number_format($laporan->skor_prioritas, 2) }}
                        </p>
                        <p class="text-xs text-slate-400 uppercase mt-1">{{ $laporan->tingkat_prioritas }}</p>
                    </div>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between"><span class="text-slate-500">Keparahan</span><span class="font-medium">{{ number_format($laporan->skor_keparahan, 2) }}</span></div>
                        <div class="flex justify-between"><span class="text-slate-500">Pelapor</span><span class="font-medium">{{ number_format($laporan->skor_pelapor, 2) }}</span></div>
                        <div class="flex justify-between"><span class="text-slate-500">Fasilitas</span><span class="font-medium">{{ number_format($laporan->skor_fasilitas, 2) }}</span></div>
                    </div>
                @else
                    <p class="text-sm text-slate-400 text-center py-4">Skor belum dihitung</p>
                @endif
            </div>

            {{-- Reporter info --}}
            <div class="bg-white rounded-xl border border-slate-200 p-6">
                <h2 class="font-semibold text-slate-900 mb-3 text-sm uppercase tracking-wider">Pelapor</h2>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-emerald-100 rounded-full flex items-center justify-center text-emerald-700 font-semibold text-sm">
                        {{ strtoupper(substr($laporan->pelapor->nama_lengkap, 0, 2)) }}
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-700">{{ $laporan->pelapor->nama_lengkap }}</p>
                        <p class="text-xs text-slate-400">{{ $laporan->created_at->format('d M Y H:i') }}</p>
                    </div>
                </div>
            </div>

            {{-- Facility proximity --}}
            @if($laporan->fasilitas_terdekat_id)
                <div class="bg-white rounded-xl border border-slate-200 p-6">
                    <h2 class="font-semibold text-slate-900 mb-3 text-sm uppercase tracking-wider">Fasilitas Terdekat</h2>
                    @if($laporan->fasilitasTerdekat)
                        <p class="text-sm text-slate-700 font-medium">{{ $laporan->fasilitasTerdekat->nama }}</p>
                        <p class="text-xs text-slate-500">{{ $laporan->fasilitasTerdekat->jenis }}</p>
                        <p class="text-xs text-slate-400 mt-1">Jarak: {{ $laporan->jarak_fasilitas_meter }}m</p>
                    @endif
                </div>
            @endif

            {{-- Duplicate info --}}
            @if($laporan->jumlah_pelapor > 1)
                <div class="bg-amber-50 rounded-xl border border-amber-200 p-6">
                    <h2 class="font-semibold text-amber-800 mb-2 text-sm flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Laporan Duplikat Terdeteksi
                    </h2>
                    <p class="text-sm text-amber-700">Laporan ini telah dilaporkan oleh {{ $laporan->jumlah_pelapor }} pelapor di radius deduplikasi.</p>
                </div>
            @endif

            {{-- Actions --}}
            @if(auth()->id() === $laporan->pelapor_id && in_array($laporan->status, ['menunggu_verifikasi']))
                <div class="bg-white rounded-xl border border-slate-200 p-6 space-y-3">
                    <a href="{{ route('laporan.edit', $laporan) }}" class="w-full inline-flex items-center justify-center gap-2 bg-emerald-600 text-white font-medium px-4 py-2.5 rounded-xl hover:bg-emerald-700 transition-colors focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Edit Laporan
                    </a>
                    <form method="POST" action="{{ route('laporan.destroy', $laporan) }}" onsubmit="return confirm('Yakin ingin menghapus laporan ini?');">
                        @csrf @method('DELETE')
                        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 bg-white text-red-600 font-medium px-4 py-2.5 rounded-xl border border-red-200 hover:bg-red-50 transition-colors focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            Hapus Laporan
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const map = L.map('detail-map', { center: [{{ $laporan->latitude }}, {{ $laporan->longitude }}], zoom: 16, dragging: false, scrollWheelZoom: false });
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; OSM', maxZoom: 19 }).addTo(map);
    L.marker([{{ $laporan->latitude }}, {{ $laporan->longitude }}]).addTo(map).bindPopup('{{ addslashes($laporan->judul) }}').openPopup();
});
</script>
@endpush