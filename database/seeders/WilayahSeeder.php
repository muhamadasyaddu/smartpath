<?php

namespace Database\Seeders;

use App\Models\Wilayah;
use Illuminate\Database\Seeder;

class WilayahSeeder extends Seeder
{
    public function run(): void
    {
        /*
         * Kota Depok
         */
        $depok = Wilayah::updateOrCreate(
            [
                'kode_bps' => '3276',
            ],
            [
                'nama' => 'Kota Depok',
                'level' => 'kota_kabupaten',
                'latitude' => -6.4025,
                'longitude' => 106.7942,
                'aktif' => true,
            ]
        );


        /*
         * Titik referensi kecamatan.
         *
         * Catatan:
         * titik ini digunakan sebagai reference point MVP,
         * bukan polygon batas administrasi resmi.
         */
        $kecamatan = [

            [
                'nama' => 'Beji',
                'kode_bps' => '3276010',
                'latitude' => -6.3758806,
                'longitude' => 106.8237374,
            ],

            [
                'nama' => 'Pancoran Mas',
                'kode_bps' => '3276020',
                'latitude' => -6.3971623,
                'longitude' => 106.8001396,
            ],

            [
                'nama' => 'Cipayung',
                'kode_bps' => '3276030',
                'latitude' => -6.4279175,
                'longitude' => 106.8001396,
            ],

            [
                'nama' => 'Sukmajaya',
                'kode_bps' => '3276040',
                'latitude' => -6.3853366,
                'longitude' => 106.8473377,
            ],

            [
                'nama' => 'Cimanggis',
                'kode_bps' => '3276050',
                'latitude' => -6.3644564,
                'longitude' => 106.8591387,
            ],

            [
                'nama' => 'Tapos',
                'kode_bps' => '3276060',
                'latitude' => -6.4099620,
                'longitude' => 106.8768415,
            ],

            [
                'nama' => 'Sawangan',
                'kode_bps' => '3276070',
                'latitude' => -6.4085961,
                'longitude' => 106.7647475,
            ],

            [
                'nama' => 'Bojongsari',
                'kode_bps' => '3276080',
                'latitude' => -6.3991308,
                'longitude' => 106.7411559,
            ],

            [
                'nama' => 'Limo',
                'kode_bps' => '3276090',
                'latitude' => -6.3701361,
                'longitude' => 106.7729399,
            ],

            [
                'nama' => 'Cinere',
                'kode_bps' => '3276100',
                'latitude' => -6.3360895,
                'longitude' => 106.7883416,
            ],

            [
                'nama' => 'Cilodong',
                'kode_bps' => '3276110',
                'latitude' => -6.4369807,
                'longitude' => 106.8355372,
            ],
        ];


        foreach ($kecamatan as $data) {

            Wilayah::updateOrCreate(
                [
                    'kode_bps' => $data['kode_bps'],
                ],
                [
                    'induk_id' => $depok->id,
                    'nama' => $data['nama'],
                    'level' => 'kecamatan',
                    'latitude' => $data['latitude'],
                    'longitude' => $data['longitude'],
                    'aktif' => true,
                ]
            );
        }
    }
}