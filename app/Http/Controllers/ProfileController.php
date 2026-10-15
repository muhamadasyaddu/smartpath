<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use App\Models\Audit;
use App\Models\Wilayah;
use Illuminate\Support\Facades\Storage;
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
    public function update(UpdateProfileRequest $request)
    {
        $user = $request->user();
        $validated = $request->validated();

        $fotoLama = $user->foto_profil;
        $fotoBaru = null;

        /*
         * Simpan foto baru dengan nama yang dibuat oleh Laravel.
         * Jangan mempercayai nama file dari perangkat pengguna.
         */
        if ($request->hasFile('foto_profil')) {
            $fotoBaru = $request->file('foto_profil')
                ->store('profil', 'public');

            if (!$fotoBaru) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'foto_profil' => 'Foto gagal disimpan. Silakan coba lagi.',
                    ]);
            }

            $validated['foto_profil'] = $fotoBaru;
        }

        $dataLama = [
            'nama_lengkap' => $user->nama_lengkap,
            'email' => $user->email,
        ];

        try {
            $user->update($validated);
        } catch (Throwable $exception) {
            /*
             * Jika pembaruan database gagal, hapus file baru yang
             * telanjur tersimpan agar tidak menjadi file yatim.
             */
            if ($fotoBaru) {
                Storage::disk('public')->delete($fotoBaru);
            }

            throw $exception;
        }

        /*
         * Hapus foto lama hanya setelah foto baru berhasil disimpan
         * dan perubahan pengguna sudah dilakukan.
         */
        if (
            $fotoBaru &&
            $fotoLama &&
            $fotoLama !== $fotoBaru
        ) {
            Storage::disk('public')->delete($fotoLama);
        }

        Audit::log(
            $user->id,
            'ubah_profil',
            'users',
            $user->id,
            $dataLama,
            [
                'nama_lengkap' => $user->nama_lengkap,
                'email' => $user->email,
            ],
            'Profil diperbarui'
        );

        return redirect()
            ->route('profile.edit')
            ->with('sukses', 'Profil berhasil diperbarui.');
    }
}