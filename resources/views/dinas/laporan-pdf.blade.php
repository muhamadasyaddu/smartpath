<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <title>Laporan {{ $laporan->kode_laporan }}</title>

    <style>
        @page {
            margin: 35px 40px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #1e293b;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 22px;
        }

        .header-left {
            width: 65%;
            vertical-align: middle;
        }

        .header-right {
            width: 35%;
            vertical-align: middle;
            text-align: right;
        }

        .brand {
            font-size: 22px;
            font-weight: bold;
            color: #059669;
            margin-bottom: 3px;
        }

        .subtitle {
            font-size: 11px;
            color: #64748b;
            margin: 0;
        }

        .document-title {
            font-size: 18px;
            font-weight: bold;
            color: #0f172a;
            margin: 0;
        }

        .document-code {
            font-size: 10px;
            color: #64748b;
            margin-top: 4px;
        }

        .divider {
            height: 2px;
            background: #10b981;
            margin-bottom: 20px;
        }

        .section {
            margin-bottom: 18px;
        }

        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: #0f766e;
            text-transform: uppercase;
            margin-bottom: 8px;
            letter-spacing: 0.3px;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-table th,
        .info-table td {
            border: 1px solid #dbe4ec;
            padding: 8px 9px;
            vertical-align: top;
        }

        .info-table th {
            width: 28%;
            background: #f8fafc;
            color: #334155;
            font-weight: bold;
            text-align: left;
        }

        .info-table td {
            color: #334155;
            background: #ffffff;
        }

        .status-box {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
        }

        .status-belum {
            background: #fef3c7;
            color: #b45309;
        }

        .status-proses {
            background: #dbeafe;
            color: #2563eb;
        }

        .status-selesai {
            background: #d1fae5;
            color: #047857;
        }

        .status-default {
            background: #e2e8f0;
            color: #475569;
        }

        .priority-box {
            padding: 12px;
            border: 1px solid #dbe4ec;
            background: #f8fafc;
        }

        .priority-score {
            font-size: 22px;
            font-weight: bold;
            color: #059669;
        }

        .priority-label {
            font-size: 10px;
            font-weight: bold;
            margin-top: 2px;
        }

        .priority-high {
            color: #dc2626;
        }

        .priority-medium {
            color: #d97706;
        }

        .priority-low {
            color: #059669;
        }

        .location-table {
            width: 100%;
            border-collapse: collapse;
        }

        .location-table td {
            border: 1px solid #dbe4ec;
            padding: 9px;
            vertical-align: top;
        }

        .location-label {
            width: 28%;
            background: #f8fafc;
            font-weight: bold;
            color: #334155;
        }

        .location-value {
            color: #475569;
        }

        .footer {
            margin-top: 25px;
            padding-top: 10px;
            border-top: 1px solid #cbd5e1;
            font-size: 9px;
            color: #64748b;
        }

        .footer-table {
            width: 100%;
            border-collapse: collapse;
        }

        .footer-left {
            text-align: left;
        }

        .footer-right {
            text-align: right;
        }

        .muted {
            color: #64748b;
        }

        .strong {
            font-weight: bold;
            color: #0f172a;
        }
    </style>
</head>

