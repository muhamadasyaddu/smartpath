<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePengaturanPrioritasRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'label' => ['required', 'string', 'max:100'],
            'bobot_keparahan' => ['required', 'numeric', 'between:0,1'],
            'bobot_pelapor' => ['required', 'numeric', 'between:0,1'],
            'bobot_fasilitas' => ['required', 'numeric', 'between:0,1'],
            'radius_deduplikasi_m' => ['required', 'integer', 'min:10', 'max:500'],
            'radius_fasilitas_m' => ['required', 'integer', 'min:100', 'max:5000'],
            'adalah_aktif' => ['nullable', 'boolean'],
            'catatan' => ['nullable', 'string'],
            'berlaku_sejak' => ['nullable', 'date'],
            'berlaku_hingga' => ['nullable', 'date', 'after:berlaku_sejak'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $total = (float) $this->bobot_keparahan + (float) $this->bobot_pelapor + (float) $this->bobot_fasilitas;
            if (round($total, 2) !== 1.00) {
                $validator->errors()->add('bobot_keparahan', 'Total bobot harus sama dengan 1.00 (saat ini: ' . number_format($total, 2) . ').');
            }
        });
    }

    public function messages(): array
    {
        return [
            'label.required' => 'Label konfigurasi harus diisi.',
            'bobot_keparahan.required' => 'Bobot keparahan harus diisi.',
            'bobot_pelapor.required' => 'Bobot pelapor harus diisi.',
            'bobot_fasilitas.required' => 'Bobot fasilitas harus diisi.',
            'radius_deduplikasi_m.required' => 'Radius deduplikasi harus diisi.',
            'radius_fasilitas_m.required' => 'Radius fasilitas harus diisi.',
            'berlaku_hingga.after' => 'Tanggal berlaku hingga harus setelah berlaku sejak.',
        ];
    }
}