<?php

namespace App\Imports;

use App\Models\UserSchedule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow; 
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Carbon\Carbon;

class UserScheduleImport implements ToModel, WithStartRow
{
    public function model(array $row)
    {
        // Pastikan baris tidak kosong
        if (!isset($row[0])) {
            return null;
        }

        $tanggalExcel = $row[2];
        
        // Pengecekan cerdas untuk menghindari error floor()
        if (is_numeric($tanggalExcel)) {
            // Jika terbaca sebagai angka serial Excel (misal: 46090)
            $tanggalValid = Date::excelToDateTimeObject($tanggalExcel)->format('Y-m-d');
        } else {
            // Jika terbaca sebagai teks biasa (misal: "2026-04-04")
            $tanggalValid = Carbon::parse($tanggalExcel)->format('Y-m-d');
        }

        return new UserSchedule([
            'user_id'       => $row[0], 
            'schedule_id'   => $row[1], 
            'schedule_date' => $tanggalValid, 
        ]);
    }

    // Mulai membaca dari baris ke-2
    public function startRow(): int
    {
        return 2;
    }
}