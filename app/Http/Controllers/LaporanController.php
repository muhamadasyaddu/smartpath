<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLaporanRequest;
use App\Http\Requests\UpdateLaporanRequest;
use App\Models\Audit;
use App\Models\FasilitasPublik;
use App\Models\FotoLaporan;
use App\Models\KategoriHambatan;
use App\Models\KonfigurasiSistem;
use App\Models\Laporan;
use App\Models\Notifikasi;
use App\Models\RiwayatStatusLaporan;
use App\Models\Wilayah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\User;
use Throwable;

class LaporanController extends Controller
{
   
    public function index(Request $request)
    {

        // Halaman administrasi menggunakan tampilan khusus.
        if (auth()->user()->isAdmin()) {
            return redirect()->route('admin.laporan.index');
        }
        // Admin melihat seluruh laporan, termasuk laporan kandidat duplikasi.
        // Warga hanya melihat laporan yang dibuat oleh akunnya sendiri.
        $query = Laporan::query()
            ->with([
                'kategoriHambatan',
                'pelapor',
                'wilayah',
                'fotoLaporan',
                'laporanInduk',
            ]);

        if (!auth()->user()->isAdmin()) {
            $query->where('pelapor_id', auth()->id());
        }

        if ($request->filled('status')) {
            $query->where(
                'status',
                $request->string('status')->toString()
            );
        }

        if ($request->filled('kategori')) {
            $query->where(
                'kategori_hambatan_id',
                $request->integer('kategori')
            );
        }

        $laporan = $query
            ->orderByDesc('created_at')
            ->paginate(12)
            ->withQueryString();

        // Hitungan status menggunakan cakupan data yang sama dengan daftar.
        $countQuery = Laporan::query();

        if (!auth()->user()->isAdmin()) {
            $countQuery->where('pelapor_id', auth()->id());
        }

        $statusCounts = [
            'semua' => (clone $countQuery)->count(),
            'menunggu_verifikasi' => (clone $countQuery)
                ->where('status', 'menunggu_verifikasi')->count(),
            'diverifikasi' => (clone $countQuery)
                ->where('status', 'diverifikasi')->count(),
            'dalam_perbaikan' => (clone $countQuery)
                ->where('status', 'dalam_perbaikan')->count(),
            'selesai' => (clone $countQuery)
                ->where('status', 'selesai')->count(),
            'ditolak' => (clone $countQuery)
                ->where('status', 'ditolak')->count(),
        ];

        return view(
            'Laporan.index',
            compact('laporan', 'statusCounts')
        );
    }

    
/**
 * Daftar laporan khusus administrator.
 *
 * Menampilkan laporan, alamat, status, dan jarak dari
 * laporan pembanding terdekat dengan kategori yang sama.
 *
 * Tidak mengubah keputusan verifikasi secara otomatis.
 */
public function adminIndex(Request $request)
{
    abort_unless(
        auth()->user() && auth()->user()->isAdmin(),
        403
    );

    $statusOptions = [
        'menunggu_verifikasi',
        'diverifikasi',
        'ditolak',
        'dalam_perbaikan',
        'selesai',
        'diarsipkan',
    ];

    $query = Laporan::query()->with([
        'kategoriHambatan',
        'pelapor',
        'wilayah',
        'fotoLaporan',
        'laporanInduk',
    ]);

    // Filter status hanya menerima status yang dikenal.
    if (
        $request->filled('status')
        && in_array(
            $request->query('status'),
            $statusOptions,
            true
        )
    ) {
        $query->where(
            'status',
            $request->query('status')
        );
    }

    // Pencarian kode, judul, alamat, dan deskripsi.
    if ($request->filled('q')) {
        $keyword = trim(
            (string) $request->query('q')
        );

        $query->where(function ($subQuery) use ($keyword) {
            $subQuery
                ->where('kode_laporan', 'like', "%{$keyword}%")
                ->orWhere('judul', 'like', "%{$keyword}%")
                ->orWhere('alamat_lengkap', 'like', "%{$keyword}%")
                ->orWhere('deskripsi', 'like', "%{$keyword}%");
        });
    }

    // Filter kategori harus berupa ID numerik.
    if (
        $request->filled('kategori')
        && ctype_digit((string) $request->query('kategori'))
    ) {
        $query->where(
            'kategori_hambatan_id',
            (int) $request->query('kategori')
        );
    }

    $laporan = $query
        ->orderByDesc('created_at')
        ->paginate(15)
        ->withQueryString();

    // Radius diambil dari konfigurasi aktif yang digunakan
    // oleh mekanisme deduplikasi proyek.
    $pengaturan = \App\Models\PengaturanPrioritas::getActive();

    $radiusMeter = (int) (
        $pengaturan?->radius_deduplikasi_m
        ?? KonfigurasiSistem::getValue(
            'radius_deduplikasi_meter',
            50
        )
    );

    $kategoriHambatan = KategoriHambatan::aktif()
        ->urutTampil()
        ->get();

    /*
     * Ambil laporan induk yang masih aktif secara operasional
     * untuk kategori yang terdapat pada halaman ini.
     *
     * Anak laporan tidak dijadikan titik induk pembanding
     * agar satu kelompok laporan tidak dihitung berulang.
     */
    $kategoriIds = $laporan->getCollection()
        ->pluck('kategori_hambatan_id')
        ->filter()
        ->unique()
        ->values();

    $laporanPembanding = Laporan::query()
        ->whereNull('laporan_induk_id')
        ->whereIn(
            'status',
            [
                'menunggu_verifikasi',
                'diverifikasi',
                'dalam_perbaikan',
            ]
        )
        ->whereIn('kategori_hambatan_id', $kategoriIds)
        ->get([
            'id',
            'kode_laporan',
            'judul',
            'kategori_hambatan_id',
            'latitude',
            'longitude',
            'alamat_lengkap',
            'status',
        ]);

    /*
     * Cari laporan pembanding terdekat menggunakan Haversine.
     * Perhitungan dilakukan pada data halaman yang sedang
     * ditampilkan, bukan pada seluruh data setiap kali render.
     */
    foreach ($laporan as $item) {
        $terdekat = null;
        $jarakTerdekat = INF;

        foreach ($laporanPembanding as $kandidat) {
            // Jangan membandingkan laporan dengan dirinya sendiri.
            if ((int) $kandidat->id === (int) $item->id) {
                continue;
            }

            $jarak = $this->calculateHaversineDistance(
                (float) $item->latitude,
                (float) $item->longitude,
                (float) $kandidat->latitude,
                (float) $kandidat->longitude
            );

            if ($jarak < $jarakTerdekat) {
                $jarakTerdekat = $jarak;
                $terdekat = $kandidat;
            }
        }

        $item->setRelation(
            'laporanPembandingTerdekat',
            $terdekat
        );

        $item->setAttribute(
            'jarak_pembanding_meter',
            $terdekat
                ? round($jarakTerdekat, 2)
                : null
        );

        $item->setAttribute(
            'dalam_radius_50m',
            $terdekat !== null
                && $jarakTerdekat <= $radiusMeter
        );
    }

    // Statistik keseluruhan, tidak terpengaruh filter halaman.
    $countQuery = Laporan::query();

    $statusCounts = [
        'semua' => (clone $countQuery)->count(),

        'menunggu_verifikasi' => (clone $countQuery)
            ->where('status', 'menunggu_verifikasi')
            ->count(),

        'diverifikasi' => (clone $countQuery)
            ->where('status', 'diverifikasi')
            ->count(),

        'dalam_perbaikan' => (clone $countQuery)
            ->where('status', 'dalam_perbaikan')
            ->count(),

        'selesai' => (clone $countQuery)
            ->where('status', 'selesai')
            ->count(),

        'ditolak' => (clone $countQuery)
            ->where('status', 'ditolak')
            ->count(),
    ];

    // Data peta hanya memuat informasi yang diperlukan.
    $mapReports = $laporan->getCollection()
        ->map(function ($item) {
            return [
                'id' => $item->id,
                'kode' => $item->kode_laporan,
                'judul' => $item->judul,

                'latitude' => (float) $item->latitude,
                'longitude' => (float) $item->longitude,

                'alamat' => $item->alamat_lengkap
                    ?: ($item->wilayah?->nama ?? 'Alamat belum tersedia'),

                'status' => $item->status_label,

                'skor' => $item->skor_prioritas !== null
                    ? (float) $item->skor_prioritas
                    : null,

                'prioritas' => $item->tingkat_prioritas,

                'jarak' => $item->jarak_pembanding_meter,

                'dalam_radius' => $item->dalam_radius_50m,

                'kode_pembanding' =>
                    $item->laporanPembandingTerdekat?->kode_laporan,
            ];
        })
        ->values();

    return view(
        'Admin.laporan.index',
        compact(
            'laporan',
            'kategoriHambatan',
            'radiusMeter',
            'statusCounts',
            'mapReports'
        )
    );
}


