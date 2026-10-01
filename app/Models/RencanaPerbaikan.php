<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RencanaPerbaikan extends Model
{
    use HasFactory;

    protected $table = 'rencana_perbaikan';

    protected $fillable = [
        'laporan_id',
        'tindakan',
        'penanggung_jawab',
        'tanggal_mulai',
        'target_selesai',
        'tanggal_selesai',
        'estimasi_anggaran',
        'realisasi_anggaran',
        'status',
        'catatan',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'target_selesai' => 'date',
        'tanggal_selesai' => 'date',
        'estimasi_anggaran' => 'decimal:2',
        'realisasi_anggaran' => 'decimal:2',
    ];

    /**
     * Relasi ke laporan.
     */
    public function laporan()
    {
        return $this->belongsTo(Laporan::class, 'laporan_id');
    }

    /**
     * Mendapatkan sisa anggaran.
     */
    public function getSisaAnggaranAttribute()
    {
        return max(
            0,
            (float) $this->estimasi_anggaran - (float) $this->realisasi_anggaran
        );
    }

    /**
     * Mendapatkan label status.
     */
    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'belum_dimulai' => 'Belum Dimulai',
            'dalam_perbaikan' => 'Dalam Perbaikan',
            'selesai' => 'Selesai',
            default => ucfirst(str_replace('_', ' ', $this->status)),
        };
    }
}