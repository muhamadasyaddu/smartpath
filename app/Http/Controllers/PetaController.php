<?php

namespace App\Http\Controllers;

use App\Models\Laporan;
use App\Models\KategoriHambatan;
use App\Models\FasilitasPublik;
use App\Models\Wilayah;

class PetaController extends Controller
{
    /**
     * Tampilkan peta interaktif publik.
     */
    public function index()
    {
        $kategoriHambatan = KategoriHambatan::aktif()->urutTampil()->get();
        $wilayahList = Wilayah::aktif()->level('kecamatan')->get();

        return view('peta.index', compact('kategoriHambatan', 'wilayahList'));
    }

    /**
     * Ambil data laporan untuk ditampilkan di peta (API endpoint).
     */
    public function getLaporanData()
    {
        $laporan = Laporan::induk()
            ->terverifikasi()
            ->with(['kategoriHambatan', 'wilayah', 'foto', 'pelapor'])
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'kode_laporan' => $item->kode_laporan,
                    'judul' => $item->judul,
                    'latitude' => (float) $item->latitude,
                    'longitude' => (float) $item->longitude,
                    'status' => $item->status,
                    'status_label' => $item->status_label,
                    'skor_prioritas' => (float) $item->skor_prioritas,
                    'tingkat_prioritas' => $item->tingkat_prioritas,
                    'kategori' => $item->kategoriHambatan?->nama,
                    'kategori_warna' => $item->kategoriHambatan?->warna_penanda,
                    'jumlah_pelapor' => $item->jumlah_pelapor,
                    'alamat_lengkap' => $item->alamat_lengkap,
                    'foto_utama' => $item->foto->where('adalah_utama', true)->first()?->url,
                    'created_at' => $item->created_at->toISOString(),
                    'wilayah' => $item->wilayah?->nama,
                ];
            });

        return response()->json($laporan);
    }

    /**
     * Ambil data fasilitas publik untuk ditampilkan di peta.
     */
    public function getFasilitasData()
    {
        $fasilitasPublik = FasilitasPublik::aktif()
            ->with('wilayah')
            ->get()
            ->map(function ($fasilitas) {
                return [
                    'id' => $fasilitas->id,
                    'nama' => $fasilitas->nama,
                    'jenis' => $fasilitas->jenis,
                    'latitude' => (float) $fasilitas->latitude,
                    'longitude' => (float) $fasilitas->longitude,
                    'bobot_vital' => $fasilitas->bobot_vital,
                    'alamat' => $fasilitas->alamat,
                    'wilayah' => $fasilitas->wilayah?->nama,
                ];
            });

        return response()->json($fasilitasPublik);
    }

   public function nearby()
{
    $laporan = Laporan::induk()
        ->terverifikasi()
        ->with([
            'kategoriHambatan',
            'wilayah',
            'foto'
        ])
        ->whereNotNull('latitude')
        ->whereNotNull('longitude')
        ->get();

    $laporanData = $laporan->map(function ($item) {
        return [
            'id' => $item->id,
            'judul' => $item->judul,
            'latitude' => (float) $item->latitude,
            'longitude' => (float) $item->longitude,
            'kategori' => $item->kategoriHambatan?->nama,
            'alamat' => $item->alamat_lengkap,
            'tingkat_prioritas' => $item->tingkat_prioritas,
            'skor_prioritas' => $item->skor_prioritas !== null
                ? (float) $item->skor_prioritas
                : null,
        ];
    })->values();

    return view('peta.nearby', compact('laporanData'));
}
}