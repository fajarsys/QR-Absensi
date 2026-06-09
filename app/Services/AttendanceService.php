<?php


namespace App\Services;

use Carbon\Carbon;
use App\Models\Office;
use App\Models\Attendance;
use App\Models\UserSchedule;

class AttendanceService
{
    /**
     * Mengambil data absensi bulanan beserta data user yang diurutkan
     */
    /**
     * Prints the user details.
     *
     * @param int $bulan
     * @param int $tahun
    */
    public function getMonthlyAttendances($bulan, $tahun)
    {
        return Attendance::with('user')
            ->join('users', 'attendances.user_id', '=', 'users.id')
            ->whereMonth('attendances.date', $bulan)
            ->whereYear('attendances.date', $tahun)
            ->orderBy('users.name', 'asc')
            ->orderBy('attendances.date', 'asc')
            ->select('attendances.*')
            ->get();
    }

    public function getUserAttendances(int $userId, int $month, int $year)
    {
        return Attendance::where('user_id', $userId)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->orderBy('date', 'asc')
            ->get();
    }

    /**
     * Memproses data scan QR karyawan
     * Mengembalikan array berisi: status, pesan, dan tujuan redirect
     */
    public function processScan(array $data, int $userId)
    {
        $waktuScan = Carbon::now();
        $hariIni = Carbon::today();
        
        // Pengecekan Validitas QR Code
        $validQrCode = env('QR_CODE_SECRET', 'ABSENSI-KANTOR-123');
        if (!isset($data['qr_code']) || $data['qr_code'] !== $validQrCode) {
            return [
                'status' => 'error',
                'message' => 'Gagal absen! QR Code yang Anda scan tidak valid atau palsu.',
                'redirect' => 'back'
            ];
        }

        // Pengecekan Lokasi
        $office = Office::first();

        if (!$office) {
            return [
                'status' => 'error',
                'message' => 'Gagal absen! Titik koordinat kantor belum diatur oleh Admin.',
                'redirect' => 'back' 
            ];
        }
        $distance = $this->calculateDistance(
            $data['latitude'], 
            $data['longitude'], 
            $office->latitude, 
            $office->longitude
        );

        if ($distance > $office->radius) {
            return [
                'status' => 'error',
                'message' => 'Gagal absen! Anda berada di luar jangkauan. Jarak: ' . round($distance) . 'm.',
                'redirect' => 'back' // Instruksi untuk Controller
            ];
        }

        // Cek Absen Hari Ini
        $absenHariIni = Attendance::where('user_id', $userId)
                                  ->whereDate('date', $hariIni)
                                  ->first();

        // PINTU 3: Sudah Masuk & Pulang
        if ($absenHariIni && $absenHariIni->clock_out != null) {
            return [
                'status' => 'error',
                'message' => 'Akses Ditolak: Anda sudah menyelesaikan absensi hari ini.',
                'redirect' => 'dashboard'
            ];
        }

        // PINTU 2: Sudah Masuk, Tinggal Pulang
        if ($absenHariIni && $absenHariIni->clock_out == null) {
            $absenHariIni->update([
                'clock_out' => $waktuScan->format('H:i:s'),
                'latitude_out' => $data['latitude'],
                'longitude_out' => $data['longitude'],
            ]);

            return [
                'status' => 'success',
                'message' => 'Berhasil Absen Pulang! Jarak: ' . round($distance) . ' meter. Hati-hati di jalan!',
                'redirect' => 'dashboard'
            ];
        }

        // PINTU 1: Proses Absen Masuk
        $tanggalHariIni = $hariIni->format('Y-m-d');
        $jadwalHariIni = UserSchedule::with('schedule')
                        ->where('user_id', $userId)
                        ->where('schedule_date', $tanggalHariIni) 
                        ->first();

        if (!$jadwalHariIni) {
            return [
                'status' => 'error',
                'message' => 'Akses Ditolak: Anda tidak memiliki jadwal shift hari ini.',
                'redirect' => 'back'
            ];
        }

        // Logika Keterlambatan
        $jamMasuk = Carbon::parse($jadwalHariIni->schedule->start_time);
        $batasWaktu = $jamMasuk->copy()->addMinutes($jadwalHariIni->schedule->late_tolerance);
        
        $notes = 'Tepat Waktu';
        $pesanPemberitahuan = 'Berhasil Absen Masuk! Semangat bekerja!';

        if ($waktuScan->greaterThan($batasWaktu)) {
            $menitTelat = $jamMasuk->diffInMinutes($waktuScan);
            $notes = 'Terlambat ' . $menitTelat . ' menit';
            $pesanPemberitahuan = 'Berhasil Absen Masuk! Namun Anda terlambat ' . $menitTelat . ' menit.';
        } elseif ($waktuScan->lessThan($jamMasuk->copy()->subMinutes(60))) {
            $menitLembur = $waktuScan->diffInMinutes($jamMasuk);
            $notes = 'Lembur Masuk: ' . $menitLembur . ' menit';
            $pesanPemberitahuan = 'Berhasil Absen Masuk! Anda datang ' . $menitLembur . ' menit lebih awal (Lembur).';
        }

        // Simpan Data
        Attendance::create([
            'user_id' => $userId,
            'date' => $tanggalHariIni,
            'clock_in' => $waktuScan->format('H:i:s'),
            'latitude_in' => $data['latitude'],
            'longitude_in' => $data['longitude'],
            'status' => 'Hadir',
            'notes' => $notes
        ]);

        return [
            'status' => 'success',
            'message' => $pesanPemberitahuan,
            'redirect' => 'dashboard'
        ];
    }


    /**
    * Menghitung jarak antara dua titik koordinat
    * @param float $lat1 Latitude titik awal
    * @param float $lon1 Longitude titik awal
    * @param float $lat2 Latitude titik tujuan
    * @param float $lon2 Longitude titik tujuan
    * @return float Jarak dalam satuan meter
    */

    // Fungsi Rumus Haversine (Standar Internasional)
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // Radius bumi dalam satuan meter

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        $distance = $earthRadius * $c; // Hasil dalam meter

        return $distance;
    }
}