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

        return max(1, min($maxFoto, 10));
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

    /**
     * Validasi konsistensi kecamatan dengan koordinat laporan.
     *
     * Catatan:
     * MVP belum memiliki polygon batas administrasi.
     * Oleh karena itu digunakan titik referensi kecamatan.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {

            if (
                $validator->errors()->hasAny([
                    'wilayah_id',
                    'latitude',
                    'longitude',
                ])
            ) {
                return;
            }

            $wilayahId = (int) $this->input('wilayah_id');

            $latitude = (float) $this->input('latitude');

            $longitude = (float) $this->input('longitude');


            /*
             * Kecamatan yang dipilih user.
             */
            $wilayahDipilih = Wilayah::query()
                ->where('id', $wilayahId)
                ->where('level', 'kecamatan')
                ->where('aktif', true)
                ->first();


            if (!$wilayahDipilih) {

                $validator->errors()->add(
                    'wilayah_id',
                    'Kecamatan yang dipilih tidak valid.'
                );

                return;
            }


            /*
             * Semua kecamatan aktif yang mempunyai
             * titik referensi.
             */
            $kecamatanAktif = Wilayah::query()
                ->where('level', 'kecamatan')
                ->where('aktif', true)
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->get([
                    'id',
                    'nama',
                    'latitude',
                    'longitude',
                ]);


            if ($kecamatanAktif->isEmpty()) {

                $validator->errors()->add(
                    'wilayah_id',
                    'Data referensi kecamatan belum tersedia. Hubungi administrator.'
                );

                return;
            }


            /*
             * Titik referensi kecamatan yang dipilih.
             */
            if (
                $wilayahDipilih->latitude === null
                || $wilayahDipilih->longitude === null
            ) {

                $validator->errors()->add(
                    'wilayah_id',
                    'Kecamatan yang dipilih belum memiliki titik referensi lokasi.'
                );

                return;
            }


            /*
             * Jarak koordinat laporan ke kecamatan pilihan.
             */
            $jarakDipilih = $this->haversine(
                $latitude,
                $longitude,
                (float) $wilayahDipilih->latitude,
                (float) $wilayahDipilih->longitude
            );


            /*
             * Cari kecamatan referensi terdekat.
             */
            $terdekat = null;
            $jarakTerdekat = INF;

            foreach ($kecamatanAktif as $kecamatan) {

                $jarak = $this->haversine(
                    $latitude,
                    $longitude,
                    (float) $kecamatan->latitude,
                    (float) $kecamatan->longitude
                );

                if ($jarak < $jarakTerdekat) {

                    $jarakTerdekat = $jarak;
                    $terdekat = $kecamatan;
                }
            }


            if (!$terdekat) {
                return;
            }


            /*
             * Jangan menolak kasus yang masih dekat
             * dengan batas wilayah hanya karena perbedaan
             * kecil pada titik referensi.
             *
             * Untuk MVP:
             *
             * - minimum toleransi 1.500 meter
             * - atau 1.25x jarak kecamatan terdekat
             */
            $batasToleransi = max(
                1500,
                $jarakTerdekat * 1.25
            );


            $bukanKecamatanTerdekat =
                $terdekat->id !== $wilayahDipilih->id;


            $jelasTidakSesuai =
                $jarakDipilih > $batasToleransi;


            if (
                $bukanKecamatanTerdekat
                && $jelasTidakSesuai
            ) {

                $validator->errors()->add(
                    'wilayah_id',
                    "Koordinat laporan berada lebih dekat ke Kecamatan {$terdekat->nama}. Kecamatan {$wilayahDipilih->nama} tidak sesuai dengan titik lokasi. Silakan pilih kecamatan yang sesuai dengan marker GPS/peta."
                );
            }
        });
    }

    /**
     * Haversine distance dalam meter.
     */
    private function haversine(
        float $lat1,
        float $lng1,
        float $lat2,
        float $lng2
    ): float {

        $earthRadius = 6371000;

        $latFrom = deg2rad($lat1);
        $latTo = deg2rad($lat2);

        $lngFrom = deg2rad($lng1);
        $lngTo = deg2rad($lng2);

        $latDelta = $latTo - $latFrom;
        $lngDelta = $lngTo - $lngFrom;

        $a =
            sin($latDelta / 2) ** 2
            +
            cos($latFrom)
            * cos($latTo)
            * sin($lngDelta / 2) ** 2;

        $a = min(1, max(0, $a));

        return $earthRadius
            * (
                2
                * atan2(
                    sqrt($a),
                    sqrt(1 - $a)
                )
            );
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
                'Kecamatan harus dipilih.',

            'wilayah_id.exists' =>
                'Kecamatan tidak valid atau sedang nonaktif.',

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