<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Notifications\CustomResetPasswordNotification;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $table = 'users';

    protected $fillable = [
        'nama_lengkap',
        'email',
        'google_id',
        'kata_sandi',
        'nomor_hp',
        'peran',
        'wilayah_id',
        'foto_profil',
        'adalah_disabilitas',
        'jenis_disabilitas',
        'email_terverifikasi',
        'terakhir_masuk',
        'token_reset',
        'token_reset_kadaluarsa',
        'aktif',
    ];

    protected $hidden = [
        'kata_sandi',
        'google_id',
        'token_reset',
        'remember_token',
    ];

    protected $casts = [
        'email_terverifikasi' => 'boolean',
        'adalah_disabilitas' => 'boolean',
        'aktif' => 'boolean',
        'terakhir_masuk' => 'datetime',
        'token_reset_kadaluarsa' => 'datetime',
    ];

    /**
     * Password autentikasi Laravel menggunakan kolom kata_sandi.
     */
    public function getAuthPassword(): string
    {
        return $this->kata_sandi;
    }

    /**
     * Relasi wilayah pengguna.
     */
    public function wilayah(): BelongsTo
    {
        return $this->belongsTo(Wilayah::class, 'wilayah_id');
    }

    /**
     * Laporan yang dibuat pengguna.
     */
    public function laporanDibuat(): HasMany
    {
        return $this->hasMany(Laporan::class, 'pelapor_id');
    }

    /**
     * Verifikasi yang dilakukan pengguna.
     */
    public function verifikasiDilakukan(): HasMany
    {
        return $this->hasMany(VerifikasiLaporan::class, 'admin_id');
    }

    /**
     * Riwayat perubahan status laporan.
     */
    public function riwayatStatus(): HasMany
    {
        return $this->hasMany(RiwayatStatusLaporan::class, 'diubah_oleh_id');
    }

    /**
     * Notifikasi pengguna.
     */
    public function notifikasi(): HasMany
    {
        return $this->hasMany(Notifikasi::class, 'penerima_id');
    }

    /**
     * Audit yang dilakukan pengguna.
     */
    public function audit(): HasMany
    {
        return $this->hasMany(Audit::class, 'pengguna_id');
    }

    /**
     * Pengaturan prioritas yang dibuat pengguna.
     */
    public function pengaturanPrioritas(): HasMany
    {
        return $this->hasMany(PengaturanPrioritas::class, 'dibuat_oleh_id');
    }

    /**
     * Scope pengguna aktif.
     */
    public function scopeAktif($query)
    {
        return $query->where('aktif', true);
    }

    /**
     * Scope berdasarkan role.
     */
    public function scopePeran($query, string $peran)
    {
        return $query->where('peran', $peran);
    }

    /**
     * Pemeriksaan role umum.
     */
    public function hasRole(string $role): bool
    {
        return $this->peran === $role;
    }

    /**
     * Pemeriksaan administrator.
     *
     * 'admin' tetap diterima untuk kompatibilitas
     * dengan data lama dari branch Ratna.
     */
    public function isAdmin(): bool
    {
        return in_array($this->peran, [
            'administrator',
            'admin',
        ], true);
    }

    /**
     * Pemeriksaan petugas dinas.
     */
    public function isDinas(): bool
    {
        return $this->peran === 'dinas';
    }

    /**
     * Pemeriksaan warga.
     */
    public function isWarga(): bool
    {
        return $this->peran === 'warga';
    }

    /**
     * Menentukan dashboard berdasarkan role.
     */
    public function getDashboardRouteName(): string
    {
        return match ($this->peran) {
            'administrator',
            'admin' => 'admin.dashboard',

            'dinas' => 'dinas.dashboard',

            'warga' => 'warga.dashboard',

            default => 'beranda',
        };
    }

    /**
     * Nama lengkap pengguna.
     */
    public function getNamaLengkapAttribute(): string
    {
        return $this->attributes['nama_lengkap'] ?? '';
    }

    /**
     * Jumlah notifikasi belum dibaca.
     */
    public function getNotifikasiBelumDibacaCountAttribute(): int
    {
        return $this->notifikasi()
            ->where('sudah_dibaca', false)
            ->count();
    }

    /**
     * Override pengiriman notifikasi reset password
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomResetPasswordNotification($token));
    }
}