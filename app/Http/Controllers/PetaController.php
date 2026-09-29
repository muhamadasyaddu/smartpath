<?php

namespace App\Http\Controllers;

use App\Models\FasilitasPublik;
use App\Models\KategoriHambatan;
use App\Models\Laporan;
use App\Models\Wilayah;
use Illuminate\Http\Request;

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

                    'wilayah' =>
                        $fasilitas->wilayah?->nama,
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
            'peta.nearby',
            compact('laporanData')
        );
    }
}