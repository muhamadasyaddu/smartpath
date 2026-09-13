@extends('layouts.admin')

@section('title', 'Detail Kategori Hambatan')
@section('page_title', 'Detail Kategori Hambatan')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <div class="flex items-center justify-between gap-4">
        <div>
            <p class="text-xs font-mono text-slate-400">ID #{{ $kategoriHambatan->id }}</p>
            <h2 class="text-xl font-bold text-slate-900 mt-1">
                {{ $kategoriHambatan->nama }}
            </h2>
        </div>

        <a
            href="{{ route('admin.kategori-hambatan.edit', $kategoriHambatan) }}"
            class="inline-flex items-center gap-2 bg-emerald-600 text-white font-semibold px-4 py-2.5 rounded-xl hover:bg-emerald-700 transition-colors text-sm"
        >
            Edit Kategori
        </a>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 p-6">
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-5">

            <div>
                <dt class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                    Nama
                </dt>
                <dd class="mt-1 text-sm font-medium text-slate-800">
                    {{ $kategoriHambatan->nama }}
                </dd>
            </div>

            <div>
                <dt class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                    Slug
                </dt>
                <dd class="mt-1 text-sm font-mono text-slate-700">
                    {{ $kategoriHambatan->slug }}
                </dd>
            </div>

            <div>
                <dt class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                    Bobot Keparahan
                </dt>
                <dd class="mt-1 text-sm font-medium text-slate-800">
                    {{ $kategoriHambatan->bobot_keparahan }}/100
                    <span class="text-slate-400 font-normal">
                        ({{ $kategoriHambatan->tingkat_keparahan }})
                    </span>
                </dd>
            </div>

            <div>
                <dt class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                    Status
                </dt>
                <dd class="mt-1">
                    @if($kategoriHambatan->aktif)
                        <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-semibold bg-green-100 text-green-700">
                            Aktif
                        </span>
                    @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-semibold bg-slate-100 text-slate-500">
                            Nonaktif
                        </span>
                    @endif
                </dd>
            </div>

            <div>
                <dt class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                    Urutan Tampil
                </dt>
                <dd class="mt-1 text-sm text-slate-700">
                    {{ $kategoriHambatan->urutan_tampil }}
                </dd>
            </div>

            <div>
                <dt class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                    Jumlah Laporan
                </dt>
                <dd class="mt-1 text-sm text-slate-700">
                    {{ $kategoriHambatan->laporan_count ?? 0 }}
                </dd>
            </div>

            <div>
                <dt class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                    Warna Penanda
                </dt>
                <dd class="mt-1 flex items-center gap-2 text-sm text-slate-700">
                    <span
                        class="w-5 h-5 rounded-full border border-slate-200"
                        style="background-color: {{ $kategoriHambatan->warna_penanda ?? '#64748b' }}"
                        aria-hidden="true"
                    ></span>
                    {{ $kategoriHambatan->warna_penanda ?: '-' }}
                </dd>
            </div>

            <div>
                <dt class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                    Ikon
                </dt>
                <dd class="mt-1 text-sm text-slate-700">
                    {{ $kategoriHambatan->ikon ?: '-' }}
                </dd>
            </div>
        </dl>

        <div class="mt-6 pt-5 border-t border-slate-100">
            <dt class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                Keterangan
            </dt>
            <dd class="mt-2 text-sm leading-relaxed text-slate-600">
                {{ $kategoriHambatan->keterangan ?: 'Tidak ada keterangan.' }}
            </dd>
        </div>
    </div>

    <a
        href="{{ route('admin.kategori-hambatan.index') }}"
        class="inline-flex text-sm font-medium text-slate-500 hover:text-slate-700"
    >
        ← Kembali ke daftar
    </a>
</div>
@endsection