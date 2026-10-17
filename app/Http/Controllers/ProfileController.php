<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use App\Models\Wilayah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Throwable;

class ProfileController extends Controller
{
    /**
     * Menampilkan profil pengguna yang sedang login.
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
     * Memperbarui data profil selain foto profil.
     */
    public function update(UpdateProfileRequest $request)
    {
        $user = $request->user();
        $data = $request->validated();

        // Deteksi perubahan email tanpa membedakan huruf besar/kecil.
        $emailBerubah = strcasecmp(
            $user->email,
            $data['email']
        ) !== 0;

        // Perbarui hanya field profil yang diizinkan.
        $user->nama_lengkap = $data['nama_lengkap'];
        $user->email = $data['email'];
        $user->nomor_hp = $data['nomor_hp'] ?? null;
        $user->wilayah_id = $data['wilayah_id'] ?? null;

        $user->adalah_disabilitas = (bool) (
            $data['adalah_disabilitas'] ?? false
        );

        // Jenis disabilitas hanya disimpan jika dipilih.
        $user->jenis_disabilitas = $user->adalah_disabilitas
            ? ($data['jenis_disabilitas'] ?? null)
            : null;

        // Email yang berubah harus diverifikasi kembali.
        if ($emailBerubah) {
            $user->email_terverifikasi = false;
        }

        $user->save();

        return redirect()
            ->route('profile.edit')
            ->with('sukses', 'Data profil berhasil diperbarui.');
    }

    /**
     * Mengunggah foto profil pengguna.
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
        ], [
            'foto_profil.required' =>
                'Pilih foto profil terlebih dahulu.',
            'foto_profil.image' =>
                'File harus berupa gambar yang valid.',
            'foto_profil.mimes' =>
                'Foto harus berformat JPG, JPEG, PNG, atau WEBP.',
            'foto_profil.max' =>
                'Ukuran foto maksimal 2 MB.',
        ]);

        $user = $request->user();
        $fotoLama = $user->foto_profil;

        // Simpan foto baru di disk public Laravel.
        $fotoBaru = $request->file('foto_profil')
            ->store('foto-profil', 'public');

        if (!$fotoBaru) {
            return back()->withErrors([
                'foto_profil' =>
                    'Foto gagal disimpan. Silakan coba kembali.',
            ]);
        }

        try {
            $user->foto_profil = $fotoBaru;
            $user->save();
        } catch (Throwable $e) {
            // Bersihkan file baru jika penyimpanan database gagal.
            Storage::disk('public')->delete($fotoBaru);

            throw $e;
        }

        // Hapus foto lama setelah foto baru berhasil disimpan.
        if ($fotoLama && $fotoLama !== $fotoBaru) {
            Storage::disk('public')->delete($fotoLama);
        }

        return redirect()
            ->route('profile.edit')
            ->with('sukses', 'Foto profil berhasil diperbarui.');
    }

    /**
     * Menghapus foto profil pengguna.
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

        // Perbarui database terlebih dahulu.
        $user->foto_profil = null;
        $user->save();

        // Hapus file lama dari penyimpanan.
        Storage::disk('public')->delete($fotoLama);

        return redirect()
            ->route('profile.edit')
            ->with('sukses', 'Foto profil berhasil dihapus.');
    }
}