<?php

namespace App\Services;

use App\Models\Leave;
use App\Models\Attendance;
use App\Events\IzinDiajukan;
use App\Events\ActivityLogged;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\UploadedFile;

class LeaveService
{
    public function getAllLeavesForAdmin()
    {
        return Leave::with('user')->latest()->get();
    }

    /**
     * Prints the user details.
     * @param int|string $userId The ID of the user.
     */
    public function getLeavesByUser($userId)
    {
        return Leave::where('user_id', $userId)->latest()->get();
    }

    public function createLeave(array $data, int $userId, ?UploadedFile $file)
    {
        return DB::transaction(function () use ($data, $userId, $file) {
            $attachmentPath = $file ? $file->store('dokumen_izin', 'public') : null;

            $izin = Leave::create([
                'user_id' => $userId,
                'type' => $data['type'],
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'reason' => $data['reason'],
                'attachment' => $attachmentPath,
                'status' => 'pending'
            ]);

            event(new IzinDiajukan($izin));

            broadcast(new ActivityLogged([
                'id' => 'leave_' . $izin->id,
                'type' => 'leave',
                'user_name' => \App\Models\User::find($userId)->name,
                'description' => 'Mengajukan Izin (' . $izin->reason . ')',
                'status' => 'pending',
                'time' => now()->format('H:i')
            ]));

            return $izin;
        });
    }

    public function updateLeaveStatus(int $leaveId, string $status)
    {
        // Menggunakan DB Transaction agar tabel leaves dan attendances sinkron. 
        // Jika looping absensi gagal, status izin tidak akan terubah (rollback otomatis).
        return DB::transaction(function () use ($leaveId, $status) {
            $leave = Leave::findOrFail($leaveId);
            $leave->update(['status' => $status]);

            if ($status === 'approved') {
                $period = CarbonPeriod::create($leave->start_date, $leave->end_date);

                foreach ($period as $date) {
                    Attendance::updateOrCreate(
                        [
                            'user_id' => $leave->user_id,
                            'date' => $date->format('Y-m-d')
                        ],
                        [
                            'status' => $leave->type,
                            'clock_in' => '00:00:00',
                            'clock_out' => '00:00:00',
                            'notes' => 'Disetujui otomatis: ' . $leave->reason
                        ]
                    );
                }
            }

            return $leave;
        });
    }

    public function deleteLeave(int $leaveId)
    {
        $leave = Leave::findOrFail($leaveId);

        // Hapus file dokumen jika ada di storage
        if ($leave->attachment) {
            Storage::disk('public')->delete($leave->attachment);
        }

        return $leave->delete();
    }
}
