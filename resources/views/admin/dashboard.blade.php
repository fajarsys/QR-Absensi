@extends('layouts.admin')

@section('content')

<!-- Header Kustom -->
<div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
    <div>
        @php
            $hour = now()->format('H');
            if ($hour < 11) {
                $greeting = 'Selamat Pagi';
            } elseif ($hour < 15) {
                $greeting = 'Selamat Siang';
            } elseif ($hour < 18) {
                $greeting = 'Selamat Sore';
            } else {
                $greeting = 'Selamat Malam';
            }
        @endphp
        <h2 class="fw-bold mb-0 text-dark">{{ $greeting }}, Admin!</h2>
        <p class="text-muted mb-0">{{ now()->translatedFormat('l, d F Y') }}</p>
    </div>
</div>

<div class="row g-4">
    <!-- Kolom Kiri: Metrik Utama (70%) -->
    <div class="col-lg-8">

        <h5 class="fw-bold text-secondary mb-3">Rekapitulasi Absensi (Hari Ini)</h5>

        <div class="row g-4">
            <!-- Total Karyawan -->
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4 position-relative overflow-hidden">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-2 d-inline-flex">
                                <i class="bi bi-people-fill fs-5"></i>
                            </div>
                        </div>
                        <h6 class="text-muted fw-semibold mb-1">Total Karyawan</h6>
                        <h2 class="fw-bold mb-0 text-dark">{{ \App\Models\User::count() }}</h2>
                    </div>
                </div>
            </div>

            <!-- Hadir Hari Ini -->
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4 position-relative overflow-hidden">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="bg-success bg-opacity-10 text-success rounded-3 p-2 d-inline-flex">
                                <i class="bi bi-calendar-check-fill fs-5"></i>
                            </div>
                        </div>
                        <h6 class="text-muted fw-semibold mb-1">Hadir Hari Ini</h6>
                        <h2 class="fw-bold mb-0 text-dark">
                            {{ \App\Models\Attendance::whereDate('date', now())->where('status', 'hadir')->count() }}
                        </h2>
                    </div>
                </div>
            </div>

            <!-- Status Kantor -->
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4 position-relative overflow-hidden">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="bg-info bg-opacity-10 text-info rounded-3 p-2 d-inline-flex">
                                <i class="bi bi-building-fill fs-5"></i>
                            </div>
                        </div>
                        <h6 class="text-muted fw-semibold mb-1">Lokasi Kantor</h6>
                        <h4 class="fw-bold mb-0 text-dark">STMIK Mercusuar</h4>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Kolom Kanan: Real-Time Activity Log (30%) -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0">Real-Time Activity Log</h6>
                <span class="spinner-grow spinner-grow-sm text-danger" role="status" aria-hidden="true" id="live-indicator" title="Live Connection Active"></span>
            </div>
            <div class="card-body p-4">

                <div class="position-relative" id="activity-log-container" style="max-height: 400px; overflow-y: auto;">
                    @if(isset($recentActivities) && $recentActivities->count() > 0)
                    @foreach($recentActivities as $act)
                    <div class="d-flex mb-4 activity-item" id="{{ $act['id'] }}">
                        <div class="me-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white shadow-sm" style="width: 40px; height: 40px; background-color: {{ $act['type'] == 'attendance' ? '#198754' : ($act['type'] == 'leave' ? '#ffc107' : '#0d6efd') }};">
                                {{ strtoupper(substr($act['user_name'], 0, 1)) }}
                            </div>
                        </div>
                        <div>
                            <div class="fw-bold text-dark">{{ $act['user_name'] }} <span class="text-muted ms-1 fs-6 fw-normal">• {{ $act['time'] }}</span></div>
                            <div class="text-secondary small">{{ $act['description'] }}</div>
                            @if($act['type'] == 'leave' || $act['type'] == 'overtime')
                            <span class="badge bg-light text-dark border mt-1">{{ $act['status'] }}</span>
                            @endif
                        </div>
                    </div>
                    @endforeach
                    @else
                    <p class="text-muted text-center mt-4" id="no-activity-msg">No recent activities.</p>
                    @endif
                </div>

            </div>
        </div>
    </div>
</div>

<script type="module">
    document.addEventListener('DOMContentLoaded', function() {
        if (window.Echo) {
            window.Echo.channel('admin.activities')
                .listen('ActivityLogged', (e) => {

                    let noMsg = document.getElementById('no-activity-msg');
                    if (noMsg) noMsg.remove();

                    let container = document.getElementById('activity-log-container');
                    let bgColor = e.type == 'attendance' ? '#198754' : (e.type == 'leave' ? '#ffc107' : '#0d6efd');
                    let initial = e.user_name.charAt(0).toUpperCase();

                    let statusBadge = (e.type == 'leave' || e.type == 'overtime') ?
                        `<span class="badge bg-light text-dark border mt-1">${e.status}</span>` :
                        '';

                    let newItem = document.createElement('div');
                    newItem.className = "d-flex mb-4 activity-item shadow-sm p-2 rounded-3 bg-light border border-primary";
                    newItem.id = e.id;
                    newItem.style.transition = "all 1s ease";
                    newItem.innerHTML = `
                        <div class="me-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white shadow-sm" style="width: 40px; height: 40px; background-color: ${bgColor};">
                                ${initial}
                            </div>
                        </div>
                        <div>
                            <div class="fw-bold text-dark">${e.user_name} <span class="text-muted ms-1 fs-6 fw-normal">• ${e.time}</span></div>
                            <div class="text-secondary small">${e.description}</div>
                            ${statusBadge}
                        </div>
                    `;

                    container.prepend(newItem);

                    // Efek highlight menghilang perlahan
                    setTimeout(() => {
                        newItem.classList.remove('shadow-sm', 'bg-light', 'border', 'border-primary', 'p-2');
                    }, 5000);
                });
        }
    });
</script>
@endsection