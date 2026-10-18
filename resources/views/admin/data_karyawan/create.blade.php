@extends('layouts.admin')

@section('content')
<div class="card border-0 shadow-sm" style="border-radius: 15px;">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center" style="border-radius: 15px 15px 0 0;">
        <h6 class="fw-bold text-primary mb-0"><i class="bi bi-person-plus"></i> Tambah Karyawan Baru</h6>
        <a href="{{ route('admin.users.adminIndex') }}" class="btn btn-sm btn-secondary fw-bold" style="border-radius: 8px;">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card-body p-4">
        @if ($errors->any())
        <div class="alert alert-danger mb-4 alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <form action="{{ route('admin.users.adminStore') }}" method="POST">
            @csrf

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold text-secondary">Nama Lengkap</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="Contoh: Budi Santoso" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold text-secondary">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="Contoh: budi@mercusuar.ac.id" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold text-secondary">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold text-secondary">Jabatan</label>
                    <input type="text" name="jabatan" class="form-control" value="{{ old('jabatan') }}" placeholder="Contoh: Staff IT, Supervisor, dll" required>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold text-secondary">Role Akses</label>
                <select name="role" class="form-select" required>
                    <option value="karyawan" {{ old('role') == 'karyawan' ? 'selected' : '' }}>Karyawan (Akses Absensi)</option>
                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin (Akses Kelola Sistem)</option>
                </select>
            </div>

            <div class="d-grid mt-2">
                <button type="submit" class="btn btn-primary py-2 fw-bold" style="border-radius: 8px;">
                    <i class="bi bi-save me-1"></i> Simpan Data Karyawan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
