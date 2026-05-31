@extends('layouts.user')
@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h4 class="fw-bold text-primary">
                <i class="bi bi-clock-history me-2"></i>Riwayat Kehadiran Saya
            </h4>
            <p class="text-muted">Pantau rekam jejak absensi Anda setiap harinya.</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 15px;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-secondary">
                        <tr>
                            <th class="ps-4 py-3">No</th>
                            <th class="py-3">Tanggal</th>
                            <th class="py-3">Waktu Rekam</th>
                            <th class="py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($riwayatAbsen as $index => $absen)
                            <tr>
                                <td class="ps-4 text-muted">
                                    {{ $riwayatAbsen->firstItem() + $index }}
                                </td>
                                
                                <td class="fw-bold text-dark">
                                    {{ \Carbon\Carbon::parse($absen->created_at)->translatedFormat('l, d F Y') }}
                                </td>
                                
                                <td>
                                    <span class="text-secondary">
                                        <i class="bi bi-clock me-1"></i>
                                        {{ \Carbon\Carbon::parse($absen->created_at)->format('H:i') }} WIB
                                    </span>
                                </td>
                                
                                <td>
                                    @if(strtolower($absen->status) == 'hadir')
                                        <span class="badge bg-success px-3 py-2 rounded-pill"><i class="bi bi-check-circle me-1"></i> Hadir</span>
                                    @elseif(strtolower($absen->status) == 'telat')
                                        <span class="badge bg-warning text-dark px-3 py-2 rounded-pill"><i class="bi bi-exclamation-triangle me-1"></i> Telat</span>
                                    @elseif(strtolower($absen->status) == 'izin' || strtolower($absen->status) == 'sakit')
                                        <span class="badge bg-secondary px-3 py-2 rounded-pill"><i class="bi bi-info-circle me-1"></i> {{ ucfirst($absen->status) }}</span>
                                    @else
                                        <span class="badge bg-light text-dark border px-3 py-2 rounded-pill">{{ ucfirst($absen->status) }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="bi bi-calendar-x fs-1 d-block mb-3"></i>
                                        Belum ada riwayat kehadiran yang tercatat.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        @if($riwayatAbsen->hasPages())
        <div class="card-footer bg-white border-top py-3 d-flex justify-content-center" style="border-radius: 0 0 15px 15px;">
            {{ $riwayatAbsen->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>

@endsection