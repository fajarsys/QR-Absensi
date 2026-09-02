<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Schedule;
use App\Models\UserSchedule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow; 
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Carbon\Carbon;

class UserScheduleImport implements ToModel, WithStartRow
{
    public int $berhasil = 0;
    public int $gagal = 0;
    public array $barisGagal = [];

    public function model(array $row): \Illuminate\Database\Eloquent\Model|array|null
    {
        // Pastikan baris tidak kosong
        if (!isset($row[0]) || empty(trim($row[0]))) {
            return null;
        }

        $baris = $this->berhasil + $this->gagal + 2; // +2 karena header di baris 1

        // Kolom A: Nama Karyawan (bukan ID)
        $namaKaryawan = trim($row[0]);
        $user = User::whereRaw('LOWER(name) = ?', [strtolower($namaKaryawan)])->first();

        if (!$user) {
            $this->gagal++;
            $this->barisGagal[] = "Baris {$baris}: Nama karyawan \"{$namaKaryawan}\" tidak ditemukan";
            return null;
        }

        // Kolom B: Nama Shift (bukan ID)
        $namaShift = trim($row[1] ?? '');
        $schedule = Schedule::whereRaw('LOWER(name) = ?', [strtolower($namaShift)])->first();

        if (!$schedule) {
            $this->gagal++;
            $this->barisGagal[] = "Baris {$baris}: Nama shift \"{$namaShift}\" tidak ditemukan";
            return null;
        }

        // Kolom C: Tanggal
        $tanggalExcel = $row[2] ?? null;

        if (empty($tanggalExcel)) {
            $this->gagal++;
            $this->barisGagal[] = "Baris {$baris}: Kolom tanggal kosong";
            return null;
        }
        
        // Pengecekan cerdas untuk menghindari error floor()
        if (is_numeric($tanggalExcel)) {
            // Jika terbaca sebagai angka serial Excel (misal: 46090)
            $tanggalValid = Date::excelToDateTimeObject($tanggalExcel)->format('Y-m-d');
        } else {
            // Jika terbaca sebagai teks biasa (misal: "2026-04-04")
            try {
                $tanggalValid = Carbon::parse($tanggalExcel)->format('Y-m-d');
            } catch (\Exception $e) {
                $this->gagal++;
                $this->barisGagal[] = "Baris {$baris}: Format tanggal \"{$tanggalExcel}\" tidak valid";
                return null;
            }
        }

        $this->berhasil++;

        return new UserSchedule([
            'user_id'       => $user->id, 
            'schedule_id'   => $schedule->id, 
            'schedule_date' => $tanggalValid, 
        ]);
    }

    // Mulai membaca dari baris ke-2
    public function startRow(): int
    {
        return 2;
    }
}