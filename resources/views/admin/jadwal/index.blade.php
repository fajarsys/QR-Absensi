@extends('layouts.admin')

@section('content')

@if (session('success'))
<div class="alert alert-success alert-dismissible fade show mb-4 shadow-sm" style="border-radius: 10px;" role="alert">
    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if (session('error'))
<div class="alert alert-danger alert-dismissible fade show mb-4 shadow-sm" style="border-radius: 10px;" role="alert">
    <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if ($errors->any())
<div class="alert alert-danger alert-dismissible fade show mb-4 shadow-sm" style="border-radius: 10px;" role="alert">
    <i class="bi bi-exclamation-triangle-fill me-2"></i>
    <ul class="mb-0">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
    <div class="card-body p-4">

        <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
            <h5 class="fw-bold text-primary mb-0"><i class="bi bi-calendar-plus"></i> Kelola Jadwal Dosen</h5>

            <div class="d-flex gap-2">
                <button type="button" class="btn btn-sm btn-danger shadow-sm" data-bs-toggle="modal" data-bs-target="#modalHapusBulanan">
                    <i class="bi bi-calendar2-x"></i> Hapus Bulanan
                </button>

                <button type="button" class="btn btn-sm btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahManual">
                    <i class="bi bi-plus-circle"></i> Tambah Manual
                </button>
            </div>
        </div>

        <h6 class="fw-bold text-secondary mb-3"><i class="bi bi-file-earmark-excel"></i> Import via Excel</h6>
        <form action="{{ route('admin.schedule.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="input-group">
                <input type="file" class="form-control" name="file_excel" id="file_excel" accept=".xlsx, .xls, .csv" required>
                <button class="btn btn-success fw-bold" type="submit">
                    <i class="bi bi-cloud-upload"></i> Upload & Proses
                </button>
            </div>
            <div class="form-text text-muted mt-2">
                *Format kolom: A (Nama Dosen), B (Nama Shift), C (Tanggal format YYYY-MM-DD). Mulai data dari baris ke-2.
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modalTambahManual" tabindex="-1" aria-labelledby="modalTambahManualLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h6 class="modal-title fw-bold" id="modalTambahManualLabel"><i class="bi bi-calendar-plus"></i> Tambah Jadwal Manual</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('admin.schedule.storeManual') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary">Pilih Dosen</label>
                        <select name="user_id" class="form-select" required>
                            <option value="">-- Pilih Dosen --</option>
                            @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary">Pilih Kelas</label>
                        <select name="schedule_id" class="form-select" required>
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($schedules as $shift)
                            <option value="{{ $shift->id }}">{{ $shift->name ?? 'Shift ID: '.$shift->id }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary">Pilih Tanggal</label>
                        <input type="date" name="schedule_date" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Simpan Jadwal</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="modalHapusBulanan" tabindex="-1" aria-labelledby="modalHapusBulananLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white">
                <h6 class="modal-title fw-bold" id="modalHapusBulananLabel"><i class="bi bi-calendar2-x"></i> Hapus Semua Jadwal Bulanan</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('admin.schedule.destroyMonth') }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body p-4">
                    <div class="alert alert-warning text-dark mb-4" style="font-size: 14px;">
                        <i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>
                        Peringatan: Jadwal <strong>SEMUA KARYAWAN</strong> pada bulan dan tahun yang dipilih akan <strong>dihapus permanen</strong>.
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-secondary">Bulan</label>
                            <select name="month" class="form-select" required>
                                <option value="">-- Pilih Bulan --</option>
                                @foreach(range(1, 12) as $m)
                                <option value="{{ $m }}">{{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-secondary">Tahun</label>
                            <select name="year" class="form-select" required>
                                @for($y = date('Y'); $y >= 2024; $y--)
                                <option value="{{ $y }}">{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger"><i class="bi bi-trash-fill"></i> Hapus Semua Jadwal</button>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="mt-4">
    <h6 class="fw-bold mb-3 text-secondary"><i class="bi bi-card-checklist"></i> Klasifikasi Jadwal Dosen</h6>

    <div class="row">
        @forelse ($userSchedules->groupBy('user_id') as $userId => $jadwals)
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 10px;">

                <div class="card-header bg-primary text-white py-2">
                    <h6 class="mb-0 fw-bold">
                        <i class="bi bi-person-fill me-1"></i> {{ $jadwals->first()->user->name }}
                    </h6>
                </div>

                <ul class="list-group list-group-flush">
                    @foreach ($jadwals as $jadwal)
                    <li class="list-group-item py-2">

                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="fw-bold text-secondary d-block" style="font-size: 14px;">
                                    {{ \Carbon\Carbon::parse($jadwal->schedule_date)->translatedFormat('l, d M Y') }}
                                </span>
                                <span class="badge bg-light text-dark border mt-1">
                                    {{ $jadwal->schedule->name ?? 'Shift ' . $jadwal->schedule_id }}
                                </span>
                            </div>

                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.schedule.edit', $jadwal->id) }}" class="btn btn-sm btn-outline-warning" title="Edit Jadwal" style="padding: 0.1rem 0.4rem; font-size: 12px;">
                                    <i class="bi bi-pencil-fill"></i>
                                </a>

                                @php
                                $confirmMessage = "Hapus jadwal tanggal " . \Carbon\Carbon::parse($jadwal->schedule_date)->translatedFormat('d M Y') . "?";
                                @endphp

                                <form action="{{ route('admin.schedule.destroy', $jadwal->id) }}" method="POST" onsubmit="return confirm('{{ addslashes($confirmMessage) }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus Jadwal" style="padding: 0.1rem 0.4rem; font-size: 12px;">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                </form>
                            </div>
                        </div>

                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="alert alert-light text-muted text-center border-0 shadow-sm py-4">
                <i class="bi bi-calendar-x fs-1 d-block mb-2"></i>
                Belum ada data jadwal yang tersimpan.
            </div>
        </div>
        @endforelse
    </div>
</div>

@endsection