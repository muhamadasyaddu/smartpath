@extends('layouts.app')

@section('title', 'Profil Saya - SmartPath')

@section('content')
@include('partials.nav-public')

<div class="mx-auto max-w-4xl px-4 py-10 sm:px-6 lg:px-8">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-900">Profil Saya</h1>
        <p class="mt-2 text-sm text-slate-500">
            Kelola informasi akun SmartPath Anda.
        </p>
    </div>

    @if (session('sukses'))
        <div role="status" class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            {{ session('sukses') }}
        </div>
    @endif

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
    <section class="mb-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
        <h2 class="mb-5 text-lg font-semibold text-slate-900">Foto Profil</h2>

        <div class="flex flex-col gap-5 sm:flex-row sm:items-center">
            <div class="flex h-24 w-24 shrink-0 items-center justify-center overflow-hidden rounded-full border border-slate-200 bg-emerald-100 text-2xl font-semibold text-emerald-700">
                @if (
                    $user->foto_profil &&
                    \Illuminate\Support\Facades\Storage::disk('public')->exists($user->foto_profil)
                )
                    <button
                        type="button"
                        id="openProfilePhoto"
                        class="h-full w-full cursor-zoom-in"
                        aria-label="Perbesar foto profil"
                    >
                        <img
                            id="profilePhotoPreview"
                            src="{{ asset('storage/' . ltrim($user->foto_profil, '/')) }}"
                            alt="Foto profil {{ $user->nama_lengkap }}"
                            class="h-full w-full object-cover"
                        >
                    </button>
                    <span id="profilePhotoInitial" class="hidden">
                        {{ mb_strtoupper(mb_substr($user->nama_lengkap ?: 'U', 0, 1)) }}
                    </span>
                @else
                    <button
                        type="button"
                        id="openProfilePhoto"
                        class="hidden h-full w-full cursor-zoom-in"
                        aria-label="Perbesar foto profil"
                    >
                        <img
                            id="profilePhotoPreview"
                            src=""
                            alt="Pratinjau foto profil"
                            class="h-full w-full object-cover"
                        >
                    </button>
                    <span id="profilePhotoInitial">
                        {{ mb_strtoupper(mb_substr($user->nama_lengkap ?: 'U', 0, 1)) }}
                    </span>
                @endif
            </div>

            <div class="min-w-0 flex-1">
                <p class="text-sm text-slate-600">
                    Klik foto untuk melihat ukuran besar.
                </p>
                <p class="mt-1 text-xs leading-5 text-slate-500">
                    Format JPG, PNG, atau WEBP. Ukuran maksimal 2 MB.
                </p>

                {{-- FORM UPLOAD FOTO --}}
                <form
                    id="profilePhotoForm"
                    action="{{ route('profile.updateFoto') }}"
                    method="POST"
                    enctype="multipart/form-data"
                    class="mt-4"
                >
                    @csrf
                    @method('PUT')

                    <input
                        type="file"
                        name="foto_profil"
                        id="foto_profil"
                        accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                        class="hidden"
                    >

                    <div class="flex flex-wrap gap-2">
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

                        @if ($user->foto_profil)
                            <button
                                type="button"
                                id="deleteProfilePhoto"
                                class="rounded-xl bg-red-50 px-4 py-2.5 text-sm font-semibold text-red-700 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                            >
                                Hapus Foto
                            </button>
                        @endif
                    </div>

                    <p
                        id="photoSelectionStatus"
                        class="mt-2 text-sm text-slate-500"
                        aria-live="polite"
                    ></p>

                    @error('foto_profil')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </form>

                {{-- FORM HAPUS FOTO --}}
                <form
                    id="deleteProfilePhotoForm"
                    action="{{ route('profile.deleteFoto') }}"
                    method="POST"
                    class="hidden"
                >
                    @csrf
                    @method('DELETE')
                </form>
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

            {{-- NAMA LENGKAP --}}
            <div>
                <label for="nama_lengkap" class="block text-sm font-semibold text-slate-700">
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
                    class="mt-2 block w-full rounded-xl border border-slate-300 px-4 py-3 text-sm text-slate-900 focus:border-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-700/20"
                >
                @error('nama_lengkap')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- EMAIL --}}
            <div>
                <label for="email" class="block text-sm font-semibold text-slate-700">
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
                    class="mt-2 block w-full rounded-xl border border-slate-300 px-4 py-3 text-sm text-slate-900 focus:border-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-700/20"
                >
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- NOMOR HP --}}
            <div>
                <label for="nomor_hp" class="block text-sm font-semibold text-slate-700">
                    Nomor HP
                </label>
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

            {{-- ROLE AKUN --}}
            <div>
                <label for="peran" class="block text-sm font-semibold text-slate-700">
                    Role akun
                </label>
                <input
                    id="peran"
                    type="text"
                    value="{{ ucfirst($user->peran) }}"
                    readonly
                    class="mt-2 block w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600"
                >
                <p class="mt-1 text-xs text-slate-500">
                    Role akun hanya dapat diubah melalui pengelolaan akun oleh pihak berwenang.
                </p>
            </div>

            {{-- WILAYAH --}}
            <div>
                <label for="wilayah_id" class="block text-sm font-semibold text-slate-700">
                    Wilayah
                </label>
                <select
                    id="wilayah_id"
                    name="wilayah_id"
                    class="mt-2 block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 focus:border-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-700/20"
                >
                    <option value="">Belum memilih wilayah</option>

                    @foreach (($wilayah ?? collect()) as $item)
                        <option
                            value="{{ $item->id }}"
                            @selected((string) old('wilayah_id', $user->wilayah_id) === (string) $item->id)
                        >
                            {{ $item->nama }}
                        </option>
                    @endforeach
                </select>
                @error('wilayah_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-slate-500">
                    Pilih wilayah tempat Anda berada.
                </p>
            </div>

            {{-- INFORMASI DISABILITAS --}}
            <div class="rounded-xl border border-slate-200 p-4">
                <h2 class="font-semibold text-slate-800">Informasi Disabilitas</h2>
                <p class="mt-1 text-sm text-slate-500">
                    Informasi ini membantu SmartPath menyesuaikan pengalaman aksesibilitas Anda.
                </p>

                <input type="hidden" name="adalah_disabilitas" value="0">

                <label for="adalah_disabilitas" class="mt-4 flex items-start gap-3">
                    <input
                        id="adalah_disabilitas"
                        name="adalah_disabilitas"
                        type="checkbox"
                        value="1"
                        @checked((bool) old('adalah_disabilitas', $user->adalah_disabilitas))
                        class="mt-1 rounded border-slate-300 text-emerald-700 focus:ring-emerald-600"
                    >
                    <span class="text-sm text-slate-700">
                        Saya merupakan penyandang disabilitas.
                    </span>
                </label>

                <div class="mt-4">
                    <label for="jenis_disabilitas" class="block text-sm font-semibold text-slate-700">
                        Jenis disabilitas
                    </label>
                    <input
                        id="jenis_disabilitas"
                        name="jenis_disabilitas"
                        type="text"
                        value="{{ old('jenis_disabilitas', $user->jenis_disabilitas) }}"
                        maxlength="100"
                        placeholder="Contoh: tunanetra"
                        class="mt-2 block w-full rounded-xl border border-slate-300 px-4 py-3 text-sm text-slate-900 focus:border-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-700/20"
                    >
                    @error('jenis_disabilitas')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- TOMBOL SIMPAN DATA PROFIL --}}
            <div class="flex justify-end border-t border-slate-100 pt-5">
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
    class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/80 p-4"
    role="dialog"
    aria-modal="true"
    aria-label="Foto profil ukuran besar"
>
    <button
        type="button"
        id="closeProfilePhoto"
        class="absolute right-5 top-5 rounded-full bg-white px-4 py-2 text-2xl font-bold text-slate-900"
        aria-label="Tutup foto"
    >
        &times;
    </button>

    <img
        id="profilePhotoLarge"
        src=""
        alt="Foto profil ukuran besar"
        class="max-h-[85vh] max-w-full rounded-xl object-contain"
    >
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('foto_profil');
    const chooseButton = document.getElementById('chooseProfilePhoto');
    const saveButton = document.getElementById('saveProfilePhoto');
    const deleteButton = document.getElementById('deleteProfilePhoto');
    const preview = document.getElementById('profilePhotoPreview');
    const initial = document.getElementById('profilePhotoInitial');
    const openButton = document.getElementById('openProfilePhoto');
    const modal = document.getElementById('profilePhotoModal');
    const largePhoto = document.getElementById('profilePhotoLarge');
    const closeButton = document.getElementById('closeProfilePhoto');
    const deleteForm = document.getElementById('deleteProfilePhotoForm');
    const status = document.getElementById('photoSelectionStatus');

    let temporaryUrl = null;

    chooseButton?.addEventListener('click', function () {
        input?.click();
    });

    input?.addEventListener('change', function () {
        const file = input.files?.[0];

        if (!file) return;

        const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];

        if (!allowedTypes.includes(file.type)) {
            input.value = '';
            alert('Pilih foto dengan format JPG, PNG, atau WEBP.');
            return;
        }

        if (file.size > 2 * 1024 * 1024) {
            input.value = '';
            alert('Ukuran foto maksimal 2 MB.');
            return;
        }

        if (temporaryUrl) {
            URL.revokeObjectURL(temporaryUrl);
        }

        temporaryUrl = URL.createObjectURL(file);

        if (preview) {
            preview.src = temporaryUrl;
            preview.classList.remove('hidden');
        }

        initial?.classList.add('hidden');
        openButton?.classList.remove('hidden');
        openButton?.classList.add('block');
        saveButton?.classList.remove('hidden');

        if (status) {
            status.textContent = 'Pratinjau diperbarui. Klik Simpan Foto untuk menyimpan.';
        }
    });

    openButton?.addEventListener('click', function () {
        if (!preview || !preview.src) return;

        largePhoto.src = preview.src;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        closeButton?.focus();
    });

    function closeModal() {
        modal?.classList.add('hidden');
        modal?.classList.remove('flex');
        if (largePhoto) largePhoto.src = '';
    }

    closeButton?.addEventListener('click', closeModal);

    modal?.addEventListener('click', function (event) {
        if (event.target === modal) closeModal();
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') closeModal();
    });

    deleteButton?.addEventListener('click', function () {
        if (!confirm('Yakin ingin menghapus foto profil?')) return;

        deleteForm?.submit();
    });

    window.addEventListener('beforeunload', function () {
        if (temporaryUrl) URL.revokeObjectURL(temporaryUrl);
    });
});
</script>
@endpush
