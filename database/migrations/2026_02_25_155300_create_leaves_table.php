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
        Schema::create('leaves', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // Siapa yang ngajuin
        $table->date('start_date'); // Tanggal mulai izin
        $table->date('end_date'); // Tanggal selesai izin
        $table->enum('type', ['sakit', 'izin', 'cuti']); // Jenis izin
        $table->text('reason'); // Alasan izin
        $table->string('attachment')->nullable(); // Foto surat dokter/bukti (opsional)
        
        // Status approval dari admin (defaultnya 'pending' atau menunggu)
        $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
        
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leaves');
    }
};
