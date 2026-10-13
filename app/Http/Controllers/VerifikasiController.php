<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVerifikasiRequest;
use App\Mail\NotifikasiLaporanMail;
use App\Models\Audit;
use App\Models\FasilitasPublik;
use App\Models\Laporan;
use App\Models\Notifikasi;
use App\Models\RiwayatStatusLaporan;
use App\Models\VerifikasiLaporan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class VerifikasiController extends Controller
{
    /**
     * Daftar antrian verifikasi.
     *
     * Semua laporan pending ditampilkan, termasuk child hasil deduplikasi.
     * Dengan demikian tidak ada laporan warga yang "hilang" dari antrian.
     */
    public function index(Request $request)
    {
        $query = Laporan::query()
            ->with([
                'kategoriHambatan',
                'pelapor',
                'wilayah',
                'fotoLaporan',
                'laporanInduk',
            ]);

        if ($request->filled('status')) {
            $query->where(
                'status',
                $request->string('status')->toString()
            );
        } else {
            $query->where('status', 'menunggu_verifikasi');
        }

        $laporan = $query
            ->orderByRaw(
                "CASE WHEN status = 'menunggu_verifikasi' THEN 0 ELSE 1 END"
            )
            ->orderBy('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('verifikasi.index', compact('laporan'));
    }

    
    /**
     * Detail laporan untuk admin.
     */
    public function show(Laporan $laporan)
    {
        $laporan->load([
            'kategoriHambatan',
            'pelapor',
            'wilayah',
            'fotoLaporan',
            'laporanInduk',
            'laporanAnak.pelapor',
            'laporanAnak.kategoriHambatan',
            'laporanAnak.fotoLaporan',
            'verifikasi.admin',
            'riwayatStatus.diubahOleh',
            'fasilitasTerdekat',
        ]);

        $pengaturan = \App\Models\PengaturanPrioritas::getActive();

        $radiusDeduplikasiMeter = (int) (
            $pengaturan?->radius_deduplikasi_m
            ?? \App\Models\KonfigurasiSistem::getValue(
                'radius_deduplikasi_meter',
                50
            )
        );

        $jarakKeIndukMeter = null;

        if ($laporan->laporanInduk) {
            $jarakKeIndukMeter = $this->hitungJarakMeter(
                (float) $laporan->latitude,
                (float) $laporan->longitude,
                (float) $laporan->laporanInduk->latitude,
                (float) $laporan->laporanInduk->longitude
            );
        }

        foreach ($laporan->laporanAnak as $anak) {
            $anak->setAttribute(
                'jarak_dari_induk_meter',
                round(
                    $this->hitungJarakMeter(
                        (float) $laporan->latitude,
                        (float) $laporan->longitude,
                        (float) $anak->latitude,
                        (float) $anak->longitude
                    ),
                    2
                )
            );
        }

        return view(
            'verifikasi.show',
            compact(
                'laporan',
                'radiusDeduplikasiMeter',
                'jarakKeIndukMeter'
            )
        );
    }

    /**
     * Menghitung jarak geografis dua koordinat GPS
     * menggunakan rumus Haversine.
     */
    protected function hitungJarakMeter(
        float $lat1,
        float $lng1,
        float $lat2,
        float $lng2
    ): float {
        $radiusBumi = 6371000;

        $lat1 = deg2rad($lat1);
        $lat2 = deg2rad($lat2);
        $deltaLat = $lat2 - $lat1;
        $deltaLng = deg2rad($lng2 - $lng1);

        $a = sin($deltaLat / 2) ** 2
            + cos($lat1)
            * cos($lat2)
            * sin($deltaLng / 2) ** 2;

        $a = min(1, max(0, $a));

        return $radiusBumi * 2 * atan2(
            sqrt($a),
            sqrt(1 - $a)
        );
    }



    
    /**
     * Memisahin kandidat duplikasi yang setelah diperiksa admin
     * ternyata merupakan objek hambatan yang berbeda.
     *
     * Hanya laporan yang masih menunggu verifikasi yang dapat dipisahkan.
     * Setelah terpisah, laporan tetap menunggu keputusan persetujuan admin.
     */
    public function jadikanMandiri(Laporan $laporan)
    {
        if ($laporan->status !== 'menunggu_verifikasi') {
            return back()->with(
                'galat',
                'Hanya laporan yang menunggu verifikasi yang dapat dipisahkan.'
            );
        }

        if (!$laporan->laporan_induk_id) {
            return back()->with(
                'info',
                'Laporan ini sudah merupakan laporan mandiri.'
            );
        }

        try {
            DB::transaction(function () use ($laporan) {
                // Kunci data laporan untuk mengurangi risiko perubahan bersamaan.
                $laporanTerkunci = Laporan::query()
                    ->whereKey($laporan->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if (
                    $laporanTerkunci->status !== 'menunggu_verifikasi'
                    || !$laporanTerkunci->laporan_induk_id
                ) {
                    throw new \RuntimeException(
                        'Status laporan berubah. Muat ulang halaman dan periksa kembali.'
                    );
                }

                $parent = Laporan::query()
                    ->whereKey($laporanTerkunci->laporan_induk_id)
                    ->lockForUpdate()
                    ->first();

                // Lepaskan relasi child-parent, tanpa menghapus laporan/fotonya.
                $laporanTerkunci->update([
                    'laporan_induk_id' => null,
                    'jumlah_pelapor' => 1,
                ]);

                // Perbarui jumlah pelapor unik pada laporan induk sebelumnya.
                if ($parent) {
                    $this->syncParentReporterCount($parent);

                    if ($this->isScoreableStatus($parent->status)) {
                        $this->calculateAndSavePriority($parent);
                    }
                }

                // Catat tindakan admin untuk audit.
                Audit::log(
                    auth()->id(),
                    'laporan_dipisahkan_dari_induk',
                    'laporan',
                    $laporanTerkunci->id,
                    [
                        'laporan_induk_id' => $parent?->id,
                    ],
                    [
                        'laporan_induk_id' => null,
                    ],
                    'Laporan dipisahkan dari kandidat induk karena dinilai sebagai objek berbeda.'
                );
            });

            return redirect()
                ->route('admin.verifikasi.show', $laporan)
                ->with(
                    'sukses',
                    'Laporan berhasil dipisahkan. Periksa kembali bukti dan setujui laporan jika valid.'
                );
        } catch (\Throwable $e) {
            report($e);

            return back()->with(
                'galat',
                'Laporan belum berhasil dipisahkan. Muat ulang halaman dan coba kembali.'
            );
        }
    }



    /**
     * Terima laporan pending.
     */
    public function approve(
        StoreVerifikasiRequest $request,
        Laporan $laporan
    ) {
        $this->ensurePending($laporan);

        try {
            $notifikasi = DB::transaction(function () use ($request, $laporan) {
                $statusSebelumnya = $laporan->status;

                VerifikasiLaporan::create([
                    'laporan_id' => $laporan->id,
                    'admin_id' => auth()->id(),
                    'keputusan' => 'disetujui',
                    'catatan_admin' => $request->validated('catatan_admin'),
                    'kategori_koreksi' => $request->validated('kategori_koreksi'),
                ]);

                $laporan->update([
                    'status' => 'diverifikasi',
                ]);

                /*
                 * Parent adalah titik yang ditampilkan di peta.
                 * Jika yang diverifikasi adalah child, skor parent
                 * diperbarui agar agregasi tetap konsisten.
                 */
                if ($laporan->laporan_induk_id) {
                    $parent = Laporan::find($laporan->laporan_induk_id);

                    if ($parent) {
                        $this->syncParentReporterCount($parent);

                        if ($this->isScoreableStatus($parent->status)) {
                            $this->calculateAndSavePriority($parent);
                        }
                    }
                } else {
                    $this->syncParentReporterCount($laporan);
                    $this->calculateAndSavePriority($laporan);
                }

                RiwayatStatusLaporan::create([
                    'laporan_id' => $laporan->id,
                    'diubah_oleh_id' => auth()->id(),
                    'status_sebelumnya' => $statusSebelumnya,
                    'status_baru' => 'diverifikasi',
                    'keterangan' => 'Laporan diverifikasi dan disetujui.',
                ]);

                $notifikasi = Notifikasi::create([
                    'penerima_id' => $laporan->pelapor_id,
                    'laporan_id' => $laporan->id,
                    'jenis' => 'laporan_diverifikasi',
                    'judul' => 'Laporan Diverifikasi',
                    'pesan' => "Laporan {$laporan->kode_laporan} telah diverifikasi.",
                    'tautan' => route('laporan.show', $laporan),
                ]);

                Audit::log(
                    auth()->id(),
                    'verifikasi_setujui',
                    'laporan',
                    $laporan->id,
                    ['status' => $statusSebelumnya],
                    ['status' => 'diverifikasi'],
                    "Laporan disetujui: {$laporan->kode_laporan}"
                );

                return $notifikasi;
            });
        } catch (Throwable $e) {
            report($e);

            return back()->with(
                'galat',
                'Laporan belum dapat diverifikasi. Silakan coba lagi.'
            );
        }

        $emailTerkirim = $this->sendStatusEmail($laporan, $notifikasi);

        return redirect()
            ->route('admin.verifikasi.index')
            ->with($emailTerkirim ? 'sukses' : 'galat', $emailTerkirim
                ? 'Laporan berhasil diverifikasi dan email notifikasi telah dikirim.'
                : 'Laporan berhasil diverifikasi, tetapi email notifikasi gagal dikirim. Periksa email pelapor dan konfigurasi email.');
    }

    /**
     * Tolak laporan pending.
     */
    public function reject(
        StoreVerifikasiRequest $request,
        Laporan $laporan
    ) {
        $this->ensurePending($laporan);

        try {
            $notifikasi = DB::transaction(function () use ($request, $laporan) {
                $statusSebelumnya = $laporan->status;
                $validated = $request->validated();

                VerifikasiLaporan::create([
                    'laporan_id' => $laporan->id,
                    'admin_id' => auth()->id(),
                    'keputusan' => 'ditolak',
                    'catatan_admin' => $validated['catatan_admin'],
                    'kategori_koreksi' => $validated['kategori_koreksi'] ?? null,
                ]);

                $laporan->update([
                    'status' => 'ditolak',
                ]);

                if ($laporan->laporan_induk_id) {
                    $parent = Laporan::find($laporan->laporan_induk_id);

                    if ($parent) {
                        $this->syncParentReporterCount($parent);

                        if ($this->isScoreableStatus($parent->status)) {
                            $this->calculateAndSavePriority($parent);
                        }
                    }
                }

                RiwayatStatusLaporan::create([
                    'laporan_id' => $laporan->id,
                    'diubah_oleh_id' => auth()->id(),
                    'status_sebelumnya' => $statusSebelumnya,
                    'status_baru' => 'ditolak',
                    'keterangan' => $validated['catatan_admin'],
                ]);

                $notifikasi = Notifikasi::create([
                    'penerima_id' => $laporan->pelapor_id,
                    'laporan_id' => $laporan->id,
                    'jenis' => 'laporan_ditolak',
                    'judul' => 'Laporan Ditolak',
                    'pesan' => "Laporan {$laporan->kode_laporan} ditolak. Alasan: {$validated['catatan_admin']}",
                    'tautan' => route('laporan.show', $laporan),
                ]);

                Audit::log(
                    auth()->id(),
                    'verifikasi_tolak',
                    'laporan',
                    $laporan->id,
                    ['status' => $statusSebelumnya],
                    ['status' => 'ditolak'],
                    "Laporan ditolak: {$laporan->kode_laporan}"
                );

                return $notifikasi;
            });
        } catch (Throwable $e) {
            report($e);

            return back()->with(
                'galat',
                'Laporan belum dapat ditolak. Silakan coba lagi.'
            );
        }

        $emailTerkirim = $this->sendStatusEmail($laporan, $notifikasi);

        return redirect()
            ->route('admin.verifikasi.index')
            ->with($emailTerkirim ? 'sukses' : 'galat', $emailTerkirim
                ? 'Laporan telah ditolak, alasannya disimpan, dan email notifikasi telah dikirim.'
                : 'Laporan berhasil ditolak, tetapi email notifikasi gagal dikirim. Periksa email pelapor dan konfigurasi email.');
    }

    /**
     * Fitur lama: kembalikan laporan.
     * Tetap dipertahankan agar kerangka aplikasi tidak rusak.
     */
    public function return(
        StoreVerifikasiRequest $request,
        Laporan $laporan
    ) {
        $this->ensurePending($laporan);

        try {
            $notifikasi = DB::transaction(function () use ($request, $laporan) {
                $statusSebelumnya = $laporan->status;
                $validated = $request->validated();

                VerifikasiLaporan::create([
                    'laporan_id' => $laporan->id,
                    'admin_id' => auth()->id(),
                    'keputusan' => 'dikembalikan',
                    'catatan_admin' => $validated['catatan_admin'] ?? null,
                    'kategori_koreksi' => $validated['kategori_koreksi'] ?? null,
                ]);

                $laporan->update([
                    'status' => 'menunggu_verifikasi',
                ]);

                RiwayatStatusLaporan::create([
                    'laporan_id' => $laporan->id,
                    'diubah_oleh_id' => auth()->id(),
                    'status_sebelumnya' => $statusSebelumnya,
                    'status_baru' => 'menunggu_verifikasi',
                    'keterangan' => $validated['catatan_admin']
                        ?? 'Laporan dikembalikan untuk diperbaiki.',
                ]);

                $notifikasi = Notifikasi::create([
                    'penerima_id' => $laporan->pelapor_id,
                    'laporan_id' => $laporan->id,
                    'jenis' => 'laporan_dikembalikan',
                    'judul' => 'Laporan Dikembalikan',
                    'pesan' => "Laporan {$laporan->kode_laporan} dikembalikan untuk diperbaiki.",
                    'tautan' => route('laporan.show', $laporan),
                ]);

                Audit::log(
                    auth()->id(),
                    'verifikasi_kembalikan',
                    'laporan',
                    $laporan->id,
                    ['status' => $statusSebelumnya],
                    ['status' => 'menunggu_verifikasi'],
                    "Laporan dikembalikan: {$laporan->kode_laporan}"
                );

                return $notifikasi;
            });
        } catch (Throwable $e) {
            report($e);

            return back()->with(
                'galat',
                'Laporan belum dapat dikembalikan. Silakan coba lagi.'
            );
        }

        $emailTerkirim = $this->sendStatusEmail($laporan, $notifikasi);

        return redirect()
            ->route('admin.verifikasi.index')
            ->with($emailTerkirim ? 'sukses' : 'galat', $emailTerkirim
                ? 'Laporan dikembalikan ke antrian verifikasi dan email notifikasi telah dikirim.'
                : 'Laporan berhasil dikembalikan, tetapi email notifikasi gagal dikirim. Periksa email pelapor dan konfigurasi email.');
    }

    /**
     * Fitur pasca-verifikasi yang sudah ada.
     */
    public function markInProgress(Request $request, Laporan $laporan)
    {
        abort_unless(
            $laporan->status === 'diverifikasi',
            422,
            'Hanya laporan terverifikasi yang dapat masuk tahap perbaikan.'
        );

        $statusSebelumnya = $laporan->status;

        $laporan->update([
            'status' => 'dalam_perbaikan',
        ]);

        RiwayatStatusLaporan::create([
            'laporan_id' => $laporan->id,
            'diubah_oleh_id' => auth()->id(),
            'status_sebelumnya' => $statusSebelumnya,
            'status_baru' => 'dalam_perbaikan',
            'keterangan' => $request->input('keterangan', 'Perbaikan dimulai.'),
        ]);

        $notifikasi = Notifikasi::create([
            'penerima_id' => $laporan->pelapor_id,
            'laporan_id' => $laporan->id,
            'jenis' => 'laporan_dalam_perbaikan',
            'judul' => 'Perbaikan Dimulai',
            'pesan' => "Laporan {$laporan->kode_laporan} sedang dalam perbaikan.",
            'tautan' => route('laporan.show', $laporan),
        ]);

        Audit::log(
            auth()->id(),
            'status_dalam_perbaikan',
            'laporan',
            $laporan->id,
            ['status' => $statusSebelumnya],
            ['status' => 'dalam_perbaikan'],
            "Laporan dalam perbaikan: {$laporan->kode_laporan}"
        );

        $emailTerkirim = $this->sendStatusEmail($laporan, $notifikasi);

        return back()->with($emailTerkirim ? 'sukses' : 'galat', $emailTerkirim
            ? 'Status laporan diubah menjadi Dalam Perbaikan dan email notifikasi telah dikirim.'
            : 'Status laporan berhasil diubah, tetapi email notifikasi gagal dikirim. Periksa email pelapor dan konfigurasi email.');
    }

    /**
     * Fitur pasca-verifikasi yang sudah ada.
     */
    public function markCompleted(Request $request, Laporan $laporan)
    {
        abort_unless(
            $laporan->status === 'dalam_perbaikan',
            422,
            'Hanya laporan dalam perbaikan yang dapat diselesaikan.'
        );

        $statusSebelumnya = $laporan->status;

        $laporan->update([
            'status' => 'selesai',
        ]);

        RiwayatStatusLaporan::create([
            'laporan_id' => $laporan->id,
            'diubah_oleh_id' => auth()->id(),
            'status_sebelumnya' => $statusSebelumnya,
            'status_baru' => 'selesai',
            'keterangan' => $request->input('keterangan', 'Perbaikan selesai.'),
        ]);

        $notifikasi = Notifikasi::create([
            'penerima_id' => $laporan->pelapor_id,
            'laporan_id' => $laporan->id,
            'jenis' => 'laporan_selesai',
            'judul' => 'Perbaikan Selesai',
            'pesan' => "Laporan {$laporan->kode_laporan} telah selesai diperbaiki.",
            'tautan' => route('laporan.show', $laporan),
        ]);

        Audit::log(
            auth()->id(),
            'status_selesai',
            'laporan',
            $laporan->id,
            ['status' => $statusSebelumnya],
            ['status' => 'selesai'],
            "Laporan selesai: {$laporan->kode_laporan}"
        );

        $emailTerkirim = $this->sendStatusEmail($laporan, $notifikasi);

        return back()->with($emailTerkirim ? 'sukses' : 'galat', $emailTerkirim
            ? 'Status laporan diubah menjadi Selesai dan email notifikasi telah dikirim.'
            : 'Status laporan berhasil diubah, tetapi email notifikasi gagal dikirim. Periksa email pelapor dan konfigurasi email.');
    }

    protected function sendStatusEmail(Laporan $laporan, Notifikasi $notifikasi): bool
    {
        $pelapor = $laporan->pelapor;

        if (! $pelapor || ! $pelapor->email) {
            Log::warning('Email notifikasi status laporan tidak dikirim karena email pelapor tidak tersedia.', [
                'laporan_id' => $laporan->id,
                'pelapor_id' => $laporan->pelapor_id,
            ]);

            return false;
        }

        try {
            Mail::to($pelapor->email)->send(
                new NotifikasiLaporanMail($notifikasi->pesan, $laporan->judul)
            );
        } catch (Throwable $e) {
            Log::error('Gagal mengirim email notifikasi status laporan.', [
                'laporan_id' => $laporan->id,
                'pelapor_id' => $pelapor->id,
                'status' => $laporan->status,
                'message' => $e->getMessage(),
            ]);

            return false;
        }

        return true;
    }

    /**
     * Validasi state untuk mencegah approve/reject berulang.
     */
    protected function ensurePending(Laporan $laporan): void
    {
        abort_unless(
            $laporan->status === 'menunggu_verifikasi',
            422,
            'Laporan ini sudah tidak berada dalam antrian verifikasi.'
        );
    }

    protected function isScoreableStatus(string $status): bool
    {
        return in_array(
            $status,
            ['diverifikasi', 'dalam_perbaikan', 'selesai'],
            true
        );
    }

    /**
     * Sinkronisasi jumlah pelapor unik.
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
     * Hitung dan simpan skor prioritas.
     */
    protected function calculateAndSavePriority(Laporan $laporan): void
    {
        $pengaturan = \App\Models\PengaturanPrioritas::getActive();

        $radiusFasilitas = (int) (
            $pengaturan?->radius_fasilitas_m ?? 500
        );

        $fasilitasTerdekat = FasilitasPublik::getNearest(
            (float) $laporan->latitude,
            (float) $laporan->longitude,
            $radiusFasilitas
        );

        if ($fasilitasTerdekat) {
            $jarak = $fasilitasTerdekat->calculateDistance(
                (float) $laporan->latitude,
                (float) $laporan->longitude
            );

            $laporan->fasilitas_terdekat_id = $fasilitasTerdekat->id;
            $laporan->jarak_fasilitas_meter = round($jarak, 2);
        } else {
            $laporan->fasilitas_terdekat_id = null;
            $laporan->jarak_fasilitas_meter = null;
        }

        $skor = $laporan->fresh([
            'kategoriHambatan',
        ])->calculatePriorityScore();

        $laporan->update([
            'skor_keparahan' => $skor['skor_keparahan'],
            'skor_pelapor' => $skor['skor_pelapor'],
            'skor_fasilitas' => $skor['skor_fasilitas'],
            'skor_prioritas' => $skor['skor_prioritas'],
            'dihitung_pada' => now(),
        ]);
    }
}