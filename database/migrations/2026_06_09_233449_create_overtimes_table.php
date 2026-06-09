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
        Schema::create('overtimes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Tanggal pelaksanaan lembur
            $table->date('tanggal');
            // Membedakan inisiatif mandiri atau perintah atasan (SPL)
            $table->enum('tipe_lembur', ['pengajuan', 'perintah'])->default('pengajuan');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->integer('durasi_menit')->default(0);
            $table->text('keterangan')->nullable();
            // Relasi ke atasan yang menyetujui (nullable karena di awal statusnya pasti pending)
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('overtimes');
    }
};
