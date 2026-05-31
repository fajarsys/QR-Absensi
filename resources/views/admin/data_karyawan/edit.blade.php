@extends('layouts.admin')

@section('content')
<div class="card border-0 shadow-sm" style="border-radius: 15px;">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center" style="border-radius: 15px 15px 0 0;">
        <h6 class="fw-bold text-primary mb-0"><i class="bi bi-pencil-square"></i> Edit Data Karyawan</h6>
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

        <form action="{{ route('admin.users.adminUpdate', $user->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold text-secondary">Nama Lengkap</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold text-secondary">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold text-secondary">Password Baru (Opsional)</label>
                    <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak ingin mengubah password">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold text-secondary">Jabatan</label>
                    <input type="text" name="jabatan" class="form-control" value="{{ old('jabatan', $user->jabatan) }}" required>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold text-secondary">Role Akses</label>
                <select name="role" class="form-select" required>
                    <option value="karyawan" {{ old('role', $user->role) == 'karyawan' ? 'selected' : '' }}>Karyawan (Akses Absensi)</option>
                    <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin (Akses Kelola Sistem)</option>
                </select>
            </div>

            <div class="d-grid mt-2">
                <button type="submit" class="btn btn-primary py-2 fw-bold" style="border-radius: 8px;">
                    <i class="bi bi-save me-1"></i> Perbarui Data Karyawan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection