<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class AutoCheckout extends Command
{
    protected $signature = 'absen:auto-checkout';

    protected $description = 'Menyapu bersih absen menggantung dan mengirim laporan ke Telegram.';

    public function handle()
    {
        $hariIni = Carbon::today();

        // Cari absen menggantung yang belum dieksekusi robot
        $absenMenggantung = Attendance::whereDate('date', $hariIni)
            ->whereNotNull('clock_in')
            ->whereNull('clock_out')
            ->where('status', '!=', 'Lupa Pulang')
            ->get();

        $jumlahDiubah = 0;

        foreach ($absenMenggantung as $absen) {
            $catatanLama = $absen->notes;
            $absen->update([
                'clock_out' => null, // Dibiarkan kosong agar tidak merusak perhitungan total jam kerja
                'status' => 'Lupa Pulang',
                'notes'  => empty($catatanLama) || $catatanLama === '[NULL]' ? 'Sistem: Tidak Absen Pulang' : $catatanLama . ' | Sistem: Tidak Absen Pulang'
            ]);
            $jumlahDiubah++;
        }

        // Format pesan laporan
        $pesan = "🤖 *Laporan Robot Sapu Bersih*\n";
        $pesan .= "Tanggal: " . Carbon::now()->format('d-m-Y') . "\n";
        $pesan .= "Eksekusi: Berhasil melakukan auto-checkout pada *{$jumlahDiubah}* karyawan.";

        // Tampilkan di terminal lokal
        $this->info($pesan);

        // Eksekusi pengiriman ke Telegram
        $telegramToken = env('TELEGRAM_BOT_TOKEN');
        $chatId = env('TELEGRAM_CHAT_ID');

        if ($telegramToken && $chatId) {
            try {
                Http::post("https://api.telegram.org/bot{$telegramToken}/sendMessage", [
                    'chat_id' => $chatId,
                    'text' => $pesan,
                    'parse_mode' => 'Markdown'
                ]);
            } catch (\Exception $e) {
                $this->error("Gagal mengirim ke Telegram: " . $e->getMessage());
            }
        } else {
            $this->warn("Kredensial Telegram (Token/Chat ID) tidak ditemukan");
        }
    }
}