    /**
     * Form laporan baru.
     */
    public function create()
    {
        $kategoriHambatan = KategoriHambatan::aktif()
            ->urutTampil()
            ->get();

        $maxFoto = (int) KonfigurasiSistem::getValue(
            'max_foto_per_laporan',
            5
        );

        return view(
            'Laporan.create',
            compact('kategoriHambatan', 'maxFoto')
        );
    }

    /**
     * Simpan laporan baru.
     *
     * Seluruh proses dibuat atomik: laporan, foto, riwayat,
     * notifikasi, dan audit berhasil bersama-sama atau dibatalkan.
     */
    public function store(StoreLaporanRequest $request)
    {

        

        $validated = $request->validated();
        $storedPaths = [];

        try {
            $laporan = DB::transaction(function () use (
                $request,
                $validated,
                &$storedPaths
            ) {
                $laporanInduk = $this->findDuplicateReport(
                    (float) $validated['latitude'],
                    (float) $validated['longitude'],
                    (int) $validated['kategori_hambatan_id']
                );

                
                $fasilitasTerdekat = FasilitasPublik::getNearest(
                    (float) $validated['latitude'],
                    (float) $validated['longitude'],
                    PHP_FLOAT_MAX
                );

                $jarakFasilitas = $fasilitasTerdekat
                    ? $fasilitasTerdekat->calculateDistance(
                        (float) $validated['latitude'],
                        (float) $validated['longitude']
                    )
                    : null;


                $laporan = Laporan::create([
                    'pelapor_id' => auth()->id(),
                    'kategori_hambatan_id' => $validated['kategori_hambatan_id'],
                    'wilayah_id' => $validated['wilayah_id'],
                    'laporan_induk_id' => $laporanInduk?->id,
                    'latitude' => $validated['latitude'],
                    'longitude' => $validated['longitude'],
                    'alamat_lengkap' => $validated['alamat_lengkap'] ?? null,
                    'jarak_fasilitas_meter' => $jarakFasilitas !== null
                        ? round($jarakFasilitas, 2)
                        : null,
                    'judul' => $validated['judul'],
                    'deskripsi' => $validated['deskripsi'],
                    'status' => 'menunggu_verifikasi',
                    'jumlah_pelapor' => 1,
                    'sumber_koordinat' => $validated['sumber_koordinat'],
                    'platform_pelapor' => 'web',
                    'fasilitas_terdekat_id' => $fasilitasTerdekat?->id,
                        
                ]);

                /*
                 * Foto disimpan di storage/app/public/laporan/{id}.
                 * Gunakan extension hasil deteksi MIME, bukan extension
                 * mentah dari nama file pengguna.
                 */
                foreach ($request->file('foto', []) as $index => $file) {
                    $extension = $file->extension();
                    $namaFile = (string) Str::uuid() . '.' . $extension;

                    $pathFile = $file->storeAs(
                        'laporan/' . $laporan->id,
                        $namaFile,
                        'public'
                    );

                    if ($pathFile === false) {
                        throw new \RuntimeException(
                            'Foto laporan gagal disimpan.'
                        );
                    }

                    $storedPaths[] = $pathFile;

                    $dimensions = @getimagesize($file->getRealPath());

                    FotoLaporan::create([
                        'laporan_id' => $laporan->id,
                        'nama_file' => $namaFile,
                        'nama_asli' => Str::limit(
                            $file->getClientOriginalName(),
                            255,
                            ''
                        ),
                        'path_file' => $pathFile,
                        'mime_type' => $file->getMimeType(),
                        'ukuran_byte' => $file->getSize(),
                        'lebar_px' => $dimensions[0] ?? null,
                        'tinggi_px' => $dimensions[1] ?? null,
                        'adalah_utama' => $index === 0,
                        'urutan' => $index,
                    ]);
                }

                /*
                 * Jika merupakan child, hitung ulang jumlah pelapor
                 * unik pada parent. Ini mencegah satu akun menaikkan
                 * jumlah pelapor berkali-kali.
                 */
                if ($laporanInduk) {
                    $this->syncParentReporterCount($laporanInduk);
                }

                RiwayatStatusLaporan::create([
                    'laporan_id' => $laporan->id,
                    'diubah_oleh_id' => auth()->id(),
                    'status_sebelumnya' => null,
                    'status_baru' => 'menunggu_verifikasi',
                    'keterangan' => 'Laporan baru dibuat.',
                ]);

                Notifikasi::create([
                    'penerima_id' => auth()->id(),
                    'laporan_id' => $laporan->id,
                    'jenis' => 'laporan_diterima',
                    'judul' => 'Laporan Diterima',
                    'pesan' => "Laporan {$laporan->kode_laporan} telah diterima dan menunggu verifikasi.",
                    'tautan' => route('laporan.show', $laporan),
                ]);

                /*
                * Notifikasi administrator.
                *
                * Administrator perlu mengetahui bahwa ada
                * laporan baru yang masuk ke antrean verifikasi.
                */
                $adminIds = User::query()
                    ->whereIn('peran', [
                        'administrator',
                        'admin',
                    ])
                    ->where('aktif', true)
                    ->pluck('id');

                foreach ($adminIds as $adminId) {

                    Notifikasi::create([
                        'penerima_id' => $adminId,
                        'laporan_id' => $laporan->id,
                        'jenis' => 'sistem',
                        'judul' => 'Laporan Baru Masuk',
                        'pesan' =>
                            "Laporan {$laporan->kode_laporan} "
                            . "menunggu verifikasi administrator.",
                        'tautan' =>
                            route(
                                'admin.verifikasi.show',
                                $laporan
                            ),
                    ]);
                }

                if ($laporanInduk) {
                    Notifikasi::create([
                        'penerima_id' => auth()->id(),
                        'laporan_id' => $laporan->id,
                        'jenis' => 'duplikat_digabung',
                        'judul' => 'Laporan Terdeteksi Duplikat',
                        'pesan' => "Laporan Anda terhubung dengan laporan induk {$laporanInduk->kode_laporan} karena berada pada kategori dan radius yang sama.",
                        'tautan' => route('laporan.show', $laporan),
                    ]);
                }

                Audit::log(
                    auth()->id(),
                    'buat_laporan',
                    'laporan',
                    $laporan->id,
                    null,
                    $laporan->toArray(),
                    "Laporan baru: {$laporan->kode_laporan}"
                );

                return $laporan;
            });

            return redirect()
                ->route('laporan.show', $laporan)
                ->with(
                    'sukses',
                    'Laporan berhasil dikirim dan menunggu verifikasi.'
                );
        } catch (Throwable $e) {

    foreach ($storedPaths as $path) {

        Storage::disk('public')
            ->delete($path);
    }

    report($e);

    return back()
        ->withInput()
        ->withErrors([
            'laporan' =>
                'Laporan belum dapat disimpan. Silakan coba lagi.',
        ]);
    }
    }

