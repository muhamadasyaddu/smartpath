@extends('layouts.admin')

@section('title', 'Verifikasi Laporan')
@section('page_title', 'Verifikasi Laporan')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-lg font-semibold text-slate-900">
                Antrian Verifikasi
            </h2>
            <p class="text-sm text-slate-500">
                Periksa bukti, kategori, lokasi, dan relevansi laporan warga.
            </p>
        </div>

        <select
            id="filter-status"
            class="rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm"
            aria-label="Filter status laporan"
        >
            <option value="menunggu_verifikasi" {{ request('status', 'menunggu_verifikasi') === 'menunggu_verifikasi' ? 'selected' : '' }}>
                Menunggu Verifikasi
            </option>
            <option value="diverifikasi" {{ request('status') === 'diverifikasi' ? 'selected' : '' }}>
                Diverifikasi
            </option>
            <option value="dalam_perbaikan" {{ request('status') === 'dalam_perbaikan' ? 'selected' : '' }}>
                Dalam Perbaikan
            </option>
            <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>
                Selesai
            </option>
            <option value="ditolak" {{ request('status') === 'ditolak' ? 'selected' : '' }}>
                Ditolak
            </option>
            <option value="" {{ request('status') === '' ? 'selected' : '' }}>
                Semua Status
            </option>
        </select>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table
                class="w-full text-sm"
                role="table"
                aria-label="Daftar laporan untuk verifikasi"
            >
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/50">
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider" scope="col">Kode</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider" scope="col">Laporan</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider" scope="col">Kategori</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider" scope="col">Pelapor</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider" scope="col">Status</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider" scope="col">SLA</th>
                        <th class="text-right px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider" scope="col">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse($laporan as $item)
                        @php
                            $isPending = $item->status === 'menunggu_verifikasi';
                            $slaExpired = $isPending && $item->created_at->lt(now()->subHours(48));
                            $slaHours = $isPending
                                ? max(0, (int) $item->created_at->diffInHours(now()))
                                : null;
                        @endphp

                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-4 py-3 font-mono text-xs text-slate-500">
                                {{ $item->kode_laporan }}
                            </td>

                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    @if($item->fotoUtama)
                                        <img
                                            src="{{ $item->fotoUtama->url }}"
                                            alt=""
                                            class="w-9 h-9 rounded-lg object-cover"
                                            aria-hidden="true"
                                        >
                                    @endif

                                    <div>
                                        <a
                                            href="{{ route('admin.verifikasi.show', $item) }}"
                                            class="font-medium text-slate-800 hover:text-emerald-700 transition-colors"
                                        >
                                            {{ \Illuminate\Support\Str::limit($item->judul, 40) }}
                                        </a>

                                        @if($item->laporanInduk)
                                            <p class="text-[11px] text-amber-700 mt-0.5">
                                                Duplikat dari {{ $item->laporanInduk->kode_laporan }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <td class="px-4 py-3 text-slate-600">
                                @if($item->kategoriHambatan)
                                    <span class="inline-flex items-center gap-1">
                                        <span
                                            class="w-2 h-2 rounded-full"
                                            style="background-color: {{ $item->kategoriHambatan->warna_penanda ?? '#64748b' }}"
                                            aria-hidden="true"
                                        ></span>
                                        {{ $item->kategoriHambatan->nama }}
                                    </span>
                                @else
                                    —
                                @endif
                            </td>

                            <td class="px-4 py-3 text-slate-600">
                                {{ $item->pelapor->nama_lengkap ?? '-' }}
                            </td>

                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-semibold {{ $item->warna }}">
                                    {{ $item->status_label }}
                                </span>
                            </td>

                            <td class="px-4 py-3">
                                @if($isPending)
                                    @if($slaExpired)
                                        <span class="inline-flex items-center gap-1 text-xs font-semibold text-red-700">
                                            <span class="w-2 h-2 rounded-full bg-red-500" aria-hidden="true"></span>
                                            Melewati 48 jam
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-700">
                                            <span class="w-2 h-2 rounded-full bg-emerald-500" aria-hidden="true"></span>
                                            {{ $slaHours }} jam
                                        </span>
                                    @endif
                                @else
                                    <span class="text-xs text-slate-400">—</span>
                                @endif
                            </td>

                            <td class="px-4 py-3 text-right">
                                <a
                                    href="{{ route('admin.verifikasi.show', $item) }}"
                                    class="inline-flex items-center gap-1 text-emerald-700 hover:text-emerald-800 font-medium text-xs transition-colors focus:outline-none focus:ring-2 focus:ring-emerald-500 rounded"
                                >
                                    Lihat Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center text-slate-400">
                                Tidak ada laporan pada filter ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($laporan->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 flex justify-center">
                {{ $laporan->withQueryString()->links('pagination::tailwind') }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('filter-status')?.addEventListener('change', function () {
    const url = new URL(window.location.href);

    if (this.value) {
        url.searchParams.set('status', this.value);
    } else {
        url.searchParams.delete('status');
    }

    url.searchParams.delete('page');
    window.location.href = url.toString();
});
</script>
@endpush