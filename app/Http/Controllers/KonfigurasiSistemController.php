<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreKonfigurasiSistemRequest;
use App\Http\Requests\UpdateKonfigurasiSistemRequest;
use App\Models\Audit;
use App\Models\KonfigurasiSistem;

class KonfigurasiSistemController extends Controller
{
    /**
     * Menampilkan seluruh konfigurasi sistem.
     */
    public function index()
    {
        $konfigurasi = KonfigurasiSistem::query()
            ->orderBy('kunci')
            ->paginate(20);

        return view(
            'Admin.konfigurasi-sistem.index',
            compact('konfigurasi')
        );
    }

    /**
     * Form edit konfigurasi.
     */
    public function edit(KonfigurasiSistem $konfigurasiSistem)
    {
        abort_unless(
            $konfigurasiSistem->dapat_diedit_ui,
            403,
            'Konfigurasi ini dikunci dari perubahan melalui antarmuka.'
        );

        return view(
            'Admin.konfigurasi-sistem.edit',
            compact('konfigurasiSistem')
        );
    }

    /**
     * Memperbarui konfigurasi.
     */
    public function update(
        UpdateKonfigurasiSistemRequest $request,
        KonfigurasiSistem $konfigurasiSistem
    ) {
        abort_unless(
            $konfigurasiSistem->dapat_diedit_ui,
            403,
            'Konfigurasi ini dikunci dari perubahan melalui antarmuka.'
        );

        $validated = $request->validated();

        $dataLama = $konfigurasiSistem->toArray();

        $konfigurasiSistem->update($validated);

        Audit::log(
            auth()->id(),
            'ubah_konfigurasi_sistem',
            'konfigurasi_sistem',
            $konfigurasiSistem->id,
            $dataLama,
            $konfigurasiSistem->fresh()->toArray(),
            "Konfigurasi diubah: {$konfigurasiSistem->kunci}"
        );

        return redirect()
            ->route('admin.konfigurasi-sistem.index')
            ->with(
                'sukses',
                'Konfigurasi berhasil diperbarui.'
            );
    }

    /**
     * Menambahkan konfigurasi baru.
     *
     * Route tetap dipertahankan agar struktur resource
     * yang sudah ada tidak rusak.
     */
    public function store(StoreKonfigurasiSistemRequest $request)
    {
        $konfigurasiSistem = KonfigurasiSistem::create(
            $request->validated()
        );

        Audit::log(
            auth()->id(),
            'buat_konfigurasi_sistem',
            'konfigurasi_sistem',
            $konfigurasiSistem->id,
            null,
            $konfigurasiSistem->toArray(),
            "Konfigurasi baru: {$konfigurasiSistem->kunci}"
        );

        return redirect()
            ->route('admin.konfigurasi-sistem.index')
            ->with(
                'sukses',
                'Konfigurasi berhasil ditambahkan.'
            );
    }
}