    /**
     * Unduh ringkasan laporan untuk Dinas.
     * Dipertahankan untuk kompatibilitas tombol pada header Dinas.
     */
   
       public function unduh()
{
    abort_unless(auth()->user()->isDinas(), 403);

    $laporan = Laporan::query()
        ->induk()
        ->with('kategoriHambatan')
        ->orderByDesc('created_at')
        ->get();

    return response()->streamDownload(function () use ($laporan) {

        $handle = fopen('php://output', 'w');

        fputcsv($handle, [
            'Kode',
            'Judul',
            'Kategori',
            'Status',
            'Prioritas',
            'Jumlah Pelapor',
            'Latitude',
            'Longitude',
            'Tanggal',
        ], ';');

        foreach ($laporan as $item) {

            fputcsv($handle, [
                $item->kode_laporan,
                $item->judul,
                $item->kategoriHambatan?->nama ?? '',
                $item->status_label,
                $item->skor_prioritas !== null
                    ? number_format((float) $item->skor_prioritas, 2, '.', '')
                    : '',
                $item->jumlah_pelapor,
                $item->latitude,
                $item->longitude,
                $item->created_at?->format('Y-m-d H:i:s'),
            ], ';');
        }

        fclose($handle);

    }, 'smartpath-laporan-' . now()->format('Y-m-d-His') . '.csv', [
        'Content-Type' => 'text/csv; charset=UTF-8',
    ]);
}


        
        public function unduhPdf($id)
        {
            $user = auth()->user();

            abort_unless($user && $user->isDinas(), 403);

            $laporan = Laporan::query()
                ->with('kategoriHambatan')
                ->findOrFail($id);

            $pdf = Pdf::loadView(
                'Dinas.laporan-pdf',
                compact('laporan')
            );

            return $pdf->download(
                'laporan-' . $laporan->kode_laporan . '.pdf'
            );
        }

            
        /**
         * Detail laporan.
         */
        public function show(Laporan $laporan)
        {
            $this->authorizeView($laporan);

            $laporan->load([
                'kategoriHambatan',
                'pelapor',
                'wilayah',
                'fotoLaporan',
                'verifikasi.admin',
                'riwayatStatus.diubahOleh',
                'laporanInduk',
                'laporanAnak.pelapor',
                'fasilitasTerdekat',
            ]);

            // Fallback untuk laporan lama yang belum memiliki
            // fasilitas terdekat tersimpan.
            $fasilitas = $laporan->fasilitasTerdekat;

            if (!$fasilitas) {
                $fasilitas = FasilitasPublik::getNearest(
                    (float) $laporan->latitude,
                    (float) $laporan->longitude,
                    PHP_FLOAT_MAX
                );

                if ($fasilitas) {
                    // Hanya untuk tampilan; tidak menulis ulang database.
                    $laporan->setRelation(
                        'fasilitasTerdekat',
                        $fasilitas
                    );
                }
            }

            // Gunakan jarak tersimpan. Jika belum ada, hitung jarak
            // berdasarkan koordinat fasilitas yang ditampilkan.
            if ($fasilitas && $laporan->jarak_fasilitas_meter === null) {
                $laporan->setAttribute(
                    'jarak_fasilitas_meter',
                    round(
                        $fasilitas->calculateDistance(
                            (float) $laporan->latitude,
                            (float) $laporan->longitude
                        ),
                        2
                    )
                );
            }

            return view('Laporan.show', compact('laporan'));
        }


