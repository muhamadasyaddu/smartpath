<?php

namespace App\Http\Controllers;

use App\Models\RencanaPerbaikan;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class KinerjaAnggaranController extends Controller
{
    /**
     * Menentukan status rencana secara otomatis.
     */
    private function tentukanStatus($tanggalMulai, $tanggalSelesai)
    {
        if (!empty($tanggalSelesai)) {
            return 'selesai';
        }

        if (empty($tanggalMulai)) {
            return 'belum_dimulai';
        }

        $mulai = Carbon::parse($tanggalMulai)->startOfDay();
        $hariIni = Carbon::today();

        if ($mulai->isAfter($hariIni)) {
            return 'belum_dimulai';
        }

        return 'dalam_perbaikan';
    }


    /**
     * Menentukan apakah rencana terlambat.
     */
    private function isTerlambat(
        $targetSelesai,
        $tanggalSelesai
    ) {
        if (
            empty($targetSelesai)
            || !empty($tanggalSelesai)
        ) {
            return false;
        }

        return Carbon::parse($targetSelesai)
            ->startOfDay()
            ->isBefore(Carbon::today());
    }


    /**
     * Halaman Kinerja & Anggaran Dinas.
     */
    public function index(Request $request)
    {
        abort_unless(
            auth()->user()->isDinas(),
            403
        );


        /*
        |--------------------------------------------------------------------------
        | DATA RENCANA PERBAIKAN
        |--------------------------------------------------------------------------
        */

        $rencanaQuery = RencanaPerbaikan::query()
            ->with([
                'laporan.kategoriHambatan',
                'laporan.wilayah',
            ])
            ->whereHas('laporan', function ($query) {

                $query
                    ->induk()
                    ->aktif();

            });


        /*
        |--------------------------------------------------------------------------
        | AMBIL SEMUA DATA
        |--------------------------------------------------------------------------
        */

        $semuaRencana = $rencanaQuery
            ->orderByDesc('created_at')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | STATUS OTOMATIS
        |--------------------------------------------------------------------------
        */

        foreach ($semuaRencana as $rencana) {

            $rencana->status_otomatis =
                $this->tentukanStatus(
                    $rencana->tanggal_mulai,
                    $rencana->tanggal_selesai
                );


            $rencana->terlambat =
                $this->isTerlambat(
                    $rencana->target_selesai,
                    $rencana->tanggal_selesai
                );


            /*
            | Prioritas laporan.
            */

            $skor = $rencana->laporan
                ? (float) $rencana->laporan->skor_prioritas
                : 0;


            if ($skor >= 70) {

                $rencana->prioritas_label = 'Tinggi';

            } elseif ($skor >= 40) {

                $rencana->prioritas_label = 'Sedang';

            } else {

                $rencana->prioritas_label = 'Rendah';
            }
        }


        /*
        |--------------------------------------------------------------------------
        | FILTER STATUS
        |--------------------------------------------------------------------------
        */

        $filter = $request->query(
            'status',
            'semua'
        );


        $allowedFilter = [
            'semua',
            'belum_dimulai',
            'dalam_perbaikan',
            'terlambat',
            'selesai',
        ];


        if (
            !in_array(
                $filter,
                $allowedFilter,
                true
            )
        ) {
            $filter = 'semua';
        }


        /*
        |--------------------------------------------------------------------------
        | TERAPKAN FILTER
        |--------------------------------------------------------------------------
        */

        $rencanaTampil = $semuaRencana;


        if ($filter === 'belum_dimulai') {

            $rencanaTampil =
                $semuaRencana->filter(function ($rencana) {

                    return $rencana->status_otomatis
                        === 'belum_dimulai';

                });

        } elseif ($filter === 'dalam_perbaikan') {

            $rencanaTampil =
                $semuaRencana->filter(function ($rencana) {

                    return $rencana->status_otomatis
                        === 'dalam_perbaikan';

                });

        } elseif ($filter === 'terlambat') {

            $rencanaTampil =
                $semuaRencana->filter(function ($rencana) {

                    return $rencana->terlambat === true;

                });

        } elseif ($filter === 'selesai') {

            $rencanaTampil =
                $semuaRencana->filter(function ($rencana) {

                    return $rencana->status_otomatis
                        === 'selesai';

                });
        }


        /*
        |--------------------------------------------------------------------------
        | PAGINATION MANUAL
        |--------------------------------------------------------------------------
        */

        $perPage = 10;

        $page = max(
            1,
            (int) $request->query(
                'page',
                1
            )
        );


        $totalData =
            $rencanaTampil->count();


        $rencanaTampil =
            $rencanaTampil
                ->values()
                ->slice(
                    ($page - 1) * $perPage,
                    $perPage
                );


        /*
        |--------------------------------------------------------------------------
        | REKAP KINERJA
        |--------------------------------------------------------------------------
        */

        $totalRencana =
            $semuaRencana->count();


        $belumDimulai =
            $semuaRencana
                ->where(
                    'status_otomatis',
                    'belum_dimulai'
                )
                ->count();


        $dalamPerbaikan =
            $semuaRencana
                ->where(
                    'status_otomatis',
                    'dalam_perbaikan'
                )
                ->count();


        $selesai =
            $semuaRencana
                ->where(
                    'status_otomatis',
                    'selesai'
                )
                ->count();


        $terlambat =
            $semuaRencana
                ->where(
                    'terlambat',
                    true
                )
                ->count();


        /*
        |--------------------------------------------------------------------------
        | REKAP ANGGARAN
        |--------------------------------------------------------------------------
        */

        $totalEstimasi =
            (float) $semuaRencana
                ->sum('estimasi_anggaran');


        $totalRealisasi =
            (float) $semuaRencana
                ->sum('realisasi_anggaran');


        $sisaAnggaran =
            $totalEstimasi
            - $totalRealisasi;


        /*
        | Jangan tampilkan angka negatif sebagai sisa.
        */

        $sisaAnggaran =
            max(
                0,
                $sisaAnggaran
            );


        $persentaseRealisasi =
            $totalEstimasi > 0
                ? round(
                    (
                        $totalRealisasi
                        / $totalEstimasi
                    ) * 100,
                    1
                )
                : 0;


        /*
        |--------------------------------------------------------------------------
        | PERSENTASE PENYELESAIAN
        |--------------------------------------------------------------------------
        */

        $persentaseSelesai =
            $totalRencana > 0
                ? round(
                    (
                        $selesai
                        / $totalRencana
                    ) * 100,
                    1
                )
                : 0;


        /*
        |--------------------------------------------------------------------------
        | REKAP PER KATEGORI
        |--------------------------------------------------------------------------
        */

        $rekapKategori =
            $semuaRencana
                ->groupBy(function ($rencana) {

                    return optional(
                        optional($rencana->laporan)
                            ->kategoriHambatan
                    )->nama
                        ?? 'Kategori Lainnya';

                })
                ->map(function (
                    $items,
                    $namaKategori
                ) {

                    $estimasi =
                        (float) $items
                            ->sum('estimasi_anggaran');


                    $realisasi =
                        (float) $items
                            ->sum('realisasi_anggaran');


                    $persen =
                        $estimasi > 0
                            ? round(
                                (
                                    $realisasi
                                    / $estimasi
                                ) * 100,
                                1
                            )
                            : 0;


                    return (object) [

                        'nama_kategori' =>
                            $namaKategori,

                        'jumlah_rencana' =>
                            $items->count(),

                        'estimasi' =>
                            $estimasi,

                        'realisasi' =>
                            $realisasi,

                        'persentase' =>
                            min(
                                100,
                                $persen
                            ),
                    ];
                })
                ->values();


        /*
        |--------------------------------------------------------------------------
        | VIEW
        |--------------------------------------------------------------------------
        */

        return view(
            'kinerja anggaran.index',
            compact(
                'rencanaTampil',
                'filter',
                'totalData',
                'perPage',
                'page',

                'totalRencana',
                'belumDimulai',
                'dalamPerbaikan',
                'selesai',
                'terlambat',

                'totalEstimasi',
                'totalRealisasi',
                'sisaAnggaran',
                'persentaseRealisasi',

                'persentaseSelesai',

                'rekapKategori'
            )
        );
    }
}