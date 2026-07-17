# Absensi System - Project Documentation

## Overview
Sistem presensi karyawan modern berbasis web, dirancang untuk efisiensi, akurasi, dan keamanan data perusahaan. Dilengkapi fitur validasi perangkat untuk mencegah kecurangan absensi.

## Key Features
- **Device Locking**: Absensi terkunci pada perangkat karyawan yang terdaftar.
- **QR Scan Attendance**: Proses presensi cepat melalui scan QR code.
- **Leave & Overtime Request**: Sistem pengajuan izin dan lembur yang terintegrasi.
- **Admin Dashboard**: Manajemen karyawan, jadwal, dan pelaporan absensi yang komprehensif.

## Tech Stack
- Framework: Laravel
- Frontend: Tailwind CSS
- Database: MySQL
- Authentication: Middleware-based device locking

## Workflow Summary
1. **Auth**: Login karyawan dengan validasi perangkat.
2. **Attendance**: Scan QR code di kantor.
3. **Admin**: Verifikasi pengajuan izin/lembur.
4. **Report**: Export data absensi bulanan.

## License
MIT