    /**
     * Form edit.
     */
    public function edit(Laporan $laporan)
    {
        $this->authorizeEdit($laporan);

        $kategoriHambatan = KategoriHambatan::aktif()
            ->urutTampil()
            ->get();

        $wilayahList = Wilayah::aktif()
            ->level('kecamatan')
            ->orderBy('nama')
            ->get();

        return view(
            'Laporan.edit',
            compact('laporan', 'kategoriHambatan', 'wilayahList')
        );
    }

    /**
     * Update data isi laporan.
     *
     * Status tidak pernah boleh diubah oleh warga melalui endpoint ini.
     */
    public function update(
        UpdateLaporanRequest $request,
        Laporan $laporan
    ) {
        $this->authorizeEdit($laporan);

        if ($laporan->status !== 'menunggu_verifikasi') {
            return back()->with(
                'galat',
                'Laporan hanya dapat diubah selama masih menunggu verifikasi.'
            );
        }

        $dataLama = $laporan->toArray();

        $laporan->update($request->validated());

        Audit::log(
            auth()->id(),
            'ubah_laporan',
            'laporan',
            $laporan->id,
            $dataLama,
            $laporan->fresh()->toArray(),
            "Laporan diubah: {$laporan->kode_laporan}"
        );

        return redirect()
            ->route('laporan.show', $laporan)
            ->with('sukses', 'Laporan berhasil diperbarui.');
    }

