@extends('layouts.admin')

@section('content')
<div class="card border-0 shadow-sm" style="border-radius: 15px;">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center" style="border-radius: 15px 15px 0 0;">
        <h6 class="fw-bold text-primary mb-0"><i class="bi bi-pencil-square"></i> Edit Jadwal</h6>
        <a href="{{ route('admin.schedule') }}" class="btn btn-sm btn-secondary fw-bold" style="border-radius: 8px;">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card-body p-4">
        @if ($errors->any())
            <div class="alert alert-danger mb-4 alert-dismissible fade show" style="border-radius: 10px;" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form action="{{ route('admin.schedule.adminUpdate', $userSchedule->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="mb-4">
                <label class="form-label fw-bold text-secondary">Nama Dosen</label>
                <input type="text" class="form-control bg-light" value="{{ $userSchedule->user->name }}" readonly>
                <div class="form-text text-muted">*Nama Dosen tidak dapat diubah pada mode edit. Hapus jadwal jika salah orang.</div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold text-secondary">Pilih Shift Baru</label>
                <select name="schedule_id" class="form-select" required>
                    <option value="">-- Pilih Shift --</option>
                    @foreach($schedules as $shift)
                        <option value="{{ $shift->id }}" {{ $userSchedule->schedule_id == $shift->id ? 'selected' : '' }}>
                            {{ $shift->name ?? 'Shift ID: '.$shift->id }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold text-secondary">Ubah Tanggal</label>
                <input type="date" name="schedule_date" class="form-control" value="{{ \Carbon\Carbon::parse($userSchedule->schedule_date)->format('Y-m-d') }}" required>
            </div>

            <div class="d-grid mt-2">
                <button type="submit" class="btn btn-primary py-2 fw-bold" style="border-radius: 8px;">
                    <i class="bi bi-save me-1"></i> Perbarui Jadwal
                </button>
            </div>
        </form>
    </div>
</div>
@endsection