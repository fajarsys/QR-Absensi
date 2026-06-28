<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Leave;
use App\Models\Overtime;

class AdminDashboardService
{
    public function getRecentActivities()
    {
        // Ambil data hari ini (atau beberapa hari terakhir jika mau)
        $today = now()->toDateString();

        $attendances = Attendance::with('user')
            ->whereDate('date', $today)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => 'att_' . $item->id,
                    'type' => 'attendance',
                    'user_name' => $item->user->name,
                    'description' => 'Melakukan Absen ' . ($item->clock_out ? 'Pulang' : 'Masuk'),
                    'status' => $item->status,
                    'time' => $item->updated_at->format('H:i'),
                    'timestamp' => $item->updated_at->timestamp,
                ];
            });

        $leaves = Leave::with('user')
            ->whereDate('created_at', $today)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => 'leave_' . $item->id,
                    'type' => 'leave',
                    'user_name' => $item->user->name,
                    'description' => 'Mengajukan Izin (' . $item->reason . ')',
                    'status' => $item->status,
                    'time' => $item->created_at->format('H:i'),
                    'timestamp' => $item->created_at->timestamp,
                ];
            });

        $overtimes = Overtime::with('user')
            ->whereDate('created_at', $today)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => 'overtime_' . $item->id,
                    'type' => 'overtime',
                    'user_name' => $item->user->name,
                    'description' => 'Mengajukan Lembur (' . $item->reason . ')',
                    'status' => $item->status,
                    'time' => $item->created_at->format('H:i'),
                    'timestamp' => $item->created_at->timestamp,
                ];
            });

        // Gabungkan semua collection, lalu urutkan berdasarkan timestamp menurun (terbaru di atas)
        return collect()
            ->concat($attendances)
            ->concat($leaves)
            ->concat($overtimes)
            ->sortByDesc('timestamp')
            ->values();
    }
}
