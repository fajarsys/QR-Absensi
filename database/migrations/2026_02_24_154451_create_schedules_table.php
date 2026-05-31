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
        Schema::create('schedules', function (Blueprint $table) {
        $table->id();
        $table->string('name'); // Contoh: 'Shift Pagi', 'Shift Jaga Malam'
        $table->time('start_time'); // Jam mulai masuk (contoh: 08:00:00)
        $table->time('end_time'); // Jam pulang (contoh: 16:00:00)
        $table->integer('late_tolerance')->default(0); // Toleransi telat dalam menit (misal: 15)
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
