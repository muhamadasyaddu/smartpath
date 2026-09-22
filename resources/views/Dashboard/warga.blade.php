@extends('layouts.app')

@section('title', 'Dashboard Warga - SmartPath')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #warga-map { height: 360px; width: 100%; }
    @media (max-width: 640px) { #warga-map { height: 300px; } }
    .warga-map-card .leaflet-popup-content-wrapper { border-radius: 12px; }
    .warga-map-card .leaflet-popup-content { margin: 12px 14px; }
    @keyframes wargaMarkerPulse {
        0%,100% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.2); opacity: .65; }
    }
</style>
@endpush

@section('content')
@include('partials.nav-public')

<main class="max-w-7xl mx-auto w-full px-4 py-10 sm:px-6 lg:px-8">
    @if(session('sukses'))
        <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
            {{ session('sukses') }}
        </div>
    @endif

    <div class="mb-8">
        <p class="text-sm font-semibold uppercase tracking-wide text-emerald-600">Dashboard Warga</p>
        <h1 class="mt-2 text-3xl font-bold text-slate-900 dark:text-white">Halo, {{ $user->nama_lengkap ?? 'Warga' }}</h1>
        <p class="mt-2 text-slate-600 dark:text-slate-400">Kelola laporan aksesibilitas dan pantau perkembangannya di sini.</p>
    </div>

    <div class="grid gap-6 md:grid-cols-2">
        <a href="{{ route('laporan.create') }}" class="rounded-2xl bg-emerald-600 p-6 text-white shadow-sm transition hover:bg-emerald-700">
            <div class="w-10 h-10 rounded-xl bg-emerald-500/30 flex items-center justify-center mb-4">
                <i data-lucide="plus-circle" class="w-6 h-6"></i>
            </div>
            <h2 class="text-lg font-bold">Buat Laporan</h2>
            <p class="mt-2 text-sm text-emerald-50">Laporkan hambatan aksesibilitas di sekitar Anda.</p>
        </a>

        <a href="{{ route('laporan.index') }}" class="rounded-2xl border border-slate-200 dark:border-white/10 bg-white dark:bg-[#0b1822] p-6 shadow-sm transition hover:border-emerald-500 hover:shadow-md group">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center text-emerald-600">
                    <i data-lucide="file-text" class="w-6 h-6"></i>
                </div>
                <span class="text-3xl font-bold text-slate-900 dark:text-white">{{ $jumlahLaporan ?? 0 }}</span>
            </div>
            <h2 class="text-lg font-bold text-slate-900 dark:text-white group-hover:text-emerald-600 transition">Laporan Saya</h2>
            <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">Lihat dan kelola seluruh laporan yang pernah Anda buat.</p>
        </a>
    </div>

    <!-- MAP CARD -->
    <section class="warga-map-card mt-6 rounded-2xl border border-slate-200 dark:border-white/10 bg-white dark:bg-[#0b1822] shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200 dark:border-white/10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center">