    /**
     * Soft delete hanya untuk laporan milik sendiri yang masih pending.
     */
    public function destroy(Laporan $laporan)
    {
        $this->authorizeEdit($laporan);

        if ($laporan->status !== 'menunggu_verifikasi') {
            return back()->with(
                'galat',
                'Laporan hanya dapat dihapus selama masih menunggu verifikasi.'
            );
        }

        $dataLama = $laporan->toArray();

        /*
        * Simpan ID parent sebelum soft delete.
        * Ini penting karena setelah delete(), model tetap memiliki
        * nilai atribut tetapi kita tidak perlu bergantung pada relasi.
        */
        $laporanIndukId = $laporan->laporan_induk_id;

        $laporan->delete();

        /*
        * Jika child dihapus, jumlah pelapor parent harus disinkronkan.
        */
        if ($laporanIndukId) {
            $parent = Laporan::find($laporanIndukId);

            if ($parent) {
                $this->syncParentReporterCount($parent);
            }
        }

        Audit::log(
            auth()->id(),
            'hapus_laporan',
            'laporan',
            $laporan->id,
            $dataLama,
            null,
            "Laporan dihapus: {$laporan->kode_laporan}"
        );

        return redirect()
            ->route('laporan.index')
            ->with('sukses', 'Laporan berhasil dihapus.');
    }

