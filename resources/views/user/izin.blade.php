@extends('layouts.user')
@section('content')
<div class="container mt-4 mb-5">
    
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" style="border-radius: 10px;" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" style="border-radius: 10px;" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm" style="border-radius: 15px;">
        <div class="card-header bg-primary text-white py-3" style="border-radius: 15px 15px 0 0;">
            <h6 class="mb-0 fw-bold"><i class="bi bi-envelope-paper"></i> Form Pengajuan Izin / Sakit</h6>
        </div>
        
        <div class="card-body p-4">
            <form action="{{ route('izin.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="mb-3">
                    <label class="form-label fw-bold text-secondary">Jenis Izin</label>
                    <select name="type" class="form-select" required>
                        <option value="">-- Pilih Jenis --</option>
                        <option value="Sakit">Sakit</option>
                        <option value="Izin">Izin</option>
                        <option value="Cuti">Cuti</option>
                    </select>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold text-secondary">Mulai Tanggal</label>
                        <input type="date" name="start_date" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold text-secondary">Sampai Tanggal</label>
                        <input type="date" name="end_date" class="form-control" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold text-secondary">Alasan / Keterangan</label>
                    <textarea name="reason" class="form-control" rows="3" placeholder="Jelaskan alasan izin secara detail..." required></textarea>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold text-secondary">Bukti Dokumen <span class="text-danger">*</span></label>
                    <input type="file" name="attachment" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
                    <div class="form-text text-muted">
                        *Wajib dilampirkan jika Sakit (Surat Dokter). Format: JPG, PNG, atau PDF. Maks: 2MB.
                    </div>
                </div>

                <div class="d-grid mt-2">
                    <button type="submit" class="btn btn-primary py-2 fw-bold" style="border-radius: 8px;">
                        <i class="bi bi-send me-1"></i> Kirim Pengajuan
                    </button>
                </div>
            </form>
            
        </div>
        
    </div>
    <div class="card border-0 shadow-sm mt-4" style="border-radius: 15px;">
        <div class="card-header bg-secondary text-white py-3" style="border-radius: 15px 15px 0 0;">
            <h6 class="mb-0 fw-bold"><i class="bi bi-clock-history"></i> Riwayat Pengajuan Saya</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Jenis</th>
                            <th>Tanggal Izin</th>
                            <th>Alasan</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($riwayatIzin as $riwayat)
                        <tr>
                            <td class="ps-4">
                                <span class="badge bg-{{ $riwayat->type == 'Sakit' ? 'danger' : ($riwayat->type == 'Cuti' ? 'success' : 'warning') }}">
                                    {{ $riwayat->type }}
                                </span>
                            </td>
                            <td>
                                <small class="fw-bold">
                                    {{ \Carbon\Carbon::parse($riwayat->start_date)->translatedFormat('d M Y') }} 
                                    s/d 
                                    {{ \Carbon\Carbon::parse($riwayat->end_date)->translatedFormat('d M Y') }}
                                </small>
                            </td>
                            <td><small>{{ Str::limit($riwayat->reason, 40) }}</small></td>
                            <td>
                                @if($riwayat->status == 'pending')
                                    <span class="badge bg-secondary"><i class="bi bi-hourglass-split"></i> Menunggu</span>
                                @elseif($riwayat->status == 'approved')
                                    <span class="badge bg-success"><i class="bi bi-check-circle"></i> Disetujui</span>
                                @else
                                    <span class="badge bg-danger"><i class="bi bi-x-circle"></i> Ditolak</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-4 d-block mb-2"></i>
                                Belum ada riwayat pengajuan.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection