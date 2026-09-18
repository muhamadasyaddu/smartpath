@extends('layouts.app')

@section('title', 'Navigasi SmartPath')

@section('content')

<div class="container py-4">

    <div class="mb-4">
        <a href="{{ url()->previous() }}" class="text-decoration-none">
            ← Kembali
        </a>

        <h1 class="mt-3 mb-2">Navigasi SmartPath</h1>

        <p class="text-muted">
            Tentukan tujuan perjalanan untuk mendapatkan informasi
            aksesibilitas di sepanjang perjalanan.
        </p>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">

            <h2 class="h5 mb-3">Lokasi Tujuan</h2>

            <label for="tujuan" class="form-label">
                Masukkan tujuan
            </label>

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

            <button
                type="button"
                id="btn-mulai-navigasi"
                class="btn btn-primary mt-4"
            >
                📍 Mulai Navigasi
            </button>

            <div
                id="status-navigasi"
                class="mt-3"
                role="status"
                aria-live="polite"
            ></div>

        </div>
    </div>

</div>

@endsection


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const btnNavigasi = document.getElementById('btn-mulai-navigasi');
    const tujuanInput = document.getElementById('tujuan');
    const statusNavigasi = document.getElementById('status-navigasi');

    btnNavigasi.addEventListener('click', function () {

        const tujuan = tujuanInput.value.trim();

        // Cek tujuan
        if (tujuan === '') {
            statusNavigasi.innerHTML = `
                <div class="alert alert-warning">
                    Silakan masukkan tujuan terlebih dahulu.
                </div>
            `;

            tujuanInput.focus();
            return;
        }

        // Cek dukungan GPS
        if (!navigator.geolocation) {
            statusNavigasi.innerHTML = `
                <div class="alert alert-danger">
                    Perangkat atau browser kamu tidak mendukung GPS.
                </div>
            `;
            return;
        }

        // Tampilan loading
        btnNavigasi.disabled = true;
        btnNavigasi.innerHTML = '📍 Mendeteksi lokasi...';

        statusNavigasi.innerHTML = `
            <div class="alert alert-info">
                Sedang mendeteksi lokasi kamu...
            </div>
        `;

        // Ambil lokasi pengguna
        navigator.geolocation.getCurrentPosition(

           function (position) {

    const latitude = position.coords.latitude;
    const longitude = position.coords.longitude;
    const accuracy = position.coords.accuracy;

    console.log('Latitude pengguna:', latitude);
    console.log('Longitude pengguna:', longitude);
    console.log('Akurasi GPS:', accuracy);

    statusNavigasi.innerHTML = `
        <div class="alert alert-info">
            📍 Lokasi kamu berhasil dideteksi.<br>
            Sedang mencari koordinat tujuan...
        </div>
    `;

    fetch('{{ route('navigasi.cari-tujuan') }}', {

        method: 'POST',

        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },

        body: JSON.stringify({
            tujuan: tujuan
        })

    })
    .then(response => response.json())
    .then(data => {

        if (!data.success) {
            throw new Error(
                data.message || 'Tujuan tidak ditemukan.'
            );
        }

        console.log('Tujuan:', data.tujuan);
        console.log('Latitude tujuan:', data.latitude);
        console.log('Longitude tujuan:', data.longitude);
    fetch('{{ route('navigasi.rute') }}', {

    method: 'POST',

    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
        'Accept': 'application/json'
    },

    body: JSON.stringify({
        latitude_awal: latitude,
        longitude_awal: longitude,
        latitude_tujuan: data.latitude,
        longitude_tujuan: data.longitude
    })

})
.then(response => response.json())
.then(rute => {

    if (!rute.success) {
        throw new Error(
            rute.message || 'Rute tidak ditemukan.'
        );
    }

    console.log('Jarak rute:', rute.distance);
    console.log('Durasi rute:', rute.duration);
    console.log('Geometry:', rute.geometry);

    const jarakKm = (rute.distance / 1000).toFixed(2);
    const durasiMenit = Math.ceil(rute.duration / 60);

    statusNavigasi.innerHTML = `
        <div class="alert alert-success">

            <strong>Rute berhasil ditemukan.</strong>

            <div class="mt-2">

                <div>
                    <strong>Tujuan:</strong>
                    ${escapeHtml(data.tujuan)}
                </div>

                <div>
                    <strong>Jarak:</strong>
                    ${jarakKm} km
                </div>

                <div>
                    <strong>Perkiraan waktu:</strong>
                    ${durasiMenit} menit
                </div>

            </div>

        </div>
    `;

    /*
     * Simpan geometry untuk tahap berikutnya.
     * Nanti geometry ini digunakan untuk
     * mengecek hambatan di sepanjang rute.
     */
    window.ruteNavigasi = rute.geometry;

    console.log(
        'Rute navigasi berhasil disimpan:',
        window.ruteNavigasi
    );

    btnNavigasi.disabled = false;
    btnNavigasi.innerHTML = '📍 Mulai Navigasi';

})
.catch(error => {

    console.error('Routing Error:', error);

    statusNavigasi.innerHTML = `
        <div class="alert alert-danger">
            ${escapeHtml(error.message)}
        </div>
    `;

    btnNavigasi.disabled = false;
    btnNavigasi.innerHTML = '📍 Mulai Navigasi';
});
        statusNavigasi.innerHTML = `
            <div class="alert alert-success">

                <strong>Lokasi dan tujuan berhasil ditemukan.</strong>

                <div class="mt-2">

                    <div>
                        <strong>Tujuan:</strong>
                        ${escapeHtml(data.tujuan)}
                    </div>

                    <div>
                        <strong>Latitude pengguna:</strong>
                        ${latitude}
                    </div>

                    <div>
                        <strong>Longitude pengguna:</strong>
                        ${longitude}
                    </div>

                    <div>
                        <strong>Latitude tujuan:</strong>
                        ${data.latitude}
                    </div>

                    <div>
                        <strong>Longitude tujuan:</strong>
                        ${data.longitude}
                    </div>

                    <div>
                        <strong>Akurasi GPS:</strong>
                        ±${Math.round(accuracy)} meter
                    </div>

                </div>

            </div>
        `;

        btnNavigasi.disabled = false;
        btnNavigasi.innerHTML = '📍 Mulai Navigasi';

    })
    .catch(error => {

        console.error('Geocoding Error:', error);

        statusNavigasi.innerHTML = `
            <div class="alert alert-danger">
                ${escapeHtml(error.message)}
            </div>
        `;

        btnNavigasi.disabled = false;
        btnNavigasi.innerHTML = '📍 Mulai Navigasi';
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
                    <div class="alert alert-danger">
                        ${pesan}
                    </div>
                `;

                btnNavigasi.disabled = false;
                btnNavigasi.innerHTML = '📍 Mulai Navigasi';
            },

            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            }
        );
    });


    // Mencegah teks tujuan dimasukkan langsung sebagai HTML
    function escapeHtml(text) {

        const div = document.createElement('div');

        div.textContent = text;

        return div.innerHTML;
    }

});
</script>
@endpush