<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateKonfigurasiSistemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check()
            && auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'nilai' => [
                'required',
                'string',
                'max:10000',
            ],

            'keterangan' => [
                'nullable',
                'string',
                'max:255',
            ],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {

            $konfigurasi = $this->route(
                'konfigurasiSistem'
            );

            if (!$konfigurasi) {
                return;
            }

            $type = $konfigurasi->tipe_nilai;
            $value = trim((string) $this->input('nilai'));

            $valid = match ($type) {

                'angka' =>
                    filter_var(
                        $value,
                        FILTER_VALIDATE_INT
                    ) !== false,

                'desimal' =>
                    is_numeric($value),

                'boolean' =>
                    in_array(
                        strtolower($value),
                        [
                            '0',
                            '1',
                            'true',
                            'false',
                            'ya',
                            'tidak',
                        ],
                        true
                    ),

                'json' => $this->isValidJson($value),

                default => true,
            };

            if (!$valid) {

                $validator->errors()->add(
                    'nilai',
                    "Nilai tidak sesuai dengan tipe {$type}."
                );
            }
        });
    }

    protected function isValidJson(string $value): bool
    {
        json_decode($value);

        return json_last_error() === JSON_ERROR_NONE;
    }

    public function messages(): array
    {
        return [
            'nilai.required' =>
                'Nilai konfigurasi harus diisi.',

            'nilai.max' =>
                'Nilai konfigurasi terlalu panjang.',

            'keterangan.max' =>
                'Keterangan maksimal 255 karakter.',
        ];
    }
}