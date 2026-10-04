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
     * GPS dengan akurasi sampai 100 meter dianggap
     * cukup baik untuk digunakan sebagai titik awal.
     *
     * User tetap dapat melihat marker dan menggesernya
     * apabila titik tidak sesuai dengan kondisi lapangan.
     */
    'warning_accuracy_meters' => 100,

    /*
     * Jika akurasi GPS lebih dari 500 meter,
     * sistem menganggap posisi otomatis terlalu tidak presisi.
     *
     * User harus menggeser marker secara manual.
     */
    'manual_recommended_accuracy_meters' => 500,

    /*
     * Batas waktu total pencarian GPS.
     */
    'watch_timeout_ms' => 15000,
],

];