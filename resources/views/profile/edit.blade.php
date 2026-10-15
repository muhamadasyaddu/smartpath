
@extends('layouts.app')

@section('title', 'Profil Saya - SmartPath')

@section('content')
@include('partials.nav-public')

<main id="main-content" class="mx-auto max-w-4xl px-4 py-10 sm:px-6 lg:px-8">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-900">Profil Saya</h1>
        <p class="mt-2 text-sm text-slate-500">
            Kelola informasi akun SmartPath Anda.
        </p>
    </div>

    @if (session('sukses'))
        <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800" role="status">
            {{ session('sukses') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">
            <ul class="list-disc space-y-1 pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form
        action="{{ route('profile.update') }}"
        method="POST"
        enctype="multipart/form-data"
        class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8"
    >
        @csrf
        @method('PUT')

        <div class="space-y-6">
            {{-- FOTO PROFIL --}}
            <div class="flex flex-col gap-4 border-b border-slate-100 pb-6 sm:flex-row sm:items-center">
                <div class="flex h-24 w-24 shrink-0 items-center justify-center overflow-hidden rounded-full border border-slate-200 bg-emerald-100 text-xl font-semibold text-emerald-700">
                    @if (
                        $user->foto_profil &&
                        \Illuminate\Support\Facades\Storage::disk('public')->exists($user->foto_profil)
                    )
                        <img
                            id="profile-photo-preview"
                            src="{{ asset('storage/' . ltrim($user->foto_profil, '/')) }}"
                            alt="Foto profil {{ $user->nama_lengkap }}"
                            class="h-full w-full object-cover"
                        >
                        <span id="profile-photo-initial" class="hidden">
                            {{ mb_strtoupper(mb_substr($user->nama_lengkap ?: 'U', 0, 1)) }}
                        </span>
                    @else
                        <img
                            id="profile-photo-preview"
                            src=""
                            alt="Pratinjau foto profil"
                            class="hidden h-full w-full object-cover"
                        >
                        <span id="profile-photo-initial">
                            {{ mb_strtoupper(mb_substr($user->nama_lengkap ?: 'U', 0, 1)) }}
                        </span>
                    @endif
                </div>

                <div class="min-w-0 flex-1">
                    <label for="foto_profil" class="block text-sm font-semibold text-slate-700">
                        Foto profil
                    </label>

                    <input
                        id="foto_profil"
                        name="foto_profil"
                        type="file"
                        accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                        aria-describedby="foto-profil-help"
                        class="mt-2 block w-full text-sm text-slate-600 file:mr-4 file:rounded-lg file:border-0 file:bg-emerald-50 file:px-4 file:py-2 file:font-medium file:text-emerald-800 hover:file:bg-emerald-100"
                    >

                    <p id="foto-profil-help" class="mt-2 text-xs leading-5 text-slate-500">
                        Format JPG, PNG, atau WEBP. Ukuran maksimal 2 MB.
                        Pilih foto lalu tekan Simpan Perubahan.
                    </p>

                    @error('foto_profil')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

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

            {{-- ROLE AKUN: TIDAK DAPAT DIUBAH DARI FORM PROFIL --}}
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

            {{-- SIMPAN --}}
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
</main>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('foto_profil');
    const preview = document.getElementById('profile-photo-preview');
    const initial = document.getElementById('profile-photo-initial');

    if (!input || !preview || !initial) return;

    let temporaryUrl = null;

    input.addEventListener('change', function () {
        const file = this.files && this.files[0];

        if (!file) return;

        const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];

        if (!allowedTypes.includes(file.type)) {
            this.value = '';
            alert('Pilih foto dengan format JPG, PNG, atau WEBP.');
            return;
        }

        if (file.size > 2 * 1024 * 1024) {
            this.value = '';
            alert('Ukuran foto maksimal 2 MB.');
            return;
        }

        if (temporaryUrl) URL.revokeObjectURL(temporaryUrl);

        temporaryUrl = URL.createObjectURL(file);
        preview.src = temporaryUrl;
        preview.classList.remove('hidden');
        initial.classList.add('hidden');
    });
});
</script>
@endpush