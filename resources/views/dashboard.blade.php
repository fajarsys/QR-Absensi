@extends('layouts.user')

@section('content')
<div class="container mt-3 mb-5">

    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm" style="border-radius: 10px;" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show shadow-sm" style="border-radius: 10px;" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
    </div>
    <div class="card-body p-4">
        <h6 class="text-secondary fw-bold mb-3">Status Kehadiran Hari Ini:</h6>

        @if($todayAttendance)

        @if(in_array($todayAttendance->status, ['Sakit', 'Izin', 'Cuti']))

        <div class="alert alert-warning mb-0 border-0 shadow-sm" style="border-radius: 10px;">
            <i class="bi bi-info-circle-fill me-2"></i>
            Hari ini Anda berstatus: <strong class="text-uppercase">{{ $todayAttendance->status }}</strong>.
            <hr class="my-2">
            <small class="text-muted">
                Catatan: {{ $todayAttendance->notes ?? 'Disetujui oleh Admin' }}
            </small>
        </div>

        @else

        <span class="badge bg-{{ $todayAttendance->status == 'Terlambat' ? 'danger' : 'success' }} px-3 py-2 fs-6">
            {{ $todayAttendance->status }}
        </span>

        <div class="mt-3">
            <small class="text-muted d-block">
                <i class="bi bi-box-arrow-in-right"></i> Jam Masuk: <strong>{{ $todayAttendance->clock_in }}</strong>
            </small>
        </div>

        @if($lateMinutes > 0)
        <div class="text-danger mt-2 small fw-bold">
            <i class="bi bi-exclamation-triangle"></i> Terlambat {{ $lateMinutes }} menit.
        </div>
        @endif

        @endif

        @else
        <span class="badge bg-secondary px-3 py-2 fs-6">Belum Absen</span>
        @endif
    </div>

    <div class="row g-3 mb-4">
        <div class="dropdown">
            <button class="btn btn-primary shadow-sm dropdown-toggle" type="button" id="dropdownPengajuan" data-bs-toggle="dropdown" aria-expanded="false" style="border-radius: 8px;">
                <i class="bi bi-plus-circle me-1"></i> Buat Pengajuan
            </button>

            <ul class="dropdown-menu shadow border-0" aria-labelledby="dropdownPengajuan" style="border-radius: 10px;">
                <li>
                    <a class="dropdown-item py-2" href="{{ route('izin.create') }}">
                        <i class="bi bi-envelope-paper text-primary me-2"></i> Pengajuan Izin/Sakit
                    </a>
                </li>
                <li>
                    <a class="dropdown-item py-2" href="{{ route('lembur.create') }}">
                        <i class="bi bi-clock-history text-warning me-2"></i> Pengajuan Lembur
                    </a>
                </li>
            </ul>
        </div>
        <div class="col-6">
            <a href="{{ route('profile.index') }}" class="card border-0 shadow-sm text-center py-4 text-decoration-none h-100" style="border-radius: 15px;">
                <i class="bi bi-person-badge text-success mb-2" style="font-size: 2.2rem;"></i>
                <h6 class="mb-0 text-dark fw-bold">Profil Saya</h6>
            </a>
        </div>
        <div class="col-6">
            <a href="{{ route('schedule.index') }}" class="card border-0 shadow-sm text-center py-4 text-decoration-none h-100" style="border-radius: 15px;">
                <i class="bi bi-calendar-week me-2"></i>
                <h6 class="mb-0 text-dark fw-bold">Lihat Jadwal</h6>
            </a>
        </div>
    </div>


</div>
@endsection