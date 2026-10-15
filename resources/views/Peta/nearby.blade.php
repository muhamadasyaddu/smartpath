@extends('layouts.app')

@section('title', 'Nearby Obstacles - SmartPath')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/smartpath-warga.css') }}">
@endpush

@section('content')
<div class="warga-shell">
    @include('partials.sidebar-warga')

    <div class="warga-workspace">
        @include('partials.nav-public')
        @include('partials.bar-mobile-warga')

<style>
    .nearby-page {
        min-height: calc(100vh - 64px);
        background: #f8fafc;
        padding: 32px 24px 50px;
    }

    .nearby-container {
        max-width: 1100px;
        margin: 0 auto;
    }

    .nearby-header {
        margin-bottom: 28px;
    }

    .nearby-back {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: #059669;
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 18px;
    }

    .nearby-back:hover {
        color: #047857;
    }

    .nearby-title {
        font-size: 30px;
        font-weight: 700;
        color: #062a25;
        margin: 0;
    }

    .nearby-description {
        margin-top: 8px;
        color: #64748b;
        font-size: 15px;
        line-height: 1.7;
        max-width: 720px;
    }

    .location-card {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        padding: 22px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 28px;
        box-shadow: 0 4px 15px rgba(15, 23, 42, 0.04);
    }

    .location-info {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .location-icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        background: #d1fae5;
        color: #059669;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }

    .location-text h2 {
        margin: 0 0 4px;
        color: #0f172a;
        font-size: 16px;
        font-weight: 700;
    }

    .location-text p {
        margin: 0;
        color: #64748b;
        font-size: 13px;
    }

    .location-button {
        border: none;
        background: #059669;
        color: white;
        padding: 11px 18px;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: 0.2s ease;
    }

    .location-button:hover {
        background: #047857;
    }

    .location-button:disabled {
        opacity: 0.7;
        cursor: wait;
    }

    .location-button:focus-visible,
    .nearby-back:focus-visible,
    .obstacle-card:focus-visible {
        outline: 3px solid #059669;
        outline-offset: 3px;
    }

    .section-heading {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 16px;
    }

    .section-heading h2 {
        margin: 0;
        color: #0f172a;
        font-size: 19px;
        font-weight: 700;
    }

    .radius-label {
        color: #64748b;
        font-size: 13px;
        background: #f1f5f9;
        padding: 7px 11px;
        border-radius: 8px;
    }

    .obstacle-list {
        display: grid;
        gap: 14px;
    }

    .obstacle-card {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 20px;
        display: flex;
        align-items: flex-start;
        gap: 17px;
        box-shadow: 0 3px 12px rgba(15, 23, 42, 0.035);
    }

    .obstacle-icon {
          width: 46px;
    height: 46px;
    border-radius: 12px;
    background: #fff7ed;
    color: #ea580c;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 19px;
    flex-shrink: 0;
}

    .obstacle-content {
        flex: 1;
        min-width: 0;
    }

    .obstacle-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        margin-bottom: 7px;
    }

    .obstacle-title {
        margin: 0;
        color: #0f172a;
        font-size: 16px;
        font-weight: 700;
    }

    .priority-badge {
        display: inline-flex;
        align-items: center;
        padding: 5px 9px;
        border-radius: 7px;
        font-size: 11px;
        font-weight: 700;
        white-space: nowrap;
    }

    .priority-high {
        background: #fee2e2;
        color: #b91c1c;
    }

    .priority-medium {
        background: #fef3c7;
        color: #b45309;
    }

    .priority-low {
        background: #dcfce7;
        color: #15803d;
    }

    .obstacle-category {
        color: #059669;
        font-size: 13px;
        font-weight: 600;
        margin-bottom: 5px;
    }

    .obstacle-address {
        color: #64748b;
        font-size: 13px;
        line-height: 1.6;
        margin: 0;
    }

    .obstacle-distance {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-top: 10px;
        color: #334155;
        font-size: 12px;
        font-weight: 600;
        background: #f8fafc;
        padding: 6px 9px;
        border-radius: 7px;
    }

    .empty-state {
        background: white;
        border: 1px dashed #cbd5e1;
        border-radius: 16px;
        padding: 50px 25px;
        text-align: center;
    }

    .empty-icon {
        width: 58px;
        height: 58px;
        border-radius: 50%;
        background: #f1f5f9;
        color: #64748b;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 15px;
        font-size: 23px;
    }

    .empty-state h3 {
        margin: 0 0 7px;
        color: #0f172a;
        font-size: 17px;
        font-weight: 700;
    }

    .empty-state p {
        margin: 0;
        color: #64748b;
        font-size: 13px;
        line-height: 1.6;
    }

    .accessibility-note {
        margin-top: 28px;
        background: #ecfdf5;
        border: 1px solid #a7f3d0;
        border-radius: 15px;
        padding: 18px 20px;
        display: flex;
        gap: 13px;
        align-items: flex-start;
    }

    .accessibility-note i {
        color: #059669;
        margin-top: 2px;
        font-size: 17px;
    }

    .accessibility-note p {
        margin: 0;
        color: #065f46;
        font-size: 13px;
        line-height: 1.7;
    }

    .sr-only {
        position: absolute;
        width: 1px;
        height: 1px;
        padding: 0;
        margin: -1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        white-space: nowrap;
        border: 0;
    }

    /* SCREEN READER MELAYANG */
    #nearby-screen-reader {
        position: fixed;
        right: 24px;
        bottom: 24px;
        z-index: 9999;
        display: flex;
        align-items: center;
        gap: 9px;
        padding: 12px 17px;
        border: 1px solid #047857;
        border-radius: 999px;
        background: #047857;
        color: #fff;
        font-size: 14px;
        font-weight: 700;
        box-shadow: 0 4px 14px rgba(15, 23, 42, 0.18);
        cursor: pointer;
    }

    #nearby-screen-reader:hover {
        background: #065f46;
    }

    #nearby-screen-reader:focus-visible {
        outline: 3px solid #10b981;
        outline-offset: 4px;
    }

    #nearby-screen-reader i {
        font-size: 19px;
    }

    @media (max-width: 700px) {
        .nearby-page {
            padding: 24px 16px 40px;
        }

        .nearby-title {
            font-size: 25px;
        }

        .location-card {
            flex-direction: column;
            align-items: stretch;
        }

        .location-button {
            justify-content: center;
            width: 100%;
        }

        .section-heading {
            align-items: flex-start;
            flex-direction: column;
            gap: 10px;
        }

        .obstacle-card {
            padding: 16px;
        }

        .obstacle-top {
            align-items: flex-start;
            flex-direction: column;
            gap: 7px;
        }

        #nearby-screen-reader {
            right: 14px;
            bottom: 14px;
            padding: 12px 14px;
            font-size: 13px;
        }
        .empty-icon--warning {
    background: #fff7ed;
    color: #ea580c;
}

