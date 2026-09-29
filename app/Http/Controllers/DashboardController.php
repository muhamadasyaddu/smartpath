<?php

namespace App\Http\Controllers;

use App\Models\Audit;
use App\Models\KategoriHambatan;
use App\Models\Laporan;
use App\Models\RencanaPerbaikan;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Dashboard administrator.
     *
     * Dashboard admin dipisahkan dari dashboard dinas
     * agar perubahan UI administrator tidak mempengaruhi
     * alur dashboard dinas.
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $status = $request->query('status');

        $allowedStatuses = [
            'menunggu_verifikasi',
            'diverifikasi',
            'ditolak',
            'dalam_perbaikan',
            'selesai',
            'diarsipkan',
        ];

        /*
         * Query dasar:
         * - hanya laporan induk
         * - soft deleted otomatis tidak ikut
         */
        $laporanDasar = Laporan::query()
            ->induk()
            ->aktif();

        /*
         * Query khusus tabel laporan terbaru.
         */
        $laporanTerfilter = clone $laporanDasar;

        if ($search !== '') {
            $laporanTerfilter->where(function ($query) use ($search) {
                $query->where('kode_laporan', 'like', "%{$search}%")
                    ->orWhere('judul', 'like', "%{$search}%")
                    ->orWhere('alamat_lengkap', 'like', "%{$search}%");
            });
        }

        if (in_array($status, $allowedStatuses, true)) {
            $laporanTerfilter->status($status);
        }

        /*
         * ==========================================================
         * KPI UTAMA
         * ==========================================================
         */

        $totalLaporan = (clone $laporanDasar)->count();

        $menungguVerifikasi = (clone $laporanDasar)
            ->status('menunggu_verifikasi')
            ->count();

        /*
         * Terverifikasi mencakup:
         * diverifikasi
         * dalam_perbaikan
         * selesai
         */
        $laporanTerverifikasi = (clone $laporanDasar)
            ->terverifikasi()
            ->count();

        $dalamPerbaikan = (clone $laporanDasar)
            ->status('dalam_perbaikan')
            ->count();

        $selesai = (clone $laporanDasar)
            ->status('selesai')
            ->count();

        $prioritasTinggiCount = (clone $laporanDasar)
            ->whereNotNull('skor_prioritas')
            ->where('skor_prioritas', '>=', 70)
            ->count();

        /*
         * ==========================================================
         * PERUBAHAN 7 HARI
         * ==========================================================
         */

        $sekarang = now();

        $awalPeriode = $sekarang->copy()->subDays(7);

        $awalSebelumnya = $sekarang->copy()->subDays(14);

        $laporanMingguIni = (clone $laporanDasar)
            ->where('created_at', '>=', $awalPeriode)
            ->count();

        $laporanMingguSebelumnya = (clone $laporanDasar)
            ->whereBetween(
                'created_at',
                [
                    $awalSebelumnya,
                    $awalPeriode
                ]
            )
            ->count();

        $persentasePerubahan = $laporanMingguSebelumnya > 0
            ? round(
                (
                    ($laporanMingguIni - $laporanMingguSebelumnya)
                    / $laporanMingguSebelumnya
                ) * 100
            )
            : null;

        /*
         * ==========================================================
         * TOP PRIORITAS
         * ==========================================================
         */

        $laporanPrioritasTinggi = (clone $laporanDasar)
            ->with([
                'kategoriHambatan',
                'wilayah',
                'pelapor',
                'foto'
            ])
            ->whereNotNull('skor_prioritas')
            ->where('skor_prioritas', '>=', 70)
            ->orderByDesc('skor_prioritas')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        /*
         * ==========================================================
         * LAPORAN TERBARU
         * ==========================================================
         */

        $laporanTerbaru = $laporanTerfilter
            ->with([
                'kategoriHambatan',
                'pelapor',
                'wilayah',
                'foto'
            ])
            ->orderByDesc('created_at')
            ->limit(4)
            ->get();

        /*
         * ==========================================================
         * KATEGORI
         * ==========================================================
         */

        $perKategori = KategoriHambatan::query()
            ->aktif()
            ->urutTampil()
            ->withCount([
                'laporan' => fn ($query) =>
                    $query->induk()->aktif(),
            ])
            ->get();

        /*
         * ==========================================================
         * DISTRIBUSI STATUS
         * ==========================================================
         */

        $statusChart = [
            'menunggu_verifikasi' => $menungguVerifikasi,

            'diverifikasi' => (clone $laporanDasar)
                ->status('diverifikasi')
                ->count(),

            'dalam_perbaikan' => $dalamPerbaikan,

            'selesai' => $selesai,

            'ditolak' => (clone $laporanDasar)
                ->status('ditolak')
                ->count(),
        ];

        /*
         * ==========================================================
         * TREN 7 HARI
         * ==========================================================
         */

        $trenRaw = (clone $laporanDasar)
            ->where(
                'created_at',
                '>=',
                now()->startOfDay()->subDays(6)
            )
            ->selectRaw(
                'DATE(created_at) as tanggal, COUNT(*) as jumlah'
            )
            ->groupByRaw('DATE(created_at)')
            ->orderBy('tanggal')
            ->pluck('jumlah', 'tanggal');

        $tren7Hari = collect(range(6, 0))
            ->map(function ($daysAgo) use ($trenRaw) {

                $date = now()
                    ->startOfDay()
                    ->subDays($daysAgo);

                $key = $date->format('Y-m-d');

                return [
                    'tanggal' => $key,
                    'label' => $date->format('d M'),
                    'jumlah' => (int) (
                        $trenRaw[$key] ?? 0
                    ),
                ];
            })
            ->values();

        /*
         * ==========================================================
         * DATA PETA LEAFLET
         * ==========================================================
         */

        $petaLaporan = (clone $laporanDasar)
            ->with([
                'kategoriHambatan',
                'wilayah',
                'foto'
            ])
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get()
            ->map(function (Laporan $laporan) {

                return [
                    'id' => $laporan->id,

                    'kode' => $laporan->kode_laporan,

                    'judul' => $laporan->judul,

                    'latitude' => (float) $laporan->latitude,

                    'longitude' => (float) $laporan->longitude,

                    'status' => $laporan->status,

                    'status_label' => $laporan->status_label,

                    'prioritas' => $laporan->tingkat_prioritas,

                    'skor_prioritas' =>
                        $laporan->skor_prioritas !== null
                            ? (float) $laporan->skor_prioritas
                            : null,

                    'kategori_id' =>
                        $laporan->kategori_hambatan_id,

                    'kategori' =>
                        $laporan->kategoriHambatan?->nama,

                    'warna' =>
                        $laporan->kategoriHambatan?->warna_penanda,

                    'alamat' =>
                        $laporan->alamat_lengkap,

                    'created_at' =>
                        $laporan->created_at?->toISOString(),
                ];
            })
            ->values();

        /*
         * ==========================================================
         * AKTIVITAS TERBARU
         * ==========================================================
         *
         * Hanya aktivitas yang berhubungan dengan laporan.
         * Login/logout tidak memenuhi panel aktivitas laporan.
         */

        $aktivitasTerbaru = Audit::query()
            ->where('tabel_terkait', 'laporan')
            ->with('pengguna')
            ->orderByDesc('created_at')
            ->limit(4)
            ->get();

        /*
         * Jumlah area/wilayah unik yang memiliki laporan.
         */

        $areaDipantau = (clone $laporanDasar)
            ->whereNotNull('wilayah_id')
            ->distinct()
            ->count('wilayah_id');

        /*
         * ==========================================================
         * KIRIM DATA KE DASHBOARD ADMIN
         * ==========================================================
         */

        return view('Dashboard.admin', [

            'statistik' => [

                'total' =>
                    $totalLaporan,

                'menunggu' =>
                    $menungguVerifikasi,

                'diverifikasi' =>
                    $laporanTerverifikasi,

                'dalam_perbaikan' =>
                    $dalamPerbaikan,

                'selesai' =>
                    $selesai,

                'kritis' =>
                    $prioritasTinggiCount,
            ],

            'totalLaporan' =>
                $totalLaporan,

            'menungguVerifikasi' =>
                $menungguVerifikasi,

            'laporanTerverifikasi' =>
                $laporanTerverifikasi,

            'dalamPerbaikan' =>
                $dalamPerbaikan,

            'selesai' =>
                $selesai,

            'prioritasTinggiCount' =>
                $prioritasTinggiCount,

            'persentasePerubahan' =>
                $persentasePerubahan,

            'laporanMingguIni' =>
                $laporanMingguIni,

            'laporanPrioritasTinggi' =>
                $laporanPrioritasTinggi,

            'laporanTerbaru' =>
                $laporanTerbaru,

            'perKategori' =>
                $perKategori,

            'statusChart' =>
                $statusChart,

            'tren7Hari' =>
                $tren7Hari,

            'petaLaporan' =>
                $petaLaporan,

            'aktivitasTerbaru' =>
                $aktivitasTerbaru,

            'areaDipantau' =>
                $areaDipantau,
        ]);
    }

    
    /**
     * Data real-time Dashboard Administrator.
     *
     * Endpoint ini digunakan oleh AJAX polling setiap 60 detik.
     * Tidak melakukan reload halaman.
     */
    public function live()
    {
        $laporanDasar = Laporan::query()
            ->induk()
            ->aktif();

        $total =
            (clone $laporanDasar)->count();

        $menunggu =
            (clone $laporanDasar)
                ->status('menunggu_verifikasi')
                ->count();

        $diverifikasi =
            (clone $laporanDasar)
                ->status('diverifikasi')
                ->count();

        $dalamPerbaikan =
            (clone $laporanDasar)
                ->status('dalam_perbaikan')
                ->count();

        $selesai =
            (clone $laporanDasar)
                ->status('selesai')
                ->count();

        $prioritasTinggi =
            (clone $laporanDasar)
                ->whereNotNull('skor_prioritas')
                ->where('skor_prioritas', '>=', 70)
                ->count();

        $areaDipantau =
            (clone $laporanDasar)
                ->whereNotNull('wilayah_id')
                ->distinct()
                ->count('wilayah_id');

        $petaLaporan =
            (clone $laporanDasar)
                ->with([
                    'kategoriHambatan',
                    'wilayah',
                    'foto',
                ])
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->orderByDesc('skor_prioritas')
                ->orderByDesc('created_at')
                ->get()
                ->map(function (Laporan $laporan) {

                    $fotoUtama =
                        $laporan->foto
                            ->firstWhere(
                                'adalah_utama',
                                true
                            )
                        ??
                        $laporan->foto
                            ->sortBy('urutan')
                            ->first();

                    return [
                        'id' =>
                            $laporan->id,

                        'kode' =>
                            $laporan->kode_laporan,

                        'judul' =>
                            $laporan->judul,

                        'latitude' =>
                            (float) $laporan->latitude,

                        'longitude' =>
                            (float) $laporan->longitude,

                        'status' =>
                            $laporan->status,

                        'status_label' =>
                            $laporan->status_label,

                        'skor_prioritas' =>
                            $laporan->skor_prioritas !== null
                                ? (float) $laporan->skor_prioritas
                                : null,

                        'prioritas' =>
                            $laporan->tingkat_prioritas,

                        'kategori_id' =>
                            $laporan->kategori_hambatan_id,

                        'kategori' =>
                            $laporan->kategoriHambatan?->nama,

                        'warna' =>
                            $laporan->kategoriHambatan?->warna_penanda
                            ?? '#64748b',

                        'alamat' =>
                            $laporan->alamat_lengkap,

                        'jumlah_pelapor' =>
                            (int) $laporan->jumlah_pelapor,

                        'foto_utama' =>
                            $fotoUtama?->url,

                        'created_at' =>
                            $laporan->created_at?->toISOString(),
                    ];
                })
                ->values();

        $topPrioritas =
            (clone $laporanDasar)
                ->with([
                    'kategoriHambatan',
                    'wilayah',
                    'foto',
                ])
                ->whereNotNull('skor_prioritas')
                ->orderByDesc('skor_prioritas')
                ->orderByDesc('created_at')
                ->limit(5)
                ->get()
                ->map(function (Laporan $laporan) {

                    $fotoUtama =
                        $laporan->foto
                            ->firstWhere(
                                'adalah_utama',
                                true
                            )
                        ??
                        $laporan->foto
                            ->sortBy('urutan')
                            ->first();

                    return [
                        'id' =>
                            $laporan->id,

                        'judul' =>
                            $laporan->judul,

                        'alamat' =>
                            $laporan->alamat_lengkap
                            ?:
                            $laporan->wilayah?->nama,

                        'skor' =>
                            (float) $laporan->skor_prioritas,

                        'jumlah_pelapor' =>
                            (int) $laporan->jumlah_pelapor,

                        'foto' =>
                            $fotoUtama?->url,

                        'kategori' =>
                            $laporan->kategoriHambatan?->nama,
                    ];
                })
                ->values();

        return response()->json([
            'generated_at' =>
                now()->toISOString(),

            'statistik' => [
                'total' =>
                    $total,

                'menunggu' =>
                    $menunggu,

                'diverifikasi' =>
                    $diverifikasi,

                'dalam_perbaikan' =>
                    $dalamPerbaikan,

                'selesai' =>
                    $selesai,

                'kritis' =>
                    $prioritasTinggi,
            ],

            'area_dipantau' =>
                $areaDipantau,

            'peta_laporan' =>
                $petaLaporan,

            'top_prioritas' =>
                $topPrioritas,
        ]);
    }
    
    /**
     * Dashboard DINAS.
     *
     * Jangan diarahkan ke index() karena index()
     * sekarang khusus administrator.
     */
   /**
 * Dashboard DINAS.
 *
 * Dashboard Dinas menampilkan:
 * - statistik laporan
 * - laporan prioritas tinggi
 * - laporan terbaru
 * - peta sebaran laporan
 * - ringkasan realisasi anggaran dari rencana perbaikan
 *
 * Data anggaran bukan anggaran resmi pemerintah.
 * Data berasal dari data simulasi/prototype pada rencana_perbaikan.
 */
