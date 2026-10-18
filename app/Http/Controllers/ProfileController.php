<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use App\Models\Wilayah;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Throwable;

class ProfileController extends Controller
{
    /**
     * Direktori penyimpanan foto profil pada disk public.
     */
    private const DIREKTORI_FOTO = 'foto-profil';

    /**
     * Menampilkan profil pengguna yang sedang login.
     *
     * Administrator dan Dinas memakai layout internal,
     * warga tetap memakai layout warga.
     */
    public function edit(): View
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

        $tampilan = ($user->isAdmin() || $user->isDinas())
            ? 'profile.admin'
            : 'profile.edit';

        return view($tampilan, compact('user', 'wilayah'));
    }

    /**
     * Memperbarui data profil selain foto profil.
     */
    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->validated();

        // Deteksi perubahan email tanpa membedakan huruf besar/kecil.
        $emailBerubah = strcasecmp(
            $user->email,
            $data['email']
        ) !== 0;

        $user->nama_lengkap = $data['nama_lengkap'];
        $user->email = $data['email'];
        $user->nomor_hp = $data['nomor_hp'] ?? null;

        // Wilayah hanya diubah jika field memang dikirim form.
        // Form profil admin/dinas tidak mengirimnya.
        if (array_key_exists('wilayah_id', $data)) {
            $user->wilayah_id = $data['wilayah_id'];
        }

        // Data disabilitas hanya diubah jika dikirim form warga.
        if (array_key_exists('adalah_disabilitas', $data)) {
            $user->adalah_disabilitas = (bool) $data['adalah_disabilitas'];

            $user->jenis_disabilitas = $user->adalah_disabilitas
                ? ($data['jenis_disabilitas'] ?? null)
                : null;
        }

        // Email yang berubah harus diverifikasi kembali.
        if ($emailBerubah) {
            $user->email_terverifikasi = false;
        }

        $user->save();

        return $this->kembaliKeProfil('Data profil berhasil diperbarui.');
    }

    /**
     * Mengunggah foto profil pengguna.
     */
    public function updateFoto(Request $request): RedirectResponse
    {
        $request->validate([
            'foto_profil' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
                'dimensions:max_width=6000,max_height=6000',
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
            'foto_profil.dimensions' =>
                'Dimensi foto maksimal 6000 x 6000 piksel.',
        ]);

        $user = $request->user();
        $fotoLama = $user->foto_profil;

        // Simpan foto baru di disk public dengan nama acak.
        $fotoBaru = $request->file('foto_profil')
            ->store(self::DIREKTORI_FOTO, 'public');

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
            $this->hapusBerkas($fotoBaru);

            throw $e;
        }

        // Hapus foto lama setelah foto baru berhasil disimpan.
        if ($fotoLama && $fotoLama !== $fotoBaru) {
            $this->hapusBerkas($fotoLama);
        }

        return $this->kembaliKeProfil('Foto profil berhasil diperbarui.');
    }

    /**
     * Menghapus foto profil pengguna.
     */
    public function deleteFoto(Request $request): RedirectResponse
    {
        $user = $request->user();
        $fotoLama = $user->foto_profil;

        if (!$fotoLama) {
            return $this->kembaliKeProfil('Foto profil memang sudah kosong.');
        }

        // Perbarui database terlebih dahulu.
        $user->foto_profil = null;
        $user->save();

        // Hapus file dari penyimpanan.
        $this->hapusBerkas($fotoLama);

        return $this->kembaliKeProfil('Foto profil berhasil dihapus.');
    }

    /**
     * Kembali ke halaman profil. View yang tampil ditentukan
     * oleh peran pengguna pada method edit().
     */
    private function kembaliKeProfil(string $pesan): RedirectResponse
    {
        return redirect()
            ->route('profile.edit')
            ->with('sukses', $pesan);
    }

    /**
     * Menghapus berkas hanya jika berada di direktori foto profil.
     */
    private function hapusBerkas(?string $path): void
    {
        if ($path && str_starts_with($path, self::DIREKTORI_FOTO . '/')) {
            Storage::disk('public')->delete($path);
        }
    }
}