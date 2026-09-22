@extends('layouts.app')

@section('title', 'Navigasi SmartPath')

@section('content')

<style>
    #hasil-hambatan,
    #kontrol-navigasi {
        margin-top: 20px;
    }

    #status-perjalanan {
        margin-top: 16px;
    }

    .hambatan-aman,
    .hambatan-ditemukan,
    .hambatan-pesan,
    .info-perjalanan {
        padding: 20px;
        border-radius: 12px;
        margin-top: 16px;
    }

    .hambatan-aman {
        background: #ecfdf5;
        border: 1px solid #a7f3d0;
    }

    .hambatan-ditemukan {
        background: #fff7ed;
        border: 1px solid #fed7aa;
    }

    .hambatan-pesan {
        background: #fef2f2;
        border: 1px solid #fecaca;
    }

    .info-perjalanan {
        background: #eff6ff;
        border: 1px solid #bfdbfe;
    }

    .hambatan-aman h3,
    .hambatan-ditemukan h3,
    .info-perjalanan h3 {
        margin: 0 0 8px;
    }

    .hambatan-card {
        background: white;
        padding: 16px;
        margin-top: 12px;
        border-radius: 10px;
        border: 1px solid #e5e7eb;
    }

    .hambatan-card h4 {
        margin-top: 0;
        margin-bottom: 12px;
    }

    .hambatan-card p {
        margin: 6px 0;
    }

    .btn-mulai-perjalanan,
    .btn-hentikan-navigasi {
        width: 100%;
        padding: 12px 18px;
        font-size: 16px;
        font-weight: 600;
    }

    .status-gps {
        font-size: 14px;
        margin-top: 10px;
    }

    #peta-navigasi {
        height: min(58vh, 520px);
        min-height: 320px;
        border-radius: 14px;
        overflow: hidden;
        background: #e5e7eb;
    }

    .leaflet-control-attribution {
        font-size: 10px;
    }
</style>

<div class="container py-4">

    {{-- HEADER --}}
    <div class="mb-4">
        <a href="{{ url()->previous() }}" class="text-decoration-none">
            ← Kembali
        </a>

        <h1 class="mt-3 mb-2">Navigasi SmartPath</h1>

        <p class="text-muted">
            Tentukan tujuan perjalanan untuk mendapatkan informasi aksesibilitas di sepanjang perjalanan.
        </p>
    </div>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-2">
            <div id="peta-navigasi" aria-label="Peta navigasi SmartPath"></div>
        </div>
    </div>

    {{-- FORM TUJUAN --}}
    <div class="card shadow-sm border-0">
        <div class="card-body p-4">

            <h2 class="h5 mb-3">Lokasi Tujuan</h2>

            <label for="tujuan" class="form-label">Masukkan tujuan</label>

            <input
                type="text"
                id="tujuan"
                class="form-control"
                placeholder="Contoh: Stasiun Depok"
                aria-describedby="tujuan-help"
            >

            <small id="tujuan-help" class="text-muted">
                Masukkan nama tempat atau alamat tujuan kamu.
            </small>

            <button type="button" id="btn-mulai-navigasi" class="btn btn-primary mt-4">
                📍 Cari Rute
            </button>

            <div id="status-navigasi" class="mt-3" role="status" aria-live="polite"></div>

        </div>
    </div>

    {{-- HASIL RUTE / KONTROL NAVIGASI --}}
    <div id="kontrol-navigasi"></div>

    {{-- STATUS PERJALANAN --}}
    <div id="status-perjalanan"></div>

    {{-- HASIL HAMBATAN --}}
    <div id="hasil-hambatan"></div>

</div>

@endsection


@push('scripts')

