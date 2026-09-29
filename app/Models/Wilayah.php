<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Wilayah extends Model
{
    use HasFactory;

    protected $table = 'wilayah';

    protected $fillable = [
        'induk_id',
        'nama',
        'level',
        'kode_bps',
        'latitude',
        'longitude',
        'aktif'
    ];

    protected $casts = [
        'aktif' => 'boolean',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    /**
     * Parent wilayah (self-reference)
     */
    public function induk(): BelongsTo
    {
        return $this->belongsTo(Wilayah::class, 'induk_id');
    }

    /**
     * Child wilayah (daerah bawahan)
     */
    public function anak(): HasMany
    {
        return $this->hasMany(Wilayah::class, 'induk_id');
    }

    /**
     * Users di dalam wilayah
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'wilayah_id');
    }

    /**
     * Laporan di dalam wilayah
     */
    public function laporan(): HasMany
    {
        return $this->hasMany(Laporan::class, 'wilayah_id');
    }

    /**
     * Fasilitas publik di dalam wilayah
     */
    public function fasilitasPublik(): HasMany
    {
        return $this->hasMany(FasilitasPublik::class, 'wilayah_id');
    }



    /**
     * Menghitung jarak titik referensi wilayah
     * terhadap koordinat laporan dalam meter.
     */
    public function distanceFrom(
        float $latitude,
        float $longitude
    ): float {
        $earthRadius = 6371000;

        $latFrom = deg2rad(
            (float) $this->latitude
        );

        $latTo = deg2rad(
            $latitude
        );

        $lngFrom = deg2rad(
            (float) $this->longitude
        );

        $lngTo = deg2rad(
            $longitude
        );

        $latDelta =
            $latTo - $latFrom;

        $lngDelta =
            $lngTo - $lngFrom;

        $a =
            sin($latDelta / 2) ** 2
            +
            cos($latFrom)
            *
            cos($latTo)
            *
            sin($lngDelta / 2) ** 2;

        $a =
            min(
                1,
                max(0, $a)
            );

        return $earthRadius
            * 2
            * atan2(
                sqrt($a),
                sqrt(1 - $a)
            );
    }


    /**
     * Menentukan Kecamatan referensi terdekat
     * berdasarkan koordinat laporan.
     *
     * Nilai ini hanya digunakan sebagai data internal.
     * Pengguna tidak memilih Kecamatan pada form laporan.
     */
    public static function nearestKecamatanByReferencePoint(
        float $latitude,
        float $longitude
    ): ?self {

        $wilayahList =
            static::query()
                ->aktif()
                ->level('kecamatan')
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->get();

        $nearest = null;

        $nearestDistance = INF;

        foreach ($wilayahList as $wilayah) {

            $distance =
                $wilayah->distanceFrom(
                    $latitude,
                    $longitude
                );

            if (
                $distance <
                $nearestDistance
            ) {

                $nearestDistance =
                    $distance;

                $nearest =
                    $wilayah;
            }
        }

        return $nearest;
    }

    /**
     * Scope for active wilayah
     */
    public function scopeAktif($query)
    {
        return $query->where('aktif', true);
    }

    /**
     * Scope for specific level
     */
    public function scopeLevel($query, string $level)
    {
        return $query->where('level', $level);
    }

    /**
     * dapetin semua nama hierarki
     */
    public function getNamaLengkapAttribute(): string
    {
        $nama = $this->nama;
        if ($this->induk) {
            return $this->induk->nama_lengkap . ' > ' . $nama;
        }
        return $nama;
    }

    /**
     * Get children recursively
     */
    public function getAnakRecursive(): array
    {
        $children = [];
        foreach ($this->anak as $child) {
            $children[] = $child;
            $children = array_merge($children, $child->getAnakRecursive());
        }
        return $children;
    }
}