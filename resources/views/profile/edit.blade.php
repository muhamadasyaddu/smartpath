
@extends('layouts.app')

@section('title', 'Profil Saya - SmartPath')

@section('content')

@include('partials.nav-public')

<main
    id="main-content"
    class="max-w-4xl mx-auto px-4 py-10 sm:px-6 lg:px-8"
>
    {{-- JUDUL --}}
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-900">
            Profil Saya
        </h1>

        <p class="mt-2 text-sm text-slate-500">
            Kelola informasi akun SmartPath Anda.
        </p>
    </div>

    {{-- PESAN BERHASIL --}}
    @if(session('sukses'))
        <div
            class="mb-6 rounded-xl border border-emerald-200
                   bg-emerald-50 px-4 py-3 text-sm text-emerald-800"
            role="status"
        >
            {{ session('sukses') }}
        </div>
    @endif

    {{-- PESAN VALIDASI --}}
    @if($errors->any())
        <div
            class="mb-6 rounded-xl border border-red-200
                   bg-red-50 px-4 py-3 text-sm text-red-800"
            role="alert"
        >
            <ul class="list-disc pl-5 space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- FORM PROFIL --}}
    <form
        action="{{ route('profile.update') }}"
        method="POST"
        enctype="multipart/form-data"
        class="rounded-2xl border border-slate-200
               bg-white p-6 shadow-sm sm:p-8"
    >
        @csrf
        @method('PUT')

        <div class="space-y-6">

            {{-- FOTO PROFIL --}}
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
                @if($user->foto_profil)
                    <img
                        src="{{ asset('storage/' . $user->foto_profil) }}"
                        alt="Foto profil {{ $user->nama_lengkap }}"
                        class="h-24 w-24 rounded-full border
                               border-slate-200 object-cover"
                    >
                @else
                    <div
                        class="flex h-24 w-24 shrink-0 items-center
                               justify-center rounded-full bg-emerald-100
                               text-3xl font-bold text-emerald-800"
                        aria-label="Inisial pengguna"
                    >
                        {{ strtoupper(substr($user->nama_lengkap, 0, 1)) }}
                    </div>
                @endif

                <div class="flex-1">
                    <label
                        for="foto_profil"
                        class="block text-sm font-semibold text-slate-700"
                    >
                        Foto profil
                    </label>

                    <input
                        id="foto_profil"
                        name="foto_profil"
                        type="file"
                        accept="image/jpeg,image/png,image/webp"
                        class="mt-2 block w-full text-sm text-slate-600"
                    >

                    <p class="mt-1 text-xs text-slate-500">
                        Format JPG, PNG, atau WEBP. Maksimal 2 MB.
                    </p>
                </div>
            </div>

            {{-- NAMA LENGKAP --}}
            <div>
                <label
                    for="nama_lengkap"
                    class="block text-sm font-semibold text-slate-700"
                >
                    Nama lengkap
                </label>

                <input
                    id="nama_lengkap"
                    name="nama_lengkap"
                    type="text"
                    value="{{ old('nama_lengkap', $user->nama_lengkap) }}"
                    required
                    maxlength="150"
                    autocomplete="name"
                    class="mt-2 block w-full rounded-xl
                           border border-slate-300 px-4 py-3
                           text-sm text-slate-900
                           focus:border-emerald-700
                           focus:outline-none focus:ring-2
                           focus:ring-emerald-700/20"
                >
            </div>

            {{-- EMAIL --}}
            <div>
                <label
                    for="email"
                    class="block text-sm font-semibold text-slate-700"
                >
                    Email
                </label>

                <input
                    id="email"
                    name="email"
                    type="email"
                    value="{{ old('email', $user->email) }}"
                    required
                    maxlength="150"
                    autocomplete="email"
                    class="mt-2 block w-full rounded-xl
                           border border-slate-300 px-4 py-3
                           text-sm text-slate-900
                           focus:border-emerald-700
                           focus:outline-none focus:ring-2
                           focus:ring-emerald-700/20"
                >
            </div>

            {{-- NOMOR HP --}}
            <div>
                <label
                    for="nomor_hp"
                    class="block text-sm font-semibold text-slate-700"
                >
                    Nomor HP
                </label>

                <input
                    id="nomor_hp"
                    name="nomor_hp"
                    type="tel"
                    value="{{ old('nomor_hp', $user->nomor_hp) }}"
                    maxlength="20"
                    autocomplete="tel"
                    class="mt-2 block w-full rounded-xl
                           border border-slate-300 px-4 py-3
                           text-sm text-slate-900
                           focus:border-emerald-700
                           focus:outline-none focus:ring-2
                           focus:ring-emerald-700/20"
                >
            </div>

            {{-- ROLE AKUN --}}
            <div>
                <label
                    for="peran"
                    class="block text-sm font-semibold text-slate-700"
                >
                    Role akun
                </label>

                <input
                    id="peran"
                    type="text"
                    value="{{ ucfirst($user->peran) }}"
                    readonly
                    class="mt-2 block w-full rounded-xl
                           border border-slate-200 bg-slate-50
                           px-4 py-3 text-sm text-slate-600"
                >

                <p class="mt-1 text-xs text-slate-500">
                    Role akun hanya dapat diubah melalui pengelolaan akun.
                </p>
            </div>

            {{-- WILAYAH --}}
            <div>
                <label
                    for="wilayah_id"
                    class="block text-sm font-semibold text-slate-700"
                >
                    Wilayah
                </label>

                <select
                    id="wilayah_id"
                    name="wilayah_id"
                    class="mt-2 block w-full rounded-xl
                           border border-slate-300 bg-white
                           px-4 py-3 text-sm text-slate-900
                           focus:border-emerald-700
                           focus:outline-none focus:ring-2
                           focus:ring-emerald-700/20"
                >
                    <option value="">
                        Belum memilih wilayah
                    </option>

                    @foreach(($wilayah ?? collect()) as $item)
                        <option
                            value="{{ $item->id }}"
                            @selected(
                                (string) old(
                                    'wilayah_id',
                                    $user->wilayah_id
                                ) === (string) $item->id
                            )
                        >
                            {{ $item->nama_lengkap }}
                        </option>
                    @endforeach
                </select>

                <p class="mt-1 text-xs text-slate-500">
                    Pilih wilayah tempat Anda berada.
                </p>
            </div>

            {{-- INFORMASI DISABILITAS --}}
            <div class="rounded-xl border border-slate-200 p-4">
                <h2 class="font-semibold text-slate-800">
                    Informasi Disabilitas
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Informasi ini membantu SmartPath menyesuaikan
                    pengalaman aksesibilitas Anda.
                </p>

                {{-- Nilai 0 dikirim jika checkbox tidak dicentang --}}
                <input
                    type="hidden"
                    name="adalah_disabilitas"
                    value="0"
                >

                <label class="mt-4 flex items-start gap-3">
                    <input
                        id="adalah_disabilitas"
                        name="adalah_disabilitas"
                        type="checkbox"
                        value="1"
                        @checked(
                            (bool) old(
                                'adalah_disabilitas',
                                $user->adalah_disabilitas
                            )
                        )
                        class="mt-1 rounded border-slate-300
                               text-emerald-700 focus:ring-emerald-600"
                    >

                    <span class="text-sm text-slate-700">
                        Saya merupakan penyandang disabilitas.
                    </span>
                </label>

                <div class="mt-4">
                    <label
                        for="jenis_disabilitas"
                        class="block text-sm font-semibold text-slate-700"
                    >
                        Jenis disabilitas
                    </label>

                    <input
                        id="jenis_disabilitas"
                        name="jenis_disabilitas"
                        type="text"
                        value="{{ old(
                            'jenis_disabilitas',
                            $user->jenis_disabilitas
                        ) }}"
                        maxlength="100"
                        placeholder="Contoh: tunanetra"
                        class="mt-2 block w-full rounded-xl
                               border border-slate-300 px-4 py-3
                               text-sm text-slate-900
                               focus:border-emerald-700
                               focus:outline-none focus:ring-2
                               focus:ring-emerald-700/20"
                    >
                </div>
            </div>

            {{-- TOMBOL SIMPAN --}}
            <div class="flex justify-end border-t border-slate-100 pt-5">
                <button
                    type="submit"
                    class="rounded-xl bg-emerald-800
                           px-5 py-3 text-sm font-semibold
                           text-white transition hover:bg-emerald-900
                           focus:outline-none focus:ring-2
                           focus:ring-emerald-700 focus:ring-offset-2"
                >
                    Simpan Perubahan
                </button>
            </div>

        </div>
    </form>
</main>

@endsection
