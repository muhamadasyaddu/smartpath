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
use Throwable;

class LaporanController extends Controller
{
    /**
     * Riwayat laporan.
     *
     * Warga hanya boleh melihat laporan miliknya sendiri.
     * Admin tetap dapat melihat seluruh laporan melalui route ini
     * jika diperlukan untuk kompatibilitas dengan sistem lama.
     */
    public function index(Request $request)
    {
        $query = Laporan::induk()
            ->with(['kategoriHambatan', 'pelapor', 'wilayah', 'fotoLaporan']);

        if (!auth()->user()->isAdmin()) {
            $query->where('pelapor_id', auth()->id());
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status')->toString());
        }

        if ($request->filled('kategori')) {
            $query->where('kategori_hambatan_id', $request->integer('kategori'));
        }

        $laporan = $query
            ->orderByDesc('created_at')
            ->paginate(12)
            ->withQueryString();

        $countQuery = Laporan::induk();

        if (!auth()->user()->isAdmin()) {
            $countQuery->where('pelapor_id', auth()->id());
        }

        $statusCounts = [
            'semua' => (clone $countQuery)->count(),
            'menunggu_verifikasi' => (clone $countQuery)->where('status', 'menunggu_verifikasi')->count(),
            'diverifikasi' => (clone $countQuery)->where('status', 'diverifikasi')->count(),
            'dalam_perbaikan' => (clone $countQuery)->where('status', 'dalam_perbaikan')->count(),
            'selesai' => (clone $countQuery)->where('status', 'selesai')->count(),
            'ditolak' => (clone $countQuery)->where('status', 'ditolak')->count(),
        ];

        return view('Laporan.index', compact('laporan', 'statusCounts'));
    }

    /**
     * Form laporan baru.
     */
    public function create()
    {
        $kategoriHambatan = KategoriHambatan::aktif()
            ->urutTampil()
            ->get();

        $wilayahList = Wilayah::aktif()
            ->level('kecamatan')
            ->orderBy('nama')
            ->get();

        $maxFoto = (int) KonfigurasiSistem::getValue(
            'max_foto_per_laporan',
            5
        );

        return view(
            'Laporan.create',
            compact('kategoriHambatan', 'wilayahList', 'maxFoto')
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

                $laporan = Laporan::create([
                    'pelapor_id' => auth()->id(),
                    'kategori_hambatan_id' => $validated['kategori_hambatan_id'],
                    'wilayah_id' => $validated['wilayah_id'],
                    'laporan_induk_id' => $laporanInduk?->id,
                    'latitude' => $validated['latitude'],
                    'longitude' => $validated['longitude'],
                    'alamat_lengkap' => $validated['alamat_lengkap'] ?? null,
                    'judul' => $validated['judul'],
                    'deskripsi' => $validated['deskripsi'],
                    'status' => 'menunggu_verifikasi',
                    'jumlah_pelapor' => 1,
                    'sumber_koordinat' => $validated['sumber_koordinat'],
                    'platform_pelapor' => 'web',
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
        Storage::disk('public')->delete($path);
    }

    report($e);

    return back()
        ->withInput()
        ->withErrors([
            'laporan' => config('app.debug')
                ? 'Laporan gagal disimpan: ' . $e->getMessage()
                : 'Laporan belum dapat disimpan. Silakan coba lagi.',
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
    abort_unless(auth()->user()->isDinas(), 403);

    $laporan = Laporan::query()
        ->with('kategoriHambatan')
        ->findOrFail($id);

    $pdf = \PDF::loadView('dinas.laporan-pdf', compact('laporan'));

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

        $laporan->delete();

        /*
         * Jika child dihapus, jumlah pelapor parent harus disinkronkan.
         */
        if ($laporan->laporan_induk_id) {
            $parent = Laporan::find($laporan->laporan_induk_id);

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
        $radiusMeter = (int) KonfigurasiSistem::getValue(
            'radius_deduplikasi_meter',
            50
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