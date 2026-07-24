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
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

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
            $attachmentPath = null;
            
            if ($file) {
                $mimeType = $file->getMimeType();
                
                // Jika file berupa gambar, gunakan Intervention Image (WebP)
                if (str_starts_with($mimeType, 'image/')) {
                    \Illuminate\Support\Facades\Storage::disk('public')->makeDirectory('dokumen_izin');
                    
                    $filename = 'dokumen_izin/' . uniqid('izin_') . '.webp';
                    $fullPath = storage_path('app/public/' . $filename);
                    
                    try {
                        // Inisialisasi Intervention Image
                        $manager = new ImageManager(new Driver());
                        $image = $manager->decode($file->getRealPath());
                        
                        // Perkecil ukuran proporsional jika lebarnya lebih dari 1000px
                        $image->scaleDown(width: 1000);
                        
                        // Simpan sebagai WebP dengan kualitas 60% (Sangat efisien)
                        $image->save($fullPath, quality: 60);
                        $attachmentPath = $filename;
                    } catch (\Throwable $e) {
                        // Fallback jika terjadi error
                        $attachmentPath = $file->store('dokumen_izin', 'public');
                    }
                } else {
                    // Jika bukan gambar (misal PDF), simpan seperti biasa
                    $attachmentPath = $file->store('dokumen_izin', 'public');
                }
            }

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
