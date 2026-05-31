<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\UserSchedule;
use App\Models\Attendance;

class DashboardService
{
    /**
     * Create a new class instance.
     */
    public function getTodayDashboardData(int $userId): array
    {
        $tanggalHariIni = date('Y-m-d');

        // 1. Ambil data absen hari ini (Bisa berisi Hadir, Terlambat, Sakit, atau Izin)
        $todayAttendance = Attendance::where('user_id', $userId)
            ->whereDate('date', $tanggalHariIni)
            ->first();

        $lateMinutes = 0;

        // 2. Hitung keterlambatan HANYA jika statusnya 'Terlambat'
        if ($todayAttendance && $todayAttendance->status == 'Terlambat') {

            // Pencarian jadwal diubah menggunakan kolom 'date'
            $jadwal = UserSchedule::with('schedule')
                ->where('user_id', $userId())
                ->where('schedule_date', $tanggalHariIni)
                ->first();

            if ($jadwal) {
                $jamMasuk = Carbon::parse($jadwal->schedule->start_time);
                $jamAbsen = Carbon::parse($todayAttendance->clock_in);

                $lateMinutes = $jamMasuk->diffInMinutes($jamAbsen);
            }
        }

        return [
            'todayAttendance' => $todayAttendance,
            'lateMinutes' => $lateMinutes
        ];
    }
}
