@extends('layouts.admin')

@section('title', 'Profil Saya')

@section('content')
@php
    $diskPublik = \Illuminate\Support\Facades\Storage::disk('public');

    $fotoUrl = ($user->foto_profil && $diskPublik->exists($user->foto_profil))
        ? asset('storage/' . ltrim($user->foto_profil, '/'))
        : null;

    $inisial   = mb_strtoupper(mb_substr($user->nama_lengkap ?: 'U', 0, 1));
    $labelPeran = $user->isAdmin() ? 'Administrator' : 'Petugas Dinas';
    $rutePulang = route($user->getDashboardRouteName());
@endphp

<div class="mx-auto max-w-4xl">

    <div class="mb-8">
        <h2 class="text-2xl font-bold text-slate-900">Profil Saya</h2>
        <p class="mt-2 text-sm text-slate-500">
            Kelola informasi akun {{ $labelPeran }} SmartPath Anda.
        </p>
    </div>

    @if ($errors->any())
        <div role="alert" class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            <ul class="list-disc space-y-1 pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- KARTU FOTO PROFIL --}}
    <section class="mb-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8" aria-labelledby="judul-foto-profil">
        <h3 id="judul-foto-profil" class="mb-5 text-lg font-semibold text-slate-900">Foto Profil</h3>

        <div class="flex flex-col gap-5 sm:flex-row sm:items-center">

            <div class="flex h-24 w-24 shrink-0 items-center justify-center overflow-hidden rounded-full border border-slate-200 bg-emerald-100 text-2xl font-semibold text-emerald-700">
                <button
                    type="button"
                    id="openProfilePhoto"
                    class="{{ $fotoUrl ? '' : 'hidden' }} h-full w-full cursor-zoom-in"
                    aria-label="Perbesar foto profil"
                >
                    <img
                        id="profilePhotoPreview"
                        @if ($fotoUrl) src="{{ $fotoUrl }}" @endif
                        alt="Foto profil {{ $user->nama_lengkap }}"
                        class="h-full w-full object-cover"
                    >
                </button>

                <span id="profilePhotoInitial" class="{{ $fotoUrl ? 'hidden' : '' }}" aria-hidden="true">
                    {{ $inisial }}
                </span>
            </div>

            <div class="min-w-0 flex-1">
                <p class="text-sm text-slate-600">Klik foto untuk melihat ukuran besar.</p>
                <p class="mt-1 text-xs leading-5 text-slate-500">
                    Format JPG, PNG, atau WEBP. Ukuran maksimal 2 MB.
                </p>

                <div class="mt-4 flex flex-wrap items-center gap-2">

                    {{-- FORM UPLOAD FOTO --}}
                    <form
                        id="profilePhotoForm"
                        action="{{ route('profile.updateFoto') }}"
                        method="POST"
                        enctype="multipart/form-data"
                        class="flex flex-wrap items-center gap-2"
                    >
                        @csrf
                        @method('PUT')

                        <input
                            type="file"
                            name="foto_profil"
                            id="foto_profil"
                            accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                            class="hidden"
                            aria-label="Pilih file foto profil"
                        >

                        <button
                            type="button"
                            id="chooseProfilePhoto"
                            class="rounded-xl bg-emerald-800 px-4 py-2.5 text-sm font-semibold text-white hover:bg-emerald-900 focus:outline-none focus:ring-2 focus:ring-emerald-700 focus:ring-offset-2"
                        >
                            Ubah Foto
                        </button>

                        <button
                            type="submit"
                            id="saveProfilePhoto"
                            class="hidden rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-700 focus:ring-offset-2"
                        >
                            Simpan Foto
                        </button>
                    </form>

                    {{-- FORM HAPUS FOTO (konfirmasi memakai smartpath-admin-pages.js) --}}
                    @if ($user->foto_profil)
                        <form
                            action="{{ route('profile.deleteFoto') }}"
                            method="POST"
                            data-confirm="Yakin ingin menghapus foto profil?"
                        >
                            @csrf
                            @method('DELETE')

                            <button
                                type="submit"
                                class="rounded-xl bg-red-50 px-4 py-2.5 text-sm font-semibold text-red-700 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                            >
                                Hapus Foto
                            </button>
                        </form>
                    @endif
                </div>

                <p id="photoSelectionStatus" class="mt-2 text-sm text-slate-500" role="status" aria-live="polite"></p>
            </div>
        </div>
    </section>

    {{-- FORM DATA PROFIL --}}
    <form
        action="{{ route('profile.update') }}"
        method="POST"
        class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8"
    >
        @csrf
        @method('PUT')

        <div class="space-y-6">

            <div>
                <label for="nama_lengkap" class="block text-sm font-semibold text-slate-700">Nama lengkap</label>
                <input
                    id="nama_lengkap"
                    name="nama_lengkap"
                    type="text"
                    value="{{ old('nama_lengkap', $user->nama_lengkap) }}"
                    required
                    maxlength="150"
                    autocomplete="name"
                    class="mt-2 block w-full rounded-xl border border-slate-300 px-4 py-3 text-sm text-slate-900 focus:border-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-700/20"
                >
                @error('nama_lengkap')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-semibold text-slate-700">Email</label>
                <input
                    id="email"
                    name="email"
                    type="email"
                    value="{{ old('email', $user->email) }}"
                    required
                    maxlength="150"
                    autocomplete="email"
                    class="mt-2 block w-full rounded-xl border border-slate-300 px-4 py-3 text-sm text-slate-900 focus:border-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-700/20"
                >
                <p class="mt-1 text-xs text-slate-500">
                    Mengubah email akan menandai email tersebut belum terverifikasi.
                </p>
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="nomor_hp" class="block text-sm font-semibold text-slate-700">Nomor HP</label>
                <input
                    id="nomor_hp"
                    name="nomor_hp"
                    type="tel"
                    value="{{ old('nomor_hp', $user->nomor_hp) }}"
                    maxlength="20"
                    autocomplete="tel"
                    class="mt-2 block w-full rounded-xl border border-slate-300 px-4 py-3 text-sm text-slate-900 focus:border-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-700/20"
                >
                @error('nomor_hp')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid gap-6 sm:grid-cols-2">
                <div>
                    <label for="peran" class="block text-sm font-semibold text-slate-700">Role akun</label>
                    <input
                        id="peran"
                        type="text"
                        value="{{ $labelPeran }}"
                        readonly
                        aria-readonly="true"
                        class="mt-2 block w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600"
                    >
                    <p class="mt-1 text-xs text-slate-500">
                        Role hanya dapat diubah melalui pengelolaan pengguna.
                    </p>
                </div>

                <div>
                    <label for="terakhir_masuk" class="block text-sm font-semibold text-slate-700">Terakhir masuk</label>
                    <input
                        id="terakhir_masuk"
                        type="text"
                        value="{{ $user->terakhir_masuk ? $user->terakhir_masuk->locale('id')->translatedFormat('d F Y, H:i') : 'Belum tercatat' }}"
                        readonly
                        aria-readonly="true"
                        class="mt-2 block w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600"
                    >
                </div>
            </div>

            <div class="flex flex-col-reverse gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:justify-end">
                <a
                    href="{{ $rutePulang }}"
                    class="rounded-xl border border-slate-300 px-5 py-3 text-center text-sm font-semibold text-slate-700 transition hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-emerald-700 focus:ring-offset-2"
                >
                    Kembali ke Dashboard
                </a>

                <button
                    type="submit"
                    class="rounded-xl bg-emerald-800 px-5 py-3 text-sm font-semibold text-white transition hover:bg-emerald-900 focus:outline-none focus:ring-2 focus:ring-emerald-700 focus:ring-offset-2"
                >
                    Simpan Perubahan
                </button>
            </div>

        </div>
    </form>

