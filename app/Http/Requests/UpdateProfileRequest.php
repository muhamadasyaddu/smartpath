<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $userId = $this->user()->id;

        return [
            'nama_lengkap' => [
                'required',
                'string',
                'max:150',
            ],

            'email' => [
                'required',
                'string',
                'email',
                'max:150',
                Rule::unique('users', 'email')->ignore($userId),
            ],

            'nomor_hp' => [
                'nullable',
                'string',
                'max:20',
            ],

            'foto_profil' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],

            'wilayah_id' => [
                'nullable',
                'integer',
                'exists:wilayah,id',
            ],

            'adalah_disabilitas' => [
                'nullable',
                'boolean',
            ],

            'jenis_disabilitas' => [
                'nullable',
                'string',
                'max:100',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'nama_lengkap.required' =>
                'Nama lengkap wajib diisi.',
            'nama_lengkap.max' =>
                'Nama lengkap maksimal 150 karakter.',

            'email.required' =>
                'Email wajib diisi.',
            'email.email' =>
                'Masukkan alamat email yang valid.',
            'email.unique' =>
                'Email tersebut sudah digunakan akun lain.',

            'nomor_hp.max' =>
                'Nomor HP maksimal 20 karakter.',

            'foto_profil.image' =>
                'File yang diunggah harus berupa gambar.',
            'foto_profil.mimes' =>
                'Foto harus berformat JPG, JPEG, PNG, atau WEBP.',
            'foto_profil.max' =>
                'Ukuran foto maksimal 2 MB.',

            'wilayah_id.exists' =>
                'Wilayah yang dipilih tidak tersedia.',
            'jenis_disabilitas.max' =>
                'Jenis disabilitas maksimal 100 karakter.',
        ];
    }
}