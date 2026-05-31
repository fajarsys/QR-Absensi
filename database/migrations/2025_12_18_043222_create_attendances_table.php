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
    Schema::create('attendances', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // Relasi ke tabel users
        
        $table->date('date'); // Tanggal absen (biar mudah filter harian)
        $table->time('clock_in'); // Jam masuk
        $table->time('clock_out')->nullable(); // Jam pulang (nullable karena pas masuk belum ada isinya)
        
        // Bukti lokasi user saat absen
        $table->double('latitude_in')->nullable();
        $table->double('longitude_in')->nullable();
        $table->double('latitude_out')->nullable();
        $table->double('longitude_out')->nullable();

        $table->string('status')->default('hadir'); // hadir, terlambat, izin
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