<script>
document.addEventListener('DOMContentLoaded', function () {

    // ==========================================================
    // ELEMEN HTML
    // ==========================================================
    const btnNavigasi = document.getElementById('btn-mulai-navigasi');
    const tujuanInput = document.getElementById('tujuan');
    const statusNavigasi = document.getElementById('status-navigasi');
    const kontrolNavigasi = document.getElementById('kontrol-navigasi');
    const statusPerjalanan = document.getElementById('status-perjalanan');
    const hasilHambatan = document.getElementById('hasil-hambatan');

    // ==========================================================
    // STATE NAVIGASI
    // ==========================================================
    let watchId = null;
    let navigasiAktif = false;
    let posisiSekarang = null;
    let posisiAwal = null;
    let tujuanNavigasi = null;
    let geometryNavigasi = null;
    let hambatanNavigasi = [];
    let hambatanSudahDiumumkan = new Set();
    let langkahNavigasi = [];
    let langkahBerikutnyaIndex = 0;
    let langkahSudahDiumumkan = new Set();
    let petaNavigasi = null;
    let markerPengguna = null;
    let markerTujuan = null;
    let garisRute = null;

    // KONSTANTA BATAS JARAK
    const BATAS_PERINGATAN_HAMBATAN = 50; // Jarak peringatan hambatan (meter)
    const BATAS_TUJUAN = 20;              // Jarak sampai tujuan (meter)
    const BATAS_PRA_ARAHAN = 80;          // Mulai memberi peringatan pra-arahan (meter)
    const BATAS_ARAHAN = 50;              // Jarak eksekusi instruksi suara (meter)

    if (typeof L !== 'undefined') {
        petaNavigasi = L.map('peta-navigasi', { zoomControl: true })
            .setView([-6.4025, 106.8166], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(petaNavigasi);
    }

    // ==========================================================
    // EVENT LISTENER TOMBOL CARI RUTE
    // ==========================================================
    btnNavigasi.addEventListener('click', function () {

        const tujuan = tujuanInput.value.trim();

        if (tujuan === '') {
            statusNavigasi.innerHTML = `
                <div class="alert alert-warning">
                    Silakan masukkan tujuan terlebih dahulu.
                </div>
            `;
            tujuanInput.focus();
            return;
        }

        if (!navigator.geolocation) {
            statusNavigasi.innerHTML = `
                <div class="alert alert-danger">
                    Perangkat atau browser kamu tidak mendukung GPS.
                </div>
            `;
            return;
        }

        // Reset data navigasi sebelumnya
        hentikanPemantauanGPS();
        navigasiAktif = false;
        posisiSekarang = null;
        posisiAwal = null;
        tujuanNavigasi = null;
        geometryNavigasi = null;
        hambatanNavigasi = [];
        hambatanSudahDiumumkan.clear();
        langkahSudahDiumumkan.clear();
        langkahBerikutnyaIndex = 0;

        kontrolNavigasi.innerHTML = '';
        statusPerjalanan.innerHTML = '';
        hasilHambatan.innerHTML = '';

        // Loading state
        btnNavigasi.disabled = true;
        btnNavigasi.innerHTML = '📍 Mendeteksi lokasi...';
        statusNavigasi.innerHTML = `
            <div class="alert alert-info">
                Sedang mendeteksi lokasi kamu...
            </div>
        `;

        // Ambil GPS Awal
        navigator.geolocation.getCurrentPosition(
            function (position) {
                const latitude = position.coords.latitude;
                const longitude = position.coords.longitude;
                const accuracy = position.coords.accuracy;

                posisiAwal = { latitude, longitude };
                posisiSekarang = { latitude, longitude, accuracy };
                perbaruiMarkerPengguna(latitude, longitude, accuracy);

                console.log('GPS AWAL:', latitude, longitude, 'Akurasi:', accuracy);

                statusNavigasi.innerHTML = `
                    <div class="alert alert-info">
                        📍 Lokasi kamu berhasil dideteksi.<br>
                        Sedang mencari koordinat tujuan...
                    </div>
                `;

                // Cari Lokasi Tujuan via Server
                fetch('{{ route('navigasi.cari-tujuan') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        tujuan: tujuan,
                        latitude: latitude,
                        longitude: longitude
                    })
                })
                .then(async response => {
                    const data = await response.json();
                    console.log('RESPONSE PENCARIAN TUJUAN:', data);

                    if (!response.ok) {
                        throw new Error(data.message || 'Pencarian tujuan gagal.');
                    }
                    return data;
                })
                .then(data => {
                    if (!data.success) {
                        throw new Error(data.message || 'Tujuan tidak ditemukan.');
                    }

                    // Jika hasil pencarian berupa daftar pilihan lokasi (kategori)
                    if (data.tipe_pencarian === 'kategori' && Array.isArray(data.hasil)) {
                        console.log('PILIHAN TUJUAN:', data.hasil);
                        tampilkanPilihanTujuan(data.hasil);

                        statusNavigasi.innerHTML = `
                            <div class="alert alert-info">
                                <strong>📍 Pilih tujuan</strong><br>
                                Ditemukan ${data.hasil.length} pilihan di sekitar lokasi kamu. Silakan pilih salah satu tujuan.
                            </div>
                        `;
                        return null;
                    }

                    // Jika tujuan spesifik
                    tujuanNavigasi = {
                        nama: data.tujuan,
                        latitude: parseFloat(data.latitude),
                        longitude: parseFloat(data.longitude)
                    };

                    console.log('TUJUAN DITEMUKAN:', data);
                    console.log('JARAK TUJUAN DARI PENGGUNA:', data.jarak_pengguna, 'meter');

                    return cariRuteKeTujuan(latitude, longitude, data.latitude, data.longitude);
                })
                .then(rute => {
                    if (!rute) return; // Jika memilih dari kategori, rute diproses setelah diklik
                    prosesHasilRute(rute, latitude, longitude, accuracy);
                })
                .catch(error => {
                    console.error('Navigasi Error:', error);
                    statusNavigasi.innerHTML = `
                        <div class="alert alert-danger">
                            ${escapeHtml(error.message)}
                        </div>
                    `;
                })
                .finally(() => {
                    btnNavigasi.disabled = false;
                    btnNavigasi.innerHTML = '📍 Cari Rute';
                });
            },
            function (error) {
                console.error('GPS Error:', error);
                let pesan = '';
                switch (error.code) {
                    case error.PERMISSION_DENIED:
                        pesan = 'Izin lokasi ditolak. Silakan izinkan akses lokasi pada browser.';
                        break;
                    case error.POSITION_UNAVAILABLE:
                        pesan = 'Lokasi tidak tersedia. Pastikan GPS/lokasi perangkat aktif.';
                        break;
                    case error.TIMEOUT:
                        pesan = 'Waktu untuk mendapatkan lokasi habis. Silakan coba lagi.';
                        break;
                    default:
                        pesan = 'Terjadi kesalahan saat mendapatkan lokasi.';
                }

                statusNavigasi.innerHTML = `
                    <div class="alert alert-danger">${escapeHtml(pesan)}</div>
                `;
                btnNavigasi.disabled = false;
                btnNavigasi.innerHTML = '📍 Cari Rute';
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            }
        );
    });

    // ==========================================================
    // FUNGSI PENCARIAN & PROSES RUTE
    // ==========================================================
    function tampilkanPilihanTujuan(hasil) {
        kontrolNavigasi.innerHTML = `
            <div class="card shadow-sm border-0 mt-4">
                <div class="card-body p-4">
                    <h2 class="h5 mb-3">📍 Pilih Tujuan</h2>
                    <p class="text-muted">Pilih salah satu tujuan yang berada di sekitar lokasi kamu.</p>
                    <div id="daftar-pilihan-tujuan"></div>
                </div>
            </div>
        `;

        const daftar = document.getElementById('daftar-pilihan-tujuan');

        hasil.forEach(function (item) {
            const jarak = formatJarak(item.jarak_pengguna);
            const tombol = document.createElement('button');

            tombol.type = 'button';
            tombol.className = 'btn btn-outline-primary w-100 text-start mb-3 p-3';
            tombol.innerHTML = `
                <strong>${escapeHtml(item.tujuan)}</strong><br>
                <small class="text-muted">📍 ${escapeHtml(jarak)}</small>
            `;

            tombol.addEventListener('click', function () {
                console.log('TUJUAN DIPILIH:', item);

                tujuanNavigasi = {
                    nama: item.tujuan,
                    latitude: parseFloat(item.latitude),
                    longitude: parseFloat(item.longitude)
                };

                statusNavigasi.innerHTML = `
                    <div class="alert alert-info">
                        📍 Tujuan dipilih.<br>
                        <strong>${escapeHtml(item.tujuan)}</strong><br>
                        Sedang mencari rute perjalanan...
                    </div>
                `;

                kontrolNavigasi.innerHTML = '';

                cariRuteKeTujuan(
                    posisiAwal.latitude,
                    posisiAwal.longitude,
                    item.latitude,
                    item.longitude
                )
                .then(function (rute) {
                    if (rute) {
                        prosesHasilRute(
                            rute,
                            posisiAwal.latitude,
                            posisiAwal.longitude,
                            posisiSekarang ? posisiSekarang.accuracy : 0
                        );
                    }
                })
                .catch(function (error) {
                    console.error('Error rute:', error);
                    statusNavigasi.innerHTML = `
                        <div class="alert alert-danger">
                            ${escapeHtml(error.message)}
                        </div>
                    `;
                });
            });

            daftar.appendChild(tombol);
        });
    }

    function cariRuteKeTujuan(latitudeAwal, longitudeAwal, latitudeTujuan, longitudeTujuan) {
        const dataRute = {
            latitude_awal: latitudeAwal,
            longitude_awal: longitudeAwal,
            latitude_tujuan: latitudeTujuan,
            longitude_tujuan: longitudeTujuan
        };

        console.log('DATA YANG DIKIRIM KE RUTE:', dataRute);

        return fetch('{{ route('navigasi.rute') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify(dataRute)
        })
        .then(async response => {
            const hasilRute = await response.json();
            console.log('RESPONSE RUTE:', hasilRute);

            if (!response.ok) {
                if (hasilRute.errors) {
                    const pesanValidasi = Object.values(hasilRute.errors).flat().join('<br>');
                    throw new Error(pesanValidasi);
                }
                throw new Error(hasilRute.message || 'Rute tidak dapat ditemukan.');
            }

            if (!hasilRute.success) {
                throw new Error(hasilRute.message || 'Rute tidak ditemukan.');
            }

            return hasilRute;
        });
    }

    function prosesHasilRute(rute, latitude, longitude, accuracy) {
        geometryNavigasi = rute.geometry;
        window.ruteNavigasi = rute.geometry;
        langkahNavigasi = rute.steps || [];
        langkahBerikutnyaIndex = 0;
        tampilkanRuteDiPeta(rute.geometry, latitude, longitude);

        const jarakKm = (rute.distance / 1000).toFixed(2);
        const durasiMenit = Math.ceil(rute.duration / 60);

        console.log('RUTE BERHASIL:', rute);
        console.log('JARAK:', jarakKm, 'km');
        console.log('DURASI:', durasiMenit, 'menit');

        statusNavigasi.innerHTML = `
            <div class="alert alert-success">
                <strong>🛣️ Rute berhasil ditemukan.</strong>
                <div class="mt-2">
                    <div><strong>Tujuan:</strong> ${escapeHtml(dataTujuan())}</div>
                    <div><strong>Jarak:</strong> ${jarakKm} km</div>
                    <div><strong>Perkiraan waktu:</strong> ${durasiMenit} menit</div>
                    <div><strong>Lokasi awal:</strong> ${latitude}, ${longitude}</div>
                    <div><strong>Lokasi tujuan:</strong> ${tujuanNavigasi.latitude}, ${tujuanNavigasi.longitude}</div>
                    <div><strong>Akurasi GPS:</strong> ±${Math.round(accuracy)} meter</div>
                </div>
            </div>
        `;

        cekHambatanSepanjangRute(rute.geometry);
        tampilkanTombolMulai(jarakKm, durasiMenit);
    }

    // ==========================================================
    // KONTROL MULAI NAVIGASI
    // ==========================================================
    function tampilkanTombolMulai(jarakKm, durasiMenit) {
        kontrolNavigasi.innerHTML = `
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h2 class="h5 mb-3">🧭 Rute Siap Digunakan</h2>
                    <p class="text-muted">
                        Rute menuju tujuan sudah ditemukan. Tekan tombol berikut untuk mulai memantau posisi kamu selama perjalanan.
                    </p>
                    <div class="mb-3">
                        <div><strong>Jarak:</strong> ${jarakKm} km</div>
                        <div><strong>Perkiraan waktu:</strong> ${durasiMenit} menit</div>
                    </div>
                    <button type="button" id="btn-mulai-perjalanan" class="btn btn-success btn-mulai-perjalanan">
                        ▶ Mulai Perjalanan
                    </button>
                </div>
            </div>
        `;

        document.getElementById('btn-mulai-perjalanan').addEventListener('click', mulaiNavigasiAktif);
    }

    function mulaiNavigasiAktif() {
        if (!tujuanNavigasi) {
            tampilkanPesanPerjalanan('Tujuan belum tersedia.', 'error');
            return;
        }
        if (!geometryNavigasi) {
            tampilkanPesanPerjalanan('Data rute belum tersedia.', 'error');
            return;
        }
        if (!navigator.geolocation) {
            tampilkanPesanPerjalanan('GPS tidak tersedia pada perangkat ini.', 'error');
            return;
        }

        hentikanPemantauanGPS();

        navigasiAktif = true;
        hambatanSudahDiumumkan.clear();
        langkahSudahDiumumkan.clear();

        // Lewati step "depart" jika pertama kali
        langkahBerikutnyaIndex = 0;
        if (langkahNavigasi.length > 0 && langkahNavigasi[0].type === 'depart') {
            langkahBerikutnyaIndex = 1;
        }

        kontrolNavigasi.innerHTML = `
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h2 class="h5 mb-3">🧭 Navigasi Sedang Berjalan</h2>
                    <div class="alert alert-info">
                        <strong>Tujuan:</strong> ${escapeHtml(tujuanNavigasi.nama)}
                    </div>
                    <div id="info-posisi">📍 Menunggu pembaruan posisi GPS...</div>
                    <div class="mt-3">
                        <button type="button" id="btn-hentikan-navigasi" class="btn btn-danger btn-hentikan-navigasi">
                            ⏹ Hentikan Navigasi
                        </button>
                    </div>
                </div>
            </div>
        `;

        document.getElementById('btn-hentikan-navigasi').addEventListener('click', hentikanNavigasi);

        bacakanTeks('Navigasi dimulai. Tujuan kamu adalah ' + tujuanNavigasi.nama);
        mulaiPemantauanGPS();
    }

    // ==========================================================
    // PEMANTAUAN GPS (WATCH POSITION)
    // ==========================================================
    function mulaiPemantauanGPS() {
        watchId = navigator.geolocation.watchPosition(
            function (position) {
                if (!navigasiAktif) return;

                const latitude = position.coords.latitude;
                const longitude = position.coords.longitude;
                const accuracy = position.coords.accuracy;

                posisiSekarang = { latitude, longitude, accuracy };
                perbaruiMarkerPengguna(latitude, longitude, accuracy);

                updateInfoPosisi();

                const jarakKeTujuan = hitungJarakMeter(
                    latitude,
                    longitude,
                    tujuanNavigasi.latitude,
                    tujuanNavigasi.longitude
                );

                if (jarakKeTujuan <= BATAS_TUJUAN) {
                    sampaiTujuan(jarakKeTujuan);
                    return;
                }

                cekHambatanTerdekat(latitude, longitude);
                prosesVoiceGuidance(latitude, longitude);
            },
            function (error) {
                console.error('Watch GPS Error:', error);
                tampilkanPesanPerjalanan('Posisi GPS tidak dapat diperbarui. Pastikan lokasi perangkat tetap aktif.', 'error');
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 3000
            }
        );
    }

    function perbaruiMarkerPengguna(latitude, longitude, accuracy = 0) {
        if (!petaNavigasi) return;

        const posisi = [latitude, longitude];
        const popup = `Posisi kamu<br>Akurasi GPS: ±${Math.round(accuracy)} meter`;

        if (!markerPengguna) {
            markerPengguna = L.circleMarker(posisi, {
                radius: 9,
                color: '#ffffff',
                weight: 3,
                fillColor: '#2563eb',
                fillOpacity: 1
            }).addTo(petaNavigasi);
        } else {
            markerPengguna.setLatLng(posisi);
        }

        markerPengguna.bindPopup(popup);

        if (navigasiAktif) {
            petaNavigasi.panTo(posisi, { animate: true, duration: 0.5 });
        } else {
            petaNavigasi.setView(posisi, 15);
        }
    }

    function tampilkanRuteDiPeta(geometry, latitudeAwal, longitudeAwal) {
        if (!petaNavigasi || !geometry || !Array.isArray(geometry.coordinates)) return;

        const koordinat = geometry.coordinates.map(function (item) {
            return [item[1], item[0]];
        });

        if (garisRute) petaNavigasi.removeLayer(garisRute);
        garisRute = L.polyline(koordinat, {
            color: '#2563eb',
            weight: 7,
            opacity: 0.85,
            lineCap: 'round',
            lineJoin: 'round'
        }).addTo(petaNavigasi);

        if (markerTujuan) petaNavigasi.removeLayer(markerTujuan);
        markerTujuan = L.marker([
            tujuanNavigasi.latitude,
            tujuanNavigasi.longitude
        ]).addTo(petaNavigasi).bindPopup(
            `<strong>Tujuan</strong><br>${escapeHtml(tujuanNavigasi.nama)}`
        );

        perbaruiMarkerPengguna(
            latitudeAwal,
            longitudeAwal,
            posisiSekarang ? posisiSekarang.accuracy : 0
        );
        petaNavigasi.fitBounds(garisRute.getBounds(), { padding: [32, 32] });
    }

    function updateInfoPosisi() {
        const infoPosisi = document.getElementById('info-posisi');
        if (!infoPosisi || !posisiSekarang) return;

        const jarakKeTujuan = hitungJarakMeter(
            posisiSekarang.latitude,
            posisiSekarang.longitude,
            tujuanNavigasi.latitude,
            tujuanNavigasi.longitude
        );

        const jarakText = jarakKeTujuan >= 1000
            ? (jarakKeTujuan / 1000).toFixed(2) + ' km'
            : Math.round(jarakKeTujuan) + ' meter';

        infoPosisi.innerHTML = `
            <div class="info-perjalanan">
                <h3>📍 Posisi Saat Ini</h3>
                <p><strong>Latitude:</strong> ${posisiSekarang.latitude}</p>
                <p><strong>Longitude:</strong> ${posisiSekarang.longitude}</p>
                <p><strong>Akurasi GPS:</strong> ±${Math.round(posisiSekarang.accuracy)} meter</p>
                <p><strong>Jarak ke tujuan:</strong> ${jarakText}</p>
                <p class="status-gps">🔄 Posisi GPS sedang diperbarui.</p>
            </div>
        `;
    }

    // ==========================================================
    // PENGECEKAN HAMBATAN
    // ==========================================================
    function cekHambatanSepanjangRute(geometry) {
        fetch("{{ route('navigasi.cek-hambatan') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ geometry })
        })
        .then(response => {
            if (!response.ok) {
                return response.text().then(text => {
                    console.error('Response error:', text);
                    throw new Error('Gagal mengecek hambatan.');
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                hambatanNavigasi = data.hambatan || [];
                tampilkanHambatan(hambatanNavigasi);
            } else {
                tampilkanPesanHambatan(data.message || 'Pengecekan hambatan gagal.', 'error');
            }
        })
        .catch(error => {
            console.error('Hambatan Error:', error);
            tampilkanPesanHambatan('Terjadi kesalahan saat mengecek hambatan.', 'error');
        });
    }

    function cekHambatanTerdekat(latitude, longitude) {
        if (!hambatanNavigasi || hambatanNavigasi.length === 0) return;

        hambatanNavigasi.forEach(function (hambatan) {
            if (!hambatan.latitude || !hambatan.longitude) return;

            const jarak = hitungJarakMeter(
                latitude,
                longitude,
                parseFloat(hambatan.latitude),
                parseFloat(hambatan.longitude)
            );

            if (jarak <= BATAS_PERINGATAN_HAMBATAN) {
                beriPeringatanHambatan(hambatan, jarak);
            }
        });
    }

    function beriPeringatanHambatan(hambatan, jarak) {
        const id = String(hambatan.id);

        if (hambatanSudahDiumumkan.has(id)) return;
        hambatanSudahDiumumkan.add(id);

        let jarakText = jarak < 1 ? 'kurang dari satu meter'
            : (jarak >= 1000 ? (jarak / 1000).toFixed(1) + ' kilometer' : Math.round(jarak) + ' meter');

        const namaHambatan = hambatan.judul || 'hambatan aksesibilitas';
        const pesan = `Peringatan. Terdapat ${namaHambatan} sekitar ${jarakText} dari posisi kamu.`;

        tampilkanPeringatanAktif(pesan);
        bacakanTeks(pesan);
    }

    function tampilkanPeringatanAktif(pesan) {
        statusPerjalanan.innerHTML = `
            <div class="alert alert-warning" role="alert" aria-live="assertive">
                <strong>⚠️ Peringatan Hambatan</strong>
                <div class="mt-2">${escapeHtml(pesan)}</div>
            </div>
        `;
    }

    function tampilkanHambatan(hambatan) {
        if (!hambatan || hambatan.length === 0) {
            const pesan = 'Tidak ditemukan laporan hambatan aksesibilitas terverifikasi di sepanjang rute yang diperiksa.';
            hasilHambatan.innerHTML = `
                <div class="hambatan-aman" role="status" aria-live="polite">
                    <h3>Tidak ditemukan hambatan</h3>
                    <p>${escapeHtml(pesan)}</p>
                </div>
            `;

            if (!navigasiAktif) bacakanTeks(pesan);
            return;
        }

        let html = `
            <div class="hambatan-ditemukan" role="alert" aria-live="assertive">
                <h3>⚠️ Hambatan ditemukan: ${hambatan.length}</h3>
        `;

        hambatan.forEach(function (item, index) {
            html += `
                <article class="hambatan-card">
                    <h4>Hambatan ${index + 1}: ${escapeHtml(item.judul)}</h4>
                    <p><strong>Kategori:</strong> ${escapeHtml(item.kategori || 'Tidak diketahui')}</p>
                    <p><strong>Jarak dari rute:</strong> ${item.jarak_dari_rute} meter</p>
                    <p><strong>Prioritas:</strong> ${escapeHtml(item.tingkat_prioritas || 'Belum dinilai')}</p>
                    <p><strong>Lokasi:</strong> ${escapeHtml(item.alamat || 'Alamat tidak tersedia')}</p>
                </article>
            `;
        });

        html += `</div>`;
        hasilHambatan.innerHTML = html;

        if (!navigasiAktif) {
            let teksSuara = `Ditemukan ${hambatan.length} hambatan aksesibilitas sepanjang rute. `;
            hambatan.forEach(function (item, index) {
                teksSuara += `Hambatan ${index + 1}: ${item.judul || 'Tidak diketahui'}. Jarak dari rute ${item.jarak_dari_rute} meter. `;
            });
            bacakanTeks(teksSuara);
        }
    }

    function tampilkanPesanHambatan(pesan, tipe = 'info') {
        hasilHambatan.innerHTML = `
            <div class="hambatan-pesan" role="alert" aria-live="assertive">
                ${escapeHtml(pesan)}
            </div>
        `;
    }

    function tampilkanPesanPerjalanan(pesan, tipe = 'info') {
        const classAlert = tipe === 'error' ? 'alert alert-danger' : 'alert alert-info';
        statusPerjalanan.innerHTML = `
            <div class="${classAlert}" role="alert" aria-live="polite">
                ${escapeHtml(pesan)}
            </div>
        `;
    }

    function sampaiTujuan(jarak) {
        if (!navigasiAktif) return;

        navigasiAktif = false;
        hentikanPemantauanGPS();

        const pesan = 'Anda telah sampai di tujuan ' + tujuanNavigasi.nama;

        statusPerjalanan.innerHTML = `
            <div class="alert alert-success" role="status" aria-live="assertive">
                <strong>🏁 Tujuan Tercapai</strong>
                <div class="mt-2">${escapeHtml(tujuanNavigasi.nama)}</div>
                <div class="mt-2">Anda telah sampai di tujuan.</div>
            </div>
        `;

        bacakanTeks(pesan);
    }

    // ==========================================================
    // VOICE GUIDANCE
    // ==========================================================
    function prosesVoiceGuidance(latitude, longitude) {
        if (!navigasiAktif) return;

        if (!langkahNavigasi.length) {
            console.log('Tidak ada langkah navigasi.');
            return;
        }

        const langkah = langkahNavigasi[langkahBerikutnyaIndex];

        if (!langkah) {
            console.log('Semua langkah navigasi sudah selesai.');
            return;
        }

        if (langkah.latitude === null || langkah.longitude === null) {
            console.log('Koordinat langkah tidak tersedia:', langkah);
            langkahBerikutnyaIndex++;
            return;
        }

        const jarak = hitungJarakMeter(
            latitude,
            longitude,
            langkah.latitude,
            langkah.longitude
        );

        console.log(
            'LANGKAH BERIKUTNYA:',
            langkahBerikutnyaIndex,
            '|',
            langkah.type,
            '|',
            langkah.modifier,
            '|',
            langkah.name,
            '| Jarak:',
            jarak.toFixed(1),
            'meter'
        );

        // 1. PERINGATAN AWAL (PRA-ARAHAN)
        if (
            jarak <= BATAS_PRA_ARAHAN &&
            jarak > BATAS_ARAHAN &&
            !langkahSudahDiumumkan.has(langkahBerikutnyaIndex)
        ) {
            const instruksiAwal = buatInstruksiSuara(langkah, 'awal');
            if (instruksiAwal) {
                console.log('VOICE PRA-ARAHAN:', instruksiAwal);
                bacakanTeks(instruksiAwal);
                langkahSudahDiumumkan.add(langkahBerikutnyaIndex);
            }
        }

        // 2. EKSEKUSI ARAHAN
        if (jarak <= BATAS_ARAHAN) {
            const instruksi = buatInstruksiSuara(langkah, 'aksi');
            if (instruksi) {
                console.log('VOICE GUIDANCE:', instruksi);
                bacakanTeks(instruksi);
            }

            langkahBerikutnyaIndex++;
            console.log('PINDAH KE LANGKAH:', langkahBerikutnyaIndex);

            // Informasi jalan berikutnya jika ada perubahan nama jalan
            const langkahBerikutnya = langkahNavigasi[langkahBerikutnyaIndex];
            if (
                langkahBerikutnya &&
                langkahBerikutnya.type === 'new name' &&
                langkahBerikutnya.name
            ) {
                const pesanJalan = `Lanjut di ${langkahBerikutnya.name}.`;
                console.log('VOICE JALAN:', pesanJalan);
                bacakanTeks(pesanJalan);
            }
        }
    }

    function buatInstruksiSuara(langkah, mode = 'aksi') {
        const type = langkah.type;
        const modifier = langkah.modifier;
        const namaJalan = langkah.name;

        if (type === 'depart') {
            return namaJalan ? `Mulai perjalanan melalui ${namaJalan}.` : 'Mulai perjalanan.';
        }

        if (type === 'arrive') {
            return 'Anda sudah mendekati tujuan.';
        }

        if (type === 'turn' || type === 'continue' || type === 'end of road') {
            if (modifier === 'left') {
                return mode === 'awal' ? 'Dalam 80 meter, belok kiri.' : (namaJalan ? `Belok kiri menuju ${namaJalan}.` : 'Belok kiri.');
            }
            if (modifier === 'right') {
                return mode === 'awal' ? 'Dalam 80 meter, belok kanan.' : (namaJalan ? `Belok kanan menuju ${namaJalan}.` : 'Belok kanan.');
            }
            if (modifier === 'slight left') {
                return mode === 'awal' ? 'Dalam 80 meter, ambil arah sedikit ke kiri.' : 'Ambil arah sedikit ke kiri.';
            }
            if (modifier === 'slight right') {
                return mode === 'awal' ? 'Dalam 80 meter, ambil arah sedikit ke kanan.' : 'Ambil arah sedikit ke kanan.';
            }
            if (modifier === 'sharp left') {
                return mode === 'awal' ? 'Dalam 80 meter, belok tajam ke kiri.' : 'Belok tajam ke kiri.';
            }
            if (modifier === 'sharp right') {
                return mode === 'awal' ? 'Dalam 80 meter, belok tajam ke kanan.' : 'Belok tajam ke kanan.';
            }
            return namaJalan ? `Lanjut melalui ${namaJalan}.` : 'Lanjut mengikuti rute.';
        }

        if (type === 'roundabout') {
            return mode === 'awal' ? 'Dalam 80 meter, masuk ke bundaran dan ikuti rute.' : 'Masuk bundaran dan ikuti rute.';
        }

        if (type === 'merge') {
            return mode === 'awal' ? 'Dalam 80 meter, bergabung ke jalur berikutnya.' : 'Bergabung ke jalur berikutnya.';
        }

        if (type === 'fork') {
            return mode === 'awal' ? 'Dalam 80 meter, ambil percabangan sesuai rute.' : 'Ambil percabangan sesuai rute.';
        }

        if (type === 'on ramp') {
            return mode === 'awal' ? 'Dalam 80 meter, masuk ke jalur berikutnya.' : 'Masuk ke jalur berikutnya.';
        }

        if (type === 'off ramp') {
            return mode === 'awal' ? 'Dalam 80 meter, keluar dari jalur sesuai rute.' : 'Keluar dari jalur sesuai rute.';
        }

        return null;
    }

    // ==========================================================
    // NAVIGASI DILANJUTKAN / DIHENTIKAN
    // ==========================================================
    function hentikanNavigasi() {
        navigasiAktif = false;
        hentikanPemantauanGPS();

        statusPerjalanan.innerHTML = `
            <div class="alert alert-secondary" role="status" aria-live="polite">
                ⏹ Navigasi dihentikan.
            </div>
        `;

        bacakanTeks('Navigasi dihentikan.');

        kontrolNavigasi.innerHTML = `
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h2 class="h5">Navigasi dihentikan</h2>
                    <p class="text-muted">
                        Kamu dapat mencari rute baru dengan memasukkan tujuan lain.
                    </p>
                </div>
            </div>
        `;
    }

    function hentikanPemantauanGPS() {
        if (watchId !== null) {
            navigator.geolocation.clearWatch(watchId);
            watchId = null;
        }
    }

    // ==========================================================
    // FUNGSI UTILITAS
    // ==========================================================
    function hitungJarakMeter(lat1, lon1, lat2, lon2) {
        const earthRadius = 6371000;
        const lat1Rad = degToRad(lat1);
        const lat2Rad = degToRad(lat2);
        const deltaLat = degToRad(lat2 - lat1);
        const deltaLon = degToRad(lon2 - lon1);

        const a = Math.sin(deltaLat / 2) * Math.sin(deltaLat / 2) +
                  Math.cos(lat1Rad) * Math.cos(lat2Rad) *
                  Math.sin(deltaLon / 2) * Math.sin(deltaLon / 2);

        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        return earthRadius * c;
    }

    function degToRad(degree) {
        return (degree * Math.PI) / 180;
    }

    function formatJarak(jarak) {
        if (jarak === null || jarak === undefined) {
            return 'Jarak tidak diketahui';
        }

        jarak = parseFloat(jarak);

        if (isNaN(jarak)) {
            return 'Jarak tidak diketahui';
        }

        if (jarak >= 1000) {
            return (jarak / 1000).toFixed(2) + ' km dari lokasi kamu';
        }

        return Math.round(jarak) + ' meter dari lokasi kamu';
    }

    function dataTujuan() {
        return (tujuanNavigasi && tujuanNavigasi.nama) ? tujuanNavigasi.nama : 'Tujuan tidak diketahui';
    }

    function escapeHtml(text) {
        if (text === null || text === undefined) return '';
        const div = document.createElement('div');
        div.textContent = String(text);
        return div.innerHTML;
    }

    function bacakanTeks(teks) {
        if (!('speechSynthesis' in window)) {
            console.warn('Browser tidak mendukung Text-to-Speech.');
            return;
        }

        window.speechSynthesis.cancel();
        const suara = new SpeechSynthesisUtterance(teks);
        suara.lang = 'id-ID';
        suara.rate = 0.9;
        suara.pitch = 1;
        suara.volume = 1;

        window.speechSynthesis.speak(suara);
    }

});
</script>

@endpush