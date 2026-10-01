@extends('layouts.app')

@section('title', 'Profil - SmartPath')

@section('content')

@include('partials.nav-public')

<main
    id="main-content"
    class="max-w-4xl mx-auto px-4 py-10 sm:px-6 lg:px-8"
>
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-900">
            Profil Saya
        </h1>

        <p class="mt-2 text-sm text-slate-500">
            Kelola informasi akun SmartPath Anda.
        </p>
    </div>

    @if(session('sukses'))
        <div
            class="mb-6 rounded-xl border border-emerald-200
                   bg-emerald-50 px-4 py-3
                   text-sm text-emerald-800"
            role="alert"
        >
            {{ session('sukses') }}
        </div>
    @endif

    @if($errors->any())
        <div
            class="mb-6 rounded-xl border border-red-200
                   bg-red-50 px-4 py-3
                   text-sm text-red-800"
            role="alert"
        >
            <ul class="list-disc pl-5 space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form
        action="{{ route('profile.update') }}"
        method="POST"
        enctype="multipart/form-data"
        class="rounded-2xl border border-slate-200
               bg-white p-6 shadow-sm"
    >
        @csrf
        @method('PUT')

        <div class="space-y-6">

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
                    class="mt-2 block w-full rounded-xl
                           border border-slate-300 px-4 py-3
                           text-sm text-slate-900
                           focus:border-emerald-700
                           focus:ring-2
                           focus:ring-emerald-700/20"
                >
            </div>

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
                    type="text"
                    value="{{ old('nomor_hp', $user->nomor_hp) }}"
                    maxlength="20"
                    class="mt-2 block w-full rounded-xl
                           border border-slate-300 px-4 py-3
                           text-sm"
                >
            </div>

            <div>
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
                    class="mt-2 block w-full text-sm"
                >
            </div>

            <div class="flex justify-end">
                <button
                    type="submit"
                    class="rounded-xl bg-emerald-800
                           px-5 py-3 text-sm font-semibold
                           text-white hover:bg-emerald-900
                           focus:outline-none
                           focus:ring-2
                           focus:ring-emerald-700
                           focus:ring-offset-2"
                >
                    Simpan Perubahan
                </button>
            </div>

        </div>
    </form>
</main>

@endsection