@extends('layouts.app')

@section('title', 'Edit Laporan - ' . $laporan->judul)

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #location-map { height: 320px; border-radius: 0.75rem; z-index: 1; }
</style>
@endpush

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Breadcrumb --}}
    <nav class="text-sm text-slate-400 mb-6" aria-label="Breadcrumb">
        <ol class="flex items-center gap-2">
            <li><a href="{{ route('laporan.index') }}" class="hover:text-emerald-700 transition-colors">Laporan</a></li>
            <li aria-hidden="true">/</li>
            <li><a href="{{ route('laporan.show', $laporan) }}" class="hover:text-emerald-700 transition-colors">{{ $laporan->kode_laporan }}</a></li>
            <li aria-hidden="true">/</li>
            <li class="text-slate-700 font-medium" aria-current="page">Edit</li>
        </ol>
    </nav>

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-900">Edit Laporan</h1>
        <p class="text-sm text-slate-500 mt-1">{{ $laporan->kode_laporan }} — {{ $laporan->judul }}</p>
    </div>

    <form method="POST" action="{{ route('laporan.update', $laporan) }}" enctype="multipart/form-data" novalidate>
        @csrf @method('PUT')
        <div class="space-y-6">

            {{-- Error summary --}}
            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-800 rounded-xl p-4" role="alert" aria-live="polite">
                    <h3 class="font-semibold mb-1">Mohon perbaiki kesalahan berikut:</h3>
                    <ul class="text-sm list-disc list-inside space-y-0.5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Kategori --}}
            <div class="bg-white rounded-xl border border-slate-200 p-6">
                <h2 class="font-semibold text-slate-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z"/></svg>
                    Kategori Hambatan
                </h2>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                    @foreach($kategoriHambatan as $kategori)
                        <label class="relative cursor-pointer">
                            <input type="radio" name="kategori_hambatan_id" value="{{ $kategori->id }}" class="peer sr-only" aria-label="{{ $kategori->nama }}" {{ old('kategori_hambatan_id', $laporan->kategori_hambatan_id) == $kategori->id ? 'checked' : '' }}>
                            <div class="border-2 rounded-xl p-3 text-center transition-all peer-checked:border-emerald-500 peer-checked:bg-emerald-50 peer-focus:ring-2 peer-focus:ring-emerald-500 peer-focus:ring-offset-2 hover:border-slate-300 border-slate-200">
                                <span class="w-3 h-3 rounded-full inline-block mb-1.5" style="background-color: {{ $kategori->warna_penanda }}" aria-hidden="true"></span>
                                <p class="text-xs font-medium text-slate-700">{{ $kategori->nama }}</p>
                            </div>
                        </label>
                    @endforeach
                </div>
                @error('kategori_hambatan_id')
                    <p class="mt-2 text-sm text-red-600" role="alert">{{ $message }}</p>
                @enderror
            </div>

            {{-- Detail --}}
            <div class="bg-white rounded-xl border border-slate-200 p-6">
                <h2 class="font-semibold text-slate-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Detail Laporan
                </h2>
                <div class="space-y-4">
                    <div>
                        <label for="judul" class="block text-sm font-medium text-slate-700 mb-1.5">Judul Laporan <span class="text-red-500" aria-hidden="true">*</span></label>
                        <input type="text" id="judul" name="judul" value="{{ old('judul', $laporan->judul) }}" class="w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm" required aria-required="true" maxlength="255">
                        @error('judul')<p class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="deskripsi" class="block text-sm font-medium text-slate-700 mb-1.5">Deskripsi <span class="text-red-500" aria-hidden="true">*</span></label>
                        <textarea id="deskripsi" name="deskripsi" rows="4" class="w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm" required aria-required="true">{{ old('deskripsi', $laporan->deskripsi) }}</textarea>
                        @error('deskripsi')<p class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            {{-- Lokasi --}}
            <div class="bg-white rounded-xl border border-slate-200 p-6">
                <h2 class="font-semibold text-slate-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Lokasi Hambatan
                </h2>
                <div id="location-map" class="mb-4" role="application" aria-label="Peta pemilih lokasi"></div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="latitude" class="block text-sm font-medium text-slate-700 mb-1.5">Latitude</label>
                        <input type="text" id="latitude" name="latitude" value="{{ old('latitude', $laporan->latitude) }}" class="w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm font-mono" readonly>
                        @error('latitude')<p class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="longitude" class="block text-sm font-medium text-slate-700 mb-1.5">Longitude</label>
                        <input type="text" id="longitude" name="longitude" value="{{ old('longitude', $laporan->longitude) }}" class="w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm font-mono" readonly>
                        @error('longitude')<p class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div class="mt-4">
                    <label for="alamat_lengkap" class="block text-sm font-medium text-slate-700 mb-1.5">Alamat Lengkap</label>
                    <input type="text" id="alamat_lengkap" name="alamat_lengkap" value="{{ old('alamat_lengkap', $laporan->alamat_lengkap) }}" class="w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm" required>
                    @error('alamat_lengkap')<p class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Wilayah --}}
            <div class="bg-white rounded-xl border border-slate-200 p-6">
                <label for="wilayah_id" class="block text-sm font-medium text-slate-700 mb-1.5">Wilayah</label>
                <select id="wilayah_id" name="wilayah_id" class="w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm" required>
                    <option value="">Pilih Wilayah</option>
                    @foreach($wilayahList as $w)
                        <option value="{{ $w->id }}" {{ old('wilayah_id', $laporan->wilayah_id) == $w->id ? 'selected' : '' }}>{{ $w->nama }}</option>
                    @endforeach
                </select>
                @error('wilayah_id')<p class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>@enderror
            </div>

            {{-- Existing photos --}}
            @if($laporan->fotoLaporan->count() > 0)
                <div class="bg-white rounded-xl border border-slate-200 p-6">
                    <h2 class="font-semibold text-slate-900 mb-4">Foto Saat Ini</h2>
                    <div class="grid grid-cols-3 sm:grid-cols-5 gap-3">
                        @foreach($laporan->fotoLaporan as $foto)
                            <div class="relative group">
                                <img src="{{ $foto->url }}" alt="Foto laporan {{ $laporan->judul }}, gambar {{ $loop->iteration }}" class="w-full h-24 object-cover rounded-lg border border-slate-200">
                                <label class="absolute top-1 right-1 flex items-center">
                                    <input type="checkbox" name="hapus_foto[]" value="{{ $foto->id }}" class="rounded border-slate-300 text-red-500 focus:ring-red-500 w-3.5 h-3.5" aria-label="Hapus foto {{ $loop->iteration }}">
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- New photos --}}
            <div class="bg-white rounded-xl border border-slate-200 p-6">
                <h2 class="font-semibold text-slate-900 mb-4">Tambah Foto Baru</h2>
                <div class="border-2 border-dashed border-slate-300 rounded-xl p-4 text-center hover:border-emerald-400 transition-colors cursor-pointer" id="drop-zone" role="button" tabindex="0" aria-label="Pilih foto baru">
                    <p class="text-sm text-slate-600">Klik atau seret foto ke sini</p>
                    <input type="file" id="foto-input" name="foto[]" multiple accept="image/jpeg,image/png,image/webp" class="hidden">
                </div>
                <div id="foto-preview" class="grid grid-cols-3 sm:grid-cols-5 gap-3 mt-3" aria-live="polite"></div>
            </div>

            {{-- Submit --}}
            <div class="flex items-center justify-between pt-2">
                <a href="{{ route('laporan.show', $laporan) }}" class="text-sm font-medium text-slate-500 hover:text-slate-700 transition-colors">Batal</a>
                <button type="submit" class="inline-flex items-center gap-2 bg-emerald-600 text-white font-semibold px-8 py-3 rounded-xl hover:bg-emerald-700 transition-colors focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Simpan Perubahan
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const map = L.map('location-map', { center: [{{ $laporan->latitude }}, {{ $laporan->longitude }}], zoom: 16 });
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; OSM', maxZoom: 19 }).addTo(map);

    let marker = L.marker([{{ $laporan->latitude }}, {{ $laporan->longitude }}], { draggable: true }).addTo(map);
    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');
    const alamatInput = document.getElementById('alamat_lengkap');

    marker.on('dragend', function(e) {
        const pos = e.target.getLatLng();
        latInput.value = pos.lat.toFixed(7);
        lngInput.value = pos.lng.toFixed(7);
    });

    map.on('click', function(e) {
        if (marker) map.removeLayer(marker);
        marker = L.marker([e.latlng.lat, e.latlng.lng], { draggable: true }).addTo(map);
        latInput.value = e.latlng.lat.toFixed(7);
        lngInput.value = e.latlng.lng.toFixed(7);
        marker.on('dragend', function(ev) {
            const p = ev.target.getLatLng();
            latInput.value = p.lat.toFixed(7);
            lngInput.value = p.lng.toFixed(7);
        });
    });

    // File upload
    const dropZone = document.getElementById('drop-zone');
    const fotoInput = document.getElementById('foto-input');
    const preview = document.getElementById('foto-preview');
    dropZone.addEventListener('click', () => fotoInput.click());
    dropZone.addEventListener('keydown', e => { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); fotoInput.click(); }});
    dropZone.addEventListener('dragover', e => { e.preventDefault(); dropZone.classList.add('border-emerald-400'); });
    dropZone.addEventListener('dragleave', () => dropZone.classList.remove('border-emerald-400'));
    dropZone.addEventListener('drop', e => { e.preventDefault(); dropZone.classList.remove('border-emerald-400'); handleFiles(e.dataTransfer.files); });
    fotoInput.addEventListener('change', () => handleFiles(fotoInput.files));

    function handleFiles(files) {
        Array.from(files).forEach((file, index) => {
            if (!file.type.startsWith('image/')) return;
            const reader = new FileReader();
            reader.onload = e => {
                const div = document.createElement('div');
                div.className = 'relative group';
                div.innerHTML = `<img src="${e.target.result}" alt="Pratinjau foto laporan baru ${i + 1}" class="w-full h-24 object-cover rounded-lg border border-slate-200"><button type="button" class="absolute -top-1 -right-1 w-5 h-5 bg-red-700 text-white rounded-full text-[10px] flex items-center justify-center opacity-0 group-hover:opacity-100 focus:opacity-100" aria-label="Hapus foto baru ${i + 1}">&times;</button>`;
                div.querySelector('button').addEventListener('click', () => div.remove());
                preview.appendChild(div);
            };
            reader.readAsDataURL(file);
        });
    }
});
</script>
@endpush