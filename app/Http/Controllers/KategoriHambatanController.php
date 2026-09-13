<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreKategoriHambatanRequest;
use App\Http\Requests\UpdateKategoriHambatanRequest;
use App\Models\Audit;
use App\Models\KategoriHambatan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class KategoriHambatanController extends Controller
{
    public function index(Request $request)
    {
        $query = KategoriHambatan::query()
            ->withCount('laporan');

        if ($request->filled('aktif')) {
            $query->where(
                'aktif',
                $request->string('aktif')->toString() === '1'
            );
        }

        $kategoriHambatan = $query
            ->urutTampil()
            ->orderBy('nama')
            ->paginate(15)
            ->withQueryString();

        return view(
            'Admin.kategori-hambatan.index',
            compact('kategoriHambatan')
        );
    }

    public function create()
    {
        return view('Admin.kategori-hambatan.create');
    }

    public function store(StoreKategoriHambatanRequest $request)
    {
        $validated = $request->validated();

        $kategoriHambatan = KategoriHambatan::create($validated);

        Audit::log(
            auth()->id(),
            'buat_kategori_hambatan',
            'kategori_hambatan',
            $kategoriHambatan->id,
            null,
            $kategoriHambatan->toArray(),
            "Kategori hambatan baru: {$kategoriHambatan->nama}"
        );

        return redirect()
            ->route('admin.kategori-hambatan.index')
            ->with('sukses', 'Kategori hambatan berhasil ditambahkan.');
    }

    public function show(KategoriHambatan $kategoriHambatan)
    {
        $kategoriHambatan->loadCount('laporan');

        return view(
            'Admin.kategori-hambatan.show',
            compact('kategoriHambatan')
        );
    }

    public function edit(KategoriHambatan $kategoriHambatan)
    {
        return view(
            'Admin.kategori-hambatan.edit',
            compact('kategoriHambatan')
        );
    }

    public function update(
        UpdateKategoriHambatanRequest $request,
        KategoriHambatan $kategoriHambatan
    ) {
        $dataLama = $kategoriHambatan->toArray();

        $kategoriHambatan->update(
            $request->validated()
        );

        Audit::log(
            auth()->id(),
            'ubah_kategori_hambatan',
            'kategori_hambatan',
            $kategoriHambatan->id,
            $dataLama,
            $kategoriHambatan->fresh()->toArray(),
            "Kategori hambatan diubah: {$kategoriHambatan->nama}"
        );

        return redirect()
            ->route('admin.kategori-hambatan.index')
            ->with('sukses', 'Kategori hambatan berhasil diperbarui.');
    }

    public function destroy(KategoriHambatan $kategoriHambatan)
    {
        /*
         * Jangan menghapus kategori yang sudah dipakai laporan.
         * Nonaktifkan kategori tersebut sebagai gantinya.
         */
        if ($kategoriHambatan->laporan()->exists()) {
            return back()->with(
                'galat',
                'Kategori tidak dapat dihapus karena sudah digunakan oleh laporan. Nonaktifkan kategori jika sudah tidak ingin digunakan.'
            );
        }

        $dataLama = $kategoriHambatan->toArray();

        Audit::log(
            auth()->id(),
            'hapus_kategori_hambatan',
            'kategori_hambatan',
            $kategoriHambatan->id,
            $dataLama,
            null,
            "Kategori hambatan dihapus: {$kategoriHambatan->nama}"
        );

        $kategoriHambatan->delete();

        return redirect()
            ->route('admin.kategori-hambatan.index')
            ->with('sukses', 'Kategori hambatan berhasil dihapus.');
    }
}