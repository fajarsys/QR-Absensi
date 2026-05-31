@extends('layouts.admin')

@section('content')
<div class="row">
    <div class="col-md-12 mb-4">
        <h3>🏠 Pengaturan Kantor</h3>
    </div>

    <div class="col-md-7">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white">
                <h5 class="mb-0 text-primary"><i class="bi bi-geo-alt"></i> Koordinat Pusat</h5>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form action="{{ route('admin.office.update', $office->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Kantor</label>
                        <input type="text" name="name" class="form-control" value="{{ $office->name }}" placeholder="Contoh: Kantor Pusat">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Latitude</label>
                            <input type="text" name="latitude" class="form-control" value="{{ $office->latitude }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Longitude</label>
                            <input type="text" name="longitude" class="form-control" value="{{ $office->longitude }}">
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold">Radius Toleransi (Meter)</label>
                        <div class="input-group">
                            <input type="number" name="radius" class="form-control" value="{{ $office->radius }}">
                            <span class="input-group-text">Meter</span>
                        </div>
                        <div class="form-text">Karyawan tidak bisa absen jika berada di luar jarak ini.</div>
                    </div>

                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-save me-1"></i> Simpan Perubahan
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-5">
        <div class="card shadow-sm border-0 text-center h-100">
            <div class="card-header bg-white">
                <h5 class="mb-0 text-dark"><i class="bi bi-qr-code"></i> Scan Absensi</h5>
            </div>
            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                <div class="p-3 border rounded bg-white">
                    {!! QrCode::size(200)->generate($office->id) !!}
                </div>
                <p class="mt-3 text-muted">
                    Tampilkan kode ini di layar monitor kantor untuk di-scan oleh karyawan saat datang/pulang.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection