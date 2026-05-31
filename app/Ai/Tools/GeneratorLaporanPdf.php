<?php

namespace App\Ai\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use Carbon\Carbon;

class GeneratorLaporanPdf implements Tool
{
   
    public function description(): Stringable|string
    {
        return 'Tool ini digunakan untuk membuat link cetak laporan absensi. Panggil tool ini jika user meminta laporan PDF atau cetak absensi.';
    }

    /**
     * Execute the tool.
     */
    public function handle(Request $request): Stringable|string
    {
        // 1. Tangkap parameter dari AI, gunakan bulan/tahun saat ini jika AI tidak mengirimnya
        $target = strtolower($request['target'] ?? 'semua');
        $bulan  = !empty($request['bulan']) ? str_pad($request['bulan'], 2, '0', STR_PAD_LEFT) : date('m');
        $tahun  = !empty($request['tahun']) ? $request['tahun'] : date('Y');

        // 2. Logika Eksekusi: Jika targetnya "Semua"
        if ($target === 'semua' || $target === 'all') {
            // Panggil nama route untuk printAll
            $url = route('admin.kehadiran.print_all', ['month' => $bulan, 'year' => $tahun]);
            
            return "Sistem: Link laporan seluruh karyawan berhasil dibuat. Berikan balasan ini ke user: 'Laporan semua karyawan bulan {$bulan}-{$tahun} sudah siap. <a href=\"{$url}\" target=\"_blank\" class=\"text-blue-500 underline\">Klik di sini untuk mencetak</a>'";
        }

        // 3. Logika Eksekusi: Jika targetnya User Spesifik
        $user = User::where('name', 'like', "%{$target}%")->first();

        if (!$user) {
            return "Informasi Sistem: Karyawan dengan nama '{$target}' tidak ditemukan. Beritahu user bahwa namanya tidak ada di database.";
        }

        // Panggil nama route untuk printUser
        $url = route('admin.kehadiran.print', [
            'userId' => $user->id, 
            'month'  => $bulan, 
            'year'   => $tahun
        ]);

        return "Sistem: Link laporan berhasil dibuat. Berikan balasan ini ke user: 'Laporan untuk {$user->name} bulan {$bulan}-{$tahun} sudah siap. <a href=\"{$url}\" target=\"_blank\" class=\"text-blue-500 underline\">Klik di sini untuk mencetak</a>'";
        
    }
    

    /**
     * Get the tool's schema definition.
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'target' => $schema->string()
                               ->description('Nama karyawan (contoh: "Fajar") atau ketik "semua" untuk seluruh karyawan.')
                               ->required(),
            'bulan'  => $schema->string()
                               ->description('Angka bulan (contoh: "04"). Kosongkan jika tidak disebutkan.'),
            'tahun'  => $schema->string()
                               ->description('Angka tahun (contoh: "2026"). Kosongkan jika tidak disebutkan.'),
        ];
    }

}
