<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

   
public function rules(): array
{
    $userId = auth()->id();

    return [
        'nama_lengkap' => ['sometimes', 'string', 'max:150'],

        'email' => [
            'sometimes',
            'string',
            'email',
            'max:150',
            Rule::unique('users', 'email')->ignore($userId),
        ],

        'nomor_hp' => ['nullable', 'string', 'max:20'],

        'foto_profil' => [
            'nullable',
            'image',
            'mimes:jpeg,png,jpg,webp',
            'max:2048',
        ],

        'wilayah_id' => [
            'sometimes',
            'nullable',
            'integer',
            'exists:wilayah,id',
        ],

        'adalah_disabilitas' => ['nullable', 'boolean'],

        'jenis_disabilitas' => ['nullable', 'string', 'max:100'],
    ];
}


    public function messages(): array
    {
       
return [
    'nama_lengkap' => ['sometimes', 'string', 'max:150'],
    'email' => [
        'sometimes',
        'string',
        'email',
        'max:150',
Rule::unique('users', 'email')->ignore(auth()->id()),
    ],
    'nomor_hp' => ['nullable', 'string', 'max:20'],
    'foto_profil' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],

    // Validasi wilayah pengguna
    'wilayah_id' => ['sometimes', 'nullable', 'integer', 'exists:wilayah,id'],

    'adalah_disabilitas' => ['nullable', 'boolean'],
    'jenis_disabilitas' => ['nullable', 'string', 'max:100'],
];

    }
}
