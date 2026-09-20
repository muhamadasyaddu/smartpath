<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePengaturanPrioritasRequest;
use App\Http\Requests\UpdatePengaturanPrioritasRequest;
use App\Models\Audit;
use App\Models\FasilitasPublik;
use App\Models\Laporan;
use App\Models\PengaturanPrioritas;
use Illuminate\Support\Facades\DB;

class PengaturanPrioritasController extends Controller
{
    /**
     * Menampilkan seluruh konfigurasi prioritas.
     */
    public function index()
    {
        $pengaturanPrioritas = PengaturanPrioritas::with('dibuatOleh')
            ->orderByDesc('created_at')
            ->paginate(15);

        $aktif = PengaturanPrioritas::aktif()->first();

        return view(
            'Admin.pengaturan-prioritas.index',
            compact('pengaturanPrioritas', 'aktif')
        );
    }

    /**
     * Form tambah konfigurasi.
     */
    public function create()
    {
        return view('Admin.pengaturan-prioritas.create');
    }

    /**
     * Menyimpan konfigurasi baru.
     */
    public function store(StorePengaturanPrioritasRequest $request)
    {
        $validated = $request->validated();

        $validated['dibuat_oleh_id'] = auth()->id();
        $validated['adalah_aktif'] = $request->boolean('adalah_aktif');

        $pengaturanPrioritas = DB::transaction(function () use ($validated) {

            if ($validated['adalah_aktif']) {
                PengaturanPrioritas::where('adalah_aktif', true)
                    ->update([
                        'adalah_aktif' => false,
                    ]);

                $validated['berlaku_sejak']
                    = $validated['berlaku_sejak'] ?? now();
            }

            return PengaturanPrioritas::create($validated);
        });

        Audit::log(
            auth()->id(),
            'buat_pengaturan_prioritas',
            'pengaturan_prioritas',
            $pengaturanPrioritas->id,
            null,
            $pengaturanPrioritas->toArray(),
            "Pengaturan prioritas baru: {$pengaturanPrioritas->label}"
        );

        return redirect()
            ->route('admin.pengaturan-prioritas.index')
            ->with(
                'sukses',
                'Pengaturan prioritas berhasil ditambahkan.'
            );
    }

    /**
     * Resource show tidak diperlukan untuk modul ini.
     */
    public function show(PengaturanPrioritas $pengaturanPrioritas)
    {
        return redirect()->route(
            'admin.pengaturan-prioritas.edit',
            $pengaturanPrioritas
        );
    }

    /**
     * Form edit konfigurasi.
     */
    public function edit(PengaturanPrioritas $pengaturanPrioritas)
    {
        return view(
            'Admin.pengaturan-prioritas.edit',
            compact('pengaturanPrioritas')
        );
    }

    /**
     * Memperbarui konfigurasi.
     */
    public function update(
        UpdatePengaturanPrioritasRequest $request,
        PengaturanPrioritas $pengaturanPrioritas
    ) {
        $validated = $request->validated();

        $validated['adalah_aktif']
            = $request->boolean('adalah_aktif');

        $dataLama = $pengaturanPrioritas->toArray();

        DB::transaction(function () use (
            $pengaturanPrioritas,
            &$validated
        ) {

            if ($validated['adalah_aktif']) {

                PengaturanPrioritas::where(
                    'id',
                    '!=',
                    $pengaturanPrioritas->id
                )
                    ->where('adalah_aktif', true)
                    ->update([
                        'adalah_aktif' => false,
                    ]);

                $validated['berlaku_sejak']
                    = $validated['berlaku_sejak']
                    ?? $pengaturanPrioritas->berlaku_sejak
                    ?? now();
            }

            $pengaturanPrioritas->update($validated);
        });

        Audit::log(
            auth()->id(),
            'ubah_pengaturan_prioritas',
            'pengaturan_prioritas',
            $pengaturanPrioritas->id,
            $dataLama,
            $pengaturanPrioritas->fresh()->toArray(),
            "Pengaturan prioritas diubah: {$pengaturanPrioritas->label}"
        );

        return redirect()
            ->route('admin.pengaturan-prioritas.index')
            ->with(
                'sukses',
                'Pengaturan prioritas berhasil diperbarui.'
            );
    }

    /**
     * Mengaktifkan satu konfigurasi dan menonaktifkan konfigurasi lain.
     */
    public function activate(PengaturanPrioritas $pengaturanPrioritas)
    {
        DB::transaction(function () use ($pengaturanPrioritas) {

            PengaturanPrioritas::where('adalah_aktif', true)
                ->where('id', '!=', $pengaturanPrioritas->id)
                ->update([
                    'adalah_aktif' => false,
                ]);

            $pengaturanPrioritas->update([
                'adalah_aktif' => true,
                'berlaku_sejak' => now(),
            ]);
        });

        Audit::log(
            auth()->id(),
            'aktifkan_pengaturan_prioritas',
            'pengaturan_prioritas',
            $pengaturanPrioritas->id,
            null,
            null,
            "Pengaturan prioritas diaktifkan: {$pengaturanPrioritas->label}"
        );

        return redirect()
            ->route('admin.pengaturan-prioritas.index')
            ->with(
                'sukses',
                'Pengaturan prioritas berhasil diaktifkan.'
            );
    }

    /**
     * Hitung ulang skor laporan terverifikasi.
     */
    public function recalculate()
    {
        $laporanList = Laporan::induk()
            ->terverifikasi()
            ->whereNotNull('skor_prioritas')
            ->get();

        $count = 0;

        foreach ($laporanList as $laporan) {

            $fasilitasTerdekat = FasilitasPublik::getNearest(
                (float) $laporan->latitude,
                (float) $laporan->longitude,
                500
            );

            if ($fasilitasTerdekat) {

                $jarak = $fasilitasTerdekat->calculateDistance(
                    (float) $laporan->latitude,
                    (float) $laporan->longitude
                );

                $laporan->fasilitas_terdekat_id
                    = $fasilitasTerdekat->id;

                $laporan->jarak_fasilitas_meter
                    = round($jarak, 2);
            }

            $skor = $laporan->calculatePriorityScore();

            $laporan->update([
                'skor_keparahan' => $skor['skor_keparahan'],
                'skor_pelapor' => $skor['skor_pelapor'],
                'skor_fasilitas' => $skor['skor_fasilitas'],
                'skor_prioritas' => $skor['skor_prioritas'],
                'dihitung_pada' => now(),
            ]);

            $count++;
        }

        Audit::log(
            auth()->id(),
            'hitung_ulang_prioritas',
            'laporan',
            null,
            null,
            null,
            "Hitung ulang skor prioritas untuk {$count} laporan"
        );

        return redirect()
            ->route('admin.pengaturan-prioritas.index')
            ->with(
                'sukses',
                "Skor prioritas berhasil dihitung ulang untuk {$count} laporan."
            );
    }

    /**
     * Menghapus konfigurasi nonaktif.
     */
    public function destroy(PengaturanPrioritas $pengaturanPrioritas)
    {
        if ($pengaturanPrioritas->adalah_aktif) {

            return back()->with(
                'galat',
                'Pengaturan yang aktif tidak dapat dihapus. Aktifkan konfigurasi lain terlebih dahulu.'
            );
        }

        $pengaturanPrioritas->delete();

        return redirect()
            ->route('admin.pengaturan-prioritas.index')
            ->with(
                'sukses',
                'Pengaturan prioritas berhasil dihapus.'
            );
    }
}