<i class="fa-solid fa-person-walking-with-cane text-emerald-600 dark:text-emerald-400 text-base"></i> </div>
                <div>
                    <h2 class="text-base font-bold text-slate-900 dark:text-white">Peta Aksesibilitas</h2>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">Pantau kondisi aksesibilitas di Kota Depok</p>
                </div>
            </div>
            <span class="inline-flex w-fit items-center gap-1.5 text-[9px] font-semibold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-500/10 px-2.5 py-1.5 rounded-full">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> LIVE MAP
            </span>
        </div>

      

                <div id="warga-map" role="application" aria-label="Peta aksesibilitas SmartPath"></div>

                
                </div>
            </div>
                        <!-- CARI RUTE -->
            <div class="mt-4 rounded-xl border border-emerald-200 dark:border-emerald-500/20 bg-emerald-50/70 dark:bg-emerald-500/5 p-4">

                <div class="flex items-center gap-2 mb-3">
                    <div class="w-8 h-8 rounded-lg bg-emerald-600 flex items-center justify-center">
                        <i class="fa-solid fa-route text-white text-sm"></i>
                    </div>

                    <div>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white">
                            Cari Rute
                        </h3>

                        <p class="text-[10px] text-slate-600 dark:text-slate-400">
                            Tentukan tujuan untuk melihat rute dan hambatan di sepanjang perjalanan.
                        </p>
                    </div>
                </div>

                <form id="wargaRouteForm" class="flex flex-col sm:flex-row gap-2">

                    <input
                        id="wargaRouteDestination"
                        type="text"
                        placeholder="Contoh: Stasiun Depok Baru"
                        required
                        class="min-w-0 flex-1 rounded-lg border border-slate-200 dark:border-white/10 bg-white dark:bg-[#0b1822] px-3 py-2.5 text-xs text-slate-800 dark:text-white outline-none focus:border-emerald-500"
                        aria-label="Tujuan perjalanan"
                    >

                    <button
                        type="submit"
                        id="wargaRouteButton"
                        class="inline-flex items-center justify-center gap-2 rounded-lg bg-emerald-600 px-4 py-2.5 text-xs font-bold text-white hover:bg-emerald-700 transition"
                    >
                        <i class="fa-solid fa-route"></i>
                        Cari Rute
                    </button>

                </form>

                <!-- PILIHAN TUJUAN -->
                <div
                    id="wargaRouteChoices"
                    class="hidden mt-3 space-y-2"
                    aria-live="polite"
                ></div>

                <!-- HASIL RUTE -->
                <div
                    id="wargaRouteResult"
                    class="hidden mt-3 rounded-lg bg-white dark:bg-[#0b1822] border border-slate-200 dark:border-white/10 p-3"
                    aria-live="polite"
                >

                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">

                        <div>
                            <p class="text-[9px] text-slate-500 dark:text-slate-400">
                                Tujuan
                            </p>

                            <p
                                id="wargaRouteName"
                                class="text-sm font-bold text-slate-900 dark:text-white"
                            >
                                -
                            </p>
                            <p id="wargaRouteAddress" class="mt-1 text-[10px] text-slate-500 dark:text-slate-400" aria-label="Alamat tujuan">-</p>
                        </div>

                        <button
                            type="button"
                            id="wargaStartTrip"
                            class="inline-flex items-center justify-center gap-2 rounded-lg bg-emerald-600 px-4 py-2.5 text-[10px] font-bold text-white hover:bg-emerald-700 transition"
                        >
                            <i class="fa-solid fa-person-walking"></i>
                            Mulai Perjalanan
                        </button>

                    </div>

                    <div class="grid grid-cols-2 gap-2 mt-3">

                        <div class="rounded-lg bg-slate-50 dark:bg-white/5 p-3">
                            <p class="text-[9px] text-slate-500 dark:text-slate-400">
                                Jarak
                            </p>

                            <p
                                id="wargaRouteDistance"
                                class="text-sm font-bold text-slate-900 dark:text-white"
                            >
                                -
                            </p>
                        </div>

                        <div class="rounded-lg bg-slate-50 dark:bg-white/5 p-3">
                            <p class="text-[9px] text-slate-500 dark:text-slate-400">
                                Estimasi waktu
                            </p>

                            <p
                                id="wargaRouteDuration"
                                class="text-sm font-bold text-slate-900 dark:text-white"
                            >
                                -
                            </p>
                        </div>

                    </div>

                    <div
                        id="wargaRouteHazards"
                        class="mt-3 text-[10px] text-slate-600 dark:text-slate-400"
                    ></div>

                </div>

            </div>

            <div class="flex flex-wrap items-center justify-between gap-3 pt-3">
                <div class="flex flex-wrap gap-2">
                    <button type="button" id="btnLokasiSaya" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 dark:border-white/10 bg-white dark:bg-white/5 px-3 py-2 text-[10px] font-semibold text-slate-600 dark:text-slate-300 hover:border-emerald-500 hover:text-emerald-600 transition">
                        <i class="fa-solid fa-location-crosshairs"></i> Lokasi Saya
                    </button>
                    <button type="button" id="btnFasilitas" aria-pressed="false" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 dark:border-white/10 bg-white dark:bg-white/5 px-3 py-2 text-[10px] font-semibold text-slate-600 dark:text-slate-300 hover:border-emerald-500 hover:text-emerald-600 transition">
                        <i class="fa-solid fa-building"></i> Fasilitas Publik
                    </button>
                </div>
                <a href="{{ route('peta.index') }}" class="inline-flex items-center gap-1.5 text-[10px] font-semibold text-emerald-600 hover:text-emerald-700 transition">
                    Buka Peta Lengkap <i class="fa-solid fa-arrow-right text-[9px]"></i>
                </a>
            </div>

            
        </div>
    </section>
