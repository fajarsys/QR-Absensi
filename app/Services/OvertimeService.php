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
        $userId = Auth::id();
        $tanggalLembur = $validatedData['tanggal'];

        // Menghapus validasi wajib ada jadwal/absen karena lembur bisa dilakukan di Hari Libur / Tanggal Merah
        // Admin yang akan memfilter dan menyetujuinya di Dashboard.

        $waktuMulaiLembur = Carbon::parse($tanggalLembur . ' ' . $validatedData['jam_mulai']);
        $waktuSelesaiLembur = Carbon::parse($tanggalLembur . ' ' . $validatedData['jam_selesai']);

        if ($waktuMulaiLembur->greaterThanOrEqualTo($waktuSelesaiLembur)) {
            throw new Exception("Pengajuan gagal. Jam mulai lembur tidak boleh sama atau melebihi jam selesai.");
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
