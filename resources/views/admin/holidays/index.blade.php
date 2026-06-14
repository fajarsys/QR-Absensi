@extends('layouts.admin')

@section('content')
<div class="row">
    <div class="col-md-12 mb-4">
        <h3><i class="bi bi-calendar-x text-danger"></i> Daftar Hari Libur Nasional (Tanggal Merah)</h3>
        <p class="text-muted">Karyawan akan otomatis diliburkan pada tanggal yang terdaftar di bawah ini, meskipun mereka memiliki jadwal di Excel.</p>
    </div>

    <!-- Kolom Tambah Libur -->
    <div class="col-md-4">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 text-primary fw-bold"><i class="bi bi-plus-circle"></i> Tambah Libur Baru</h5>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.holidays.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Peringatan</label>
                        <input type="text" name="title" class="form-control" placeholder="Cth: Hari Kemerdekaan RI" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tanggal Merah</label>
                        <input type="date" name="holiday_date" class="form-control" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold">Deskripsi (Opsional)</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Keterangan tambahan..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Simpan Hari Libur</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Kolom Daftar Libur -->
    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Nama Peringatan</th>
                                <th>Deskripsi</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($holidays as $index => $holiday)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><span class="badge bg-danger fs-6">{{ \Carbon\Carbon::parse($holiday->holiday_date)->translatedFormat('d F Y') }}</span></td>
                                    <td class="fw-bold">{{ $holiday->title }}</td>
                                    <td>{{ $holiday->description ?? '-' }}</td>
                                    <td class="text-center">
                                        <form action="{{ route('admin.holidays.destroy', $holiday->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus hari libur ini?')">
                                                <i class="bi bi-trash"></i> Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">Belum ada data hari libur yang didaftarkan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
