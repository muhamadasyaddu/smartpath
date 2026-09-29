<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRencanaPerbaikanTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('rencana_perbaikan', function (Blueprint $table) {
            $table->id();

            // Relasi ke laporan yang akan ditindaklanjuti
            $table->foreignId('laporan_id')
                ->constrained('laporan')
                ->cascadeOnDelete();

            // Rencana tindakan
            $table->text('tindakan');

            // Penanggung jawab / bidang yang menangani
            $table->string('penanggung_jawab', 150)->nullable();

            // Waktu pelaksanaan
            $table->date('tanggal_mulai')->nullable();
            $table->date('target_selesai')->nullable();
            $table->date('tanggal_selesai')->nullable();

            // Anggaran
            $table->decimal('estimasi_anggaran', 15, 2)->default(0);
            $table->decimal('realisasi_anggaran', 15, 2)->default(0);

            // Status proses
            $table->string('status', 30)->default('belum_dimulai');

            // Catatan tambahan
            $table->text('catatan')->nullable();

            $table->timestamps();

            // Satu laporan memiliki satu rencana perbaikan aktif
            $table->unique('laporan_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('rencana_perbaikan');
    }
}