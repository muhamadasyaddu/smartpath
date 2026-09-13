<?php

namespace App\Http\Requests;

use App\Models\KonfigurasiSistem;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLaporanRequest extends FormRequest
{
    public function authorize(): bool
    {
        if (!auth()->check()) {
            return false;
        }

        return auth()->user()->isWarga()
            || auth()->user()->isAdmin();
    }

    /**
     * Mengambil batas maksimum foto dari konfigurasi sistem.
     *
     * Nilai minimal dipastikan 1 agar konfigurasi yang salah
     * tidak menyebabkan form laporan tidak dapat digunakan.
     */
    protected function getMaxFoto(): int
    {
        $maxFoto = (int) KonfigurasiSistem::getValue(
            'max_foto_per_laporan',
            5
        );

        return max(1, $maxFoto);
    }

    public function rules(): array
    {
        $maxFoto = $this->getMaxFoto();

        return [
            'kategori_hambatan_id' => [
                'required',
                Rule::exists(
                    'kategori_hambatan',
                    'id'
                )->where(
                    fn ($query) => $query->where(
                        'aktif',
                        true
                    )
                ),
            ],

            'wilayah_id' => [
                'required',
                Rule::exists(
                    'wilayah',
                    'id'
                )->where(
                    fn ($query) => $query->where(
                        'aktif',
                        true
                    )
                ),
            ],

            'latitude' => [
                'required',
                'numeric',
                'between:-90,90',
            ],

            'longitude' => [
                'required',
                'numeric',
                'between:-180,180',
            ],

            'alamat_lengkap' => [
                'nullable',
                'string',
                'max:255',
            ],

            'judul' => [
                'required',
                'string',
                'max:200',
            ],

            'deskripsi' => [
                'required',
                'string',
                'max:5000',
            ],

            'sumber_koordinat' => [
                'required',
                'in:gps_otomatis,manual',
            ],

            'foto' => [
                'required',
                'array',
                'min:1',
                'max:' . $maxFoto,
            ],

            'foto.*' => [
                'required',
                'image',
                'mimes:jpeg,png,jpg,webp',
                'max:5120',
            ],
        ];
    }

    public function messages(): array
    {
        $maxFoto = $this->getMaxFoto();

        return [
            'kategori_hambatan_id.required' =>
                'Kategori hambatan harus dipilih.',

            'kategori_hambatan_id.exists' =>
                'Kategori hambatan tidak valid atau sedang nonaktif.',

            'wilayah_id.required' =>
                'Kecamatan harus dipilih.',

            'wilayah_id.exists' =>
                'Kecamatan tidak valid atau sedang nonaktif.',

            'latitude.required' =>
                'Lokasi (latitude) harus ditentukan.',

            'latitude.numeric' =>
                'Latitude harus berupa angka.',

            'latitude.between' =>
                'Nilai latitude tidak valid.',

            'longitude.required' =>
                'Lokasi (longitude) harus ditentukan.',

            'longitude.numeric' =>
                'Longitude harus berupa angka.',

            'longitude.between' =>
                'Nilai longitude tidak valid.',

            'judul.required' =>
                'Judul laporan harus diisi.',

            'judul.max' =>
                'Judul laporan maksimal 200 karakter.',

            'deskripsi.required' =>
                'Deskripsi hambatan harus diisi.',

            'deskripsi.max' =>
                'Deskripsi laporan maksimal 5000 karakter.',

            'sumber_koordinat.required' =>
                'Sumber koordinat harus ditentukan.',

            'sumber_koordinat.in' =>
                'Sumber koordinat tidak valid.',

            'foto.required' =>
                'Minimal satu foto harus diunggah.',

            'foto.array' =>
                'Format data foto tidak valid.',

            'foto.min' =>
                'Minimal satu foto harus diunggah.',

            'foto.max' =>
                'Maksimal ' . $maxFoto . ' foto dapat diunggah.',

            'foto.*.required' =>
                'Setiap foto harus memiliki berkas.',

            'foto.*.image' =>
                'Setiap file harus berupa gambar yang valid.',

            'foto.*.mimes' =>
                'Format gambar harus JPEG, PNG, JPG, atau WebP.',

            'foto.*.max' =>
                'Ukuran setiap foto maksimal 5MB.',
        ];
    }
}