<body>

    {{-- ==========================================================
         HEADER
    =========================================================== --}}
    <table class="header-table">
        <tr>

            <td class="header-left">

                <div class="brand">
                    SmartPath
                </div>

                <p class="subtitle">
                    Platform Pelaporan Aksesibilitas Kota
                </p>

            </td>

            <td class="header-right">

                <div class="document-title">
                    Laporan Aksesibilitas
                </div>

                <div class="document-code">
                    {{ $laporan->kode_laporan }}
                </div>

            </td>

        </tr>
    </table>

    <div class="divider"></div>


    {{-- ==========================================================
         INFORMASI LAPORAN
    =========================================================== --}}
    <div class="section">

        <div class="section-title">
            Informasi Laporan
        </div>

        <table class="info-table">

            <tr>
                <th>Kode Laporan</th>

                <td>
                    <span class="strong">
                        {{ $laporan->kode_laporan }}
                    </span>
                </td>
            </tr>

            <tr>
                <th>Judul Laporan</th>

                <td>
                    {{ $laporan->judul }}
                </td>
            </tr>

            <tr>
                <th>Kategori</th>

                <td>
                    {{ $laporan->kategoriHambatan?->nama ?? '-' }}
                </td>
            </tr>

            <tr>
                <th>Jumlah Pelapor</th>

                <td>
                    {{ $laporan->jumlah_pelapor }}
                </td>
            </tr>

            <tr>
                <th>Tanggal Laporan</th>

                <td>
                    {{ $laporan->created_at?->format('d/m/Y H:i') ?? '-' }}
                </td>
            </tr>

        </table>

    </div>


    {{-- ==========================================================
         STATUS & PRIORITAS
    =========================================================== --}}
    <div class="section">

        <div class="section-title">
            Status & Prioritas
        </div>

        <table class="info-table">

            <tr>

                <th>Status</th>

                <td>

                    @php
                        $status = $laporan->status;

                        if ($status === 'diverifikasi') {
                            $statusLabel = 'Diverifikasi';
                            $statusClass = 'status-proses';
                        } elseif ($status === 'dalam_perbaikan') {
                            $statusLabel = 'Dalam Perbaikan';
                            $statusClass = 'status-proses';
                        } elseif ($status === 'selesai') {
                            $statusLabel = 'Selesai';
                            $statusClass = 'status-selesai';
                        } elseif ($status === 'menunggu_verifikasi') {
                            $statusLabel = 'Menunggu Verifikasi';
                            $statusClass = 'status-belum';
                        } elseif ($status === 'ditolak') {
                            $statusLabel = 'Ditolak';
                            $statusClass = 'status-default';
                        } else {
                            $statusLabel = $laporan->status_label ?? '-';
                            $statusClass = 'status-default';
                        }
                    @endphp

                    <span class="status-box {{ $statusClass }}">
                        {{ $statusLabel }}
                    </span>

                </td>

            </tr>

            <tr>

                <th>Skor Prioritas</th>

                <td>

                    @php
                        $skor = $laporan->skor_prioritas !== null
                            ? (float) $laporan->skor_prioritas
                            : null;

                        if ($skor !== null && $skor >= 70) {
                            $prioritasLabel = 'Tinggi';
                            $prioritasClass = 'priority-high';
                        } elseif ($skor !== null && $skor >= 40) {
                            $prioritasLabel = 'Sedang';
                            $prioritasClass = 'priority-medium';
                        } elseif ($skor !== null) {
                            $prioritasLabel = 'Rendah';
                            $prioritasClass = 'priority-low';
                        } else {
                            $prioritasLabel = '-';
                            $prioritasClass = '';
                        }
                    @endphp

                    @if($skor !== null)

                        <div class="priority-box">

                            <div class="priority-score">
                                {{ number_format($skor, 2) }}
                            </div>

                            <div class="priority-label {{ $prioritasClass }}">
                                Prioritas {{ $prioritasLabel }}
                            </div>

                        </div>

                    @else

                        -

                    @endif

                </td>

            </tr>

        </table>

    </div>


    {{-- ==========================================================
         LOKASI
    =========================================================== --}}
    <div class="section">

        <div class="section-title">
            Lokasi Laporan
        </div>

        <table class="location-table">

            <tr>

                <td class="location-label">
                    Alamat
                </td>

                <td class="location-value">

                    {{ $laporan->alamat_lengkap
                        ?? 'Lokasi tidak tersedia' }}

                </td>

            </tr>

            <tr>

                <td class="location-label">
                    Latitude
                </td>

                <td class="location-value">

                    {{ $laporan->latitude ?? '-' }}

                </td>

            </tr>

            <tr>

                <td class="location-label">
                    Longitude
                </td>

                <td class="location-value">

                    {{ $laporan->longitude ?? '-' }}

                </td>

            </tr>

        </table>

    </div>


    {{-- ==========================================================
         RINGKASAN
    =========================================================== --}}
    <div class="section">

        <div class="section-title">
            Ringkasan
        </div>

        <table class="info-table">

            <tr>

                <th>Identifikasi</th>

                <td>
                    Laporan aksesibilitas tercatat melalui sistem
                    SmartPath dan telah memiliki data lokasi serta
                    informasi prioritas.
                </td>

            </tr>

            <tr>

                <th>Status Penanganan</th>

                <td>
                    {{ $statusLabel }}
                </td>

            </tr>

            <tr>

                <th>Pelapor</th>

                <td>
                    {{ $laporan->jumlah_pelapor }}
                    pelapor
                </td>

            </tr>

        </table>

    </div>


    {{-- ==========================================================
         FOOTER
    =========================================================== --}}
    <div class="footer">

        <table class="footer-table">

            <tr>

                <td class="footer-left">
                    Dicetak melalui sistem SmartPath.
                </td>

                <td class="footer-right">
                    Kota yang Lebih Aksesibel
                </td>

            </tr>

        </table>

    </div>

</body>

</html>