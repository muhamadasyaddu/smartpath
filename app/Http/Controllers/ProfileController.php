<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Models\Wilayah;

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
            'laporanDibuat' => function ($query) {
                $query->orderByDesc('created_at')->limit(5);
            },
        ]);

        $wilayah = Wilayah::aktif()
            ->orderBy('nama')
            ->get();

        return view('profile.edit', compact('user', 'wilayah'));
    }

    /**
     * Simpan perubahan data profil.
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'nama_lengkap' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],

            'nomor_hp' => [
                'nullable',
                'string',
                'max:20',
            ],

            'wilayah_id' => [
                'nullable',
                'exists:wilayah,id',
            ],

            'adalah_disabilitas' => [
                'required',
                'boolean',
            ],

            'jenis_disabilitas' => [
                'nullable',
                'string',
                'max:255',
            ],
        ]);

        $user->nama_lengkap = $validated['nama_lengkap'];
        $user->email = $validated['email'];
        $user->nomor_hp = $validated['nomor_hp'] ?? null;
        $user->wilayah_id = $validated['wilayah_id'] ?? null;
        $user->adalah_disabilitas = $validated['adalah_disabilitas'];
        $user->jenis_disabilitas = $validated['jenis_disabilitas'] ?? null;

        $user->save();

        return redirect()
            ->route('profile.edit')
            ->with('sukses', 'Perubahan profil berhasil disimpan.');
    }

    /**
     * Ubah foto profil pengguna.
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

    /**
     * Hapus foto profil pengguna.
     */
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
