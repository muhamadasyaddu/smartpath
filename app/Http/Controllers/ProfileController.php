<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Audit;
use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Wilayah;

class ProfileController extends Controller
{
    /**
     * Tampilkan halaman profil pengguna.
     */
    public function edit()
    {
     
    $user = auth()->user();

    $user->load([
        'wilayah',
        'laporanDibuat' => fn ($q) =>
            $q->orderByDesc('created_at')->limit(5),
    ]);

$wilayah = Wilayah::aktif()
    ->orderBy('nama')
    ->get();
    return view('profile.edit', compact('user', 'wilayah'));
}
    

    /**
     * Update profil pengguna.
     */
    public function update(UpdateProfileRequest $request)
    {
        $user = auth()->user();
        $validated = $request->validated();

        if ($request->hasFile('foto_profil')) {
            if ($user->foto_profil && Storage::disk('public')->exists($user->foto_profil)) {
                Storage::disk('public')->delete($user->foto_profil);
            }

            $file = $request->file('foto_profil');
            $namaFile = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $pathFile = $file->storeAs('profil', $namaFile, 'public');
            $validated['foto_profil'] = $pathFile;
        }

        $dataLama = ['nama_lengkap' => $user->nama_lengkap, 'email' => $user->email];
        $user->update($validated);

        Audit::log(auth()->id(), 'ubah_profil', 'users', $user->id, $dataLama, ['nama_lengkap' => $user->nama_lengkap, 'email' => $user->email], 'Profil diperbarui');

        return back()->with('sukses', 'Profil berhasil diperbarui.');
    }
}