</div>

{{-- MODAL FOTO UKURAN BESAR --}}
<div
    id="profilePhotoModal"
    class="fixed inset-0 z-[1200] hidden items-center justify-center bg-black/80 p-4"
    role="dialog"
    aria-modal="true"
    aria-label="Foto profil ukuran besar"
>
    <button
        type="button"
        id="closeProfilePhoto"
        class="absolute right-5 top-5 rounded-full bg-white px-4 py-2 text-2xl font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-600"
        aria-label="Tutup foto"
    >
        &times;
    </button>

    <img
        id="profilePhotoLarge"
        alt="Foto profil ukuran besar"
        class="max-h-[85vh] max-w-full rounded-xl object-contain"
    >
</div>
@endsection


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const input        = document.getElementById('foto_profil');
    const chooseButton = document.getElementById('chooseProfilePhoto');
    const saveButton   = document.getElementById('saveProfilePhoto');
    const preview      = document.getElementById('profilePhotoPreview');
    const initial      = document.getElementById('profilePhotoInitial');
    const openButton   = document.getElementById('openProfilePhoto');
    const modal        = document.getElementById('profilePhotoModal');
    const largePhoto   = document.getElementById('profilePhotoLarge');
    const closeButton  = document.getElementById('closeProfilePhoto');
    const status       = document.getElementById('photoSelectionStatus');

    const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
    const maxBytes     = 2 * 1024 * 1024;

    let temporaryUrl = null;

    const tampilkanPesan = (teks, galat = false) => {
        if (!status) return;
        status.textContent = teks;
        status.classList.toggle('text-red-600', galat);
        status.classList.toggle('text-slate-500', !galat);
    };

    chooseButton?.addEventListener('click', () => input?.click());

    input?.addEventListener('change', () => {
        const file = input.files?.[0];

        if (!file) return;

        if (!allowedTypes.includes(file.type)) {
            input.value = '';
            saveButton?.classList.add('hidden');
            tampilkanPesan('Pilih foto dengan format JPG, PNG, atau WEBP.', true);
            return;
        }

        if (file.size > maxBytes) {
            input.value = '';
            saveButton?.classList.add('hidden');
            tampilkanPesan('Ukuran foto maksimal 2 MB.', true);
            return;
        }

        if (temporaryUrl) URL.revokeObjectURL(temporaryUrl);

        temporaryUrl = URL.createObjectURL(file);

        if (preview) preview.src = temporaryUrl;

        initial?.classList.add('hidden');
        openButton?.classList.remove('hidden');
        saveButton?.classList.remove('hidden');

        tampilkanPesan('Pratinjau diperbarui. Klik Simpan Foto untuk menyimpan.');
    });

    let pemicuModal = null;

    openButton?.addEventListener('click', () => {
        if (!preview || !preview.getAttribute('src')) return;

        pemicuModal = openButton;
        largePhoto.src = preview.src;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        closeButton?.focus();
    });

    const tutupModal = () => {
        modal?.classList.add('hidden');
        modal?.classList.remove('flex');
        if (largePhoto) largePhoto.removeAttribute('src');
        pemicuModal?.focus();
    };

    closeButton?.addEventListener('click', tutupModal);

    modal?.addEventListener('click', (event) => {
        if (event.target === modal) tutupModal();
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && modal && !modal.classList.contains('hidden')) {
            tutupModal();
        }
    });

    window.addEventListener('beforeunload', () => {
        if (temporaryUrl) URL.revokeObjectURL(temporaryUrl);
    });
});
</script>
@endpush