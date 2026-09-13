<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreVerifikasiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        $isReject = $this->routeIs('admin.verifikasi.reject');

        return [
            'catatan_admin' => [
                $isReject ? 'required' : 'nullable',
                'string',
                'max:1000',
            ],
            'kategori_koreksi' => [
                'nullable',
                Rule::exists('kategori_hambatan', 'id')->where(
                    fn ($query) => $query->where('aktif', true)
                ),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'catatan_admin.required' => 'Alasan penolakan wajib diisi.',
            'catatan_admin.max' => 'Catatan admin maksimal 1000 karakter.',
            'kategori_koreksi.exists' => 'Kategori koreksi tidak valid.',
        ];
    }
}