<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\RencanaPerbaikan;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Laporan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'laporan';

    protected $fillable = [
        'kode_laporan',
        'pelapor_id',
        'kategori_hambatan_id',
        'wilayah_id',
        'laporan_induk_id',
        'latitude',
        'longitude',
        'alamat_lengkap',
        'judul',
        'deskripsi',
        'status',
        'skor_prioritas',
        'skor_keparahan',
        'skor_pelapor',
        'skor_fasilitas',
        'fasilitas_terdekat_id',
        'jarak_fasilitas_meter',
        'jumlah_pelapor',
        'sumber_koordinat',
        'platform_pelapor',
        'dihitung_pada'
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'skor_prioritas' => 'decimal:2',
        'skor_keparahan' => 'decimal:2',
        'skor_pelapor' => 'decimal:2',
        'skor_fasilitas' => 'decimal:2',
        'jarak_fasilitas_meter' => 'decimal:2',
        'jumlah_pelapor' => 'integer',
        'dihitung_pada' => 'datetime',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        static::creating(function ($laporan) {
            if (empty($laporan->kode_laporan)) {
                $laporan->kode_laporan = $laporan->generateKodeLaporan();
            }
        });
    }

        /**
     * Generate kode laporan unik.
     *
     * Soft delete tidak boleh membuat kode lama dapat digunakan kembali,
     * karena kolom kode_laporan memiliki UNIQUE constraint.
     *
     * Format:
     * LP-YYYYMM-XXXXX
     */
    protected function generateKodeLaporan(): string
    {
        $yearMonth = now()->format('Ym');
        $prefix = "LP-{$yearMonth}-";

        
        $lastNumber = static::withTrashed()
            ->where('kode_laporan', 'like', $prefix . '%')
            ->selectRaw(
                "MAX(CAST(SUBSTRING(kode_laporan, 11) AS UNSIGNED)) as nomor_terakhir"
            )
            ->value('nomor_terakhir');

        $nextNumber = ((int) $lastNumber) + 1;

        /*
        * Format XXXXX hanya mampu menampung 99.999 laporan
        * per bulan.
        */
        if ($nextNumber > 99999) {
            throw new \RuntimeException(
                "Nomor laporan untuk {$yearMonth} telah mencapai batas maksimum."
            );
        }

        return sprintf(
            'LP-%s-%05d',
            $yearMonth,
            $nextNumber
        );
    }

    /**
     * Pelapor (user who created this report)
     */
    public function pelapor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pelapor_id');
    }

    /**
     * Kategori hambatan
     */
    public function kategoriHambatan(): BelongsTo
    {
        return $this->belongsTo(KategoriHambatan::class, 'kategori_hambatan_id');
    }

    /**
     * Wilayah
     */
    public function wilayah(): BelongsTo
    {
        return $this->belongsTo(Wilayah::class, 'wilayah_id');
    }

    /**
     * Induk laporan (for deduplication)
     */
    public function laporanInduk(): BelongsTo
    {
        return $this->belongsTo(Laporan::class, 'laporan_induk_id');
    }

    /**
     * Anak laporan (duplicates grouped under this report)
     */
    public function laporanAnak(): HasMany
    {
        return $this->hasMany(Laporan::class, 'laporan_induk_id');
    }

    /**
     * Fasilitas terdekat
     */
    public function fasilitasTerdekat(): BelongsTo
    {
        return $this->belongsTo(FasilitasPublik::class, 'fasilitas_terdekat_id');
    }

    /**
     * Foto laporan (Alias 'foto' agar aman jika ada fungsi lama yang memanggil)
     */
    public function foto(): HasMany
    {
        return $this->hasMany(FotoLaporan::class, 'laporan_id');
    }

    /**
     * Foto laporan (Relasi standar yang dipanggil di View & Controller baru)
     */
    public function fotoLaporan(): HasMany
    {
        return $this->hasMany(FotoLaporan::class, 'laporan_id');
    }

    /**
     * Foto utama laporan.
     * Kompatibilitas dengan view yang menggunakan $laporan->fotoUtama.
     */
    public function getFotoUtamaAttribute(): ?FotoLaporan
    {
        $fotoCollection = $this->relationLoaded('fotoLaporan') 
            ? $this->fotoLaporan 
            : ($this->relationLoaded('foto') ? $this->foto : $this->fotoLaporan);

        return $fotoCollection
            ->sortBy([
                ['adalah_utama', 'desc'],
                ['urutan', 'asc'],
            ])
            ->first();
    }

    /**
     * Verifikasi laporan
     */
    public function verifikasi(): HasMany
    {
        return $this->hasMany(VerifikasiLaporan::class, 'laporan_id');
    }

    /**
     * Riwayat status
     */
    public function riwayatStatus(): HasMany
    {
        return $this->hasMany(RiwayatStatusLaporan::class, 'laporan_id');
    }

    /**
     * Notifikasi terkait
     */
    public function notifikasi(): HasMany
    {
        return $this->hasMany(Notifikasi::class, 'laporan_id');
    }

    /**
     * Scope for main reports (not duplicates)
     */
    public function scopeInduk($query)
    {
        return $query->whereNull('laporan_induk_id');
    }

    /**
     * Scope for active reports
     */
    public function scopeAktif($query)
    {
        return $query->whereNull('deleted_at');
    }

    /**
     * Scope for specific status
     */
    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for verified reports
     */
    public function scopeTerverifikasi($query)
    {
        return $query->whereIn('status', ['diverifikasi', 'dalam_perbaikan', 'selesai']);
    }

    /**
     * Dapetin semua prioritas level
     */
    public function getTingkatPrioritasAttribute(): string
    {
        if ($this->skor_prioritas === null) {
            return 'Belum Dinilai';
        }

        return match (true) {
            $this->skor_prioritas >= 70 => 'Tinggi',
            $this->skor_prioritas >= 40 => 'Sedang',
            default => 'Rendah',
        };
    }

    /**
     * Get status label in Bahasa Indonesia
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'menunggu_verifikasi' => 'Menunggu Verifikasi',
            'diverifikasi' => 'Terverifikasi',
            'ditolak' => 'Ditolak',
            'dalam_perbaikan' => 'Dalam Perbaikan',
            'selesai' => 'Selesai',
            'diarsipkan' => 'Diarsipkan',
            default => $this->status,
        };
    }

    /**
     * Get status badge color
     */
    public function getStatusWarnaAttribute(): string
    {
        return match ($this->status) {
            'menunggu_verifikasi' => 'warning',
            'diverifikasi' => 'success',
            'ditolak' => 'danger',
            'dalam_perbaikan' => 'info',
            'selesai' => 'primary',
            'diarsipkan' => 'secondary',
            default => 'secondary',
        };
    }

    /**
     * Alias kompatibilitas untuk view lama yang menggunakan $laporan->warna.
     */
    public function getWarnaAttribute(): string
    {
        return match ($this->status) {
            'menunggu_verifikasi' => 'bg-amber-50 text-amber-800 border border-amber-200',
            'diverifikasi' => 'bg-emerald-50 text-emerald-800 border border-emerald-200',
            'ditolak' => 'bg-rose-50 text-rose-800 border border-rose-200',
            'dalam_perbaikan' => 'bg-sky-50 text-sky-800 border border-sky-200',
            'selesai' => 'bg-slate-100 text-slate-800 border border-slate-200',
            'diarsipkan' => 'bg-slate-100 text-slate-600 border border-slate-200',
            default => 'bg-slate-50 text-slate-600 border border-slate-200',
        };
    }

    public function calculatePriorityScore(): array
{
    $config = PengaturanPrioritas::getActive();

    /*
     * Fallback tetap tersedia agar sistem tidak gagal
     * apabila konfigurasi prioritas belum tersedia.
     */
    $bobotKeparahan = (float) ($config?->bobot_keparahan ?? 0.40);
    $bobotPelapor = (float) ($config?->bobot_pelapor ?? 0.35);
    $bobotFasilitas = (float) ($config?->bobot_fasilitas ?? 0.25);

    /*
     * Pastikan total bobot selalu 1.00.
     */
    $totalBobot =
        $bobotKeparahan
        + $bobotPelapor
        + $bobotFasilitas;

    if ($totalBobot <= 0) {
        $bobotKeparahan = 0.40;
        $bobotPelapor = 0.35;
        $bobotFasilitas = 0.25;
        $totalBobot = 1.00;
    }

    /*
     * Normalisasi bobot jika konfigurasi admin
     * belum berjumlah tepat 1.00.
     */
    if (abs($totalBobot - 1.00) > 0.001) {
        $bobotKeparahan /= $totalBobot;
        $bobotPelapor /= $totalBobot;
        $bobotFasilitas /= $totalBobot;
    }

    /*
     * ==========================================================
     * 1. KEPARAHAN
     * ==========================================================
     *
     * Nilai kategori sudah berada pada skala 0-100.
     */
    $skorKeparahan = (float) (
        $this->kategoriHambatan?->bobot_keparahan ?? 50
    );

    $skorKeparahan = max(
        0,
        min(100, $skorKeparahan)
    );

    /*
     * ==========================================================
     * 2. JUMLAH PELAPOR
     * ==========================================================
     *
     * Normalisasi MVP:
     *
     * 1 pelapor  = 10
     * 5 pelapor  = 50
     * 10+        = 100
     *
     * Cap 100 menjaga skala tetap konsisten.
     *
     * Formula ini sengaja dibuat transparan karena
     * proposal belum menentukan formula normalisasi
     * jumlah pelapor secara eksplisit.
     */
    $jumlahPelapor = max(
        1,
        (int) $this->jumlah_pelapor
    );

    $skorPelapor = min(
        100,
        $jumlahPelapor * 10
    );

    /*
     * ==========================================================
     * 3. KEDEKATAN FASILITAS
     * ==========================================================
     *
     * Semakin dekat fasilitas publik,
     * semakin tinggi skor.
     */
    $skorFasilitas = 0;

    $jarakFasilitas =
        $this->jarak_fasilitas_meter !== null
            ? (float) $this->jarak_fasilitas_meter
            : null;

    $radiusFasilitas = max(
        1,
        (int) ($config?->radius_fasilitas_m ?? 500)
    );

    if ($jarakFasilitas !== null) {
        $skorFasilitas =
            100
            -
            (
                ($jarakFasilitas / $radiusFasilitas)
                * 100
            );

        $skorFasilitas = max(
            0,
            min(100, $skorFasilitas)
        );
    }

    /*
     * ==========================================================
     * WSM
     * ==========================================================
     */
    $skorPrioritas =
        ($skorKeparahan * $bobotKeparahan)
        +
        ($skorPelapor * $bobotPelapor)
        +
        ($skorFasilitas * $bobotFasilitas);

    return [
        'skor_prioritas' => round(
            max(0, min(100, $skorPrioritas)),
            2
        ),

        'skor_keparahan' => round(
            $skorKeparahan,
            2
        ),

        'skor_pelapor' => round(
            $skorPelapor,
            2
        ),

        'skor_fasilitas' => round(
            $skorFasilitas,
            2
        ),
    ];
}


         public function rencanaPerbaikan()
        {
            return $this->hasOne(
                RencanaPerbaikan::class,
                'laporan_id'
            );
        }

}