<?php

namespace App\Http\Requests;

use App\Models\KonfigurasiSistem;
use App\Models\Laporan;
use App\Models\Wilayah;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLaporanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check()
            && (
                auth()->user()->isWarga()
                || auth()->user()->isAdmin()
            );
    }

    protected function laporanDariRute(): ?Laporan
    {
        $laporan = $this->route('laporan');

        return $laporan instanceof Laporan ? $laporan : null;
    }

    protected function getMaxFoto(): int
    {
        $maxFoto = (int) KonfigurasiSistem::getValue(
            'max_foto_per_laporan',
            5
        );

        return max(1, min($maxFoto, 10));
    }

    /**
     * Envelope wilayah uji coba (sama dengan StoreLaporanRequest).
     */
    protected function isInsidePilotArea(
        float $latitude,
        float $longitude
    ): bool {
        $bounds = config('smartpath.pilot.bounds', []);

        return $latitude >= (float) ($bounds['min_latitude'] ?? -90)
            && $latitude <= (float) ($bounds['max_latitude'] ?? 90)
            && $longitude >= (float) ($bounds['min_longitude'] ?? -180)
            && $longitude <= (float) ($bounds['max_longitude'] ?? 180);
    }

    /**
     * Wilayah selalu ditentukan server dari koordinat.
     * Nilai wilayah_id dari browser tidak dipercaya.
     */
    protected function prepareForValidation(): void
    {
        $latitude = filter_var(
            $this->input('latitude'),
            FILTER_VALIDATE_FLOAT,
            FILTER_NULL_ON_FAILURE
        );

        $longitude = filter_var(
            $this->input('longitude'),
            FILTER_VALIDATE_FLOAT,
            FILTER_NULL_ON_FAILURE
        );

        if (
            $latitude === null
            || $longitude === null
            || !$this->isInsidePilotArea((float) $latitude, (float) $longitude)
        ) {
            $this->merge(['wilayah_id' => null]);

            return;
        }

        $wilayah = Wilayah::nearestKecamatanByReferencePoint(
            (float) $latitude,
            (float) $longitude
        );

        $this->merge(['wilayah_id' => $wilayah?->id]);
    }

    public function rules(): array
    {
        $laporan = $this->laporanDariRute();

        return [
            'kategori_hambatan_id' => [
                'required',
                Rule::exists('kategori_hambatan', 'id')
                    ->where(fn ($query) => $query->where('aktif', true)),
            ],

            'wilayah_id' => [
                'required',
                Rule::exists('wilayah', 'id')
                    ->where(fn ($query) => $query
                        ->where('aktif', true)
                        ->where('level', 'kecamatan')),
            ],

            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],

            'alamat_lengkap' => ['nullable', 'string', 'max:255'],
            'judul' => ['required', 'string', 'max:200'],
            'deskripsi' => ['required', 'string', 'max:5000'],

            'hapus_foto' => ['nullable', 'array'],
            'hapus_foto.*' => [
                'integer',
                Rule::exists('foto_laporan', 'id')
                    ->where('laporan_id', $laporan?->id ?? 0),
            ],

            'foto' => ['nullable', 'array'],
            'foto.*' => [
                'image',
                'mimes:jpeg,png,jpg,webp',
                'max:5120',
            ],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $latitude = filter_var(
                $this->input('latitude'),
                FILTER_VALIDATE_FLOAT,
                FILTER_NULL_ON_FAILURE
            );

            $longitude = filter_var(
                $this->input('longitude'),
                FILTER_VALIDATE_FLOAT,
                FILTER_NULL_ON_FAILURE
            );

            if (
                $latitude !== null
                && $longitude !== null
                && !$this->isInsidePilotArea((float) $latitude, (float) $longitude)
            ) {
                $validator->errors()->add(
                    'latitude',
                    'Lokasi laporan berada di luar wilayah uji coba Kota Depok. Silakan pilih titik yang berada di Kota Depok.'
                );
            }

            $laporan = $this->laporanDariRute();

            if (!$laporan) {
                return;
            }

            $idHapus = collect((array) $this->input('hapus_foto', []))
                ->filter(fn ($nilai) => is_scalar($nilai))
                ->map(fn ($nilai) => (int) $nilai)
                ->unique()
                ->values()
                ->all();

            $jumlahDihapus = $idHapus === []
                ? 0
                : $laporan->fotoLaporan()->whereIn('id', $idHapus)->count();

            $fileBaru = $this->file('foto', []);
            $jumlahBaru = is_array($fileBaru) ? count($fileBaru) : 1;

            $sisa = $laporan->fotoLaporan()->count()
                - $jumlahDihapus
                + $jumlahBaru;

            if ($sisa < 1) {
                $validator->errors()->add(
                    'foto',
                    'Laporan harus memiliki minimal satu foto.'
                );
            }

            if ($sisa > $this->getMaxFoto()) {
                $validator->errors()->add(
                    'foto',
                    'Jumlah foto melebihi batas maksimal ' . $this->getMaxFoto() . ' foto per laporan.'
                );
            }
        });
    }

    public function messages(): array
    {
        return [
            'kategori_hambatan_id.required' => 'Kategori hambatan harus dipilih.',
            'kategori_hambatan_id.exists' => 'Kategori hambatan tidak valid atau sedang nonaktif.',
            'wilayah_id.required' => 'Wilayah laporan tidak dapat ditentukan dari lokasi. Silakan pilih kembali titik laporan pada peta.',
            'wilayah_id.exists' => 'Wilayah laporan tidak valid atau sedang nonaktif.',
            'latitude.required' => 'Lokasi laporan harus ditentukan pada peta.',
            'longitude.required' => 'Lokasi laporan harus ditentukan pada peta.',
            'judul.required' => 'Judul laporan harus diisi.',
            'judul.max' => 'Judul laporan maksimal 200 karakter.',
            'deskripsi.required' => 'Deskripsi laporan harus diisi.',
            'deskripsi.max' => 'Deskripsi laporan maksimal 5000 karakter.',
            'hapus_foto.*.exists' => 'Foto yang akan dihapus tidak ditemukan pada laporan ini.',
            'foto.*.image' => 'Berkas yang diunggah harus berupa gambar.',
            'foto.*.mimes' => 'Format foto harus JPEG, JPG, PNG, atau WEBP.',
            'foto.*.max' => 'Ukuran setiap foto maksimal 5 MB.',
        ];
    }
}