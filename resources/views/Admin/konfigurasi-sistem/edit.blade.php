@extends('layouts.admin')

@section('title', 'Edit Konfigurasi Sistem')

@section('content')

<div class="mx-auto max-w-2xl">

    <div class="mb-6">

        <a
            href="{{ route('admin.konfigurasi-sistem.index') }}"
            class="text-sm text-slate-500 hover:text-emerald-700"
        >
            ← Kembali
        </a>

        <h1 class="mt-3 text-2xl font-bold text-slate-900">
            Edit Konfigurasi Sistem
        </h1>

        <p class="mt-1 text-sm text-slate-500">
            Perbarui nilai parameter sesuai tipe datanya.
        </p>

    </div>


    @if($errors->any())

        <div
            class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800"
            role="alert"
        >

            <ul class="list-inside list-disc space-y-1">

                @foreach($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

    @endif


    <form
        method="POST"
        action="{{ route('admin.konfigurasi-sistem.update', $konfigurasiSistem) }}"
        class="space-y-6 rounded-2xl border border-slate-200 bg-white p-6"
    >

        @csrf
        @method('PUT')


        <div>

            <label class="block text-sm font-medium text-slate-700">
                Kunci
            </label>

            <input
                type="text"
                value="{{ $konfigurasiSistem->kunci }}"
                readonly
                class="mt-1.5 w-full rounded-xl border-slate-200 bg-slate-50 text-slate-500"
            >

        </div>


        <div>

            <label class="block text-sm font-medium text-slate-700">
                Tipe Nilai
            </label>

            <input
                type="text"
                value="{{ $konfigurasiSistem->tipe_nilai }}"
                readonly
                class="mt-1.5 w-full rounded-xl border-slate-200 bg-slate-50 text-slate-500"
            >

        </div>


        <div>

            <label
                for="nilai"
                class="block text-sm font-medium text-slate-700"
            >
                Nilai
                <span class="text-red-500">*</span>
            </label>

            <textarea
                id="nilai"
                name="nilai"
                rows="4"
                required
                class="mt-1.5 w-full rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500"
            >{{ old('nilai', $konfigurasiSistem->nilai) }}</textarea>


            <p class="mt-1 text-xs text-slate-500">

                @switch($konfigurasiSistem->tipe_nilai)

                    @case('angka')
                        Masukkan bilangan bulat.
                        @break

                    @case('desimal')
                        Masukkan angka desimal.
                        @break

                    @case('boolean')
                        Gunakan true/false atau 1/0.
                        @break

                    @case('json')
                        Masukkan JSON valid.
                        @break

                    @default
                        Masukkan teks biasa.

                @endswitch

            </p>

        </div>


        <div>

            <label
                for="keterangan"
                class="block text-sm font-medium text-slate-700"
            >
                Keterangan
            </label>

            <input
                id="keterangan"
                name="keterangan"
                value="{{ old('keterangan', $konfigurasiSistem->keterangan) }}"
                maxlength="255"
                class="mt-1.5 w-full rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500"
            >

        </div>


        <div class="flex justify-end gap-3">

            <a
                href="{{ route('admin.konfigurasi-sistem.index') }}"
                class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50"
            >
                Batal
            </a>

            <button
                type="submit"
                class="rounded-xl bg-emerald-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-emerald-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2"
            >
                Simpan Perubahan
            </button>

        </div>

    </form>

</div>

@endsection