<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
    {
    Schema::create('user_schedules', function (Blueprint $table) {
        $table->id();
        // Relasi ke tabel users
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        // Relasi ke tabel schedules (master shift)
        $table->foreignId('schedule_id')->constrained()->onDelete('cascade');
        // Kolom hari (Senin, Selasa, Rabu, dst)
        $table->string('day'); 
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_schedules');
    }
};