</main>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    'use strict';

    const map = L.map('warga-map', { center: [-6.4025, 106.8197], zoom: 13, zoomControl: true });
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 19
    }).addTo(map);

    const laporanLayer = L.layerGroup().addTo(map);
    const fasilitasLayer = L.layerGroup();
    let laporanData = [], fasilitasData = [], userMarker = null, userCircle = null;
    const status = document.getElementById('wargaMapStatus');

    const escapeHtml = value => {
        const el = document.createElement('div');
        el.textContent = value ?? '';
        return el.innerHTML;
    };

    const setStatus = text => { if (status) status.textContent = text; };

    function markerIcon(priority) {
        const p = String(priority || '').toLowerCase();
        const color = p === 'tinggi' ? '#ef4444' : p === 'sedang' ? '#f59e0b' : p === 'rendah' ? '#3b82f6' : '#64748b';
        return L.divIcon({
            className: 'warga-marker',
            html: `<div style="width:${p === 'tinggi' ? 18 : 15}px;height:${p === 'tinggi' ? 18 : 15}px;background:${color};border:3px solid #fff;border-radius:50%;box-shadow:0 2px 8px rgba(0,0,0,.3);${p === 'tinggi' ? 'animation:wargaMarkerPulse 2s infinite;' : ''}"></div>`,
            iconSize: [18,18], iconAnchor: [9,9], popupAnchor: [0,-9]
        });
    }

    function renderLaporan(data) {
        laporanLayer.clearLayers();
        data.forEach(item => {
            const lat = parseFloat(item.latitude), lng = parseFloat(item.longitude);
            if (!Number.isFinite(lat) || !Number.isFinite(lng)) return;

            const marker = L.marker([lat,lng], { icon: markerIcon(item.tingkat_prioritas) });
            marker.bindPopup(`
                <div style="min-width:210px;max-width:280px">
                    ${item.foto_utama ? `<img src="${escapeHtml(item.foto_utama)}" alt="Foto laporan" style="width:100%;height:115px;object-fit:cover;border-radius:8px;margin-bottom:8px">` : ''}
                    <h3 style="font-weight:700;font-size:14px;margin:0 0 5px;color:#0f172a">${escapeHtml(item.judul || 'Laporan aksesibilitas')}</h3>
                    <p style="font-size:11px;color:#64748b;margin:0 0 5px">${escapeHtml(item.kategori || 'Tanpa kategori')}</p>
                    <p style="font-size:11px;color:#475569;margin:0 0 7px">${escapeHtml(item.alamat_lengkap || 'Alamat tidak tersedia')}</p>
                    <span style="display:inline-block;padding:3px 8px;border-radius:999px;background:#ecfdf5;color:#047857;font-size:10px;font-weight:600">${escapeHtml(item.status_label || item.status || 'Terverifikasi')}</span>
                    <span style="font-size:10px;color:#64748b;margin-left:5px">Prioritas: ${escapeHtml(item.tingkat_prioritas || 'Belum Dinilai')}</span>
                    <br><a href="/laporan/${encodeURIComponent(item.id)}" style="display:inline-block;margin-top:8px;color:#059669;font-size:11px;font-weight:600;text-decoration:none">Lihat Detail →</a>
                </div>
            `, { maxWidth: 300 });
            laporanLayer.addLayer(marker);
        });
    }

    function facilityIcon() {
        return L.divIcon({
            className: 'warga-facility',
            html: `<div style="width:24px;height:24px;background:#0d9488;border:3px solid #fff;border-radius:50%;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 8px rgba(0,0,0,.3);color:white;font-size:10px"><i class="fa-solid fa-building"></i></div>`,
            iconSize: [24,24], iconAnchor: [12,12]
        });
    }

    function renderFasilitas(data) {
        fasilitasLayer.clearLayers();
        data.forEach(item => {
            const lat = parseFloat(item.latitude), lng = parseFloat(item.longitude);
            if (!Number.isFinite(lat) || !Number.isFinite(lng)) return;
            const marker = L.marker([lat,lng], { icon: facilityIcon() });
            marker.bindPopup(`<div style="min-width:180px"><h3 style="font-weight:700;font-size:13px;margin:0 0 5px;color:#0f172a">${escapeHtml(item.nama || 'Fasilitas Publik')}</h3><p style="font-size:11px;color:#64748b;margin:0">${escapeHtml(item.jenis || 'Fasilitas publik')}<br>${escapeHtml(item.alamat || 'Alamat tidak tersedia')}</p></div>`);
            fasilitasLayer.addLayer(marker);
        });
    }

    fetch('{{ route("peta.data") }}')
        .then(r => { if (!r.ok) throw new Error('Gagal memuat data laporan'); return r.json(); })
        .then(data => {
            laporanData = Array.isArray(data) ? data : [];
            renderLaporan(laporanData);
            setStatus('');
        })
        .catch(error => { console.error(error); setStatus('Data laporan belum dapat dimuat.'); });

    fetch('{{ route("peta.fasilitas") }}')
        .then(r => { if (!r.ok) throw new Error('Gagal memuat data fasilitas'); return r.json(); })
        .then(data => { fasilitasData = Array.isArray(data) ? data : []; renderFasilitas(fasilitasData); })
        .catch(error => console.error(error));

    document.getElementById('btnFasilitas').addEventListener('click', function () {
        const active = map.hasLayer(fasilitasLayer);
        if (active) {
            map.removeLayer(fasilitasLayer);
            this.setAttribute('aria-pressed', 'false');
            this.classList.remove('border-emerald-500','text-emerald-600');
        } else {
            fasilitasLayer.addTo(map);
            this.setAttribute('aria-pressed', 'true');
            this.classList.add('border-emerald-500','text-emerald-600');
        }
    });

    document.getElementById('btnLokasiSaya').addEventListener('click', function () {
        if (!navigator.geolocation) { setStatus('Perangkat atau browser tidak mendukung GPS.'); return; }
        const button = this;
        button.disabled = true;
        setStatus('Sedang mendeteksi lokasi kamu...');
        navigator.geolocation.getCurrentPosition(position => {
            const lat = position.coords.latitude, lng = position.coords.longitude, accuracy = position.coords.accuracy;
            if (userMarker) map.removeLayer(userMarker);
            if (userCircle) map.removeLayer(userCircle);
            userCircle = L.circle([lat,lng], { radius: accuracy, color:'#059669', fillColor:'#10b981', fillOpacity:.12, weight:2 }).addTo(map);
            userMarker = L.marker([lat,lng], { icon:L.divIcon({ className:'warga-user', html:'<div style="width:16px;height:16px;background:#059669;border:3px solid #fff;border-radius:50%;box-shadow:0 2px 8px rgba(0,0,0,.35)"></div>', iconSize:[16,16], iconAnchor:[8,8] }) }).addTo(map).bindPopup('Lokasi kamu').openPopup();
            map.setView([lat,lng],16);
            setStatus(`Lokasi kamu ditemukan. Akurasi GPS ±${Math.round(accuracy)} meter.`);
            button.disabled = false;
        }, error => {
            const messages = {1:'Izin lokasi ditolak. Silakan izinkan akses lokasi pada browser.',2:'Lokasi tidak tersedia. Pastikan GPS/lokasi perangkat aktif.',3:'Waktu mendapatkan lokasi habis. Silakan coba lagi.'};
            setStatus(messages[error.code] || 'Lokasi tidak dapat ditemukan.');
            button.disabled = false;
        }, { enableHighAccuracy:true, timeout:10000, maximumAge:0 });
    });

   
