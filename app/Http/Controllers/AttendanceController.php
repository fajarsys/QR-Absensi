<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AttendanceService;
use App\Http\Requests\StoreAttendanceRequest;
use App\Http\Requests\FilterAttendanceRequest;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function scan()
    {
        return view('user.scan');
    }

    public function adminIndex(FilterAttendanceRequest $request, AttendanceService $service)
    {
        $validated = $request->validated();
        $bulan = $validated['month'] ?? date('m');
        $tahun = $validated['year'] ?? date('Y');

        // 2. Lempar variabel yang sudah pasti valid ke Service
        $attendances = $service->getMonthlyAttendances($bulan, $tahun);

        return view('admin.kehadiran.index', compact('attendances', 'bulan', 'tahun'));
    }

    /**
     * Prints the user details.
     *
     * @param int $month
     * @param int $year
     */
    public function printUser(User $user, $month, $year, AttendanceService $service)
    {
        $attendances = $service->getUserAttendances($user->id, (int)$month, (int)$year);
        $overtimes = \App\Models\Overtime::where('user_id', $user->id)
                        ->whereMonth('tanggal', $month)
                        ->whereYear('tanggal', $year)
                        ->where('status', 'approved')
                        ->get()
                        ->keyBy('tanggal');

        return view('admin.kehadiran.print', compact('user', 'attendances', 'overtimes', 'month', 'year'));
    }


    /**
     * Prints the user details.
     *
     * @param int $month
     * @param int $year
     */
    public function printAll($month, $year, AttendanceService $service)
    {
        $attendances = $service->getMonthlyAttendances((int) $month, (int) $year);
        $overtimes = \App\Models\Overtime::whereMonth('tanggal', $month)
                        ->whereYear('tanggal', $year)
                        ->where('status', 'approved')
                        ->get();

        // Mengelompokkan data berdasarkan user
        $rekap = $attendances->groupBy('user_id')->map(function ($absenUser, $userId) use ($overtimes) {
            $nama = $absenUser->first()->user->name;
            
            // Total Kehadiran Fisik (Status = Hadir)
            $totalHadir = $absenUser->where('status', 'Hadir')->count();
            
            // Hitung Telat
            $totalTelat = $absenUser->filter(function($item) {
                return str_contains($item->notes, 'Terlambat');
            })->count();

            // Hitung Tepat Waktu
            $tepatWaktu = $absenUser->filter(function($item) {
                return str_contains($item->notes, 'Tepat Waktu') || str_contains($item->notes, 'Lembur Masuk');
            })->count();

            // Izin / Sakit / Cuti
            $sakit = $absenUser->where('status', 'Sakit')->count();
            $izin = $absenUser->where('status', 'Izin')->count();
            $cuti = $absenUser->where('status', 'Cuti')->count();

            // Total Lembur (menit)
            $totalLemburMenit = $overtimes->where('user_id', $userId)->sum('durasi_menit');

            return (object) [
                'nama' => $nama,
                'total_hadir' => $totalHadir,
                'tepat_waktu' => $tepatWaktu,
                'telat' => $totalTelat,
                'sakit' => $sakit,
                'izin' => $izin,
                'cuti' => $cuti,
                'total_lembur' => $totalLemburMenit
            ];
        })->values();

        return view('admin.kehadiran.print_all', compact('rekap', 'month', 'year'));
    }

    public function store(StoreAttendanceRequest $request, AttendanceService $service)
    {
        // 1. Eksekusi logika di Service
        $hasil = $service->processScan($request->validated(), Auth::id());

        // 2. Baca instruksi kemana harus redirect
        $redirect = $hasil['redirect'] === 'back'
            ? redirect()->back()
            : redirect()->route($hasil['redirect']);

        // 3. Kembalikan response dengan session alert
        return $redirect->with($hasil['status'], $hasil['message']);
    }
}
