<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
         Schema::create('laporan', function (Blueprint $table) {
            $table->id();
            $table->string('kode_laporan', 20)->unique()
                  ->comment('Format: LP-YYYYMM-XXXXX');
            $table->foreignId('pelapor_id')
                  ->constrained('users')
                  ->onDelete('restrict')
                  ->onUpdate('cascade');
            $table->foreignId('kategori_hambatan_id')
                  ->constrained('kategori_hambatan')
                  ->onDelete('restrict')
                  ->onUpdate('cascade');
            $table->foreignId('wilayah_id')
                  ->nullable()
                  ->constrained('wilayah')
                  ->onDelete('set null')
                  ->onUpdate('cascade');
            $table->foreignId('laporan_induk_id')
                  ->nullable()
                  ->constrained('laporan')
                  ->onDelete('set null')
                  ->onUpdate('cascade');
            
            // Lokasi
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->string('alamat_lengkap', 255)->nullable();
            
            // Konten
            $table->string('judul', 200);
            $table->text('deskripsi')->nullable();
            
            // Status
            $table->enum('status', [
                'menunggu_verifikasi',
                'diverifikasi',
                'ditolak',
                'dalam_perbaikan',
                'selesai',
                'diarsipkan'
            ])->default('menunggu_verifikasi');
            
            // Skor
            $table->decimal('skor_prioritas', 5, 2)->nullable();
            $table->decimal('skor_keparahan', 5, 2)->nullable();
            $table->decimal('skor_pelapor', 5, 2)->nullable();
            $table->decimal('skor_fasilitas', 5, 2)->nullable();
            $table->foreignId('fasilitas_terdekat_id')
                  ->nullable()
                  ->constrained('fasilitas_publik')
                  ->onDelete('set null')
                  ->onUpdate('cascade');
            $table->decimal('jarak_fasilitas_meter', 8, 2)->nullable();
            
            // Agregasi
            $table->smallInteger('jumlah_pelapor')->unsigned()->default(1);
            
            // Metadata
            $table->enum('sumber_koordinat', ['gps_otomatis', 'manual'])->default('gps_otomatis');
            $table->enum('platform_pelapor', ['web', 'mobile_web', 'lainnya'])->default('web');
            $table->timestamp('dihitung_pada')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('pelapor_id');
            $table->index('kategori_hambatan_id');
            $table->index('wilayah_id');
            $table->index('laporan_induk_id');
            $table->index('status');
            $table->index(['latitude', 'longitude']);
            $table->index('skor_prioritas');
            $table->index('fasilitas_terdekat_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan');
    }
};