// ==========================================================
// NAVIGASI AKTIF DI DALAM MAP
// ==========================================================

const routeForm = document.getElementById('wargaRouteForm');
const routeDestination = document.getElementById('wargaRouteDestination');
const routeButton = document.getElementById('wargaRouteButton');

const routeChoices = document.getElementById('wargaRouteChoices');
const routeResult = document.getElementById('wargaRouteResult');

const routeName = document.getElementById('wargaRouteName');
const routeAddress = document.getElementById('wargaRouteAddress');
const routeDistance = document.getElementById('wargaRouteDistance');
const routeDuration = document.getElementById('wargaRouteDuration');
const routeHazards = document.getElementById('wargaRouteHazards');

const startTrip = document.getElementById('wargaStartTrip');

let wargaCurrentPosition = null;
let wargaSelectedDestination = null;
let wargaRouteLine = null;
let wargaRouteGeometry = null;
let wargaRouteSteps = [];
let wargaRouteHazards = [];
let wargaWatchId = null;
let wargaNextStepIndex = 0;
const wargaAnnouncedSteps = new Set();


// ==========================================================
// FORMAT JARAK
// ==========================================================

function formatRouteDistance(meters) {

    meters = Number(meters || 0);

    if (meters >= 1000) {
        return (meters / 1000).toFixed(2) + ' km';
    }

    return Math.round(meters) + ' meter';
}


