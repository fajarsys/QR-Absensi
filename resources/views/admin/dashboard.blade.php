@extends('layouts.admin')

@section('content')
    <h3 class="mb-4">Dashboard Ringkasan</h3>

    <div class="row">
        <div class="col-md-4">
            <div class="card text-white bg-primary mb-3 shadow-sm" style="height: 150px;">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="card-title">Total Pegawai</h5>
                        <h2 class="fw-bold"><p>{{ \App\Models\User::count() }}</p></h2> </div>
                    <i class="bi bi-people" style="font-size: 3rem; opacity: 0.5;"></i>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card text-white bg-success mb-3 shadow-sm" style="height: 150px;">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="card-title">Hadir Hari Ini</h5>
                        <h2 class="fw-bold">
                            {{ \App\Models\Attendance::whereDate('date', now())
                                                    ->where('status', 'hadir')
                                                    ->count() }} 
                        </h2>
                    </div>
                    <i class="bi bi-check-circle" style="font-size: 3rem; opacity: 0.5;"></i>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card text-white bg-info mb-3 shadow-sm" style="height: 150px;">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="card-title">Status Kantor</h5>
                        <p class="mb-0">Kampus STMIK Mercusuar</p>
                    </div>
                    <i class="bi bi-building" style="font-size: 3rem; opacity: 0.5;"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <h5>Log Aktivitas Terbaru</h5>
            <p class="text-muted">Belum ada data absensi masuk hari ini.</p>
        </div>
    </div>
   <x-Asisten/>
@endsection