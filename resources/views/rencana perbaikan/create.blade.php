@extends('layouts.admin')

@section('title', 'Buat Rencana Perbaikan')

@section('content')

<div class="p-6">

    {{-- HEADER --}}
    <div class="mb-6">

        <a
            href="{{ route('dinas.rencana-perbaikan.index') }}"
            class="inline-flex items-center gap-2
                   text-sm text-slate-500
                   hover:text-slate-700 mb-4"
        >
            <i class="fa-solid fa-arrow-left"></i>
            Kembali ke Rencana Perbaikan
        </a>

        <h1 class="text-2xl font-bold text-slate-800">
            Buat Rencana Perbaikan
        </h1>

        <p class="text-sm text-slate-500 mt-1">
            Tentukan rencana perbaikan berdasarkan laporan yang dipilih.
        </p>

    </div>


    {{-- INFORMASI LAPORAN --}}
    <div class="bg-white rounded-2xl border border-slate-200
                shadow-sm p-6 mb-6">

        <h2 class="text-lg font-semibold text-slate-800 mb-5">
            Informasi Laporan
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

            <div>
                <p class="text-xs text-slate-500 mb-1">
                    Kode Laporan
                </p>

                <p class="font-medium text-slate-800">
                    {{ $laporan->kode_laporan }}
                </p>
            </div>


            <div>
                <p class="text-xs text-slate-500 mb-1">
                    Kategori Hambatan
                </p>

                <p class="font-medium text-slate-800">
                    {{ optional($laporan->kategoriHambatan)->nama ?? '-' }}
                </p>
            </div>


            <div>
                <p class="text-xs text-slate-500 mb-1">
                    Judul Laporan
                </p>

                <p class="font-medium text-slate-800">
                    {{ $laporan->judul }}
                </p>
            </div>


            <div>
                <p class="text-xs text-slate-500 mb-1">
                    Skor Prioritas
                </p>

                <p class="font-semibold text-slate-800">
                    {{ number_format((float) $laporan->skor_prioritas, 2) }}
                </p>
            </div>


            <div class="md:col-span-2">
                <p class="text-xs text-slate-500 mb-1">
                    Lokasi
                </p>

                <p class="text-sm text-slate-700">
                    {{ $laporan->alamat_lengkap ?? 'Lokasi tidak tersedia' }}
                </p>
            </div>

        </div>

    </div>


    {{-- FORM --}}
    <div class="bg-white rounded-2xl border border-slate-200
                shadow-sm p-6">

        <h2 class="text-lg font-semibold text-slate-800 mb-5">
            Detail Rencana Perbaikan
        </h2>


        <form
            action="{{ route('dinas.rencana-perbaikan.store') }}"
            method="POST"
            class="space-y-5"
        >

            @csrf

            <input
                type="hidden"
                name="laporan_id"
                value="{{ $laporan->id }}"
            >


            {{-- TINDAKAN --}}
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
                    placeholder="Masukkan tindakan perbaikan yang akan dilakukan..."
                >{{ old('tindakan') }}</textarea>

                @error('tindakan')
                    <p class="text-sm text-red-600 mt-1">
                        {{ $message }}
                    </p>
                @enderror

            </div>


            {{-- PENANGGUNG JAWAB --}}
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
                    value="{{ old('penanggung_jawab') }}"
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


            {{-- TANGGAL --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

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
                        value="{{ old('tanggal_mulai') }}"
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
                        min="{{ old('tanggal_mulai') }}"
                        value="{{ old('target_selesai') }}"
                        class="w-full rounded-xl border border-slate-300
                               px-4 py-3 text-sm
                               focus:border-emerald-500
                               focus:ring-emerald-500"
                    >

                    <p class="text-xs text-slate-500 mt-1">
                        Otomatis satu bulan setelah tanggal mulai dan dapat disesuaikan.
                    </p>

                    @error('target_selesai')
                        <p class="text-sm text-red-600 mt-1">
                            {{ $message }}
                        </p>
                    @enderror

                </div>

            </div>


            {{-- ANGGARAN --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                {{-- ESTIMASI --}}
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
                        value="{{ old('estimasi_anggaran', 0) }}"
                        class="w-full rounded-xl border border-slate-300
                               px-4 py-3 text-sm
                               focus:border-emerald-500
                               focus:ring-emerald-500"
                        placeholder="0"
                    >

                    <p class="text-xs text-slate-500 mt-1">
                        Perkiraan biaya yang dibutuhkan untuk pelaksanaan perbaikan.
                    </p>

                    @error('estimasi_anggaran')
                        <p class="text-sm text-red-600 mt-1">
                            {{ $message }}
                        </p>
                    @enderror

                </div>


                {{-- REALISASI --}}
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
                        value="0"
                        readonly
                        class="w-full rounded-xl border border-slate-300
                               bg-slate-50
                               px-4 py-3 text-sm
                               text-slate-500
                               cursor-not-allowed"
                    >

                    <p class="text-xs text-slate-500 mt-1">
                        Realisasi dimulai dari Rp0 dan dapat diperbarui setelah terdapat biaya pelaksanaan.
                    </p>

                </div>

            </div>


            {{-- CATATAN --}}
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
                >{{ old('catatan') }}</textarea>

                @error('catatan')
                    <p class="text-sm text-red-600 mt-1">
                        {{ $message }}
                    </p>
                @enderror

            </div>


            {{-- TOMBOL --}}
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
                    <i class="fa-solid fa-save mr-1"></i>
                    Simpan Rencana
                </button>

            </div>

        </form>

    </div>

</div>

@endsection


@push('scripts')

<script>
document.addEventListener('DOMContentLoaded', function () {

    const tanggalMulai =
        document.getElementById('tanggal_mulai');

    const targetSelesai =
        document.getElementById('target_selesai');


    function satuBulanSetelah(tanggal) {

        const [tahun, bulan, hari] =
            tanggal.split('-').map(Number);

        const hariTerakhir =
            new Date(
                tahun,
                bulan + 1,
                0
            ).getDate();

        const hasil =
            new Date(
                tahun,
                bulan,
                Math.min(hari, hariTerakhir)
            );


        function pad(nilai) {

            return String(nilai).padStart(2, '0');

        }


        return `${hasil.getFullYear()}-${pad(
            hasil.getMonth() + 1
        )}-${pad(
            hasil.getDate()
        )}`;
    }


    function isiTargetSelesai() {

        targetSelesai.min =
            tanggalMulai.value;

        targetSelesai.value =
            tanggalMulai.value
                ? satuBulanSetelah(
                    tanggalMulai.value
                )
                : '';

    }


    tanggalMulai.addEventListener(
        'input',
        isiTargetSelesai
    );

    tanggalMulai.addEventListener(
        'change',
        isiTargetSelesai
    );


    if (
        !targetSelesai.value &&
        tanggalMulai.value
    ) {

        targetSelesai.value =
            satuBulanSetelah(
                tanggalMulai.value
            );

    }


    targetSelesai.min =
        tanggalMulai.value;

});
</script>

@endpush