// ==========================================================
// FORMAT DURASI
// ==========================================================

function formatRouteDuration(seconds) {

    seconds = Number(seconds || 0);

    const minutes = Math.max(
        1,
        Math.round(seconds / 60)
    );

    return minutes + ' menit';
}


// ==========================================================
// AMBIL GPS
// ==========================================================

function getWargaCurrentPosition() {

    return new Promise((resolve, reject) => {

        if (!navigator.geolocation) {

            reject(
                new Error(
                    'Browser tidak mendukung GPS.'
                )
            );

            return;
        }

        navigator.geolocation.getCurrentPosition(

            function(position) {

                wargaCurrentPosition = {
                    latitude: position.coords.latitude,
                    longitude: position.coords.longitude,
                    accuracy: position.coords.accuracy
                };

                resolve(
                    wargaCurrentPosition
                );
            },

            function(error) {

                if (error.code === 1) {
                    reject(
                        new Error(
                            'Izin lokasi ditolak. Aktifkan GPS/lokasi browser.'
                        )
                    );
                }
                else if (error.code === 2) {
                    reject(
                        new Error(
                            'Lokasi tidak tersedia.'
                        )
                    );
                }
                else if (error.code === 3) {
                    reject(
                        new Error(
                            'Waktu mendapatkan lokasi habis.'
                        )
                    );
                }
                else {
                    reject(
                        new Error(
                            'Lokasi tidak dapat ditemukan.'
                        )
                    );
                }
            },

            {
                enableHighAccuracy: true,
                timeout: 15000,
                maximumAge: 0
            }
        );
    });
}


// ==========================================================
// GAMBAR RUTE DI LEAFLET
// ==========================================================

