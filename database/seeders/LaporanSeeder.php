<?php

namespace Database\Seeders;

use App\Models\KategoriHambatan;
use App\Models\Laporan;
use App\Models\User;
use App\Models\Wilayah;
use Illuminate\Database\Seeder;
use RuntimeException;

class LaporanSeeder extends Seeder
{
    public function run(): void
    {
        $warga = User::query()
            ->where('peran', 'warga')
            ->orderBy('id')
            ->get();
        $kategori = KategoriHambatan::aktif()
            ->urutTampil()
            ->get();
        $wilayah = Wilayah::aktif()
            ->where('level', 'kecamatan')
            ->orderBy('id')
            ->get();

        if ($warga->isEmpty() || $kategori->isEmpty() || $wilayah->isEmpty()) {
            throw new RuntimeException(
                'Jalankan UserSeeder, KategoriHambatanSeeder, dan WilayahSeeder sebelum LaporanSeeder.'
            );
        }

        $laporanContoh = [
            [
                'judul' => 'Guiding block rusak di Jalan Margonda',
                'deskripsi' => 'Beberapa ubin pemandu pecah dan terlepas sehingga jalur sulit diikuti.',
                'alamat_lengkap' => 'Jalan Margonda Raya, Beji, Depok',
                'latitude' => -6.3691200,
                'longitude' => 106.8324500,
                'status' => 'menunggu_verifikasi',
            ],
            [
                'judul' => 'Trotoar terhalang kendaraan di dekat stasiun',
                'deskripsi' => 'Kendaraan parkir menutup sebagian besar jalur pejalan kaki.',
                'alamat_lengkap' => 'Jalan Stasiun Depok Baru, Pancoran Mas, Depok',
                'latitude' => -6.3974300,
                'longitude' => 106.8221800,
                'status' => 'menunggu_verifikasi',
            ],
            [
                'judul' => 'Tidak tersedia ramp di penyeberangan',
                'deskripsi' => 'Tepi trotoar tinggi dan tidak memiliki jalur landai untuk kursi roda.',
                'alamat_lengkap' => 'Jalan Raya Bogor, Sukmajaya, Depok',
                'latitude' => -6.3948100,
                'longitude' => 106.8376100,
                'status' => 'menunggu_verifikasi',
            ],
            [
                'judul' => 'Permukaan trotoar berlubang',
                'deskripsi' => 'Permukaan jalur pejalan kaki berlubang dan berisiko membuat pengguna tersandung.',
                'alamat_lengkap' => 'Jalan Raya Sawangan, Pancoran Mas, Depok',
                'latitude' => -6.4057700,
                'longitude' => 106.7903200,
                'status' => 'diverifikasi',
                'skor_prioritas' => 76.50,
            ],
            [
                'judul' => 'Guiding block tertutup lapak sementara',
                'deskripsi' => 'Lapak sementara berdiri di atas jalur pemandu untuk pejalan kaki.',
                'alamat_lengkap' => 'Jalan Tole Iskandar, Sukmajaya, Depok',
                'latitude' => -6.4023100,
                'longitude' => 106.8419200,
                'status' => 'diverifikasi',
                'skor_prioritas' => 58.25,
            ],
            [
                'judul' => 'Akses halte belum ramah kursi roda',
                'deskripsi' => 'Perbedaan tinggi antara trotoar dan akses halte belum dilengkapi ramp.',
                'alamat_lengkap' => 'Jalan Juanda, Sukmajaya, Depok',
                'latitude' => -6.3846100,
                'longitude' => 106.8448700,
                'status' => 'diverifikasi',
                'skor_prioritas' => 82.00,
            ],
            [
                'judul' => 'Ubin pemandu terangkat di area pertokoan',
                'deskripsi' => 'Sebagian ubin pemandu terangkat dan perlu diratakan kembali.',
                'alamat_lengkap' => 'Jalan Raya Cimanggis, Cimanggis, Depok',
                'latitude' => -6.3652900,
                'longitude' => 106.8584100,
                'status' => 'dalam_perbaikan',
                'skor_prioritas' => 64.75,
            ],
            [
                'judul' => 'Trotoar menyempit karena tiang utilitas',
                'deskripsi' => 'Tiang utilitas mengurangi lebar efektif jalur pejalan kaki.',
                'alamat_lengkap' => 'Jalan Raya Tapos, Tapos, Depok',
                'latitude' => -6.4142600,
                'longitude' => 106.8781500,
                'status' => 'dalam_perbaikan',
                'skor_prioritas' => 71.20,
            ],
            [
                'judul' => 'Ramp penyeberangan sudah diperbaiki',
                'deskripsi' => 'Ramp baru telah dibuat dan dapat digunakan untuk akses penyeberangan.',
                'alamat_lengkap' => 'Jalan Abdul Wahab, Sawangan, Depok',
                'latitude' => -6.4089300,
                'longitude' => 106.7659200,
                'status' => 'selesai',
                'skor_prioritas' => 43.80,
            ],
            [
                'judul' => 'Perbaikan permukaan trotoar selesai',
                'deskripsi' => 'Permukaan yang retak telah dirapikan dan jalur dapat dilalui kembali.',
                'alamat_lengkap' => 'Jalan Cinere Raya, Cinere, Depok',
                'latitude' => -6.3367400,
                'longitude' => 106.7836200,
                'status' => 'selesai',
                'skor_prioritas' => 68.40,
            ],
        ];

        foreach ($laporanContoh as $index => $data) {
            $kategoriHambatan = $kategori[$index % $kategori->count()];
            $statusSudahDiproses = $data['status'] !== 'menunggu_verifikasi';

            $laporan = Laporan::withTrashed()->updateOrCreate(
                ['kode_laporan' => sprintf('LP-202609-%05d', 99001 + $index)],
                [
                    'pelapor_id' => $warga[$index % $warga->count()]->id,
                    'kategori_hambatan_id' => $kategoriHambatan->id,
                    'wilayah_id' => $wilayah[$index % $wilayah->count()]->id,
                    'latitude' => $data['latitude'],
                    'longitude' => $data['longitude'],
                    'alamat_lengkap' => $data['alamat_lengkap'],
                    'judul' => $data['judul'],
                    'deskripsi' => $data['deskripsi'],
                    'status' => $data['status'],
                    'skor_prioritas' => $data['skor_prioritas'] ?? null,
                    'skor_keparahan' => $statusSudahDiproses
                        ? $kategoriHambatan->bobot_keparahan
                        : null,
                    'skor_pelapor' => $statusSudahDiproses ? 50 : null,
                    'skor_fasilitas' => $statusSudahDiproses ? 50 : null,
                    'jumlah_pelapor' => 1 + ($index % 4),
                    'sumber_koordinat' => 'manual',
                    'platform_pelapor' => 'web',
                    'dihitung_pada' => $statusSudahDiproses ? now() : null,
                ]
            );

            if ($laporan->trashed()) {
                $laporan->restore();
            }
        }
    }
}
