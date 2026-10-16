<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Audit;
use App\Models\Wilayah;
use Throwable;

class ProfileController extends Controller
{
    /**
     * Tampilkan profil pengguna yang sedang login.
     */
    public function edit()
    {
        $user = auth()->user();

        $user->load([
            'wilayah',
            'laporanDibuat' => fn ($query) =>
                $query->orderByDesc('created_at')->limit(5),
        ]);

        $wilayah = Wilayah::aktif()
            ->orderBy('nama')
            ->get();

        return view('profile.edit', compact('user', 'wilayah'));
    }

    /**
     * Simpan perubahan profil.
     */
    

public function updateFoto(Request $request)
{
    $request->validate([
        'foto_profil' => [
            'required',
            'image',
            'mimes:jpg,jpeg,png,webp',
            'max:2048',
        ],
    ]);

    $user = $request->user();
    $fotoLama = $user->foto_profil;

    $fotoBaru = $request->file('foto_profil')
        ->store('foto-profil', 'public');

    if (!$fotoBaru) {
        return back()->withErrors([
            'foto_profil' => 'Foto gagal disimpan.',
        ]);
    }

    try {
        $user->foto_profil = $fotoBaru;
        $user->save();
    } catch (\Throwable $e) {
        Storage::disk('public')->delete($fotoBaru);
        throw $e;
    }

    if ($fotoLama && $fotoLama !== $fotoBaru) {
        Storage::disk('public')->delete($fotoLama);
    }

    return redirect()
        ->route('profile.edit')
        ->with('sukses', 'Foto profil berhasil diperbarui.');
}

public function deleteFoto(Request $request)
{
    $user = $request->user();
    $fotoLama = $user->foto_profil;

    if (!$fotoLama) {
        return redirect()
            ->route('profile.edit')
            ->with('sukses', 'Foto profil memang sudah kosong.');
    }

    $user->foto_profil = null;
    $user->save();

    Storage::disk('public')->delete($fotoLama);

    return redirect()
        ->route('profile.edit')
        ->with('sukses', 'Foto profil berhasil dihapus.');
}

}