function drawWargaRoute(geometry) {

    if (wargaRouteLine) {

        map.removeLayer(
            wargaRouteLine
        );
    }

    const coordinates = geometry && geometry.type === 'LineString'
        ? geometry.coordinates
        : geometry;

    if (!Array.isArray(coordinates) || coordinates.length === 0) {

        return;
    }

    wargaRouteGeometry = geometry;

    const leafletCoordinates = coordinates.map(function (coordinate) {
        return [Number(coordinate[1]), Number(coordinate[0])];
    });

    wargaRouteLine = L.polyline(
        leafletCoordinates,
        {
            color: '#059669',
            weight: 6,
            opacity: 0.85,
            lineJoin: 'round',
            lineCap: 'round'
        }
    ).addTo(map);

    map.fitBounds(
        wargaRouteLine.getBounds(),
        {
            padding: [30, 30]
        }
    );
}


// ==========================================================
// TAMPILKAN PILIHAN TUJUAN
// ==========================================================

function showRouteChoices(items) {

    routeChoices.innerHTML = '';

    routeChoices.classList.remove(
        'hidden'
    );

    items.forEach(function(item) {

        const button =
            document.createElement('button');

        button.type = 'button';

        button.className =
            'w-full text-left rounded-lg border border-slate-200 dark:border-white/10 bg-white dark:bg-[#0b1822] p-3 hover:border-emerald-500 transition';

        const nama = item.tujuan || item.nama || item.display_name || 'Tujuan';
        const alamat = item.alamat || item.display_name || nama;
        const jarak = item.jarak_pengguna ?? item.jarak;

        button.setAttribute('aria-label', `Pilih tujuan ${nama}`);
        button.innerHTML = `
            <p class="text-xs font-bold text-slate-900 dark:text-white">
                ${escapeHtml(nama)}
            </p>

            <p class="mt-1 text-[10px] text-slate-500 dark:text-slate-400">
                ${escapeHtml(alamat)}
            </p>

            ${
                jarak !== undefined && jarak !== null
                    ? `
                        <p class="mt-1 text-[10px] text-emerald-600">
                            ${formatRouteDistance(jarak)}
                        </p>
                    `
                    : ''
            }
        `;

        button.addEventListener(
            'click',
            function() {

                wargaSelectedDestination = {

                    latitude:
                        Number(item.latitude),

                    longitude:
                        Number(item.longitude),

                    nama:
                        nama,

                    alamat: alamat
                };

                routeChoices.classList.add(
                    'hidden'
                );

                buatRuteWarga();
            }
        );

        routeChoices.appendChild(
            button
        );
    });
}


// ==========================================================
// BUAT RUTE
// ==========================================================

async function buatRuteWarga() {
    if (!wargaSelectedDestination) return;

    setStatus('Sedang menghitung rute perjalanan...');

    try {
        await getWargaCurrentPosition();

        const response = await fetch('{{ route("navigasi.rute") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                latitude_awal: wargaCurrentPosition.latitude,
                longitude_awal: wargaCurrentPosition.longitude,
                latitude_tujuan: wargaSelectedDestination.latitude,
                longitude_tujuan: wargaSelectedDestination.longitude
            })
        });

        const data = await response.json();
        if (!response.ok || !data.success) {
            throw new Error(data.message || 'Rute tidak dapat dibuat.');
        }

        wargaRouteSteps = Array.isArray(data.steps) ? data.steps : [];
        drawWargaRoute(data.geometry);

        routeResult.classList.remove('hidden');
        routeName.textContent = wargaSelectedDestination.nama;
        routeAddress.textContent = wargaSelectedDestination.alamat || wargaSelectedDestination.nama;
        routeDistance.textContent = formatRouteDistance(data.distance);
        routeDuration.textContent = formatRouteDuration(data.duration);

        await cekHambatanWarga(data.geometry);
        setStatus('Rute berhasil ditampilkan pada peta.');
    } catch (error) {
        console.error('Gagal membuat rute:', error);
        setStatus(error.message || 'Gagal membuat rute.');
    }
}

