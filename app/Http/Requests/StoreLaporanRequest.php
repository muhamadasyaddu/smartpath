<?php

namespace App\Http\Requests;

use App\Models\KonfigurasiSistem;
use App\Models\Wilayah;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLaporanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check()
            && (
                auth()->user()->isWarga()
                || auth()->user()->isAdmin()
            );
    }

    protected function getMaxFoto(): int
    {
        $maxFoto = (int) KonfigurasiSistem::getValue(
            'max_foto_per_laporan',
            5
        );

        return max(
            1,
            min($maxFoto, 10)
        );
    }

    /**
     * Mengecek apakah koordinat masih berada dalam
     * geographic envelope wilayah uji coba SmartPath.
     *
     * Envelope ini bukan polygon administrasi.
     * Fungsinya sebagai lapisan perlindungan pertama
     * agar koordinat yang jelas berada di luar Depok
     * tidak diklasifikasikan sebagai kecamatan Depok.
     */
    protected function isInsidePilotArea(
        float $latitude,
        float $longitude
    ): bool {
        $bounds = config(
            'smartpath.pilot.bounds',
            []
        );

        return (
            $latitude >= (float) ($bounds['min_latitude'] ?? -90)
            && $latitude <= (float) ($bounds['max_latitude'] ?? 90)
            && $longitude >= (float) ($bounds['min_longitude'] ?? -180)
            && $longitude <= (float) ($bounds['max_longitude'] ?? 180)
        );
    }

    /**
     * Menentukan wilayah secara internal berdasarkan
     * koordinat laporan.
     *
     * Browser tidak dipercaya untuk menentukan wilayah_id.
     *
     * Penting:
     * koordinat harus lolos geographic sanity check
     * terlebih dahulu sebelum sistem mencari kecamatan
     * terdekat.
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
        ) {
            return;
        }

        /*
         * Jangan pernah mengklasifikasikan koordinat
         * di luar wilayah pilot sebagai kecamatan Depok.
         */
        if (
            !$this->isInsidePilotArea(
                (float) $latitude,
                (float) $longitude
            )
        ) {
            return;
        }

        $wilayah = Wilayah::nearestKecamatanByReferencePoint(
            (float) $latitude,
            (float) $longitude
        );

        if ($wilayah) {
            $this->merge([
                'wilayah_id' => $wilayah->id,
            ]);
        }
    }

    /**
     * Validasi tambahan setelah seluruh field
     * berhasil diproses oleh Laravel Validator.
     */
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
                $latitude === null
                || $longitude === null
            ) {
                return;
            }

            if (
                !$this->isInsidePilotArea(
                    (float) $latitude,
                    (float) $longitude
                )
            ) {
                $validator->errors()->add(
                    'latitude',
                    'Lokasi laporan berada di luar wilayah uji coba Kota Depok. Silakan pilih titik laporan yang berada di Kota Depok.'
                );

                return;
            }
        });
    }

    public function rules(): array
    {
        $maxFoto = $this->getMaxFoto();

        return [

            'kategori_hambatan_id' => [
                'required',

                Rule::exists(
                    'kategori_hambatan',
                    'id'
                )->where(
                    fn ($query) =>
                        $query->where('aktif', true)
                ),
            ],

            /*
             * Wilayah tetap berasal dari backend.
             */
            'wilayah_id' => [
                'required',

                Rule::exists(
                    'wilayah',
                    'id'
                )->where(
                    fn ($query) =>
                        $query
                            ->where('aktif', true)
                            ->where('level', 'kecamatan')
                ),
            ],

            'latitude' => [
                'required',
                'numeric',
                'between:-90,90',
            ],

            'longitude' => [
                'required',
                'numeric',
                'between:-180,180',
            ],

            'alamat_lengkap' => [
                'nullable',
                'string',
                'max:255',
            ],

            'judul' => [
                'required',
                'string',
                'max:200',
            ],

            'deskripsi' => [
                'required',
                'string',
                'max:5000',
            ],

            'sumber_koordinat' => [
                'required',
                'in:gps_otomatis,manual',
            ],

            'foto' => [
                'required',
                'array',
                'min:1',
                'max:' . $maxFoto,
            ],

            'foto.*' => [
                'required',
                'image',
                'mimes:jpeg,png,jpg,webp',
                'max:5120',
            ],
        ];
    }

    public function messages(): array
    {
        $maxFoto = $this->getMaxFoto();

        return [

            'kategori_hambatan_id.required' =>
                'Kategori hambatan harus dipilih.',

            'kategori_hambatan_id.exists' =>
                'Kategori hambatan tidak valid atau sedang nonaktif.',

            'wilayah_id.required' =>
                'Wilayah laporan tidak dapat ditentukan dari lokasi. Silakan pilih kembali titik laporan pada peta.',

            'wilayah_id.exists' =>
                'Wilayah laporan tidak valid atau sedang nonaktif.',

            'latitude.required' =>
                'Lokasi latitude harus ditentukan.',

            'latitude.numeric' =>
                'Latitude harus berupa angka.',

            'latitude.between' =>
                'Nilai latitude tidak valid.',

            'longitude.required' =>
                'Lokasi longitude harus ditentukan.',

            'longitude.numeric' =>
                'Longitude harus berupa angka.',

            'longitude.between' =>
                'Nilai longitude tidak valid.',

            'judul.required' =>
                'Judul laporan harus diisi.',

            'judul.max' =>
                'Judul laporan maksimal 200 karakter.',

            'deskripsi.required' =>
                'Deskripsi hambatan harus diisi.',

            'deskripsi.max' =>
                'Deskripsi laporan maksimal 5000 karakter.',

            'sumber_koordinat.required' =>
                'Sumber koordinat harus ditentukan.',

            'sumber_koordinat.in' =>
                'Sumber koordinat tidak valid.',

            'foto.required' =>
                'Minimal satu foto harus diunggah.',

            'foto.array' =>
                'Format data foto tidak valid.',

            'foto.min' =>
                'Minimal satu foto harus diunggah.',

            'foto.max' =>
                "Maksimal {$maxFoto} foto dapat diunggah.",

            'foto.*.required' =>
                'Setiap foto harus memiliki berkas.',

            'foto.*.image' =>
                'Setiap file harus berupa gambar yang valid.',

            'foto.*.mimes' =>
                'Format gambar harus JPEG, PNG, JPG, atau WebP.',

            'foto.*.max' =>
                'Ukuran setiap foto maksimal 5MB.',
        ];
    }
}