<?php

namespace App\Http\Controllers;

use App\Models\FasilitasPublik;
use App\Models\KategoriHambatan;
use App\Models\Laporan;
use App\Models\Wilayah;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class PetaController extends Controller
{
    /**
     * Halaman Peta Interaktif publik.
     *
     * Sesuai alur MVP:
     * hanya laporan yang sudah diverifikasi yang dipublikasikan.
     */
    public function index()
    {
        $kategoriHambatan = KategoriHambatan::query()
            ->aktif()
            ->urutTampil()
            ->get();

        return view(
            'Peta.index',
            compact('kategoriHambatan')
        );
    }

    /**
     * Halaman Peta Infrastruktur untuk role Dinas.
     *
     * Halaman ini menggunakan endpoint data peta yang sudah ada
     * sehingga tidak terjadi duplikasi logika antara peta publik
     * dan peta pemerintah.
     */
    public function indexDinas()
    {
        abort_unless(
            auth()->check() && auth()->user()->isDinas(),
            403
        );

        $kategoriHambatan = KategoriHambatan::query()
            ->aktif()
            ->urutTampil()
            ->get();

        return view(
            'Dinas.peta',
            compact('kategoriHambatan')
        );
    }

    /**
     * Data laporan untuk Peta Interaktif.
     *
     * Endpoint publik hanya mengembalikan:
     * - laporan induk
     * - laporan aktif
     * - laporan terverifikasi
     * - laporan dengan koordinat valid
     */
    public function getLaporanData(Request $request)
    {
        $allowedStatuses = [
            'diverifikasi',
            'dalam_perbaikan',
            'selesai',
        ];

        $statusFilter = collect(
            explode(
                ',',
                (string) $request->query('status', '')
            )
        )
            ->map(fn ($status) => trim($status))
            ->filter(fn ($status) =>
                in_array($status, $allowedStatuses, true)
            )
            ->unique()
            ->values();

        $kategoriFilter = collect(
            explode(
                ',',
                (string) $request->query('kategori', '')
            )
        )
            ->map(fn ($id) => filter_var(
                $id,
                FILTER_VALIDATE_INT
            ))
            ->filter(fn ($id) =>
                $id !== false && $id > 0
            )
            ->unique()
            ->values();

        $query = Laporan::query()
            ->induk()
            ->aktif()
            ->terverifikasi()
            ->with([
                'kategoriHambatan',
                'wilayah',
                'foto',
            ])
            ->whereNotNull('latitude')
            ->whereNotNull('longitude');

        if ($statusFilter->isNotEmpty()) {
            $query->whereIn(
                'status',
                $statusFilter->all()
            );
        }

        if ($kategoriFilter->isNotEmpty()) {
            $query->whereIn(
                'kategori_hambatan_id',
                $kategoriFilter->all()
            );
        }

        $laporan = $query
            ->orderByDesc('skor_prioritas')
            ->orderByDesc('created_at')
            ->get()
            ->map(function (Laporan $item) {
                $fotoUtama =
                    $item->foto
                        ->firstWhere(
                            'adalah_utama',
                            true
                        )
                    ??
                    $item->foto
                        ->sortBy('urutan')
                        ->first();

                return [
                    'id' => $item->id,

                    'kode_laporan' =>
                        $item->kode_laporan,

                    'judul' =>
                        $item->judul,

                    'deskripsi' =>
                        $item->deskripsi,

                    'latitude' =>
                        (float) $item->latitude,

                    'longitude' =>
                        (float) $item->longitude,

                    'status' =>
                        $item->status,

                    'status_label' =>
                        $item->status_label,

                    'skor_prioritas' =>
                        $item->skor_prioritas !== null
                            ? (float) $item->skor_prioritas
                            : null,

                    'tingkat_prioritas' =>
                        $item->tingkat_prioritas,

                    'kategori_id' =>
                        $item->kategori_hambatan_id,

                    'wilayah_id' =>
                        $item->wilayah_id,

                    'kategori' =>
                        $item->kategoriHambatan?->nama,

                    'kategori_warna' =>
                        $item->kategoriHambatan?->warna_penanda
                        ?? '#64748b',

                    'jumlah_pelapor' =>
                        (int) $item->jumlah_pelapor,

                    'alamat_lengkap' =>
                        $item->alamat_lengkap,

                    'wilayah' =>
                        $item->wilayah?->nama,

                    'foto_utama' =>
                        $fotoUtama?->url,

                    'created_at' =>
                        $item->created_at?->toISOString(),
                ];
            })
            ->values();

        return response()->json(
            $laporan
        );
    }



    
/**
 * Ekspor data peta aksesibilitas ke workbook Excel.
 * Hanya mengekspor laporan induk, aktif, terverifikasi,
 * dan mempunyai koordinat yang valid.
 */
public function exportExcel(Request $request)
{
    $allowedStatuses = [
        'diverifikasi',
        'dalam_perbaikan',
        'selesai',
    ];

    $statusWasProvided = $request->query->has('status');

    $statusFilter = collect(
        explode(',', (string) $request->query('status', ''))
    )
        ->map(fn ($value) => trim($value))
        ->filter(fn ($value) =>
            in_array($value, $allowedStatuses, true)
        )
        ->unique()
        ->values();

    $kategoriFilter = collect(
        explode(',', (string) $request->query('kategori', ''))
    )
        ->map(fn ($value) => filter_var($value, FILTER_VALIDATE_INT))
        ->filter(fn ($value) =>
            $value !== false && $value > 0
        )
        ->unique()
        ->values();

    $query = Laporan::query()
        ->induk()
        ->aktif()
        ->terverifikasi()
        ->with([
            'kategoriHambatan',
            'wilayah',
            'foto',
        ])
        ->whereNotNull('latitude')
        ->whereNotNull('longitude');

    /*
     * Bila parameter status dikirim tetapi tidak ada status
     * yang dipilih, hasil ekspor harus kosong, bukan semua data.
     */
    if ($statusWasProvided) {
        if ($statusFilter->isEmpty()) {
            $query->whereRaw('1 = 0');
        } else {
            $query->whereIn('status', $statusFilter->all());
        }
    }

    if ($kategoriFilter->isNotEmpty()) {
        $query->whereIn(
            'kategori_hambatan_id',
            $kategoriFilter->all()
        );
    }

    if ($request->boolean('prioritas_tinggi')) {
        $query->where(function ($builder) {
            $builder
                ->where('skor_prioritas', '>=', 70)
                ->orWhere('tingkat_prioritas', 'tinggi');
        });
    }

    $laporan = $query
        ->orderByDesc('skor_prioritas')
        ->orderByDesc('created_at')
        ->get();

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Data Laporan');

    $sheet->mergeCells('A1:M1');
    $sheet->setCellValue(
        'A1',
        'SMARTPATH | DATA PETA AKSESIBILITAS'
    );

    $sheet->mergeCells('A2:M2');
    $sheet->setCellValue(
        'A2',
        'Ringkasan laporan hambatan aksesibilitas yang telah diverifikasi'
    );

    $sheet->mergeCells('A3:M3');
    $sheet->setCellValue(
        'A3',
        'Jumlah data: ' . $laporan->count()
        . ' | Waktu ekspor: ' . now()->format('d/m/Y H:i')
    );

    $headers = [
        'Kode Laporan',
        'Judul Laporan',
        'Kategori Hambatan',
        'Status',
        'Skor Prioritas',
        'Tingkat Prioritas',
        'Jumlah Pelapor',
        'Wilayah',
        'Latitude',
        'Longitude',
        'Alamat',
        'Tanggal Laporan',
        'Foto Utama (URL)',
    ];

    foreach ($headers as $index => $header) {
        $column = chr(65 + $index);
        $sheet->setCellValue($column . '5', $header);
    }

    $row = 6;

    foreach ($laporan as $item) {
        $fotoUtama = $item->foto->firstWhere('adalah_utama', true)
            ?? $item->foto->sortBy('urutan')->first();

        /*
         * Teks dari pengguna dimasukkan secara eksplisit sebagai
         * teks agar tidak diperlakukan sebagai formula Excel.
         */
        $textValues = [
            'A' => $item->kode_laporan,
            'B' => $item->judul,
            'C' => $item->kategoriHambatan?->nama,
            'D' => $item->status_label,
            'F' => $item->tingkat_prioritas,
            'H' => $item->wilayah?->nama,
            'K' => $item->alamat_lengkap,
            'L' => $item->created_at?->format('d/m/Y H:i'),
            'M' => $fotoUtama?->url,
        ];

        foreach ($textValues as $column => $value) {
            $sheet->setCellValueExplicit(
                $column . $row,
                (string) ($value ?? ''),
                DataType::TYPE_STRING
            );
        }

        if ($item->skor_prioritas !== null) {
            $sheet->setCellValue(
                'E' . $row,
                (float) $item->skor_prioritas
            );
        }

        $sheet->setCellValue(
            'G' . $row,
            (int) $item->jumlah_pelapor
        );

        $sheet->setCellValue(
            'I' . $row,
            (float) $item->latitude
        );

        $sheet->setCellValue(
            'J' . $row,
            (float) $item->longitude
        );

        $row++;
    }

    $lastRow = max(5, $row - 1);

    // Judul utama.
    $sheet->getStyle('A1:M1')->applyFromArray([
        'font' => [
            'bold' => true,
            'size' => 16,
            'color' => ['rgb' => 'FFFFFF'],
        ],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['rgb' => '065F46'],
        ],
        'alignment' => [
            'vertical' => Alignment::VERTICAL_CENTER,
        ],
    ]);

    // Subjudul.
    $sheet->getStyle('A2:M2')->applyFromArray([
        'font' => [
            'size' => 10,
            'color' => ['rgb' => '065F46'],
        ],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['rgb' => 'D1FAE5'],
        ],
        'alignment' => [
            'vertical' => Alignment::VERTICAL_CENTER,
        ],
    ]);

    $sheet->getStyle('A3:M3')->applyFromArray([
        'font' => [
            'size' => 10,
            'color' => ['rgb' => '475569'],
        ],
        'alignment' => [
            'vertical' => Alignment::VERTICAL_CENTER,
        ],
    ]);

    // Header tabel.
    $sheet->getStyle('A5:M5')->applyFromArray([
        'font' => [
            'bold' => true,
            'color' => ['rgb' => 'FFFFFF'],
        ],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['rgb' => '047857'],
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical' => Alignment::VERTICAL_CENTER,
            'wrapText' => true,
        ],
        'borders' => [
            'bottom' => [
                'borderStyle' => Border::BORDER_MEDIUM,
                'color' => ['rgb' => '065F46'],
            ],
        ],
    ]);

    // Format isi tabel.
    if ($lastRow >= 6) {
        $sheet->getStyle('A6:M' . $lastRow)->applyFromArray([
            'font' => [
                'size' => 10,
                'color' => ['rgb' => '1E293B'],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_TOP,
                'wrapText' => true,
            ],
            'borders' => [
                'bottom' => [
                    'borderStyle' => Border::BORDER_HAIR,
                    'color' => ['rgb' => 'E2E8F0'],
                ],
            ],
        ]);

        // Warna baris bergantian untuk memudahkan pembacaan.
        for ($r = 6; $r <= $lastRow; $r++) {
            if ($r % 2 === 0) {
                $sheet->getStyle('A' . $r . ':M' . $r)
                    ->getFill()
                    ->setFillType(Fill::FILL_SOLID);

                $sheet->getStyle('A' . $r . ':M' . $r)
                    ->getFill()
                    ->getStartColor()
                    ->setRGB('F8FAFC');
            }

            $status = strtolower(
                (string) $sheet->getCell('D' . $r)->getValue()
            );

            $statusColor = match (true) {
                str_contains($status, 'selesai') => 'DCFCE7',
                str_contains($status, 'perbaikan') => 'FEF3C7',
                str_contains($status, 'diverifikasi') => 'CCFBF1',
                default => null,
            };

            if ($statusColor) {
                $sheet->getStyle('D' . $r)
                    ->getFill()
                    ->setFillType(Fill::FILL_SOLID);

                $sheet->getStyle('D' . $r)
                    ->getFill()
                    ->getStartColor()
                    ->setRGB($statusColor);
            }
        }
    }

    // Format angka.
    $sheet->getStyle('E6:E' . $lastRow)
        ->getNumberFormat()
        ->setFormatCode('0.00');

    $sheet->getStyle('G6:G' . $lastRow)
        ->getNumberFormat()
        ->setFormatCode('0');

    $sheet->getStyle('I6:J' . $lastRow)
        ->getNumberFormat()
        ->setFormatCode('0.000000');

    // Lebar kolom disesuaikan dengan isi.
    $widths = [
        'A' => 21,
        'B' => 32,
        'C' => 24,
        'D' => 20,
        'E' => 16,
        'F' => 19,
        'G' => 16,
        'H' => 20,
        'I' => 16,
        'J' => 16,
        'K' => 42,
        'L' => 22,
        'M' => 42,
    ];

    foreach ($widths as $column => $width) {
        $sheet->getColumnDimension($column)->setWidth($width);
    }

    $sheet->getRowDimension(1)->setRowHeight(30);
    $sheet->getRowDimension(2)->setRowHeight(24);
    $sheet->getRowDimension(3)->setRowHeight(21);
    $sheet->getRowDimension(5)->setRowHeight(32);

    $sheet->freezePane('A6');
    $sheet->setAutoFilter('A5:M' . $lastRow);

    // Pengaturan cetak jika workbook dicetak atau disimpan ke PDF.
    $sheet->getPageSetup()
        ->setOrientation(
            \PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE
        );

    $sheet->getPageSetup()->setFitToWidth(1);
    $sheet->getPageSetup()->setFitToHeight(0);
    $sheet->getPageSetup()->setFitToPage(true);
    $sheet->getPageMargins()->setLeft(0.25);
    $sheet->getPageMargins()->setRight(0.25);

    $fileName = 'smartpath-peta-aksesibilitas-'
        . now()->format('Y-m-d-His')
        . '.xlsx';

    return response()->streamDownload(
        function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
            $spreadsheet->disconnectWorksheets();
        },
        $fileName,
        [
            'Content-Type' =>
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0, no-cache, no-store, must-revalidate',
        ]
    );
}


    
    /**
     * Data fasilitas publik.
     */
    public function getFasilitasData()
    {
        $jenisLabel = [
            'rumah_sakit' =>
                'Rumah Sakit',

            'puskesmas' =>
                'Puskesmas',

            'sekolah' =>
                'Sekolah',

            'perguruan_tinggi' =>
                'Perguruan Tinggi',

            'halte' =>
                'Halte',

            'stasiun' =>
                'Stasiun',

            'terminal' =>
                'Terminal',

            'kantor_pemerintah' =>
                'Kantor Pemerintah',

            'pasar' =>
                'Pasar',

            'tempat_ibadah' =>
                'Tempat Ibadah',

            'lainnya' =>
                'Fasilitas Publik',
        ];

        $fasilitasPublik = FasilitasPublik::query()
            ->aktif()
            ->with('wilayah')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get()
            ->map(function ($fasilitas) use ($jenisLabel) {
                return [
                    'id' =>
                        $fasilitas->id,

                    'nama' =>
                        $fasilitas->nama,

                    'jenis' =>
                        $fasilitas->jenis,

                    'jenis_label' =>
                        $jenisLabel[
                            $fasilitas->jenis
                        ]
                        ?? 'Fasilitas Publik',

                    'latitude' =>
                        (float) $fasilitas->latitude,

                    'longitude' =>
                        (float) $fasilitas->longitude,

                    'bobot_vital' =>
                        (int) $fasilitas->bobot_vital,

                    'alamat' =>
                        $fasilitas->alamat,

                    'wilayah_id' =>
                        $fasilitas->wilayah_id,

                    'wilayah' =>
                        $fasilitas->wilayah?->nama,

                    'sumber_data' =>
                        $fasilitas->sumber_data,
                ];
            })
            ->values();

        return response()->json(
            $fasilitasPublik
        );
    }

    /**
     * Halaman daftar hambatan terdekat.
     *
     * Route lama tetap dipertahankan.
     */
    public function nearby()
    {
        $laporan = Laporan::query()
            ->induk()
            ->aktif()
            ->terverifikasi()
            ->with([
                'kategoriHambatan',
                'wilayah',
                'foto',
            ])
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();

        $laporanData = $laporan
            ->map(function ($item) {
                return [
                    'id' =>
                        $item->id,

                    'judul' =>
                        $item->judul,

                    'latitude' =>
                        (float) $item->latitude,

                    'longitude' =>
                        (float) $item->longitude,

                    'kategori' =>
                        $item->kategoriHambatan?->nama,

                    'alamat' =>
                        $item->alamat_lengkap,

                    'tingkat_prioritas' =>
                        $item->tingkat_prioritas,

                    'skor_prioritas' =>
                        $item->skor_prioritas !== null
                            ? (float) $item->skor_prioritas
                            : null,
                ];
            })
            ->values();

        return view(
            'Peta.nearby',
            compact('laporanData')
        );
    }
}