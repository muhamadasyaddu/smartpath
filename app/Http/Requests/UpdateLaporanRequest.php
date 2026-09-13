<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLaporanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check()
            && (auth()->user()->isWarga() || auth()->user()->isAdmin());
    }

    public function rules(): array
    {
        return [
            'judul' => ['required', 'string', 'max:200'],
            'deskripsi' => ['required', 'string', 'max:5000'],
            'alamat_lengkap' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'judul.required' => 'Judul laporan harus diisi.',
            'judul.max' => 'Judul laporan maksimal 200 karakter.',
            'deskripsi.required' => 'Deskripsi laporan harus diisi.',
            'deskripsi.max' => 'Deskripsi laporan maksimal 5000 karakter.',
        ];
    }
}