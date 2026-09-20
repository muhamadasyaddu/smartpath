@extends('layouts.admin')

@section('title', 'Edit Pengaturan Prioritas')

@section('content')

<div class="mx-auto max-w-3xl">

    <div class="mb-6">

        <a
            href="{{ route('admin.pengaturan-prioritas.index') }}"
            class="text-sm text-slate-500 hover:text-emerald-700"
        >
            ← Kembali
        </a>

        <h1 class="mt-3 text-2xl font-bold text-slate-900">
            Edit Pengaturan Prioritas
        </h1>

        <p class="mt-1 text-sm text-slate-500">
            Bobot harus berjumlah tepat 1,00.
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
        action="{{ route('admin.pengaturan-prioritas.update', $pengaturanPrioritas) }}"
        class="space-y-6 rounded-2xl border border-slate-200 bg-white p-6"
    >

        @csrf
        @method('PUT')


        <div>

            <label class="block text-sm font-medium text-slate-700">
                Label
            </label>

            <input
                type="text"
                name="label"
                value="{{ old('label', $pengaturanPrioritas->label) }}"
                maxlength="100"
                required
                class="mt-1.5 w-full rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500"
            >

        </div>


        <div class="grid gap-4 sm:grid-cols-3">

            <div>

                <label class="block text-sm font-medium text-slate-700">
                    Bobot Keparahan
                </label>

                <input
                    type="number"
                    step="0.01"
                    min="0"
                    max="1"
                    name="bobot_keparahan"
                    value="{{ old('bobot_keparahan', $pengaturanPrioritas->bobot_keparahan) }}"
                    required
                    class="mt-1.5 w-full rounded-xl border-slate-300"
                >

            </div>


            <div>

                <label class="block text-sm font-medium text-slate-700">
                    Bobot Pelapor
                </label>

                <input
                    type="number"
                    step="0.01"
                    min="0"
                    max="1"
                    name="bobot_pelapor"
                    value="{{ old('bobot_pelapor', $pengaturanPrioritas->bobot_pelapor) }}"
                    required
                    class="mt-1.5 w-full rounded-xl border-slate-300"
                >

            </div>


            <div>

                <label class="block text-sm font-medium text-slate-700">
                    Bobot Fasilitas
                </label>

                <input
                    type="number"
                    step="0.01"
                    min="0"
                    max="1"
                    name="bobot_fasilitas"
                    value="{{ old('bobot_fasilitas', $pengaturanPrioritas->bobot_fasilitas) }}"
                    required
                    class="mt-1.5 w-full rounded-xl border-slate-300"
                >

            </div>

        </div>


        <div class="grid gap-4 sm:grid-cols-2">

            <div>

                <label class="block text-sm font-medium text-slate-700">
                    Radius Deduplikasi
                </label>

                <input
                    type="number"
                    name="radius_deduplikasi_m"
                    min="10"
                    max="500"
                    value="{{ old('radius_deduplikasi_m', $pengaturanPrioritas->radius_deduplikasi_m) }}"
                    required
                    class="mt-1.5 w-full rounded-xl border-slate-300"
                >

            </div>


            <div>

                <label class="block text-sm font-medium text-slate-700">
                    Radius Fasilitas
                </label>

                <input
                    type="number"
                    name="radius_fasilitas_m"
                    min="100"
                    max="5000"
                    value="{{ old('radius_fasilitas_m', $pengaturanPrioritas->radius_fasilitas_m) }}"
                    required
                    class="mt-1.5 w-full rounded-xl border-slate-300"
                >

            </div>

        </div>


        <div>

            <label class="block text-sm font-medium text-slate-700">
                Catatan
            </label>

            <textarea
                name="catatan"
                rows="4"
                class="mt-1.5 w-full rounded-xl border-slate-300"
            >{{ old('catatan', $pengaturanPrioritas->catatan) }}</textarea>

        </div>


        <div class="grid gap-4 sm:grid-cols-2">

            <div>

                <label class="block text-sm font-medium text-slate-700">
                    Berlaku Sejak
                </label>

                <input
                    type="datetime-local"
                    name="berlaku_sejak"
                    value="{{ old(
                        'berlaku_sejak',
                        $pengaturanPrioritas->berlaku_sejak
                            ? $pengaturanPrioritas->berlaku_sejak->format('Y-m-d\TH:i')
                            : ''
                    ) }}"
                    class="mt-1.5 w-full rounded-xl border-slate-300"
                >

            </div>


            <div>

                <label class="block text-sm font-medium text-slate-700">
                    Berlaku Hingga
                </label>

                <input
                    type="datetime-local"
                    name="berlaku_hingga"
                    value="{{ old(
                        'berlaku_hingga',
                        $pengaturanPrioritas->berlaku_hingga
                            ? $pengaturanPrioritas->berlaku_hingga->format('Y-m-d\TH:i')
                            : ''
                    ) }}"
                    class="mt-1.5 w-full rounded-xl border-slate-300"
                >

            </div>

        </div>


        <label class="flex items-start gap-3 rounded-xl border border-slate-200 p-4">

            <input
                type="checkbox"
                name="adalah_aktif"
                value="1"
                {{ old('adalah_aktif', $pengaturanPrioritas->adalah_aktif) ? 'checked' : '' }}
                class="mt-1 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500"
            >

            <span>

                <span class="block text-sm font-semibold text-slate-800">
                    Jadikan konfigurasi aktif
                </span>

                <span class="mt-1 block text-xs text-slate-500">
                    Konfigurasi aktif sebelumnya otomatis dinonaktifkan.
                </span>

            </span>

        </label>


        <div class="flex justify-end gap-3">

            <a
                href="{{ route('admin.pengaturan-prioritas.index') }}"
                class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50"
            >
                Batal
            </a>

            <button
                type="submit"
                class="rounded-xl bg-emerald-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-emerald-800"
            >
                Simpan Perubahan
            </button>

        </div>

    </form>

</div>

@endsection