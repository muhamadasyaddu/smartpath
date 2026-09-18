<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <title>Laporan {{ $laporan->kode_laporan }}</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #1e293b;
            line-height: 1.6;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
        }

        .header h1 {
            margin: 0;
            font-size: 20px;
        }

        .header p {
            margin: 5px 0;
            color: #64748b;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #cbd5e1;
            padding: 9px;
            text-align: left;
        }

        th {
            width: 30%;
            background: #f1f5f9;
        }

        .footer {
            margin-top: 25px;
            font-size: 10px;
            color: #64748b;
        }
    </style>
</head>

<body>

    <div class="header">
        <h1>Laporan Aksesibilitas SmartPath</h1>
        <p>Data Laporan Aksesibilitas</p>
    </div>

    <table>

        <tr>
            <th>Kode Laporan</th>
            <td>{{ $laporan->kode_laporan }}</td>
        </tr>

        <tr>
            <th>Judul</th>
            <td>{{ $laporan->judul }}</td>
        </tr>

        <tr>
            <th>Kategori</th>
            <td>
                {{ $laporan->kategoriHambatan?->nama ?? '-' }}
            </td>
        </tr>

        <tr>
            <th>Status</th>
            <td>
                {{ $laporan->status_label }}
            </td>
        </tr>

        <tr>
            <th>Prioritas</th>
            <td>
                {{ $laporan->skor_prioritas !== null
                    ? number_format((float) $laporan->skor_prioritas, 2)
                    : '-' }}
            </td>
        </tr>

        <tr>
            <th>Jumlah Pelapor</th>
            <td>
                {{ $laporan->jumlah_pelapor }}
            </td>
        </tr>

        <tr>
            <th>Latitude</th>
            <td>
                {{ $laporan->latitude ?? '-' }}
            </td>
        </tr>

        <tr>
            <th>Longitude</th>
            <td>
                {{ $laporan->longitude ?? '-' }}
            </td>
        </tr>

        <tr>
            <th>Tanggal</th>
            <td>
                {{ $laporan->created_at?->format('d/m/Y H:i') }}
            </td>
        </tr>

    </table>

    <div class="footer">
        Dicetak melalui sistem SmartPath.
    </div>

</body>

</html>