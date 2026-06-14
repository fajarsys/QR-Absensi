<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OfficeController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\OvertimeController;
use App\Http\Controllers\AiController;

// =========================================================================
// 1. ZONA GUEST (BELUM LOGIN)
// =========================================================================
Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
    Route::get('/login', [AuthController::class, 'showLoginForm']);
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});


// =========================================================================
// 2. ZONA MEMBER / KARYAWAN (SUDAH LOGIN & DIKUNCI PERANGKATNYA)
// =========================================================================
Route::middleware(['auth', 'kunci.perangkat'])->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profil
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profil/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profil/update', [ProfileController::class, 'update'])->name('profile.update');

    // Jalur Izin 
    Route::get('/pengajuan/izin', [LeaveController::class, 'createIzin'])->name('izin.create');
    Route::post('/pengajuan/izin', [LeaveController::class, 'store'])->name('izin.store');

    // Jalur Lembur
    Route::get('/pengajuan/lembur', [OvertimeController::class, 'createLembur'])->name('lembur.create');
    Route::post('/lembur', [OvertimeController::class, 'store'])->name('lembur.store');

    // Scan Absen
    Route::get('/scan', [AttendanceController::class, 'scan'])->name('scan');
    Route::post('/scan', [AttendanceController::class, 'store'])->name('scan.store');

    // Jadwal Karyawan
    Route::get('/jadwal', [ScheduleController::class, 'index'])->name('schedule.index');
});


// =========================================================================
// 3. ZONA KHUSUS ADMIN - VIP (HANYA BUTUH AUTH, TERPISAH DARI KUNCI PERANGKAT)
// =========================================================================
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    // URL: /admin/dashboard | Name: admin.dashboard
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    // URL: /admin/office | Name: admin.office
    Route::get('/office', [OfficeController::class, 'index'])->name('office');
    Route::get('/office/qr-render', [OfficeController::class, 'renderQrCode'])->name('office.qr-render');
    Route::get('/office/qr-display', [OfficeController::class, 'qrDisplay'])->name('office.qr-display');
    Route::put('/office/{id}', [OfficeController::class, 'update'])->name('office.update');

    // Hari Libur
    Route::get('/holidays', [\App\Http\Controllers\HolidayController::class, 'index'])->name('holidays.index');
    Route::post('/holidays', [\App\Http\Controllers\HolidayController::class, 'store'])->name('holidays.store');
    Route::delete('/holidays/{id}', [\App\Http\Controllers\HolidayController::class, 'destroy'])->name('holidays.destroy');

    // Jalur Jadwal
    Route::get('/jadwal', [ScheduleController::class, 'adminIndex'])->name('schedule');
    Route::post('/jadwal/import', [ScheduleController::class, 'importExcel'])->name('schedule.import');
    Route::post('/jadwal/manual', [ScheduleController::class, 'storeManual'])->name('schedule.storeManual');
    Route::get('/jadwal/{id}/edit', [ScheduleController::class, 'edit'])->name('schedule.edit');
    Route::delete('/jadwal/{id}', [ScheduleController::class, 'destroy'])->name('schedule.destroy');
    Route::delete('/schedule/hapus-bulanan', [ScheduleController::class, 'destroyMonth'])->name('schedule.destroyMonth');
    Route::put('/jadwal/{id}', [ScheduleController::class, 'adminUpdate'])->name('schedule.adminUpdate');

    // Kelola Izin 
    Route::get('/izin', [LeaveController::class, 'adminIndex'])->name('izin.adminIndex');
    Route::post('/izin/{id}/status', [LeaveController::class, 'approve'])->name('izin.approve');
    Route::delete('/izin/{id}', [LeaveController::class, 'destroy'])->name('izin.destroy');

    // Kelola Lembur (Di-manage di halaman yang sama dengan Izin)
    Route::post('/lembur/{id}/status', [OvertimeController::class, 'approve'])->name('lembur.approve');
    Route::delete('/lembur/{id}', [OvertimeController::class, 'destroy'])->name('lembur.destroy');


    // Jalur Kehadiran Karyawan 
    Route::get('/kehadiran', [AttendanceController::class, 'adminIndex'])->name('attendance.adminIndex');
    Route::get('/kehadiran/cetak/{user}/{month}/{year}', [AttendanceController::class, 'printUser'])->name('attendance.printUser');
    Route::get('/kehadiran/cetak-semua/{month}/{year}', [AttendanceController::class, 'printAll'])->name('attendance.printAll');

    // Jalur Data Karyawan
    Route::get('/karyawan', [UserController::class, 'adminIndex'])->name('users.adminIndex');
    Route::get('/karyawan/create', [UserController::class, 'adminCreate'])->name('users.adminCreate');
    Route::post('/karyawan', [UserController::class, 'adminStore'])->name('users.adminStore');
    Route::get('/karyawan/{id}/edit', [UserController::class, 'adminEdit'])->name('users.adminEdit');
    Route::put('/karyawan/{id}', [UserController::class, 'adminUpdate'])->name('users.adminUpdate');
    Route::delete('/karyawan/{id}', [UserController::class, 'adminDestroy'])->name('users.adminDestroy');
    Route::post('/karyawan/{id}/reset-device', [UserController::class, 'resetDevice'])->name('users.resetDevice');
});