/* Ikon peringatan pada daftar Hambatan Terdekat */
.obstacle-icon {
    background-color: #fff7ed !important;
    color: #ea580c !important;
}

.obstacle-icon i {
    color: #ea580c !important;
}

    }
    
/* Netral untuk kartu hambatan */
.obstacle-card {
    border-color: #e2e8f0;
}

</style>

<main class="nearby-page">
    <div class="nearby-container">

        {{-- HEADER UTAMA --}}
        <header class="nearby-header">
            <a href="{{ url('/peta') }}"
               class="nearby-back"
               aria-label="Kembali ke halaman Peta SmartPath">
                <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
                Kembali ke Peta
            </a>

            <h1 class="nearby-title" id="page-title">
                Nearby Obstacles
            </h1>

            <p class="nearby-description" id="page-description">
                Temukan informasi hambatan aksesibilitas yang berada
                di sekitar lokasi kamu. Informasi ditampilkan dalam
                bentuk daftar agar lebih mudah diakses oleh pengguna
                screen reader.
            </p>
        </header>

        {{-- KONTROL LOKASI --}}
        <section class="location-card"
                 aria-labelledby="location-heading"
                 aria-describedby="location-desc">

            <div class="location-info">
                <div class="location-icon" aria-hidden="true">
                    <i class="fa-solid fa-location-crosshairs"></i>
                </div>

                <div class="location-text">
                    <h2 id="location-heading">Lokasi Saya</h2>
                    <p id="location-desc">
                        Aktifkan lokasi untuk melihat hambatan
                        aksesibilitas di sekitar kamu.
                    </p>
                </div>
            </div>

            <button
                type="button"
                class="location-button"
                id="btn-location"
                aria-label="Gunakan lokasi saya untuk mencari hambatan aksesibilitas di sekitar"
                aria-busy="false"
            >
                <i class="fa-solid fa-location-dot" aria-hidden="true"></i>
                <span>Gunakan Lokasi Saya</span>
            </button>
        </section>

        {{-- DAFTAR HAMBATAN --}}
        <section aria-labelledby="section-heading-title">
            <div class="section-heading">
                <h2 id="section-heading-title">
                    Hambatan Aksesibilitas Terdekat
                </h2>

                <span class="radius-label"
                      id="radius-desc"
                      aria-label="Jangkauan radius pencarian lima puluh meter">
                    Radius 50 meter
                </span>
            </div>

            <div id="screen-reader-announcement"
                 class="sr-only"
                 aria-live="polite"
                 aria-atomic="true"></div>

            <div id="obstacle-list"
                 class="obstacle-list"
                 role="region"
                 aria-labelledby="section-heading-title"
                 aria-describedby="radius-desc">

                <div class="empty-state" role="status">
                   <div class="empty-icon empty-icon--warning" aria-hidden="true">
    <i class="fa-solid fa-triangle-exclamation"></i>
</div>

                    <h3>Lokasi belum digunakan</h3>
                    <p>
                        Gunakan tombol "Gunakan Lokasi Saya"
                        untuk mencari hambatan aksesibilitas
                        yang berada di sekitar lokasi kamu.
                    </p>
                </div>
            </div>
        </section>

        {{-- INFORMASI AKSESIBILITAS --}}
        <aside class="accessibility-note" aria-label="Catatan Aksesibilitas">
            <i class="fa-solid fa-universal-access" aria-hidden="true"></i>
            <p>
                Halaman ini menggunakan informasi berbasis teks
                sehingga dapat dibaca oleh teknologi pembaca layar
                seperti TalkBack atau VoiceOver.
            </p>
        </aside>
    </div>
</main>

{{-- TOMBOL SCREEN READER MELAYANG --}}
<button
    type="button"
    id="nearby-screen-reader"
    aria-label="Bacakan isi halaman Nearby Obstacles"
    aria-pressed="false"
>
    <i class="fa-solid fa-blind" aria-hidden="true"></i>
    <span id="nearby-screen-reader-label">Dengar Panduan</span>
</button>

<span id="nearby-screen-reader-status"
      class="sr-only"
      role="status"
      aria-live="polite"></span>

{{-- SCRIPT GPS DAN DAFTAR HAMBATAN --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const btnLocation = document.getElementById('btn-location');
    const obstacleList = document.getElementById('obstacle-list');
    const announcement = document.getElementById('screen-reader-announcement');
    const radiusMeter = 50;
    const laporan = @json($laporanData);

    function calculateDistance(lat1, lon1, lat2, lon2) {
        const earthRadius = 6371000;
        const latFrom = lat1 * Math.PI / 180;
        const latTo = lat2 * Math.PI / 180;
        const latDelta = (lat2 - lat1) * Math.PI / 180;
        const lonDelta = (lon2 - lon1) * Math.PI / 180;

        const a = Math.sin(latDelta / 2) ** 2 +
            Math.cos(latFrom) * Math.cos(latTo) *
            Math.sin(lonDelta / 2) ** 2;

        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        return earthRadius * c;
    }

    function escapeHtml(value) {
        const element = document.createElement('div');
        element.textContent = value == null ? '' : String(value);
        return element.innerHTML;
    }

    function tampilkanHambatan(data) {
        if (data.length === 0) {
            obstacleList.innerHTML = `
                <div class="empty-state"
                     role="status"
                     tabindex="0"
                     aria-label="Tidak ditemukan hambatan aksesibilitas terverifikasi dalam radius 50 meter dari lokasi Anda.">
                    <div class="empty-icon" aria-hidden="true">
                        <i class="fa-solid fa-check"></i>
                    </div>
                    <h3>Tidak ada hambatan dalam radius 50 meter</h3>
                    <p>
                        Tidak ditemukan laporan hambatan aksesibilitas
                        terverifikasi dalam jarak 50 meter dari lokasi kamu.
                    </p>
                </div>
            `;

            announcement.textContent =
                'Pencarian selesai. Tidak ditemukan hambatan aksesibilitas dalam radius 50 meter dari lokasi Anda.';
            return;
        }

        obstacleList.innerHTML = data.map(function (item, index) {
            const priority = item.tingkat_prioritas
                ? String(item.tingkat_prioritas).toLowerCase()
                : 'belum dinilai';

            let priorityClass = 'priority-low';
            let priorityLabel = 'Prioritas Rendah';

            if (priority === 'tinggi') {
                priorityClass = 'priority-high';
                priorityLabel = 'Prioritas Tinggi';
            } else if (priority === 'sedang') {
                priorityClass = 'priority-medium';
                priorityLabel = 'Prioritas Sedang';
            } else if (priority === 'belum dinilai') {
                priorityClass = 'priority-low';
                priorityLabel = 'Belum Dinilai';
            }

            const distanceText = item.distance < 1
                ? 'kurang dari 1 meter'
                : Math.round(item.distance) + ' meter';

            const kategori = item.kategori ?? 'Kategori tidak tersedia';
            const alamat = item.alamat_lengkap ?? item.alamat ?? 'Alamat tidak tersedia';
            const judul = item.judul ?? 'Hambatan aksesibilitas';

            return `
                <article
                    class="obstacle-card"
                    tabindex="0"
                    aria-labelledby="obs-title-${index}"
                    aria-describedby="obs-cat-${index} obs-addr-${index} obs-dist-${index} obs-prio-${index}"
                >
                    <div class="obstacle-icon" aria-hidden="true">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>

                    <div class="obstacle-content">
                        <div class="obstacle-top">
                            <h3 class="obstacle-title" id="obs-title-${index}">
                                ${escapeHtml(judul)}
                            </h3>

                            <span class="priority-badge ${priorityClass}"
                                  id="obs-prio-${index}"
                                  aria-label="Tingkat bahaya: ${escapeHtml(priorityLabel)}">
                                ${escapeHtml(priorityLabel)}
                            </span>
                        </div>

                        <div class="obstacle-category" id="obs-cat-${index}">
                            Kategori: ${escapeHtml(kategori)}
                        </div>

                        <p class="obstacle-address" id="obs-addr-${index}">
                            ${escapeHtml(alamat)}
                        </p>

                        <span class="obstacle-distance" id="obs-dist-${index}">
                            <i class="fa-solid fa-location-dot" aria-hidden="true"></i>
                            ${escapeHtml(distanceText)}
                        </span>
                    </div>
                </article>
            `;
        }).join('');

        const jumlah = data.length;
        const nearest = data[0];
        const nearestDistance = nearest.distance < 1
            ? 'kurang dari 1 meter'
            : Math.round(nearest.distance) + ' meter';

        announcement.textContent =
            `Pencarian selesai. Ditemukan ${jumlah} hambatan aksesibilitas dalam radius 50 meter. Hambatan terdekat adalah ${nearest.judul}, berjarak ${nearestDistance} dari lokasi Anda.`;
    }

    if (!btnLocation || !obstacleList || !announcement) {
        console.error('Elemen Nearby tidak ditemukan.');
        return;
    }

    btnLocation.addEventListener('click', function () {
        if (!navigator.geolocation) {
            const pesan = 'Perangkat atau browser Anda tidak mendukung fitur lokasi.';

            obstacleList.innerHTML = `
                <div class="empty-state" role="alert" tabindex="0">
                    <div class="empty-icon" aria-hidden="true">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                    <h3>Browser Tidak Mendukung</h3>
                    <p>${escapeHtml(pesan)}</p>
                </div>
            `;

            announcement.textContent = pesan;
            return;
        }

        btnLocation.disabled = true;
        btnLocation.setAttribute('aria-busy', 'true');
        btnLocation.setAttribute(
            'aria-label',
            'Sedang mencari lokasi Anda, mohon tunggu...'
        );

        btnLocation.innerHTML = `
            <i class="fa-solid fa-spinner fa-spin" aria-hidden="true"></i>
            <span>Mencari lokasi...</span>
        `;

        obstacleList.innerHTML = `
            <div class="empty-state" role="status" aria-live="polite">
                <div class="empty-icon" aria-hidden="true">
                    <i class="fa-solid fa-location-crosshairs fa-spin"></i>
                </div>
                <h3>Mencari lokasi kamu...</h3>
                <p>Mohon tunggu sebentar, sistem sedang memproses koordinat Anda.</p>
            </div>
        `;

        announcement.textContent =
            'Sedang mengakses GPS dan mencari lokasi Anda.';

        navigator.geolocation.getCurrentPosition(
            function (position) {
                const userLatitude = position.coords.latitude;
                const userLongitude = position.coords.longitude;

                const nearbyReports = laporan
                    .map(function (item) {
                        return {
                            ...item,
                            distance: calculateDistance(
                                userLatitude,
                                userLongitude,
                                parseFloat(item.latitude),
                                parseFloat(item.longitude)
                            )
                        };
                    })
                    .filter(function (item) {
                        return Number.isFinite(item.distance) &&
                            Number.isFinite(parseFloat(item.latitude)) &&
                            Number.isFinite(parseFloat(item.longitude)) &&
                            item.distance <= radiusMeter;
                    })
                    .sort(function (a, b) {
                        return a.distance - b.distance;
                    });

                tampilkanHambatan(nearbyReports);

                btnLocation.disabled = false;
                btnLocation.setAttribute('aria-busy', 'false');
                btnLocation.setAttribute(
                    'aria-label',
                    'Perbarui lokasi saya untuk mencari ulang hambatan'
                );

                btnLocation.innerHTML = `
                    <i class="fa-solid fa-location-dot" aria-hidden="true"></i>
                    <span>Perbarui Lokasi</span>
                `;
            },
            function (error) {
                let pesan = 'Lokasi tidak dapat diperoleh.';
                let judulError = 'Lokasi Tidak Tersedia';

                if (error.code === 1) {
                    judulError = 'Izin Lokasi Ditolak';
                    pesan = 'Akses lokasi ditolak. Silakan berikan izin lokasi di pengaturan browser Anda untuk melanjutkan.';
                } else if (error.code === 2) {
                    judulError = 'Sinyal Lokasi Lemah';
                    pesan = 'Sinyal GPS atau jaringan lokasi tidak dapat diperoleh. Pastikan koneksi dan GPS aktif.';
                } else if (error.code === 3) {
                    judulError = 'Waktu Permintaan Habis';
                    pesan = 'Waktu pengambilan lokasi habis. Silakan coba tekan tombol perbarui kembali.';
                }

                obstacleList.innerHTML = `
                    <div class="empty-state" role="alert" tabindex="0">
                        <div class="empty-icon" aria-hidden="true">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                        </div>
                        <h3>${escapeHtml(judulError)}</h3>
                        <p>${escapeHtml(pesan)}</p>
                    </div>
                `;

                announcement.textContent = `Peringatan: ${pesan}`;

                btnLocation.disabled = false;
                btnLocation.setAttribute('aria-busy', 'false');
                btnLocation.setAttribute(
                    'aria-label',
                    'Coba lagi menggunakan lokasi saya'
                );

                btnLocation.innerHTML = `
                    <i class="fa-solid fa-location-dot" aria-hidden="true"></i>
                    <span>Gunakan Lokasi Saya</span>
                `;
            },
            {
                enableHighAccuracy: false,
                timeout: 30000,
                maximumAge: 60000
            }
        );
    });
});
</script>

