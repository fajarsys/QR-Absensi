<?php

namespace App\Ai\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;
use App\Models\User;
use Carbon\Carbon;

class CekKehadiranTool implements Tool
{
    
    public function description(): Stringable|string
    {
        return 'Tool ini digunakan untuk evaluasi kehadiran karyawan';
    }

    /**
     * Execute the tool.
     */
    public function handle(Request $request): string
    {
        $nama = $request['nama'] ?? '';

        // 1. Cari karyawan di database
        $user = User::where('name', 'like', "%{$nama}%")->first();

        // 2. Validasi jika tidak ditemukan
        if (!$user) {
            return "Informasi Sistem: Karyawan dengan nama '{$nama}' tidak ditemukan. Tolong beri tahu admin bahwa namanya tidak ada di database.";
        }

        // 3. Ambil data absensi bulan ini
        $bulanIni = Carbon::now()->month;
        $tahunIni = Carbon::now()->year;

        $hadir = $user->attendances()
                      ->whereMonth('created_at', $bulanIni)
                      ->whereYear('created_at', $tahunIni)
                      ->where('status', 'hadir')
                      ->count();

        $telat = $user->attendances()
                      ->whereMonth('created_at', $bulanIni)
                      ->whereYear('created_at', $tahunIni)
                      ->where('status', 'telat')
                      ->count();

        $izin = $user->attendances()
                     ->whereMonth('created_at', $bulanIni)
                     ->whereYear('created_at', $tahunIni)
                     ->whereIn('status', ['izin', 'sakit'])
                     ->count();

        // 4. Kembalikan string fakta ke AI
        return "Fakta Database untuk {$user->name} bulan ini: Hadir Tepat Waktu {$hadir} kali, Terlambat {$telat} kali, dan Tidak Masuk (Izin/Sakit) {$izin} kali.";
    }
    

    /**
     * Get the tool's schema definition.
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'nama' => $schema->string()->description('nama karyawan yang dicari (contoh : "angga slenting")')->required(),
        ];
    }
}
