<?php

namespace App\Http\Controllers;

use App\Models\Laporan;

class RencanaPerbaikanController extends Controller
{
    /**
     * Menampilkan halaman Rencana Perbaikan untuk Dinas.
     */
    public function index()
    {
        abort_unless(auth()->user()->isDinas(), 403);

        // Ambil laporan induk yang sudah diverifikasi
        // dan sudah memiliki skor prioritas.
        $laporan = Laporan::query()
            ->induk()
            ->whereIn('status', [
                'diverifikasi',
                'dalam_perbaikan',
                'selesai'
            ])
            ->whereNotNull('skor_prioritas')
            ->with('kategoriHambatan')
            ->orderByDesc('skor_prioritas')
            ->paginate(10);

        // Prioritas tinggi
        $prioritasTinggi = Laporan::query()
            ->induk()
            ->whereIn('status', [
                'diverifikasi',
                'dalam_perbaikan'
            ])
            ->whereNotNull('skor_prioritas')
            ->where('skor_prioritas', '>=', 70)
            ->count();

        // Sedang dalam proses perbaikan
        $dalamPenanganan = Laporan::query()
            ->induk()
            ->where('status', 'dalam_perbaikan')
            ->count();

        // Sudah selesai
        $selesai = Laporan::query()
            ->induk()
            ->where('status', 'selesai')
            ->count();

        return view(
            'rencana perbaikan.index',
            compact(
                'laporan',
                'prioritasTinggi',
                'dalamPenanganan',
                'selesai'
            )
        );
    }
}