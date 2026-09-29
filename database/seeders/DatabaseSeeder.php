<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            KonfigurasiSistemSeeder::class,
            WilayahSeeder::class,
            UserSeeder::class,
            KategoriHambatanSeeder::class,
            LaporanSeeder::class,
            FasilitasPublikSeeder::class,
            PengaturanPrioritasSeeder::class,
        ]);
    }
}
