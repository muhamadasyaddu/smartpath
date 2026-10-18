<?php

namespace App\Http\Requests;

use App\Models\Laporan;
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
        $isKoreksi = $this->routeIs('admin.verifikasi.koreksi-relasi');
        $isApprove = $this->routeIs('admin.verifikasi.approve');

        $laporan = $this->route('laporan');

        // Keputusan duplikasi wajib hanya bila laporan masih terhubung ke induk.
        $perluKeputusanDuplikasi = $isApprove
            && $laporan instanceof Laporan
            && $laporan->laporan_induk_id !== null;

        return [
            'catatan_admin' => [
                ($isReject || $isKoreksi) ? 'required' : 'nullable',
                'string',
                'max:1000',
            ],
            'kategori_koreksi' => [
                'nullable',
                Rule::exists('kategori_hambatan', 'id')->where(
                    fn ($query) => $query->where('aktif', true)
                ),
            ],
            'keputusan_duplikasi' => [
                $perluKeputusanDuplikasi ? 'required' : 'nullable',
                Rule::in(['mandiri', 'duplikat']),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'catatan_admin.required' => $this->routeIs('admin.verifikasi.koreksi-relasi')
                ? 'Alasan koreksi wajib diisi.'
                : 'Alasan penolakan wajib diisi.',
            'catatan_admin.max' => 'Catatan admin maksimal 1000 karakter.',
            'kategori_koreksi.exists' => 'Kategori koreksi tidak valid.',
            'keputusan_duplikasi.required' => 'Tentukan apakah laporan ini duplikat atau laporan mandiri.',
            'keputusan_duplikasi.in' => 'Pilihan keputusan duplikasi tidak valid.',
        ];
    }
}