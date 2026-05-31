@extends('layouts.admin')

@section('content')
<div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h6 class="fw-bold text-primary mb-0"><i class="bi bi-calendar-check"></i> Rekap Kehadiran</h6>
        
        <a href="{{ route('admin.attendance.printAll', ['month' => $bulan, 'year' => $tahun]) }}" target="_blank" class="btn btn-sm btn-danger shadow-sm">
            <i class="bi bi-printer"></i> Cetak Rekap Bulan Ini
        </a>
    </div>

    <div class="card-body">
        <form action="{{ route('admin.attendance.adminIndex') }}" method="GET" class="row g-3 mb-4 d-print-none">
            <div class="col-md-4">
                <select name="month" class="form-select">
                    @foreach(range(1, 12) as $m)
                        <option value="{{ sprintf('%02d', $m) }}" {{ $bulan == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="year" class="form-select">
                    @for($y = date('Y'); $y >= 2024; $y--)
                        <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
        </form>

        <div class="table-responsive">
    <table class="table table-hover align-middle border">
        <thead class="table-light">
            <tr>
                <th class="ps-3">Nama Karyawan</th>
                <th>Tanggal</th>
                <th>Jam Masuk</th>
                <th>Jam Pulang</th>
                <th>Status</th>
                <th>Keterangan</th> <th class="text-center d-print-none">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($attendances as $absen)
                <tr>
                    <td class="ps-3 fw-bold">{{ $absen->user->name }}</td>
                    <td>{{ \Carbon\Carbon::parse($absen->date)->translatedFormat('d M Y') }}</td>
                    <td><span class="text-success">{{ $absen->clock_in ?? '-' }}</span></td>
                    <td><span class="text-primary">{{ $absen->clock_out ?? '-' }}</span></td>
                    <td>
                        <span class="badge bg-{{ $absen->status == 'Hadir' ? 'success' : ($absen->status == 'Izin' ? 'info' : 'danger') }}">
                            {{ $absen->status }}
                        </span>
                    </td>
                    <td>
                        @if($absen->notes)
                            <small class="text-muted italic">{{ $absen->notes }}</small>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td class="text-center d-print-none">
                        <a href="{{ route('admin.attendance.printUser', ['user' => $absen->user_id, 'month' => $bulan, 'year' => $tahun]) }}" target="_blank" class="btn btn-sm btn-outline-danger" title="Cetak Rekap {{ $absen->user->name }}">
                        <i class="bi bi-printer"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-4">Data tidak ditemukan untuk periode ini.</td>
                </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    @media print {
        .d-print-none, .sidebar, .navbar, .footer, .btn-primary { 
            display: none !important; 
        }
        .card { 
            border: none; 
        }

        body { 
            background: white !important; 
        }
    }
</style>

<script>
    function printUser(name) {
        // Logika sederhana: Bisa dikembangkan untuk mengarahkan ke halaman PDF khusus user tsb
        alert('Fitur cetak detail untuk ' + name + ' sedang disiapkan.');
    }
</script>
@endsection