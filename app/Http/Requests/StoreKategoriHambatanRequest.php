<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreKategoriHambatanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:100'],
            'slug' => ['required', 'string', 'max:100', 'alpha_dash', 'unique:kategori_hambatan,slug'],
            'keterangan' => ['nullable', 'string', 'max:2000'],
            'bobot_keparahan' => ['required', 'integer', 'between:0,100'],
            'ikon' => ['nullable', 'string', 'max:100'],
            'warna_penanda' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'urutan_tampil' => ['nullable', 'integer', 'min:0', 'max:255'],
            'aktif' => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama.required' => 'Nama kategori harus diisi.',
            'slug.required' => 'Slug harus diisi.',
            'slug.alpha_dash' => 'Slug hanya boleh berisi huruf, angka, tanda hubung, dan garis bawah.',
            'slug.unique' => 'Slug sudah digunakan.',
            'keterangan.max' => 'Keterangan maksimal 2000 karakter.',
            'bobot_keparahan.required' => 'Bobot keparahan harus diisi.',
            'bobot_keparahan.between' => 'Bobot keparahan harus berada pada rentang 0-100.',
            'warna_penanda.required' => 'Warna penanda harus dipilih.',
            'warna_penanda.regex' => 'Warna penanda harus berupa kode HEX 6 digit.',
            'aktif.required' => 'Status kategori harus ditentukan.',
        ];
    }
}