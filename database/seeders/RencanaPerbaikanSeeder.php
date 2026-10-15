<?php

namespace Database\Seeders;

use App\Models\Laporan;
use App\Models\RencanaPerbaikan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use RuntimeException;

class RencanaPerbaikanSeeder extends Seeder
{
    public function run(): void
    {
        $today = Carbon::today();
        $laporanCodes = [
            'LP-202609-99004',
            'LP-202609-99005',
            'LP-202609-99006',
            'LP-202609-99007',
            'LP-202609-99008',
            'LP-202609-99009',
            'LP-202609-99010',
        ];

        $laporan = Laporan::query()
            ->whereIn('kode_laporan', $laporanCodes)
            ->get()
            ->keyBy('kode_laporan');

        if ($laporan->count() !== count($laporanCodes)) {
            throw new RuntimeException(
                'Jalankan LaporanSeeder sebelum RencanaPerbaikanSeeder.'
            );
        }

        $rencanaContoh = [
            'LP-202609-99004' => [
                'tindakan' => 'Membangun ramp aksesibel pada penyeberangan dan memasang ubin pemandu.',
                'penanggung_jawab' => 'Bidang Infrastruktur Jalan',
                'tanggal_mulai' => null,
                'target_selesai' => $today->copy()->addDays(30),
                'tanggal_selesai' => null,
                'estimasi_anggaran' => 18500000,
                'realisasi_anggaran' => 0,
                'status' => 'belum_dimulai',
                'catatan' => 'Pekerjaan menunggu persetujuan jadwal pelaksanaan.',
            ],
            'LP-202609-99005' => [
                'tindakan' => 'Menertibkan lapak dan memulihkan jalur pemandu agar dapat dilalui.',
                'penanggung_jawab' => 'Bidang Ketertiban Umum',
                'tanggal_mulai' => $today->copy()->subDays(10),
                'target_selesai' => $today->copy()->addDays(20),
                'tanggal_selesai' => null,
                'estimasi_anggaran' => 12000000,
                'realisasi_anggaran' => 6200000,
                'status' => 'dalam_perbaikan',
                'catatan' => 'Pembelian material dan pekerjaan awal telah dilakukan.',
            ],
            'LP-202609-99006' => [
                'tindakan' => 'Memperbaiki permukaan trotoar dan memasang ramp dengan kemiringan sesuai standar.',
                'penanggung_jawab' => 'Bidang Bina Marga',
                'tanggal_mulai' => $today->copy()->subDays(45),
                'target_selesai' => $today->copy()->subDays(5),
                'tanggal_selesai' => null,
                'estimasi_anggaran' => 11500000,
                'realisasi_anggaran' => 9000000,
                'status' => 'dalam_perbaikan',
                'catatan' => 'Penyelesaian tertunda karena penyesuaian utilitas bawah tanah.',
            ],
            'LP-202609-99007' => [
                'tindakan' => 'Mengganti ubin pemandu yang terangkat dan meratakan permukaan trotoar.',
                'penanggung_jawab' => 'Bidang Infrastruktur Jalan',
                'tanggal_mulai' => $today->copy()->subDays(8),
                'target_selesai' => $today->copy()->addDays(20),
                'tanggal_selesai' => null,
                'estimasi_anggaran' => 8000000,
                'realisasi_anggaran' => 3500000,
                'status' => 'dalam_perbaikan',
                'catatan' => 'Material utama sudah tersedia di lokasi.',
            ],
            'LP-202609-99008' => [
                'tindakan' => 'Menata ulang tiang utilitas agar lebar efektif trotoar tetap aman.',
                'penanggung_jawab' => 'Bidang Sarana dan Prasarana',
                'tanggal_mulai' => $today->copy()->subDays(20),
                'target_selesai' => $today->copy()->addDays(10),
                'tanggal_selesai' => null,
                'estimasi_anggaran' => 7500000,
                'realisasi_anggaran' => 4100000,
                'status' => 'dalam_perbaikan',
                'catatan' => 'Koordinasi pemindahan jaringan utilitas sedang berlangsung.',
            ],
            'LP-202609-99009' => [
                'tindakan' => 'Membangun ramp baru dan memasang marka aksesibel di titik penyeberangan.',
                'penanggung_jawab' => 'Bidang Bina Marga',
                'tanggal_mulai' => $today->copy()->subDays(40),
                'target_selesai' => $today->copy()->subDays(5),
                'tanggal_selesai' => $today->copy()->subDays(3),
                'estimasi_anggaran' => 15000000,
                'realisasi_anggaran' => 14200000,
                'status' => 'selesai',
                'catatan' => 'Pekerjaan selesai dan telah diperiksa.',
            ],
            'LP-202609-99010' => [
                'tindakan' => 'Memperbaiki bagian trotoar yang retak dan merapikan permukaannya.',
                'penanggung_jawab' => 'Bidang Infrastruktur Jalan',
                'tanggal_mulai' => $today->copy()->subDays(25),
                'target_selesai' => $today->copy()->subDays(8),
                'tanggal_selesai' => $today->copy()->subDays(8),
                'estimasi_anggaran' => 9500000,
                'realisasi_anggaran' => 8700000,
                'status' => 'selesai',
                'catatan' => 'Pekerjaan selesai dengan biaya di bawah estimasi.',
            ],
        ];

        foreach ($rencanaContoh as $kodeLaporan => $data) {
            RencanaPerbaikan::updateOrCreate(
                ['laporan_id' => $laporan[$kodeLaporan]->id],
                $data
            );
        }
    }
}