async function cekHambatanWarga(geometry) {
    const response = await fetch('{{ route("navigasi.cek-hambatan") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ geometry: geometry })
    });

    const data = await response.json();
    if (!response.ok || !data.success) {
        throw new Error(data.message || 'Pengecekan hambatan gagal.');
    }

    wargaRouteHazards = Array.isArray(data.hambatan) ? data.hambatan : [];

    if (!wargaRouteHazards.length) {
        routeHazards.innerHTML = '<p role="status">Tidak ada hambatan yang terdeteksi di sepanjang rute.</p>';
        return;
    }

    routeHazards.innerHTML = `
        <p class="font-semibold text-amber-600">Hambatan sepanjang rute: ${wargaRouteHazards.length}</p>
        <ul class="mt-1 list-disc pl-5" aria-label="Daftar hambatan sepanjang rute">
            ${wargaRouteHazards.map(item => `<li>${escapeHtml(item.judul || 'Hambatan aksesibilitas')} (${escapeHtml(String(item.jarak_dari_rute ?? 0))} meter dari rute)</li>`).join('')}
        </ul>
    `;
}


// ==========================================================
// SUBMIT CARI RUTE
// ==========================================================

routeForm.addEventListener(
    'submit',
    async function(event) {

        event.preventDefault();

        const tujuan =
            routeDestination.value.trim();

        if (!tujuan) {

            setStatus(
                'Masukkan tujuan perjalanan terlebih dahulu.'
            );

            return;
        }


        routeButton.disabled = true;

        routeButton.innerHTML =
            `
                <i class="fa-solid fa-spinner fa-spin"></i>
                Mencari...
            `;


        try {

            // GPS
            await getWargaCurrentPosition();


            // Cari tujuan
            const response =
                await fetch(
                    '{{ route("navigasi.cari-tujuan") }}',
                    {
                        method: 'POST',

                        headers: {

                            'Content-Type':
                                'application/json',

                            'X-CSRF-TOKEN':
                                '{{ csrf_token() }}',

                            'Accept':
                                'application/json'
                        },

                        body: JSON.stringify({

                            tujuan:
                                tujuan,

                            latitude:
                                wargaCurrentPosition.latitude,

                            longitude:
                                wargaCurrentPosition.longitude
                        })
                    }
                );


            const data =
                await response.json();


            if (
                !response.ok ||
                !data.success
            ) {

                throw new Error(
                    data.message ||
                    'Tujuan tidak ditemukan.'
                );
            }


            const hasil = Array.isArray(data.hasil) ? data.hasil : [];

            if (data.tipe_pencarian === 'kategori') {
                if (!hasil.length) throw new Error('Tujuan tidak ditemukan.');
                showRouteChoices(hasil);
                setStatus('Pilih salah satu tujuan yang tersedia.');
            } else if (data.latitude !== undefined && data.longitude !== undefined) {
                wargaSelectedDestination = {
                    latitude: Number(data.latitude),
                    longitude: Number(data.longitude),
                    nama: data.tujuan || tujuan,
                    alamat: data.alamat || data.tujuan || tujuan
                };
                await buatRuteWarga();
            } else {
                throw new Error('Tujuan tidak ditemukan.');
            }

        }
        catch(error) {

            console.error(
                'Navigasi:',
                error
            );

            setStatus(
                error.message ||
                'Pencarian rute gagal.'
            );

        }
        finally {

            routeButton.disabled = false;

            routeButton.innerHTML =
                `
                    <i class="fa-solid fa-route"></i>
                    Cari Rute
                `;
        }
    }
);


// ==========================================================
// MULAI PERJALANAN
// ==========================================================

