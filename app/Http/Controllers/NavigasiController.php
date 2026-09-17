<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

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
        ]);

        $tujuan = trim($request->tujuan);

        $response = Http::withHeaders([
            'User-Agent' => 'SmartPath/1.0 (Smart City Accessibility Project)',
            'Accept-Language' => 'id',
        ])->get('https://nominatim.openstreetmap.org/search', [
            'q' => $tujuan . ', Indonesia',
            'format' => 'json',
            'limit' => 1,
        ]);

        if (!$response->successful()) {
            return response()->json([
                'success' => false,
                'message' => 'Pencarian lokasi tujuan gagal. Silakan coba lagi.',
            ], 500);
        }

        $data = $response->json();

        if (empty($data)) {
            return response()->json([
                'success' => false,
                'message' => 'Lokasi tujuan tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'tujuan' => $data[0]['display_name'],
            'latitude' => (float) $data[0]['lat'],
            'longitude' => (float) $data[0]['lon'],
        ]);
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

    $url = "https://router.project-osrm.org/route/v1/foot/"
        . $longitudeAwal . ","
        . $latitudeAwal . ";"
        . $longitudeTujuan . ","
        . $latitudeTujuan;

    $response = Http::withHeaders([
        'User-Agent' => 'SmartPath/1.0',
    ])->get($url, [
        'overview' => 'full',
        'geometries' => 'geojson',
    ]);

    if (!$response->successful()) {
        return response()->json([
            'success' => false,
            'message' => 'Rute tidak dapat ditemukan.',
        ], 500);
    }

    $data = $response->json();

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

    return response()->json([
        'success' => true,
        'distance' => $route['distance'],
        'duration' => $route['duration'],
        'geometry' => $route['geometry'],
    ]);
}

public function cekHambatanRute(Request $request)
{
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

    // Ambil laporan yang sudah diverifikasi
    $laporan = Laporan::induk()
        ->terverifikasi()
        ->with('kategoriHambatan')
        ->whereNotNull('latitude')
        ->whereNotNull('longitude')
        ->get();

    $hasil = [];

    /*
     * Untuk prototype:
     * laporan dianggap berada di sekitar rute
     * jika jaraknya maksimal 20 meter dari
     * salah satu titik geometry rute.
     */
    foreach ($laporan as $item) {

        $terdekat = null;

        foreach ($coordinates as $coordinate) {

            if (count($coordinate) < 2) {
                continue;
            }

            // GeoJSON = [longitude, latitude]
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

        // Maksimal 20 meter dari rute
        if ($terdekat !== null && $terdekat <= 20) {

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

    // Hambatan terdekat dari rute ditampilkan lebih dahulu
    usort($hasil, function ($a, $b) {
        return $a['jarak_dari_rute'] <=> $b['jarak_dari_rute'];
    });

    return response()->json([
        'success' => true,
        'jumlah' => count($hasil),
        'hambatan' => $hasil,
    ]);
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