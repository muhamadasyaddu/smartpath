<?php

namespace App\Http\Controllers;

use App\Models\FasilitasPublik;
use App\Models\Audit;
use App\Http\Requests\StoreFasilitasPublikRequest;
use App\Http\Requests\UpdateFasilitasPublikRequest;
use Illuminate\Http\Request;

class FasilitasPublikController extends Controller
{
    public function index(Request $request)
    {
        $query = FasilitasPublik::with(['wilayah']);

        if ($request->filled('jenis')) {
            $query->jenis($request->jenis);
        }
        if ($request->filled('aktif') && $request->aktif === '1') {
            $query->aktif();
        }

        $fasilitasPublik = $query->orderBy('nama')->paginate(15);
        return view('Admin.fasilitas-publik.index', compact('fasilitasPublik'));
    }

    public function create()
    {
        $wilayahList = \App\Models\Wilayah::aktif()->level('kecamatan')->get();
        return view('Admin.fasilitas-publik.create', compact('wilayahList'));
    }

    public function store(StoreFasilitasPublikRequest $request)
    {
        $validated = $request->validated();
        $fasilitasPublik = FasilitasPublik::create($validated);

        Audit::log(auth()->id(), 'buat_fasilitas_publik', 'fasilitas_publik', $fasilitasPublik->id, null, $fasilitasPublik->toArray(), "Fasilitas publik baru: {$fasilitasPublik->nama}");

        return redirect()->route('Admin.fasilitas-publik.index')->with('sukses', 'Fasilitas publik berhasil ditambahkan.');
    }

    public function show(FasilitasPublik $fasilitasPublik)
    {
        $fasilitasPublik->load(['wilayah', 'laporanTerdekat' => fn($q) => $q->orderByDesc('skor_prioritas')->limit(5)]);
        return view('Admin.fasilitas-publik.show', compact('fasilitasPublik'));
    }

    public function edit(FasilitasPublik $fasilitasPublik)
    {
        $wilayahList = \App\Models\Wilayah::aktif()->level('kecamatan')->get();
        return view('Admin.fasilitas-publik.edit', compact('fasilitasPublik', 'wilayahList'));
    }

    public function update(UpdateFasilitasPublikRequest $request, FasilitasPublik $fasilitasPublik)
    {
        $dataLama = $fasilitasPublik->toArray();
        $validated = $request->validated();
        $fasilitasPublik->update($validated);

        Audit::log(auth()->id(), 'ubah_fasilitas_publik', 'fasilitas_publik', $fasilitasPublik->id, $dataLama, $fasilitasPublik->fresh()->toArray(), "Fasilitas publik diubah: {$fasilitasPublik->nama}");

        return redirect()->route('Admin.fasilitas-publik.index')->with('sukses', 'Fasilitas publik berhasil diperbarui.');
    }

    public function destroy(FasilitasPublik $fasilitasPublik)
    {
        Audit::log(auth()->id(), 'hapus_fasilitas_publik', 'fasilitas_publik', $fasilitasPublik->id, $fasilitasPublik->toArray(), null, "Fasilitas publik dihapus: {$fasilitasPublik->nama}");
        $fasilitasPublik->delete();

        return redirect()->route('Admin.fasilitas-publik.index')->with('sukses', 'Fasilitas publik berhasil dihapus.');
    }
}