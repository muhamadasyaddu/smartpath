<?php

namespace App\Http\Controllers;

use App\Models\Laporan;
use App\Models\RencanaPerbaikan;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class RencanaPerbaikanController extends Controller
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
    private function isTerlambat($targetSelesai, $tanggalSelesai)
    {
        if (empty($targetSelesai)) {
            return false;
        }

        if (!empty($tanggalSelesai)) {
            return false;
        }

        return Carbon::parse($targetSelesai)
            ->startOfDay()
            ->isBefore(Carbon::today());
    }


    public function index(Request $request)
{
    abort_unless(
        auth()->user()->isDinas(),
        403
    );

    $statusOptions = [
        'belum_ada_rencana',
        'belum_dimulai',
        'dalam_perbaikan',
        'terlambat',
        'selesai',
    ];

    $statusFilter =
        $request->input('status');

    if (
        !in_array(
            $statusFilter,
            $statusOptions,
            true
        )
    ) {
        $statusFilter = null;
    }

    $search =
        trim(
            (string) $request->input(
                'q',
                ''
            )
        );


    /*
     * ==========================================================
     * DATA LAPORAN YANG MEMANG RELEVAN UNTUK DINAS
     * ==========================================================
     */
    $laporan =
        Laporan::query()
            ->induk()
            ->whereIn(
                'status',
                [
                    'diverifikasi',
                    'dalam_perbaikan',
                    'selesai',
                ]
            )
            ->whereNotNull(
                'skor_prioritas'
            )
            ->with([
                'kategoriHambatan',
                'wilayah',
                'rencanaPerbaikan',
            ])
            ->orderByDesc(
                'skor_prioritas'
            )
            ->orderByDesc(
                'created_at'
            )
            ->get();


    /*
     * ==========================================================
     * HITUNG STATUS RENCANA SECARA OTOMATIS
     * ==========================================================
     */
    foreach ($laporan as $item) {

        $rencana =
            $item->rencanaPerbaikan;


        /*
         * Jangan skip laporan tanpa rencana.
         *
         * Ini penting agar filter:
         * "Belum Ada Rencana"
         * benar-benar bekerja.
         */
        if (!$rencana) {
            continue;
        }


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
    }


    /*
     * ==========================================================
     * PENCARIAN
     * ==========================================================
     */
    if ($search !== '') {

        $needle =
            mb_strtolower(
                $search
            );


        $laporan =
            $laporan
                ->filter(
                    function ($item) use ($needle) {

                        $haystack =
                            mb_strtolower(
                                implode(
                                    ' ',
                                    array_filter(
                                        [
                                            $item->kode_laporan,
                                            $item->judul,
                                            $item->kategoriHambatan?->nama,
                                            $item->wilayah?->nama,
                                            $item->alamat_lengkap,
                                        ]
                                    )
                                )
                            );


                        return str_contains(
                            $haystack,
                            $needle
                        );

                    }
                )
                ->values();
    }


    /*
     * ==========================================================
     * FILTER STATUS
     * ==========================================================
     */
    if ($statusFilter !== null) {

        $laporan =
            $laporan
                ->filter(
                    function ($item) use ($statusFilter) {

                        $rencana =
                            $item->rencanaPerbaikan;


                        /*
                         * Inilah filter yang sebelumnya
                         * tidak pernah dapat bekerja.
                         */
                        if (
                            $statusFilter ===
                            'belum_ada_rencana'
                        ) {

                            return !$rencana;
                        }


                        if (!$rencana) {
                            return false;
                        }


                        if (
                            $statusFilter ===
                            'terlambat'
                        ) {

                            return
                                $rencana->terlambat === true;
                        }


                        return
                            $rencana->status_otomatis ===
                            $statusFilter;
                    }
                )
                ->values();
    }


    /*
     * ==========================================================
     * DATA STATISTIK
     *
     * Gunakan scope laporan yang sama.
     * ==========================================================
     */
    $semuaLaporan =
        Laporan::query()
            ->induk()
            ->whereIn(
                'status',
                [
                    'diverifikasi',
                    'dalam_perbaikan',
                    'selesai',
                ]
            )
            ->whereNotNull(
                'skor_prioritas'
            )
            ->with(
                'rencanaPerbaikan'
            )
            ->get();


    foreach (
        $semuaLaporan as $item
    ) {

        if (
            !$item->rencanaPerbaikan
        ) {
            continue;
        }


        $item
            ->rencanaPerbaikan
            ->status_otomatis =
                $this->tentukanStatus(
                    $item->rencanaPerbaikan
                        ->tanggal_mulai,
                    $item->rencanaPerbaikan
                        ->tanggal_selesai
                );


        $item
            ->rencanaPerbaikan
            ->terlambat =
                $this->isTerlambat(
                    $item->rencanaPerbaikan
                        ->target_selesai,
                    $item->rencanaPerbaikan
                        ->tanggal_selesai
                );
    }


    $totalLaporan =
        $semuaLaporan->count();


    $belumAdaRencana =
        $semuaLaporan
            ->filter(
                fn ($item) =>
                    !$item->rencanaPerbaikan
            )
            ->count();


    $totalRencana =
        $totalLaporan -
        $belumAdaRencana;


    $belumDimulai =
        $semuaLaporan
            ->filter(
                fn ($item) =>
                    $item->rencanaPerbaikan?->status_otomatis
                    ===
                    'belum_dimulai'
            )
            ->count();


    $dalamPerbaikan =
        $semuaLaporan
            ->filter(
                fn ($item) =>
                    $item->rencanaPerbaikan?->status_otomatis
                    ===
                    'dalam_perbaikan'
            )
            ->count();


    $selesai =
        $semuaLaporan
            ->filter(
                fn ($item) =>
                    $item->rencanaPerbaikan?->status_otomatis
                    ===
                    'selesai'
            )
            ->count();


    $terlambat =
        $semuaLaporan
            ->filter(
                fn ($item) =>
                    $item->rencanaPerbaikan?->terlambat
                    === true
            )
            ->count();


    $prioritasKritis =
        $semuaLaporan
            ->filter(
                fn ($item) =>
                    (float)
                    $item->skor_prioritas >= 70
            )
            ->count();


    /*
     * ==========================================================
     * PAGINATION
     * ==========================================================
     */
    $perPage = 10;


    $currentPage =
        LengthAwarePaginator::resolveCurrentPage(
            'page'
        );


    $items =
        $laporan
            ->forPage(
                $currentPage,
                $perPage
            )
            ->values();


    $laporan =
        new LengthAwarePaginator(
            $items,
            $laporan->count(),
            $perPage,
            $currentPage,
            [
                'path' =>
                    LengthAwarePaginator
                        ::resolveCurrentPath(),

                'query' =>
                    $request->query(),
            ]
        );


    return view(
        'rencana perbaikan.index',
        compact(
            'laporan',
            'totalLaporan',
            'totalRencana',
            'belumAdaRencana',
            'belumDimulai',
            'dalamPerbaikan',
            'selesai',
            'terlambat',
            'prioritasKritis',
            'statusFilter',
            'search'
        )
    );
}


    /**
     * Form membuat rencana.
     */
    public function create(Laporan $laporan)
    {
        abort_unless(auth()->user()->isDinas(), 403);

        $laporan = Laporan::query()
            ->induk()
            ->whereIn('status', [
                'diverifikasi',
                'dalam_perbaikan',
                'selesai',
            ])
            ->whereNotNull('skor_prioritas')
            ->with([
                'kategoriHambatan',
                'wilayah',
            ])
            ->findOrFail($laporan->id);


        if ($laporan->rencanaPerbaikan()->exists()) {

            return redirect()
                ->route('dinas.rencana-perbaikan.index')
                ->with(
                    'galat',
                    'Laporan tersebut sudah memiliki rencana perbaikan.'
                );
        }


        return view(
            'rencana perbaikan.create',
            compact('laporan')
        );
    }


    /**
     * Menyimpan rencana baru.
     */
    public function store(Request $request)
    {
        abort_unless(auth()->user()->isDinas(), 403);

        $validated = $request->validate(
            [

                'laporan_id' => [
                    'required',
                    'integer',
                    'exists:laporan,id',
                ],

                'tindakan' => [
                    'required',
                    'string',
                    'min:3',
                ],

                'penanggung_jawab' => [
                    'nullable',
                    'string',
                    'max:150',
                ],

                'tanggal_mulai' => [
                    'nullable',
                    'date',
                ],

                'target_selesai' => [
                    'nullable',
                    'date',
                    'after_or_equal:tanggal_mulai',
                ],

                'estimasi_anggaran' => [
                    'nullable',
                    'numeric',
                    'min:0',
                ],

                'catatan' => [
                    'nullable',
                    'string',
                ],

            ],

            [

                'laporan_id.required' =>
                    'Laporan wajib dipilih.',

                'laporan_id.exists' =>
                    'Laporan yang dipilih tidak ditemukan.',

                'tindakan.required' =>
                    'Tindakan perbaikan wajib diisi.',

                'tindakan.min' =>
                    'Tindakan perbaikan minimal 3 karakter.',

                'target_selesai.after_or_equal' =>
                    'Target selesai harus sama dengan atau setelah tanggal mulai.',

                'estimasi_anggaran.numeric' =>
                    'Estimasi anggaran harus berupa angka.',

                'estimasi_anggaran.min' =>
                    'Estimasi anggaran tidak boleh kurang dari 0.',

            ]
        );


        $laporan = Laporan::query()
            ->induk()
            ->whereIn('status', [
                'diverifikasi',
                'dalam_perbaikan',
                'selesai',
            ])
            ->whereNotNull('skor_prioritas')
            ->findOrFail($validated['laporan_id']);


        if ($laporan->rencanaPerbaikan()->exists()) {

            return back()
                ->withInput()
                ->withErrors([
                    'laporan_id' =>
                        'Laporan tersebut sudah memiliki rencana perbaikan.',
                ]);
        }


        $statusRencana = $this->tentukanStatus(
            $validated['tanggal_mulai'] ?? null,
            null
        );


        DB::transaction(function () use (
            $validated,
            $laporan,
            $statusRencana
        ) {

            $rencana = RencanaPerbaikan::create([

                'laporan_id' =>
                    $validated['laporan_id'],

                'tindakan' =>
                    $validated['tindakan'],

                'penanggung_jawab' =>
                    $validated['penanggung_jawab'] ?? null,

                'tanggal_mulai' =>
                    $validated['tanggal_mulai'] ?? null,

                'target_selesai' =>
                    $validated['target_selesai']
                    ?? (
                        !empty($validated['tanggal_mulai'])

                            ? Carbon::parse(
                                $validated['tanggal_mulai']
                            )
                                ->addMonthNoOverflow()
                                ->toDateString()

                            : null
                    ),

                'tanggal_selesai' =>
                    null,

                'estimasi_anggaran' =>
                    $validated['estimasi_anggaran'] ?? 0,

                // RENCANA BARU SELALU DIMULAI DARI RP0
                'realisasi_anggaran' =>
                    0,

                'status' =>
                    $statusRencana,

                'catatan' =>
                    $validated['catatan'] ?? null,
            ]);


            // Sinkronisasi status laporan
            if ($rencana->status === 'dalam_perbaikan') {

                $laporan->update([
                    'status' => 'dalam_perbaikan',
                ]);

            } elseif ($rencana->status === 'selesai') {

                $laporan->update([
                    'status' => 'selesai',
                ]);
            }

        });


        return redirect()
            ->route('dinas.rencana-perbaikan.index')
            ->with(
                'sukses',
                'Rencana perbaikan berhasil ditambahkan.'
            );
    }


    /**
     * Form edit.
     */
    public function edit(RencanaPerbaikan $rencanaPerbaikan)
    {
        abort_unless(auth()->user()->isDinas(), 403);

        $rencanaPerbaikan->load([
            'laporan.kategoriHambatan',
            'laporan.wilayah',
        ]);

        return view(
            'rencana perbaikan.edit',
            compact('rencanaPerbaikan')
        );
    }


    /**
     * Update rencana.
     */
    public function update(
        Request $request,
        RencanaPerbaikan $rencanaPerbaikan
    ) {

        abort_unless(auth()->user()->isDinas(), 403);

        $validated = $request->validate(
            [

                'tindakan' => [
                    'required',
                    'string',
                    'min:3',
                ],

                'penanggung_jawab' => [
                    'nullable',
                    'string',
                    'max:150',
                ],

                'tanggal_mulai' => [
                    'nullable',
                    'date',
                ],

                'target_selesai' => [
                    'nullable',
                    'date',
                    'after_or_equal:tanggal_mulai',
                ],

                'tanggal_selesai' => [
                    'nullable',
                    'date',
                    'after_or_equal:tanggal_mulai',
                ],

                'estimasi_anggaran' => [
                    'nullable',
                    'numeric',
                    'min:0',
                ],

                'realisasi_anggaran' => [
                    'nullable',
                    'numeric',
                    'min:0',
                ],

                'catatan' => [
                    'nullable',
                    'string',
                ],

            ],

            [

                'tindakan.required' =>
                    'Tindakan perbaikan wajib diisi.',

                'tindakan.min' =>
                    'Tindakan perbaikan minimal 3 karakter.',

                'target_selesai.after_or_equal' =>
                    'Target selesai harus sama dengan atau setelah tanggal mulai.',

                'tanggal_selesai.after_or_equal' =>
                    'Tanggal selesai harus sama dengan atau setelah tanggal mulai.',

                'estimasi_anggaran.numeric' =>
                    'Estimasi anggaran harus berupa angka.',

                'estimasi_anggaran.min' =>
                    'Estimasi anggaran tidak boleh kurang dari 0.',

                'realisasi_anggaran.numeric' =>
                    'Realisasi anggaran harus berupa angka.',

                'realisasi_anggaran.min' =>
                    'Realisasi anggaran tidak boleh kurang dari 0.',
            ]
        );


        $statusRencana = $this->tentukanStatus(
            $validated['tanggal_mulai'] ?? null,
            $validated['tanggal_selesai'] ?? null
        );


        DB::transaction(function () use (
            $validated,
            $rencanaPerbaikan,
            $statusRencana
        ) {

            $rencanaPerbaikan->update([

                'tindakan' =>
                    $validated['tindakan'],

                'penanggung_jawab' =>
                    $validated['penanggung_jawab'] ?? null,

                'tanggal_mulai' =>
                    $validated['tanggal_mulai'] ?? null,

                'target_selesai' =>
                    $validated['target_selesai']
                    ?? (
                        !empty($validated['tanggal_mulai'])

                            ? Carbon::parse(
                                $validated['tanggal_mulai']
                            )
                                ->addMonthNoOverflow()
                                ->toDateString()

                            : null
                    ),

                'tanggal_selesai' =>
                    $validated['tanggal_selesai'] ?? null,

                'estimasi_anggaran' =>
                    $validated['estimasi_anggaran'] ?? 0,

                // REALISASI DIUBAH MELALUI FORM EDIT
                'realisasi_anggaran' =>
                    $validated['realisasi_anggaran'] ?? 0,

                'status' =>
                    $statusRencana,

                'catatan' =>
                    $validated['catatan'] ?? null,
            ]);


            if ($rencanaPerbaikan->status === 'dalam_perbaikan') {

                $rencanaPerbaikan->laporan->update([
                    'status' => 'dalam_perbaikan',
                ]);

            } elseif ($rencanaPerbaikan->status === 'selesai') {

                $rencanaPerbaikan->laporan->update([
                    'status' => 'selesai',
                ]);

            } elseif ($rencanaPerbaikan->status === 'belum_dimulai') {

                $rencanaPerbaikan->laporan->update([
                    'status' => 'diverifikasi',
                ]);
            }

        });


        return redirect()
            ->route('dinas.rencana-perbaikan.index')
            ->with(
                'sukses',
                'Rencana perbaikan berhasil diperbarui.'
            );
    }
}