</div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/smartpath-warga.js') }}" defer></script>

    {{-- SCREEN READER MELAYANG --}}
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const tombol = document.getElementById('nearby-screen-reader');
        const label = document.getElementById('nearby-screen-reader-label');
        const status = document.getElementById('nearby-screen-reader-status');

        if (!tombol || !label || !status) return;

        if (!('speechSynthesis' in window)) {
            tombol.disabled = true;
            status.textContent = 'Browser tidak mendukung pembaca suara.';
            return;
        }

        let membaca = false;
        let nomorBacaan = 0;

        function resetTombol(pesan) {
            membaca = false;
            tombol.setAttribute('aria-pressed', 'false');
            label.textContent = 'Dengar Panduan';
            status.textContent = pesan || '';
        }

        function hentikanBacaan() {
            nomorBacaan++;
            window.speechSynthesis.cancel();
            resetTombol('Pembacaan dihentikan.');
        }

        function mulaiBacaan() {
            const halaman = document.querySelector('.nearby-page');

            if (!halaman) {
                status.textContent = 'Konten halaman tidak ditemukan.';
                return;
            }

            const salinan = halaman.cloneNode(true);

            salinan.querySelectorAll(
                'script, style, button, input, select, textarea, .sr-only'
            ).forEach(function (elemen) {
                elemen.remove();
            });

            const teks = salinan.innerText.replace(/\s+/g, ' ').trim();

            if (!teks) {
                status.textContent = 'Tidak ada teks yang dapat dibacakan.';
                return;
            }

            nomorBacaan++;
            const idBacaan = nomorBacaan;

            window.speechSynthesis.cancel();

            const ucapan = new SpeechSynthesisUtterance(teks);
            ucapan.lang = 'id-ID';
            ucapan.rate = 0.9;
            ucapan.pitch = 1;

            membaca = true;
            tombol.setAttribute('aria-pressed', 'true');
            label.textContent = 'Hentikan Bacaan';
            status.textContent = 'Halaman sedang dibacakan.';

            ucapan.onend = function () {
                if (idBacaan !== nomorBacaan) return;
                resetTombol('Pembacaan halaman selesai.');
            };

            ucapan.onerror = function (event) {
                if (idBacaan !== nomorBacaan) return;

                if (event.error !== 'canceled' &&
                    event.error !== 'interrupted') {
                    resetTombol('Suara gagal diputar. Silakan coba lagi.');
                } else {
                    resetTombol('Pembacaan dihentikan.');
                }
            };

            window.speechSynthesis.speak(ucapan);
        }

        tombol.addEventListener('click', function () {
            if (membaca || window.speechSynthesis.speaking) {
                hentikanBacaan();
            } else {
                mulaiBacaan();
            }
        });
    });
    </script>
@endpush