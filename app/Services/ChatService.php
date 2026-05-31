<?php

namespace App\Services;

use App\Models\User;
use App\Ai\Agents\Evaluasi;

class ChatService
{
    /**
     * Create a new class instance.
     */
    public function processMessage(string $message): array
    {
        try {
            $pesanAdmin = strtolower($message);

            // 1. Logika Pengecekan Perintah Laporan / PDF
            if (str_contains($pesanAdmin, 'pdf') || str_contains($pesanAdmin, 'cetak') || str_contains($pesanAdmin, 'laporan')) {
                return $this->handleReportRequest($pesanAdmin);
            }

            // 2. Logika Panggilan AI Agent (Evaluasi)
            $agent = new Evaluasi(); 
            $agent->model = 'qwen2.5:0.5b'; 
            
            if (method_exists($agent, 'withModel')) {
                $agent = $agent->withModel(config('ai.model'));
            }

            $response = $agent->prompt($message);

            return [
                'success' => true,
                'data' => (string) $response,
                'code' => 200
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Agent gagal merespons: ' . $e->getMessage(),
                'code' => 500
            ];
        }
    }

    private function handleReportRequest(string $pesanAdmin): array
    {
        $kata = explode(' ', $pesanAdmin);
        $target = end($kata);
        $bulan = date('m');
        $tahun = date('Y');

        // Jika minta semua data
        if (in_array($target, ['semua', 'pdf', 'cetak', 'laporan'])) {
            $url = route('admin.attendance.printAll', ['month' => $bulan, 'year' => $tahun]);
            
            return [
                'success' => true,
                'data' => "Sistem: Link laporan seluruh karyawan bulan ini sudah siap. <a href='{$url}' target='_blank' style='color: blue; text-decoration: underline;'>Klik di sini untuk mencetak</a>",
                'code' => 200
            ];
        }

        // Jika minta nama spesifik
        $user = User::where('name', 'like', "%{$target}%")->first();
        
        if (!$user) {
            return [
                'success' => true, // Status HTTP tetap 200 (berhasil merespons), tapi datanya berupa informasi tidak ditemukan
                'data' => "Informasi Sistem: Karyawan dengan nama '{$target}' tidak ditemukan di database.",
                'code' => 200
            ];
        }

        $url = route('admin.attendance.printUser', ['user' => $user->id, 'month' => $bulan, 'year' => $tahun]);
        
        return [
            'success' => true,
            'data' => "Sistem: Laporan untuk {$user->name} sudah siap. <a href='{$url}' target='_blank' style='color: blue; text-decoration: underline;'>Klik di sini untuk mencetak</a>",
            'code' => 200
        ];
    }
}