    /**
     * Detail laporan untuk kebutuhan operasional.
     *
     * Digunakan oleh:
     * - Administrator
     * - Dinas
     * - Pemilik laporan
     *
     * Endpoint tetap menggunakan authorization agar
     * pengguna biasa tidak dapat melihat laporan milik orang lain.
     */
    public function showDetail(Laporan $laporan)
    {
        $this->authorizeView($laporan);

        $laporan->load([
            'kategoriHambatan',
            'pelapor',
            'wilayah',
            'fotoLaporan',
            'verifikasi.admin',
            'riwayatStatus.diubahOleh',
            'laporanInduk',
            'laporanAnak.pelapor',
            'fasilitasTerdekat',
            'rencanaPerbaikan',
        ]);

        return view(
            'Dinas.laporan-detail',
            compact('laporan')
        );
    }


    /**
     * Cari parent aktif dengan kategori sama dalam radius deduplikasi.
     *
     * Radius 50 meter adalah parameter MVP sesuai tugas Sprint 2
     * dan bagian implementasi proposal.
     */
    protected function findDuplicateReport(
        float $latitude,
        float $longitude,
        int $kategoriHambatanId
    ): ?Laporan {
        $pengaturan = \App\Models\PengaturanPrioritas::getActive();

        $radiusMeter = (int) (
            $pengaturan?->radius_deduplikasi_m
            ?? KonfigurasiSistem::getValue(
                'radius_deduplikasi_meter',
                50
            )
        );

                $latitudeDelta = $radiusMeter / 111320;
        $cosLatitude = max(
            cos(deg2rad($latitude)),
            0.01
        );
        $longitudeDelta = $radiusMeter / (111320 * $cosLatitude);

        $candidates = Laporan::query()
            ->induk()
            ->where('kategori_hambatan_id', $kategoriHambatanId)
            ->whereIn('status', [
                'menunggu_verifikasi',
                'diverifikasi',
                'dalam_perbaikan',
            ])
            ->whereBetween('latitude', [
                $latitude - $latitudeDelta,
                $latitude + $latitudeDelta,
            ])
            ->whereBetween('longitude', [
                $longitude - $longitudeDelta,
                $longitude + $longitudeDelta,
            ])
            ->orderBy('created_at')
            ->lockForUpdate()
            ->get();

        $nearest = null;
        $nearestDistance = INF;

        foreach ($candidates as $candidate) {
            $distance = $this->calculateHaversineDistance(
                $latitude,
                $longitude,
                (float) $candidate->latitude,
                (float) $candidate->longitude
            );

            if (
                $distance <= $radiusMeter
                && $distance < $nearestDistance
            ) {
                $nearest = $candidate;
                $nearestDistance = $distance;
            }
        }

        return $nearest;
    }

