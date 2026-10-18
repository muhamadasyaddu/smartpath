<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('laporan', function (Blueprint $table) {
            $table->enum('status_duplikasi', ['kandidat', 'dikonfirmasi', 'mandiri'])
                ->nullable()
                ->after('laporan_induk_id');

            $table->foreignId('duplikasi_diputuskan_oleh')
                ->nullable()
                ->after('status_duplikasi')
                ->constrained('users')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            $table->timestamp('duplikasi_diputuskan_pada')
                ->nullable()
                ->after('duplikasi_diputuskan_oleh');

            $table->index('status_duplikasi');
        });

        /*
         * Data lama:
         * - sudah melewati verifikasi  => dikonfirmasi (perilaku lama dipertahankan,
         *   admin dapat mengoreksi lewat tombol koreksi relasi)
         * - masih pending / ditolak    => kandidat
         */
        DB::table('laporan')
            ->whereNotNull('laporan_induk_id')
            ->whereIn('status', ['diverifikasi', 'dalam_perbaikan', 'selesai'])
            ->update(['status_duplikasi' => 'dikonfirmasi']);

        DB::table('laporan')
            ->whereNotNull('laporan_induk_id')
            ->whereIn('status', ['menunggu_verifikasi', 'ditolak', 'diarsipkan'])
            ->update(['status_duplikasi' => 'kandidat']);
    }

    public function down(): void
    {
        Schema::table('laporan', function (Blueprint $table) {
            $table->dropIndex(['status_duplikasi']);
            $table->dropConstrainedForeignId('duplikasi_diputuskan_oleh');
            $table->dropColumn(['status_duplikasi', 'duplikasi_diputuskan_pada']);
        });
    }
};