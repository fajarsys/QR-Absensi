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
        Schema::create('offices', function (Blueprint $table) {
        $table->id();
        $table->string('name'); // Nama Kantor/Cabang
        
        // Koordinat Kantor (Pusat)
        $table->double('latitude'); 
        $table->double('longitude'); 
        
        $table->integer('radius')->default(200); // Radius dalam meter
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offices');
    }
};
