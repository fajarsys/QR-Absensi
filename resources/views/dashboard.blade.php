@extends('layouts.user')

@section('content')
<div class="container mt-3 mb-5">

    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 rounded-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 rounded-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Kartu Status Kehadiran Hari Ini -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4 text-center">
            <h6 class="text-secondary fw-bold mb-3">
                <i class="bi bi-calendar-check me-1"></i> Status Kehadiran Hari Ini
            </h6>

            @if($todayAttendance)

                @if(in_array($todayAttendance->status, ['Sakit', 'Izin', 'Cuti']))
                    <div class="d-inline-block bg-warning bg-opacity-10 rounded-pill px-4 py-2 mb-3">
                        <span class="fw-bold text-warning">
                            <i class="bi bi-info-circle-fill me-1"></i>
                            {{ strtoupper($todayAttendance->status) }}
                        </span>
                    </div>
                    <p class="text-muted small mb-0">
                        Catatan: {{ $todayAttendance->notes ?? 'Disetujui oleh Admin' }}
                    </p>

                @else
                    <div class="d-inline-block bg-{{ $todayAttendance->status == 'Terlambat' ? 'danger' : 'success' }} bg-opacity-10 rounded-pill px-4 py-2 mb-3">
                        <span class="fw-bold text-{{ $todayAttendance->status == 'Terlambat' ? 'danger' : 'success' }}">
                            <i class="bi bi-{{ $todayAttendance->status == 'Terlambat' ? 'exclamation-triangle-fill' : 'check-circle-fill' }} me-1"></i>
                            {{ $todayAttendance->status }}
                        </span>
                    </div>

                    <div class="mb-2">
                        <small class="text-muted d-block mb-1">Clock-in Time</small>
                        <h2 class="fw-bold text-dark mb-0" style="font-size: 2.5rem; letter-spacing: 2px;">
                            {{ \Carbon\Carbon::parse($todayAttendance->clock_in)->format('H:i') }}
                        </h2>
                    </div>

                    @if($lateMinutes > 0)
                    <div class="mt-2">
                        <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 rounded-pill">
                            <i class="bi bi-exclamation-triangle me-1"></i> Terlambat {{ $lateMinutes }} menit
                        </span>
                    </div>
                    @endif
                @endif

            @else
                <div class="d-inline-block bg-secondary bg-opacity-10 rounded-pill px-4 py-2 mb-2">
                    <span class="fw-bold text-secondary">
                        <i class="bi bi-dash-circle me-1"></i> Belum Absen
                    </span>
                </div>
                <p class="text-muted small mb-0">Silakan lakukan scan QR untuk absensi.</p>
            @endif
        </div>
    </div>

    <!-- Tombol Buat Pengajuan -->
    <div class="dropdown mb-4">
        <button class="btn btn-primary shadow-sm w-100 py-3 fw-bold rounded-4" type="button" id="dropdownPengajuan" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-plus-circle me-2"></i> Buat Pengajuan
        </button>
        <ul class="dropdown-menu shadow border-0 rounded-4 w-100" aria-labelledby="dropdownPengajuan">
            <li>
                <a class="dropdown-item py-3" href="{{ route('izin.create') }}">
                    <i class="bi bi-envelope-paper text-primary me-2 fs-5"></i> Pengajuan Izin/Sakit
                </a>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
                <a class="dropdown-item py-3" href="{{ route('lembur.create') }}">
                    <i class="bi bi-clock-history text-warning me-2 fs-5"></i> Pengajuan Lembur
                </a>
            </li>
        </ul>
    </div>

    <!-- Grid Menu Navigasi -->
    <div class="row g-3 mb-4">
        <div class="col-6">
            <a href="{{ route('profile.index') }}" class="card border-0 shadow-sm text-center py-4 text-decoration-none h-100 rounded-4">
                <div class="card-body p-3">
                    <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 56px; height: 56px;">
                        <i class="bi bi-person-badge text-success fs-3"></i>
                    </div>
                    <h6 class="mb-0 text-dark fw-bold">Profil Saya</h6>
                </div>
            </a>
        </div>
        <div class="col-6">
            <a href="{{ route('schedule.index') }}" class="card border-0 shadow-sm text-center py-4 text-decoration-none h-100 rounded-4">
                <div class="card-body p-3">
                    <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 56px; height: 56px;">
                        <i class="bi bi-calendar-week text-primary fs-3"></i>
                    </div>
                    <h6 class="mb-0 text-dark fw-bold">Lihat Jadwal</h6>
                </div>
            </a>
        </div>
    </div>

</div>
@endsection