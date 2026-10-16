
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    /**
     * Izinkan pengguna yang login mengubah profilnya.
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Aturan validasi perubahan profil.
     */
    public function rules()
    {
        $userId = $this->user()->id;

        return [
            'nama_lengkap' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId),
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

            'foto_profil' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],

            'hapus_foto_profil' => [
                'nullable',
                'in:0,1',
            ],

            'adalah_disabilitas' => [
                'nullable',
                'boolean',
            ],

            'jenis_disabilitas' => [
                'nullable',
                'string',
                'max:255',
            ],
        ];
    }

    /**
     * Pesan validasi dalam bahasa Indonesia.
     */
    public function messages()
    {
        return [
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'nama_lengkap.max' => 'Nama lengkap maksimal 255 karakter.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan akun lain.',
            'nomor_hp.max' => 'Nomor HP maksimal 20 karakter.',
            'wilayah_id.exists' => 'Wilayah yang dipilih tidak valid.',
            'foto_profil.image' => 'File harus berupa gambar.',
            'foto_profil.mimes' => 'Foto harus berformat JPG, JPEG, PNG, atau WEBP.',
            'foto_profil.max' => 'Ukuran foto maksimal 2 MB.',
            'hapus_foto_profil.in' => 'Permintaan penghapusan foto tidak valid.',
        ];
    }
}
