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
        Schema::create('holidays', function (Blueprint $table) {
        $table->id();
        $table->string('title'); // Contoh: 'Hari Kemerdekaan RI'
        $table->date('holiday_date')->unique(); // Tanggal liburnya
        $table->string('description')->nullable(); // Keterangan tambahan (opsional)
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('holidays');
    }
};