startTrip.addEventListener(
    'click',
    function() {

        if (
            !wargaSelectedDestination
        ) {

            setStatus(
                'Cari tujuan terlebih dahulu.'
            );

            return;
        }


        if (wargaWatchId !== null) return;

        wargaNextStepIndex = 0;
        wargaAnnouncedSteps.clear();
        setStatus('Navigasi aktif. Posisi pengguna sedang dipantau.');
        bacakanWarga('Rute dimulai. Tujuan Anda adalah ' + wargaSelectedDestination.nama + '.');

        wargaWatchId = navigator.geolocation.watchPosition(
            function (position) {
                wargaCurrentPosition = {
                    latitude: position.coords.latitude,
                    longitude: position.coords.longitude,
                    accuracy: position.coords.accuracy
                };

                if (userMarker) map.removeLayer(userMarker);
                if (userCircle) map.removeLayer(userCircle);

                userCircle = L.circle(
                    [wargaCurrentPosition.latitude, wargaCurrentPosition.longitude],
                    { radius: wargaCurrentPosition.accuracy, color: '#059669', fillColor: '#10b981', fillOpacity: .12, weight: 2 }
                ).addTo(map);
                userMarker = L.marker([
                    wargaCurrentPosition.latitude,
                    wargaCurrentPosition.longitude
                ]).addTo(map);

                prosesPanduanWarga();
                cekHambatanTerdekatWarga();
            },
            function () {
                setStatus('Posisi GPS tidak dapat diperbarui. Pastikan lokasi perangkat tetap aktif.');
            },
            { enableHighAccuracy: true, timeout: 10000, maximumAge: 3000 }
        );
    }
);

function bacakanWarga(teks) {
    if (!('speechSynthesis' in window)) return;
    window.speechSynthesis.cancel();
    const suara = new SpeechSynthesisUtterance(teks);
    suara.lang = 'id-ID';
    suara.rate = 1;
    window.speechSynthesis.speak(suara);
}

function prosesPanduanWarga() {
    const langkah = wargaRouteSteps[wargaNextStepIndex];
    if (!langkah || langkah.latitude === null || langkah.longitude === null) return;

    const jarak = hitungJarakWarga(
        wargaCurrentPosition.latitude,
        wargaCurrentPosition.longitude,
        Number(langkah.latitude),
        Number(langkah.longitude)
    );

    if (jarak > 80 || wargaAnnouncedSteps.has(wargaNextStepIndex)) return;

    const arah = langkah.modifier === 'left'
        ? 'belok kiri'
        : langkah.modifier === 'right'
            ? 'belok kanan'
            : 'lanjut mengikuti rute';

    bacakanWarga(`Dalam sekitar ${Math.max(1, Math.round(jarak))} meter, ${arah}.`);
    wargaAnnouncedSteps.add(wargaNextStepIndex);

    if (jarak <= 25) wargaNextStepIndex += 1;
}

function cekHambatanTerdekatWarga() {
    const hambatan = wargaRouteHazards.find(function (item) {
        if (!item.latitude || !item.longitude) return false;
        return hitungJarakWarga(
            wargaCurrentPosition.latitude,
            wargaCurrentPosition.longitude,
            Number(item.latitude),
            Number(item.longitude)
        ) <= 50;
    });

    if (hambatan) {
        bacakanWarga('Hati-hati, terdapat hambatan aksesibilitas di sekitar Anda.');
    }
}

function hitungJarakWarga(lat1, lon1, lat2, lon2) {
    const rad = value => value * Math.PI / 180;
    const a = Math.sin(rad(lat2 - lat1) / 2) ** 2
        + Math.cos(rad(lat1)) * Math.cos(rad(lat2)) * Math.sin(rad(lon2 - lon1) / 2) ** 2;
    return 6371000 * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
}
    window.addEventListener('resize', () => setTimeout(() => map.invalidateSize(), 150));
    setTimeout(() => map.invalidateSize(), 300);
});
</script>
@endpush
