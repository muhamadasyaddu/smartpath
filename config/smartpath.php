<?php

return [

    /*
    |--------------------------------------------------------------------------
    | SmartPath Application Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi khusus domain SmartPath.
    |
    | MVP SmartPath menggunakan Kota Depok sebagai wilayah uji coba.
    | Batas di bawah merupakan geographic envelope berdasarkan
    | rentang koordinat Kota Depok yang tercantum pada sumber resmi
    | pemerintah daerah.
    |
    | Catatan:
    | envelope bukan polygon batas administrasi final.
    | Fungsinya sebagai geographic sanity check agar koordinat yang
    | jelas berada di luar Depok tidak masuk ke database sebagai
    | laporan Depok.
    |
    */

    'pilot' => [

        'name' => 'Kota Depok',

        'center' => [
            'latitude' => -6.4025,
            'longitude' => 106.7942,
        ],

        'bounds' => [

            /*
             * 6°28'00" LS
             */
            'min_latitude' => -6.4666667,

            /*
             * 6°18'30" LS
             */
            'max_latitude' => -6.3083333,

            /*
             * 106°42'30" BT
             */
            'min_longitude' => 106.7083333,

            /*
             * 106°55'30" BT
             */
            'max_longitude' => 106.9250000,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Location Quality
    |--------------------------------------------------------------------------
    */

    'location' => [

        /*
         * Akurasi <= 100 meter dianggap cukup baik untuk
         * ditampilkan sebagai hasil GPS.
         */
        'warning_accuracy_meters' => 100,

        /*
         * Jika lebih dari nilai ini, pengguna wajib
         * memeriksa atau menggeser marker secara manual.
         */
        'manual_recommended_accuracy_meters' => 500,

        /*
         * Waktu maksimum pencarian posisi perangkat.
         */
        'watch_timeout_ms' => 15000,
    ],

];