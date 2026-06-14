@extends('layouts.admin')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-12 mb-4 d-flex justify-content-between align-items-center">
        <h3 class="mb-0">🏠 Pengaturan Kantor</h3>
        
        <!-- Tombol Buka Layar QR Code Kiosk -->
        <a href="{{ route('admin.office.qr-display') }}" target="_blank" class="btn btn-success btn-lg shadow-sm fw-bold">
            <i class="bi bi-display me-2"></i> Buka Layar Mesin Absen (Mode Kiosk)
        </a>
    </div>

    <div class="col-md-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 text-primary fw-bold"><i class="bi bi-geo-alt"></i> Konfigurasi Lokasi Pusat</h5>
            </div>
            <div class="card-body p-4">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form action="{{ route('admin.office.update', $office->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label class="form-label fw-bold">Nama Kantor</label>
                        <input type="text" name="name" class="form-control form-control-lg" value="{{ $office->name }}" placeholder="Contoh: Kantor Pusat">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold">Latitude</label>
                            <input type="text" name="latitude" class="form-control form-control-lg" value="{{ $office->latitude }}">
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold">Longitude</label>
                            <input type="text" name="longitude" class="form-control form-control-lg" value="{{ $office->longitude }}">
                        </div>
                    </div>
                    
                    <div class="mb-5">
                        <label class="form-label fw-bold">Radius Toleransi (Meter)</label>
                        <div class="input-group input-group-lg">
                            <input type="number" name="radius" class="form-control" value="{{ $office->radius }}">
                            <span class="input-group-text">Meter</span>
                        </div>
                        <div class="form-text mt-2 text-muted">Karyawan tidak bisa absen jika berada di luar jarak ini dari titik pusat.</div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary btn-lg px-5 shadow-sm">
                            <i class="bi bi-save me-2"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection