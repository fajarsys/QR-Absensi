<?php

namespace App\Services;

use App\Models\Overtime;
use App\Models\UserSchedule;
use App\Models\Attendance;
use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Exception;
use Spatie\RouteDiscovery\Attributes\Where;

class OvertimeService
{

    public function submitPengajuanLembur(array $validatedData)
    {
        // cek jadwal hari ini dan absen masuk
        //jika jadwal tidak ditemukan -> tidak ada jadwal reguler, jika lembur harus ada jadwal reguler 

        $userId = Auth::id();
        $tanggalLembur = $validatedData['tanggal'];
        $hariIni = Carbon::today();

        $jadwal = UserSchedule::with('schedule')
            ->where('user_id', $userId)
            ->where('schedule_date', $tanggalLembur)
            ->first();

        if (!$jadwal) {
            throw new Exception("Anda tidak memiliki jadwal kerja pada tanggal tersebut.");
        }

        $absenMasuk = Attendance::where('user_id', $userId)
            ->whereDate('date', $tanggalLembur)
            ->whereNotNull('clock_in')
            ->first();

        if (!$absenMasuk) {
            throw new Exception("Anda tidak bisa mengajukan lembur karena belum absen masuk pada tanggal tersebut.");
        }

        $jamPulangNormal = $jadwal->schedule->end_time;
        $waktuPulang = Carbon::parse($tanggalLembur . ' ' . $jamPulangNormal);

        $waktuMulaiLembur = Carbon::parse($tanggalLembur . ' ' . $validatedData['jam_mulai']);
        $waktuSelesaiLembur = Carbon::parse($tanggalLembur . ' ' . $validatedData['jam_selesai']);

        if ($waktuMulaiLembur->lessThan($waktuPulang)) {
            // Memberikan pesan error yang dinamis sesuai jam pulang asli karyawan
            throw new Exception("Pengajuan gagal. Jam mulai lembur tidak boleh sebelum jam pulang normal Anda (" . $waktuPulang->format('H:i') . ").");
        }

        $durasiMenit = $waktuMulaiLembur->diffInMinutes($waktuSelesaiLembur);

        $overtime = Overtime::create([
            'user_id'      => $userId,
            'tanggal'      => $tanggalLembur,
            'tipe_lembur'  => 'pengajuan',
            'status'       => 'pending',
            'jam_mulai'    => $validatedData['jam_mulai'],
            'jam_selesai'  => $validatedData['jam_selesai'],
            'durasi_menit' => $durasiMenit,
            'keterangan'   => $validatedData['keterangan'],
        ]);

        event(new \App\Events\LemburDiajukan($overtime));

        return $overtime;
    }

    public function getAllOvertimesForAdmin()
    {
        return Overtime::with('user')->latest()->get();
    }

    public function updateOvertimeStatus(int $id, string $status)
    {
        $overtime = Overtime::findOrFail($id);
        $overtime->update(['status' => $status]);
        return $overtime;
    }

    public function deleteOvertime(int $id)
    {
        $overtime = Overtime::findOrFail($id);
        return $overtime->delete();
    }
}
