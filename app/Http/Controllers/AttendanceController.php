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
        // 1. Ambil data dari filter. Jika kosong (null), gunakan bulan/tahun saat ini
        $bulan = $request->validated('month') ?? date('m');
        $tahun = $request->validated('year') ?? date('Y');

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

        return view('admin.kehadiran.print', compact('user', 'attendances', 'month', 'year'));
    }


    /**
     * Prints the user details.
     *
     * @param int $month
     * @param int $year
     */
    public function printAll($month, $year, AttendanceService $service)
    {
        // Memanggil fungsi Service yang sama dengan yang dipakai adminIndex!
        $attendances = $service->getMonthlyAttendances((int) $month, (int) $year);

        return view('admin.kehadiran.print_all', compact('attendances', 'month', 'year'));
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
