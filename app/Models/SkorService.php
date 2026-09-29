<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SkorService extends Model
{
    /**
     * Hitung skor prioritas menggunakan tiga komponen
     * yang sudah dinormalisasi ke skala 0-100.
     *
     * Catatan:
     * normalisasi dilakukan sebelum method ini dipanggil.
     */
    public function hitungSkorPrioritas(
        float $keparahan,
        float $jumlahPelapor,
        float $fasilitasVital
    ): float {
        $bobotKeparahan = 0.40;
        $bobotPelapor = 0.35;
        $bobotFasilitas = 0.25;

        return round(
            (
                ($keparahan * $bobotKeparahan)
                +
                ($jumlahPelapor * $bobotPelapor)
                +
                ($fasilitasVital * $bobotFasilitas)
            ),
            2
        );
    }
}