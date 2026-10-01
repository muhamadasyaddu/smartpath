@extends('layouts.admin')

@section('title', 'Edit Rencana Perbaikan')

@section('content')

<div class="p-6">

    {{-- ==========================================================
         HEADER
    =========================================================== --}}
    <div class="mb-6">

        <a
            href="{{ route('dinas.rencana-perbaikan.index') }}"
            class="inline-flex items-center gap-2
                   text-sm text-slate-500
                   hover:text-slate-700 mb-4"
        >
            <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
            Kembali ke Rencana Perbaikan
        </a>

        <h1 class="text-2xl font-bold text-slate-800">
            Edit Rencana Perbaikan
        </h1>

        <p class="text-sm text-slate-500 mt-1">
            Perbarui informasi rencana dan tanggal penyelesaian perbaikan.
        </p>

    </div>


    {{-- ==========================================================
         INFORMASI LAPORAN
    =========================================================== --}}
    <div class="bg-white rounded-2xl border border-slate-200
                shadow-sm p-6 mb-6">

        <h2 class="text-lg font-semibold text-slate-800 mb-5">
            Informasi Laporan
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

            {{-- Kode --}}
            <div>

                <p class="text-xs text-slate-500 mb-1">
                    Kode Laporan
                </p>

                <p class="font-medium text-slate-800">
                    {{ $rencanaPerbaikan->laporan->kode_laporan }}
                </p>

            </div>


            {{-- Kategori --}}
            <div>

                <p class="text-xs text-slate-500 mb-1">
                    Kategori Hambatan
                </p>

                <p class="font-medium text-slate-800">
                    {{ $rencanaPerbaikan->laporan->kategoriHambatan?->nama ?? '-' }}
                </p>

            </div>


            {{-- Judul --}}
            <div>

                <p class="text-xs text-slate-500 mb-1">
                    Judul Laporan
                </p>

                <p class="font-medium text-slate-800">
                    {{ $rencanaPerbaikan->laporan->judul }}
                </p>

            </div>


            {{-- Skor --}}
            <div>

                <p class="text-xs text-slate-500 mb-1">
                    Skor Prioritas
                </p>

                <p class="font-semibold text-slate-800">
                    {{ number_format(
                        (float) $rencanaPerbaikan->laporan->skor_prioritas,
                        2
                    ) }}
                </p>

            </div>


            {{-- Lokasi --}}
            <div class="md:col-span-2">

                <p class="text-xs text-slate-500 mb-1">
                    Lokasi
                </p>

                <p class="text-sm text-slate-700">
                    {{ $rencanaPerbaikan->laporan->alamat_lengkap
                        ?? 'Lokasi tidak tersedia' }}
                </p>

            </div>

        </div>

    </div>


    {{-- ==========================================================
         FORM EDIT
    =========================================================== --}}
    <div class="bg-white rounded-2xl border border-slate-200
                shadow-sm p-6">

        <h2 class="text-lg font-semibold text-slate-800 mb-5">
            Detail Rencana Perbaikan
        </h2>


        <form
            action="{{ route(
                'dinas.rencana-perbaikan.update',
                $rencanaPerbaikan->id
            ) }}"
            method="POST"
            class="space-y-5"
        >

            @csrf
            @method('PUT')


            {{-- ==================================================
                 Tindakan
            =================================================== --}}
            <div>

                <label
                    for="tindakan"
                    class="block text-sm font-medium text-slate-700 mb-2"
                >
                    Tindakan Perbaikan
                </label>

                <textarea
                    id="tindakan"
                    name="tindakan"
                    rows="4"
                    required
                    class="w-full rounded-xl border border-slate-300
                           px-4 py-3 text-sm
                           focus:border-emerald-500
                           focus:ring-emerald-500"
                    placeholder="Masukkan tindakan perbaikan..."
                >{{ old(
                    'tindakan',
                    $rencanaPerbaikan->tindakan
                ) }}</textarea>

                @error('tindakan')

                    <p class="text-sm text-red-600 mt-1">
                        {{ $message }}
                    </p>

                @enderror

            </div>


            {{-- ==================================================
                 Penanggung Jawab
            =================================================== --}}
            <div>

                <label
                    for="penanggung_jawab"
                    class="block text-sm font-medium text-slate-700 mb-2"
                >
                    Penanggung Jawab
                </label>

                <input
                    id="penanggung_jawab"
                    type="text"
                    name="penanggung_jawab"
                    maxlength="150"
                    value="{{ old(
                        'penanggung_jawab',
                        $rencanaPerbaikan->penanggung_jawab
                    ) }}"
                    class="w-full rounded-xl border border-slate-300
                           px-4 py-3 text-sm
                           focus:border-emerald-500
                           focus:ring-emerald-500"
                    placeholder="Contoh: Bidang Infrastruktur"
                >

                @error('penanggung_jawab')

                    <p class="text-sm text-red-600 mt-1">
                        {{ $message }}
                    </p>

                @enderror

            </div>


            {{-- ==================================================
                 Tanggal Mulai + Target
            =================================================== --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                {{-- Tanggal Mulai --}}
                <div>

                    <label
                        for="tanggal_mulai"
                        class="block text-sm font-medium text-slate-700 mb-2"
                    >
                        Tanggal Mulai
                    </label>

                    <input
                        id="tanggal_mulai"
                        type="date"
                        name="tanggal_mulai"
                        value="{{ old(
                            'tanggal_mulai',
                            optional($rencanaPerbaikan->tanggal_mulai)
                                ->format('Y-m-d')
                        ) }}"
                        class="w-full rounded-xl border border-slate-300
                               px-4 py-3 text-sm
                               focus:border-emerald-500
                               focus:ring-emerald-500"
                    >

                    @error('tanggal_mulai')

                        <p class="text-sm text-red-600 mt-1">
                            {{ $message }}
                        </p>

                    @enderror

                </div>


                {{-- Target Selesai --}}
                <div>

                    <label
                        for="target_selesai"
                        class="block text-sm font-medium text-slate-700 mb-2"
                    >
                        Target Selesai
                    </label>

                    <input
                        id="target_selesai"
                        type="date"
                        name="target_selesai"
                        min="{{ old(
                            'tanggal_mulai',
                            optional($rencanaPerbaikan->tanggal_mulai)
                                ->format('Y-m-d')
                        ) }}"
                        value="{{ old(
                            'target_selesai',
                            optional($rencanaPerbaikan->target_selesai)
                                ->format('Y-m-d')
                        ) }}"
                        class="w-full rounded-xl border border-slate-300
                               px-4 py-3 text-sm
                               focus:border-emerald-500
                               focus:ring-emerald-500"
                    >

                    <p class="text-xs text-slate-500 mt-1">
                        Target tidak boleh sebelum tanggal mulai.
                    </p>

                    @error('target_selesai')

                        <p class="text-sm text-red-600 mt-1">
                            {{ $message }}
                        </p>

                    @enderror

                </div>

            </div>


            {{-- ==================================================
                 Tanggal Selesai
            =================================================== --}}
            <div>

                <label
                    for="tanggal_selesai"
                    class="block text-sm font-medium text-slate-700 mb-2"
                >
                    Tanggal Selesai
                </label>

                <input
                    id="tanggal_selesai"
                    type="date"
                    name="tanggal_selesai"
                    min="{{ old(
                        'tanggal_mulai',
                        optional($rencanaPerbaikan->tanggal_mulai)
                            ->format('Y-m-d')
                    ) }}"
                    value="{{ old(
                        'tanggal_selesai',
                        optional($rencanaPerbaikan->tanggal_selesai)
                            ->format('Y-m-d')
                    ) }}"
                    class="w-full rounded-xl border border-slate-300
                           px-4 py-3 text-sm
                           focus:border-emerald-500
                           focus:ring-emerald-500"
                >

                <p class="text-xs text-slate-500 mt-1">
                    Isi ketika pekerjaan benar-benar sudah selesai.
                    Boleh sebelum, sama dengan, atau setelah target selesai,
                    tetapi tidak boleh sebelum tanggal mulai.
                </p>

                @error('tanggal_selesai')

                    <p class="text-sm text-red-600 mt-1">
                        {{ $message }}
                    </p>

                @enderror

            </div>


            {{-- ==================================================
                 Anggaran
            =================================================== --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                {{-- Estimasi --}}
                <div>

                    <label
                        for="estimasi_anggaran"
                        class="block text-sm font-medium text-slate-700 mb-2"
                    >
                        Estimasi Anggaran
                    </label>

                    <input
                        id="estimasi_anggaran"
                        type="number"
                        name="estimasi_anggaran"
                        min="0"
                        step="0.01"
                        value="{{ old(
                            'estimasi_anggaran',
                            $rencanaPerbaikan->estimasi_anggaran ?? 0
                        ) }}"
                        class="w-full rounded-xl border border-slate-300
                               px-4 py-3 text-sm
                               focus:border-emerald-500
                               focus:ring-emerald-500"
                        placeholder="0"
                    >

                    @error('estimasi_anggaran')

                        <p class="text-sm text-red-600 mt-1">
                            {{ $message }}
                        </p>

                    @enderror

                </div>


                {{-- Realisasi --}}
                <div>

                    <label
                        for="realisasi_anggaran"
                        class="block text-sm font-medium text-slate-700 mb-2"
                    >
                        Realisasi Anggaran
                    </label>

                    <input
                        id="realisasi_anggaran"
                        type="number"
                        name="realisasi_anggaran"
                        min="0"
                        step="0.01"
                        value="{{ old(
                            'realisasi_anggaran',
                            $rencanaPerbaikan->realisasi_anggaran ?? 0
                        ) }}"
                        class="w-full rounded-xl border border-slate-300
                               px-4 py-3 text-sm
                               focus:border-emerald-500
                               focus:ring-emerald-500"
                        placeholder="0"
                    >

                    @error('realisasi_anggaran')

                        <p class="text-sm text-red-600 mt-1">
                            {{ $message }}
                        </p>

                    @enderror

                </div>

            </div>


            {{-- ==================================================
                 Catatan
            =================================================== --}}
            <div>

                <label
                    for="catatan"
                    class="block text-sm font-medium text-slate-700 mb-2"
                >
                    Catatan
                </label>

                <textarea
                    id="catatan"
                    name="catatan"
                    rows="4"
                    class="w-full rounded-xl border border-slate-300
                           px-4 py-3 text-sm
                           focus:border-emerald-500
                           focus:ring-emerald-500"
                    placeholder="Catatan tambahan..."
                >{{ old(
                    'catatan',
                    $rencanaPerbaikan->catatan
                ) }}</textarea>

                @error('catatan')

                    <p class="text-sm text-red-600 mt-1">
                        {{ $message }}
                    </p>

                @enderror

            </div>


            {{-- ==================================================
                 TOMBOL
            =================================================== --}}
            <div class="flex justify-end gap-3 pt-4">

                <a
                    href="{{ route('dinas.rencana-perbaikan.index') }}"
                    class="px-5 py-2.5 rounded-lg
                           border border-slate-300
                           text-sm font-medium text-slate-700
                           hover:bg-slate-50 transition"
                >
                    Batal
                </a>

                <button
                    type="submit"
                    class="px-5 py-2.5 rounded-lg
                           bg-emerald-600 text-white
                           text-sm font-medium
                           hover:bg-emerald-700 transition"
                >
                    <i
                        class="fa-solid fa-save mr-1"
                        aria-hidden="true"
                    ></i>

                    Simpan Perubahan
                </button>

            </div>

        </form>

    </div>

</div>

@endsection