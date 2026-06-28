@extends('layouts.admin')

@section('content')
<h3 class="mb-4">Dashboard Ringkasan</h3>

<div class="row">
    <div class="col-md-4">
        <div class="card text-white bg-primary mb-3 shadow-sm" style="height: 150px;">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="card-title">Total Pegawai</h5>
                    <h2 class="fw-bold">
                        <p>{{ \App\Models\User::count() }}</p>
                    </h2>
                </div>
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
        <h5 class="mb-3">
            Log Aktivitas Terbaru
            <span class="spinner-grow spinner-grow-sm text-danger ms-2" role="status" aria-hidden="true" id="live-indicator" title="Live Connection Active"></span>
        </h5>
        
        <div class="list-group" id="activity-log-container">
            @if(isset($recentActivities) && $recentActivities->count() > 0)
                @foreach($recentActivities as $act)
                    <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center activity-item" id="{{ $act['id'] }}">
                        <div>
                            <span class="fw-bold">{{ $act['user_name'] }}</span>
                            <span class="text-muted ms-2">{{ $act['description'] }}</span>
                            <br>
                            <small class="badge bg-secondary mt-1">{{ $act['status'] }}</small>
                        </div>
                        <span class="text-primary fw-bold">{{ $act['time'] }}</span>
                    </div>
                @endforeach
            @else
                <p class="text-muted" id="no-activity-msg">Belum ada aktivitas hari ini.</p>
            @endif
        </div>
    </div>
</div>

<script type="module">
    document.addEventListener('DOMContentLoaded', function () {
        if (window.Echo) {
            window.Echo.channel('admin.activities')
                .listen('ActivityLogged', (e) => {
                    // Hilangkan pesan kosong jika ada
                    let noMsg = document.getElementById('no-activity-msg');
                    if(noMsg) noMsg.remove();

                    // Buat elemen baru
                    let container = document.getElementById('activity-log-container');
                    let newItem = document.createElement('div');
                    newItem.className = "list-group-item list-group-item-action d-flex justify-content-between align-items-center activity-item bg-light border-primary";
                    newItem.id = e.id;
                    newItem.innerHTML = `
                        <div>
                            <span class="fw-bold">${e.user_name}</span>
                            <span class="text-muted ms-2">${e.description}</span>
                            <br>
                            <small class="badge bg-secondary mt-1">${e.status}</small>
                        </div>
                        <span class="text-primary fw-bold">${e.time}</span>
                    `;

                    // Tambahkan di urutan paling atas
                    container.prepend(newItem);

                    // Efek warna highlight perlahan menghilang
                    setTimeout(() => {
                        newItem.classList.remove('bg-light', 'border-primary');
                    }, 3000);
                });
        }
    });
</script>
@endsection