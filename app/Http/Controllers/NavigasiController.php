<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Laporan;
class NavigasiController extends Controller
{
    public function index()
    {
        return view('navigasi.index');
    }

   


public function cariTujuan(Request $request)
{
    $request->validate([
        'tujuan' => 'required|string|max:255',
        'latitude' => 'nullable|numeric',
        'longitude' => 'nullable|numeric',
    ]);

    $tujuan = trim($request->tujuan);

    $latitudePengguna = $request->latitude;
    $longitudePengguna = $request->longitude;

    /*
    |--------------------------------------------------------------------------
    | Alias kategori tujuan
    |--------------------------------------------------------------------------
    */

    $aliasTujuan = [
        'sekolah' => 'school',
        'halte' => 'halte',
        'rumah sakit' => 'hospital',
        'rs' => 'hospital',
        'stasiun' => 'train station',
        'universitas' => 'university',
        'kampus' => 'university',
    ];

    $tujuanLower = strtolower($tujuan);

    $isKategori = isset($aliasTujuan[$tujuanLower]);
    $isKategoriSekolah = $tujuanLower === 'sekolah';

    if ($isKategori) {
        $queryNominatim = $aliasTujuan[$tujuanLower] . ', Depok, Jawa Barat';
    } else {
        $queryNominatim = $tujuan;
    }

    try {

        /*
        |--------------------------------------------------------------------------
        | Parameter pencarian Nominatim
        |--------------------------------------------------------------------------
        */

        $parameterNominatim = [
            'q' => $queryNominatim,
            'format' => 'json',
            'limit' => $isKategori ? 10 : 5,
            'addressdetails' => 1,
            'countrycodes' => 'id',
            'dedupe' => 1,
            'extratags' => 1,
        ];

        /*
        |--------------------------------------------------------------------------
        | Jika kategori, batasi pencarian di sekitar pengguna
        |--------------------------------------------------------------------------
        */

        if (
            $isKategori &&
            $latitudePengguna !== null &&
            $longitudePengguna !== null
        ) {
            $lat = (float) $latitudePengguna;
            $lon = (float) $longitudePengguna;

            // Radius pencarian sekitar ±15 km
            $deltaLat = 0.15;

            $cosLat = cos(deg2rad($lat));

            if (abs($cosLat) < 0.01) {
                $cosLat = 0.01;
            }

            $deltaLon = 0.15 / $cosLat;

            $parameterNominatim['viewbox'] =
                ($lon - $deltaLon) . ',' .
                ($lat + $deltaLat) . ',' .
                ($lon + $deltaLon) . ',' .
                ($lat - $deltaLat);

            // WAJIB berada di dalam area tersebut
            $parameterNominatim['bounded'] = 1;
        }

        /*
        |--------------------------------------------------------------------------
        | Cari lokasi
        |--------------------------------------------------------------------------
        */

        $headers = [
            'User-Agent' => 'SmartPath/1.0 (Smart City Accessibility Project)',
            'Accept-Language' => 'id-ID,id;q=0.9,en;q=0.8',
        ];

        if (
            $isKategoriSekolah &&
            $latitudePengguna !== null &&
            $longitudePengguna !== null
        ) {
            $overpassQuery = '[out:json][timeout:20];'
                . 'nwr["amenity"="school"](around:15000,'
                . (float) $latitudePengguna . ','
                . (float) $longitudePengguna . ');'
                . 'out center tags;';

            $overpassEndpoints = [
                'https://overpass-api.de/api/interpreter',
                'https://overpass.kumi.systems/api/interpreter',
                'https://overpass.private.coffee/api/interpreter',
            ];

            $response = null;

            foreach ($overpassEndpoints as $overpassEndpoint) {
                try {
                    $candidateResponse = Http::timeout(12)
                        ->withHeaders($headers)
                        ->get($overpassEndpoint, [
                            'data' => $overpassQuery,
                        ]);

                    if ($candidateResponse->successful()) {
                        $response = $candidateResponse;
                        break;
                    }

                    $response = $candidateResponse;
                } catch (\Throwable $exception) {
                    continue;
                }
            }

            if ($response === null) {
                return response()->json([
                    'success' => false,
                    'message' => 'Layanan pencarian sekolah sedang tidak dapat diakses. Coba lagi beberapa saat.',
                ], 503);
            }

            $data = collect($response->json('elements', []))
                ->map(function ($hasil) {
                    $latitude = $hasil['lat'] ?? $hasil['center']['lat'] ?? null;
                    $longitude = $hasil['lon'] ?? $hasil['center']['lon'] ?? null;

                    if (
                        $latitude === null ||
                        $longitude === null ||
                        empty($hasil['tags']['name'])
                    ) {
                        return null;
                    }

                    return [
                        'display_name' => $hasil['tags']['name'],
                        'lat' => $latitude,
                        'lon' => $longitude,
                        'type' => 'school',
                        'class' => 'amenity',
                    ];
                })
                ->filter()
                ->values()
                ->all();
        } else {
            $response = Http::timeout(15)
                ->withHeaders($headers)
                ->get(
                    'https://nominatim.openstreetmap.org/search',
                    $parameterNominatim
                );

            $data = $response->json();
        }

        if (!$response->successful()) {
            return response()->json([
                'success' => false,
                'message' => 'Layanan pencarian lokasi sedang tidak dapat diakses.',
                'status_layanan' => $response->status(),
            ], 500);
        }

        /*
        |--------------------------------------------------------------------------
        | Jika kategori tidak ditemukan di area pengguna
        |--------------------------------------------------------------------------
        */

        if (empty($data)) {

            if ($isKategori) {
                return response()->json([
                    'success' => false,
                    'message' =>
                        'Tidak ditemukan ' . $tujuan .
                        ' di sekitar lokasi kamu. Coba masukkan nama tempat atau alamat yang lebih spesifik.',
                ], 404);
            }

            return response()->json([
                'success' => false,
                'message' =>
                    'Lokasi "' . $tujuan .
                    '" tidak ditemukan. Coba masukkan nama tempat atau alamat yang lebih lengkap.',
            ], 404);
        }

        /*
        |--------------------------------------------------------------------------
        | Hitung jarak dari pengguna
        |--------------------------------------------------------------------------
        */

        if (
            $latitudePengguna !== null &&
            $longitudePengguna !== null
        ) {
            foreach ($data as &$hasil) {

                $hasil['jarak_pengguna'] =
                    $this->hitungJarakMeter(
                        (float) $latitudePengguna,
                        (float) $longitudePengguna,
                        (float) $hasil['lat'],
                        (float) $hasil['lon']
                    );
            }

            unset($hasil);

            /*
            |--------------------------------------------------------------------------
            | Urutkan dari yang paling dekat
            |--------------------------------------------------------------------------
            */

            usort($data, function ($a, $b) {
                return $a['jarak_pengguna']
                    <=> $b['jarak_pengguna'];
            });
        }

        /*
        |--------------------------------------------------------------------------
        | KATEGORI → KIRIM BEBERAPA PILIHAN
        |--------------------------------------------------------------------------
        */

        if ($isKategori) {

            $hasilPilihan = collect($data)
                ->take(5)
                ->map(function ($hasil) {

                    return [
                        'tujuan' => $hasil['display_name'],
                        'alamat' => $hasil['display_name'],
                        'latitude' => (float) $hasil['lat'],
                        'longitude' => (float) $hasil['lon'],
                        'tipe' => $hasil['type'] ?? null,
                        'kelas' => $hasil['class'] ?? null,
                        'jarak_pengguna' =>
                            isset($hasil['jarak_pengguna'])
                                ? round($hasil['jarak_pengguna'], 1)
                                : null,
                    ];
                })
                ->values();

            return response()->json([
                'success' => true,
                'tipe_pencarian' => 'kategori',
                'kategori' => $tujuan,
                'hasil' => $hasilPilihan,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | TUJUAN SPESIFIK → AMBIL HASIL TERDEKAT
        |--------------------------------------------------------------------------
        */

        $hasil = $data[0];

        return response()->json([
            'success' => true,
            'tipe_pencarian' => 'spesifik',
            'tujuan' => $hasil['display_name'],
            'latitude' => (float) $hasil['lat'],
            'longitude' => (float) $hasil['lon'],
            'tipe' => $hasil['type'] ?? null,
            'kelas' => $hasil['class'] ?? null,
            'jarak_pengguna' =>
                isset($hasil['jarak_pengguna'])
                    ? round($hasil['jarak_pengguna'], 1)
                    : null,
        ]);

    } catch (\Throwable $e) {

        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan saat mencari lokasi tujuan.',
            'error' => $e->getMessage(),
        ], 500);
    }
}



    public function rute(Request $request)
{
    $request->validate([
    'latitude_awal' => 'required|numeric',
    'longitude_awal' => 'required|numeric',
    'latitude_tujuan' => 'required|numeric',
    'longitude_tujuan' => 'required|numeric',
]);


    $latitudeAwal = $request->latitude_awal;
    $longitudeAwal = $request->longitude_awal;

    $latitudeTujuan = $request->latitude_tujuan;
    $longitudeTujuan = $request->longitude_tujuan;

    $koordinat = $longitudeAwal . ","
        . $latitudeAwal . ";"
        . $longitudeTujuan . ","
        . $latitudeTujuan;

    $endpointRute = [
        'https://routing.openstreetmap.de/routed-foot/route/v1/driving/',
        'https://router.project-osrm.org/route/v1/driving/',
    ];

    $data = null;
    $statusProvider = null;

    foreach ($endpointRute as $endpoint) {
        try {
            $response = Http::timeout(20)
                ->withHeaders(['User-Agent' => 'SmartPath/1.0'])
                ->get($endpoint . $koordinat, [
                    'overview' => 'full',
                    'geometries' => 'geojson',
                    'steps' => 'true',
                ]);

            $statusProvider = $response->status();

            if ($response->successful()) {
                $data = $response->json();

                if (!empty($data['routes'])) {
                    break;
                }
            }
        } catch (\Throwable $exception) {
            continue;
        }
    }

    if ($data === null) {
        return response()->json([
            'success' => false,
            'message' => 'Layanan rute sedang tidak dapat diakses. Coba lagi beberapa saat.',
            'status_provider' => $statusProvider,
        ], 503);
    }

    if (
        empty($data['routes']) ||
        !isset($data['routes'][0]['geometry'])
    ) {
        return response()->json([
            'success' => false,
            'message' => 'Tidak ditemukan rute menuju tujuan.',
        ], 404);
    }

   $route = $data['routes'][0];

$steps = collect($route['legs'] ?? [])
    ->flatMap(function ($leg) {
        return $leg['steps'] ?? [];
    })
    ->map(function ($step) {

        $maneuver = $step['maneuver'] ?? [];

        return [
            'type' => $maneuver['type'] ?? null,
            'modifier' => $maneuver['modifier'] ?? null,
            'name' => $step['name'] ?? '',
            'distance' => (float) ($step['distance'] ?? 0),

            'latitude' => isset($maneuver['location'][1])
                ? (float) $maneuver['location'][1]
                : null,

            'longitude' => isset($maneuver['location'][0])
                ? (float) $maneuver['location'][0]
                : null,
        ];
    })
    ->values();

return response()->json([
    'success' => true,
    'distance' => $route['distance'],
    'duration' => $route['duration'],
    'geometry' => $route['geometry'],
    'steps' => $steps,
]);
}

public function cekHambatanRute(Request $request)
{
    try {
        $request->validate([
            'geometry' => 'required|array',
        ]);

        $coordinates = $request->geometry['coordinates'] ?? [];

        if (empty($coordinates)) {
            return response()->json([
                'success' => false,
                'message' => 'Data rute tidak tersedia.',
            ], 400);
        }

        $laporan = Laporan::induk()
            ->terverifikasi()
            ->with('kategoriHambatan')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();

        $hasil = [];

        foreach ($laporan as $item) {
            $terdekat = null;

            foreach ($coordinates as $coordinate) {
                if (count($coordinate) < 2) {
                    continue;
                }

                $routeLongitude = (float) $coordinate[0];
                $routeLatitude = (float) $coordinate[1];

                $jarak = $this->hitungJarakMeter(
                    (float) $item->latitude,
                    (float) $item->longitude,
                    $routeLatitude,
                    $routeLongitude
                );

                if ($terdekat === null || $jarak < $terdekat) {
                    $terdekat = $jarak;
                }
            }

            if ($terdekat !== null && $terdekat <= 50) {
                $hasil[] = [
                    'id' => $item->id,
                    'judul' => $item->judul,
                    'kategori' => $item->kategoriHambatan?->nama,
                    'alamat' => $item->alamat_lengkap,
                    'latitude' => (float) $item->latitude,
                    'longitude' => (float) $item->longitude,
                    'jarak_dari_rute' => round($terdekat, 1),
                    'tingkat_prioritas' => $item->tingkat_prioritas,
                ];
            }
        }

        usort($hasil, function ($a, $b) {
            return $a['jarak_dari_rute'] <=> $b['jarak_dari_rute'];
        });

        return response()->json([
            'success' => true,
            'jumlah' => count($hasil),
            'hambatan' => $hasil,
        ]);

    } catch (\Throwable $e) {

        return response()->json([
            'success' => false,
            'message' => 'Terjadi error pada pengecekan hambatan.',
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ], 500);
    }
}


/**
 * Menghitung jarak dua koordinat dalam meter.
 */
private function hitungJarakMeter(
    $lat1,
    $lon1,
    $lat2,
    $lon2
) {
    $earthRadius = 6371000;

    $lat1Rad = deg2rad($lat1);
    $lat2Rad = deg2rad($lat2);

    $deltaLat = deg2rad($lat2 - $lat1);
    $deltaLon = deg2rad($lon2 - $lon1);

    $a = sin($deltaLat / 2) * sin($deltaLat / 2)
        + cos($lat1Rad)
        * cos($lat2Rad)
        * sin($deltaLon / 2)
        * sin($deltaLon / 2);

    $c = 2 * atan2(
        sqrt($a),
        sqrt(1 - $a)
    );

    return $earthRadius * $c;
}
}