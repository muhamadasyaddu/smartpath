<?php

namespace App\Console\Commands;

use App\Models\FasilitasPublik;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Throwable;

class ImportFasilitasOsm extends Command
{
    protected $signature = 'osm:import-fasilitas
                            {--dry-run : Hanya menghitung hasil, tanpa menyimpan}';

    protected $description =
        'Impor fasilitas bernama dari OpenStreetMap untuk area selatan Depok';

    public function handle(): int
    {
        /*
         * Kotak pencarian awal untuk area Cinere, Gandul,
         * Meruyung, Limo dan lingkungan sekitarnya.
         *
         * Urutan Overpass: selatan, barat, utara, timur.
         * Ini bukan batas kelurahan resmi.
         */
        $south = -6.45;
        $west  = 106.72;
        $north = -6.30;
        $east  = 106.83;

        $bbox = "{$south},{$west},{$north},{$east}";

        $query = <<<OVERPASS
[out:json][timeout:60];
(
  nwr["name"]["amenity"~"hospital|school|university|college|kindergarten|bus_station|marketplace|place_of_worship|townhall"]($bbox);
  nwr["name"]["healthcare"="hospital"]($bbox);
  nwr["name"]["railway"~"station|halt"]($bbox);
  nwr["name"]["highway"="bus_stop"]($bbox);
  nwr["name"]["public_transport"="station"]($bbox);
  nwr["name"]["office"="government"]($bbox);
);
out center tags;
OVERPASS;

        try {
            $response = Http::asForm()
                ->acceptJson()
                ->withHeaders([
                    'User-Agent' => 'SmartPath-Accessibility-Map/1.0',
                ])
                ->timeout(90)
                ->retry(2, 1000)
                ->post(
                    'https://overpass-api.de/api/interpreter',
                    ['data' => $query]
                );

            $response->throw();
            $elements = $response->json('elements', []);
        } catch (Throwable $exception) {
            $this->error(
                'Gagal mengambil data OSM: ' . $exception->getMessage()
            );

            $this->line(
                'Periksa koneksi internet atau coba lagi nanti.'
            );

            return self::FAILURE;
        }

        if (!is_array($elements)) {
            $this->error('Respons OSM tidak memiliki daftar elemen yang valid.');
            return self::FAILURE;
        }

        $created = 0;
        $updated = 0;
        $skipped = 0;

        foreach ($elements as $element) {
            $tags = $element['tags'] ?? [];
            $name = trim((string) ($tags['name'] ?? ''));

            if ($name === '') {
                $skipped++;
                continue;
            }

            $lat = $element['lat'] ?? $element['center']['lat'] ?? null;
            $lon = $element['lon'] ?? $element['center']['lon'] ?? null;

            if (!is_numeric($lat) || !is_numeric($lon)) {
                $skipped++;
                continue;
            }

            $lat = (float) $lat;
            $lon = (float) $lon;

            if (
                $lat < $south || $lat > $north ||
                $lon < $west || $lon > $east
            ) {
                $skipped++;
                continue;
            }

            $jenis = $this->tentukanJenis($tags);

            if ($jenis === null) {
                $skipped++;
                continue;
            }

            $osmId = ($element['type'] ?? 'unknown')
                . '/' . ($element['id'] ?? '');

            if (str_ends_with($osmId, '/')) {
                $skipped++;
                continue;
            }

            $alamat = $this->buatAlamat($tags);

            if ($this->option('dry-run')) {
                $this->line(
                    "{$name} | {$jenis} | {$lat}, {$lon} | {$osmId}"
                );
                continue;
            }

            $existing = FasilitasPublik::query()
                ->where('osm_id', $osmId)
                ->first();

            $data = [
                'nama' => Str::limit($name, 150, ''),
                'jenis' => $jenis,
                'alamat' => $alamat,
                'latitude' => $lat,
                'longitude' => $lon,
                'sumber_data' => 'osm',
                'osm_id' => $osmId,
                'aktif' => true,
            ];

            /*
             * Jangan mengarang wilayah_id dari koordinat.
             * Wilayah dapat dipetakan kemudian dengan batas
             * administratif resmi.
             */
            if ($existing) {
                $existing->update($data);
                $updated++;
            } else {
                $data['bobot_vital'] = 50;
                FasilitasPublik::create($data);
                $created++;
            }
        }

        $this->newLine();
        $this->info('Proses impor OSM selesai.');
        $this->line('Elemen diterima: ' . count($elements));

        if ($this->option('dry-run')) {
            $this->warn('Mode dry-run: database tidak diubah.');
        } else {
            $this->line("Data baru: {$created}");
            $this->line("Data diperbarui: {$updated}");
        }

        $this->line("Data dilewati: {$skipped}");

        return self::SUCCESS;
    }

    /**
     * Pemetaan dibatasi pada jenis yang didukung enum
     * fasilitas_publik saat ini.
     */
    private function tentukanJenis(array $tags): ?string
    {
        $amenity = $tags['amenity'] ?? '';
        $healthcare = $tags['healthcare'] ?? '';
        $railway = $tags['railway'] ?? '';
        $highway = $tags['highway'] ?? '';
        $publicTransport = $tags['public_transport'] ?? '';
        $office = $tags['office'] ?? '';

        if (
            $amenity === 'hospital' ||
            $healthcare === 'hospital'
        ) {
            return 'rumah_sakit';
        }

        if (
            in_array($amenity, ['school', 'kindergarten'], true)
        ) {
            return 'sekolah';
        }

        if (
            in_array($amenity, ['university', 'college'], true)
        ) {
            return 'perguruan_tinggi';
        }

        if (
            $highway === 'bus_stop' ||
            $publicTransport === 'platform'
        ) {
            return 'halte';
        }

        if (
            in_array($railway, ['station', 'halt'], true) ||
            $publicTransport === 'station'
        ) {
            return 'stasiun';
        }

        if ($amenity === 'bus_station') {
            return 'terminal';
        }

        if ($amenity === 'marketplace') {
            return 'pasar';
        }

        if ($amenity === 'place_of_worship') {
            return 'tempat_ibadah';
        }

        if (
            $amenity === 'townhall' ||
            $office === 'government'
        ) {
            return 'kantor_pemerintah';
        }

        return null;
    }

    private function buatAlamat(array $tags): ?string
    {
        $parts = array_filter([
            $tags['addr:street'] ?? null,
            $tags['addr:suburb'] ?? null,
            $tags['addr:city'] ?? null,
            $tags['addr:postcode'] ?? null,
        ], fn ($value) => is_string($value) && trim($value) !== '');

        if ($parts === []) {
            return null;
        }

        return Str::limit(implode(', ', $parts), 255, '');
    }
}