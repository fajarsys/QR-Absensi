@extends('layouts.user') @section('content')
<div class="container mt-4">
    <div class="card shadow-sm border-0" style="border-radius: 15px;">
        <div class="card-header bg-primary text-white" style="border-radius: 15px 15px 0 0;">
            <h5 class="mb-0"><i class="bi bi-calendar-week"></i> Jadwal Shift Karyawan</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 text-center">
                    <thead class="table-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>Detail Shift</th>
                            <th>Waktu Kerja</th>
                        </tr>
                    </thead>
        <tbody>
        @forelse($userSchedules as $jadwalKu)
        <tr>
        <td class="fw-bold text-start ps-3 text-primary">
            {{ $jadwalKu->schedule_date }}
        </td>
        
        <td>
            <span class="d-block fw-bold">{{ $jadwalKu->schedule->name }}</span>
            <small class="text-muted" style="font-size: 0.75rem;">
                Toleransi: {{ $jadwalKu->schedule->late_tolerance }} mnt
            </small>
        </td>

        <td>
            <span class="badge bg-success mb-1 w-100">
                Masuk: {{ \Carbon\Carbon::parse($jadwalKu->schedule->start_time)->format('H:i') }}
            </span>
            <br>
            <span class="badge bg-danger w-100">
                Pulang: {{ \Carbon\Carbon::parse($jadwalKu->schedule->end_time)->format('H:i') }}
            </span>
        </td>
        </tr>
        @empty
        <tr>
        <td colspan="3" class="text-center text-muted py-4">
            <i class="bi bi-calendar-x fs-1 d-block mb-2 text-secondary"></i>
            Jadwal mingguan Anda belum diatur oleh Admin.
        </td>
        </tr>
        @endforelse
        </tbody>
                </table>
            </div>
        </div>
    </div>

        <div class="mt-4">
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary w-100" style="border-radius: 10px;">
                <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
            </a>
        </div>
</div>
@endsection