public function indexDinas()
{
    /*
    |--------------------------------------------------------------------------
    | DATA DASAR LAPORAN
    |--------------------------------------------------------------------------
    */

    $laporanDasar = Laporan::query()
        ->induk()
        ->aktif();


    /*
    |--------------------------------------------------------------------------
    | STATISTIK DASHBOARD DINAS
    |--------------------------------------------------------------------------
    */

    $statistik = [

        'total' =>
            (clone $laporanDasar)
                ->count(),

        'menunggu' =>
            (clone $laporanDasar)
                ->status('menunggu_verifikasi')
                ->count(),

        'dalam_perbaikan' =>
            (clone $laporanDasar)
                ->status('dalam_perbaikan')
                ->count(),

        'selesai' =>
            (clone $laporanDasar)
                ->status('selesai')
                ->count(),

        'kritis' =>
            (clone $laporanDasar)
                ->whereNotNull('skor_prioritas')
                ->where('skor_prioritas', '>=', 70)
                ->count(),
    ];


    /*
    |--------------------------------------------------------------------------
    | LAPORAN PRIORITAS TINGGI
    |--------------------------------------------------------------------------
    */

    $laporanPrioritasTinggi = (clone $laporanDasar)
        ->with([
            'kategoriHambatan',
            'wilayah',
            'pelapor',
            'foto',
            'fasilitasTerdekat',
        ])
        ->whereNotNull('skor_prioritas')
        ->where('skor_prioritas', '>=', 70)
        ->orderByDesc('skor_prioritas')
        ->orderByDesc('created_at')
        ->limit(10)
        ->get();


    /*
    |--------------------------------------------------------------------------
    | LAPORAN TERBARU
    |--------------------------------------------------------------------------
    */

    $laporanTerbaru = (clone $laporanDasar)
        ->with([
            'kategoriHambatan',
            'pelapor',
            'wilayah',
            'foto',
        ])
        ->orderByDesc('created_at')
        ->limit(10)
        ->get();


    /*
    |--------------------------------------------------------------------------
    | DATA PETA
    |--------------------------------------------------------------------------
    */

    $petaLaporan = (clone $laporanDasar)
        ->whereNotNull('latitude')
        ->whereNotNull('longitude')
        ->get([
            'id',
            'kode_laporan',
            'judul',
            'latitude',
            'longitude',
            'status',
            'skor_prioritas',
        ]);


    /*
    |--------------------------------------------------------------------------
    | DATA REALISASI ANGGARAN
    |--------------------------------------------------------------------------
    |
    | Sumber:
    | rencana_perbaikan
    |
    | estimasi_anggaran   = nilai estimasi/pagu prototype
    | realisasi_anggaran  = nilai realisasi prototype
    |
    | Data dikelompokkan berdasarkan kategori hambatan.
    |
    */

    $rencanaAnggaran = RencanaPerbaikan::query()
        ->with([
            'laporan.kategoriHambatan',
        ])
        ->whereHas('laporan', function ($query) {

            $query
                ->induk()
                ->aktif();

        })
        ->get([
            'id',
            'laporan_id',
            'estimasi_anggaran',
            'realisasi_anggaran',
        ]);


    /*
    |--------------------------------------------------------------------------
    | KELOMPOKKAN BERDASARKAN KATEGORI
    |--------------------------------------------------------------------------
    */

    $dataAnggaran = $rencanaAnggaran
        ->groupBy(function ($rencana) {

            return optional(
                optional($rencana->laporan)
                    ->kategoriHambatan
            )->nama ?? 'Kategori Lainnya';

        })
        ->map(function ($items, $namaKategori) {

            $pagu = (float) $items
                ->sum('estimasi_anggaran');

            $realisasi = (float) $items
                ->sum('realisasi_anggaran');

            return (object) [

                'nama_kategori' =>
                    $namaKategori,

                'pagu' =>
                    $pagu,

                'realisasi' =>
                    $realisasi,
            ];

        })
        ->values();


    /*
    |--------------------------------------------------------------------------
    | TOTAL ANGGARAN
    |--------------------------------------------------------------------------
    */

    $totalAnggaran = (float) $dataAnggaran
        ->sum('pagu');


    $totalRealisasi = (float) $dataAnggaran
        ->sum('realisasi');


    $persentaseRealisasi = $totalAnggaran > 0
        ? round(
            ($totalRealisasi / $totalAnggaran) * 100,
            1
        )
        : 0;


    /*
    |--------------------------------------------------------------------------
    | KIRIM DATA KE VIEW
    |--------------------------------------------------------------------------
    */

    return view(
        'Dashboard.dinas',
        compact(
            'statistik',
            'laporanPrioritasTinggi',
            'laporanTerbaru',
            'petaLaporan',
            'dataAnggaran',
            'totalAnggaran',
            'totalRealisasi',
            'persentaseRealisasi'
        )
    );
}

    /**
     * Dashboard warga.
     */
    public function indexWarga()
    {
        $user = auth()->user();

        $jumlahLaporan = Laporan::where(
            'pelapor_id',
            $user->id
        )->count();

        return view(
            'Dashboard.warga',
            compact(
                'user',
                'jumlahLaporan'
            )
        );
    }

    /**
     * Endpoint chart lama.
     *
     * Tetap dipertahankan agar route yang sudah ada
     * tidak rusak.
     */
    public function getChartData(Request $request)
    {
        $type = $request->query(
            'type',
            'status'
        );

        return match ($type) {

            'status' =>
                $this->getStatusChartData(),

            'kategori' =>
                $this->getKategoriChartData(),

            'tren' =>
                $this->getTrenChartData(),

            'prioritas' =>
                $this->getPrioritasChartData(),

            default =>
                response()->json([]),
        };
    }

    /**
     * Chart status.
     */
    protected function getStatusChartData()
    {
        $base = Laporan::query()
            ->induk()
            ->aktif();

        return response()->json([

            [
                'status' => 'Menunggu Verifikasi',
                'count' =>
                    (clone $base)
                        ->status('menunggu_verifikasi')
                        ->count(),
            ],

            [
                'status' => 'Terverifikasi',
                'count' =>
                    (clone $base)
                        ->status('diverifikasi')
                        ->count(),
            ],

            [
                'status' => 'Dalam Perbaikan',
                'count' =>
                    (clone $base)
                        ->status('dalam_perbaikan')
                        ->count(),
            ],

            [
                'status' => 'Selesai',
                'count' =>
                    (clone $base)
                        ->status('selesai')
                        ->count(),
            ],

            [
                'status' => 'Ditolak',
                'count' =>
                    (clone $base)
                        ->status('ditolak')
                        ->count(),
            ],
        ]);
    }

    /**
     * Chart kategori.
     */
    protected function getKategoriChartData()
    {
        $data = KategoriHambatan::query()
            ->aktif()
            ->urutTampil()
            ->withCount([
                'laporan' =>
                    fn ($query) =>
                        $query->induk()->aktif(),
            ])
            ->get()
            ->map(fn ($kategori) => [

                'nama' =>
                    $kategori->nama,

                'count' =>
                    (int) $kategori->laporan_count,

                'warna' =>
                    $kategori->warna_penanda,
            ]);

        return response()->json($data);
    }

    /**
     * Chart tren 30 hari.
     */
    protected function getTrenChartData()
    {
        $data = Laporan::query()
            ->induk()
            ->aktif()
            ->selectRaw(
                'DATE(created_at) as tanggal, COUNT(*) as jumlah'
            )
            ->where(
                'created_at',
                '>=',
                now()->subDays(30)
            )
            ->groupByRaw('DATE(created_at)')
            ->orderBy('tanggal')
            ->get();

        return response()->json($data);
    }

    /**
     * Chart prioritas.
     */
    protected function getPrioritasChartData()
    {
        $base = Laporan::query()
            ->induk()
            ->aktif()
            ->terverifikasi();

        return response()->json([

            [
                'tingkat' => 'Tinggi',
                'count' =>
                    (clone $base)
                        ->where('skor_prioritas', '>=', 70)
                        ->count(),
                'warna' => '#DC2626',
            ],

            [
                'tingkat' => 'Sedang',
                'count' =>
                    (clone $base)
                        ->whereBetween(
                            'skor_prioritas',
                            [40, 69.99]
                        )
                        ->count(),
                'warna' => '#D97706',
            ],

            [
                'tingkat' => 'Rendah',
                'count' =>
                    (clone $base)
                        ->where(
                            'skor_prioritas',
                            '<',
                            40
                        )
                        ->count(),
                'warna' => '#059669',
            ],
        ]);
    }
}