    /**
     * Jumlah pelapor unik pada satu kelompok laporan.
     */
    protected function syncParentReporterCount(Laporan $parent): void
    {
        $pelaporIds = Laporan::query()
            ->where(function ($query) use ($parent) {
                $query
                    ->where('id', $parent->id)
                    ->orWhere('laporan_induk_id', $parent->id);
            })
            ->whereNotIn('status', ['ditolak', 'diarsipkan'])
            ->pluck('pelapor_id')
            ->unique();

        $parent->update([
            'jumlah_pelapor' => max(1, $pelaporIds->count()),
        ]);
    }

    /**
     * Haversine distance dalam meter.
     */
    protected function calculateHaversineDistance(
        float $lat1,
        float $lng1,
        float $lat2,
        float $lng2
    ): float {
        $earthRadius = 6371000;

        $latFrom = deg2rad($lat1);
        $latTo = deg2rad($lat2);
        $lngFrom = deg2rad($lng1);
        $lngTo = deg2rad($lng2);

        $latDelta = $latTo - $latFrom;
        $lngDelta = $lngTo - $lngFrom;

        $a =
            sin($latDelta / 2) ** 2
            + cos($latFrom)
            * cos($latTo)
            * sin($lngDelta / 2) ** 2;

        $a = min(1, max(0, $a));

        $c = 2 * atan2(
            sqrt($a),
            sqrt(1 - $a)
        );

        return $earthRadius * $c;
    }

    /**
     * Warga hanya dapat melihat laporan miliknya.
     * Admin/dinas dapat mengakses untuk kebutuhan operasional.
     */
    protected function authorizeView(Laporan $laporan): void
    {
        $user = auth()->user();

        if (
            !$user->isAdmin()
            && !$user->isDinas()
            && $laporan->pelapor_id !== $user->id
        ) {
            abort(403, 'Anda tidak memiliki izin untuk melihat laporan ini.');
        }
    }




    /**
     * Hanya pemilik atau admin yang boleh mengubah laporan.
     */
    protected function authorizeEdit(Laporan $laporan): void
    {
        $user = auth()->user();

        if (
            !$user->isAdmin()
            && $laporan->pelapor_id !== $user->id
        ) {
            abort(403, 'Anda tidak memiliki izin untuk mengubah laporan ini.');
        }
    }

    public function indexDinas(Request $request)
{
    abort_unless(auth()->user()->isDinas(), 403);

    $laporan = Laporan::query()
        ->induk()
        ->with('kategoriHambatan')
        ->orderByDesc('created_at')
        ->paginate(10);

    return view('dinas.index', compact('laporan'));
}
}