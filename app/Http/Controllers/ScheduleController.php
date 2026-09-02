<?php

namespace App\Http\Controllers;

use App\Services\ScheduleService;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreManualScheduleRequest;
use App\Http\Requests\UpdateScheduleRequest;
use App\Http\Requests\DestroyMonthScheduleRequest;
use App\Http\Requests\ImportScheduleRequest;

class ScheduleController extends Controller
{
    public function index(ScheduleService $service)
    {
        $userSchedules = $service->getUserSchedules(Auth::id());
        return view('user.schedule', compact('userSchedules'));
    }

    public function adminIndex(ScheduleService $service)
    {
        $data = $service->userAllSchedule();
        return view('admin.jadwal.index', $data); // Mengirim array data langsung
    }

    public function storeManual(StoreManualScheduleRequest $request, ScheduleService $service)
    {
        $service->storeManual($request->validated());
        return redirect()->back()->with('success', 'Jadwal manual berhasil ditambahkan!');
    }

    public function edit($id, ScheduleService $service)
    {
        $data = $service->getEditData($id);
        return view('admin.jadwal.edit', $data);
    }

    public function adminUpdate(UpdateScheduleRequest $request, $id, ScheduleService $service)
    {
        $service->updateSchedule($id, $request->validated());
        return redirect()->route('admin.schedule')
            ->with('success', 'Jadwal karyawan berhasil diperbarui.');
    }

    public function destroy($id, ScheduleService $service)
    {
        $service->destroySchedule($id);
        return redirect()->back()->with('success', 'Jadwal karyawan berhasil dihapus.');
    }

    public function destroyMonth(DestroyMonthScheduleRequest $request, ScheduleService $service)
    {
        $service->destroyMonth($request->validated());
        return redirect()->back()->with('success', 'Seluruh jadwal semua karyawan pada bulan tersebut berhasil dihapus.');
    }

    public function importExcel(ImportScheduleRequest $request)
    {
        try {
            $import = new \App\Imports\UserScheduleImport;
            \Maatwebsite\Excel\Facades\Excel::import($import, $request->file('file_excel'));

            // Tidak ada baris yang berhasil sama sekali
            if ($import->berhasil === 0 && $import->gagal === 0) {
                return redirect()->back()->with('error', 'File kosong atau format kolom tidak sesuai. Pastikan kolom: A (Nama Karyawan), B (Nama Shift), C (Tanggal).');
            }

            if ($import->berhasil === 0 && $import->gagal > 0) {
                $detail = implode(' | ', array_slice($import->barisGagal, 0, 5));
                return redirect()->back()->with('error', "Tidak ada data yang berhasil di-import. {$import->gagal} baris gagal: {$detail}");
            }

            $pesan = "{$import->berhasil} jadwal berhasil di-import!";
            if ($import->gagal > 0) {
                $detail = implode(' | ', array_slice($import->barisGagal, 0, 5));
                $pesan .= " ({$import->gagal} baris dilewati: {$detail})";
            }

            return redirect()->back()->with('success', $pesan);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal meng-import data. Pastikan format file sudah benar. Error: ' . $e->getMessage());
        }
    }
}