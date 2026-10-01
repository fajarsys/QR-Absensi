@extends('layouts.admin')

@section('content')
<div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom-0" style="border-radius: 15px 15px 0 0;">
        <h6 class="fw-bold text-primary mb-0"><i class="bi bi-list-check"></i> Kelola Pengajuan Dosen</h6>
    </div>

    <!-- TABS NAVIGATION -->
    <ul class="nav nav-tabs px-3" id="pengajuanTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active fw-bold" id="izin-tab" data-bs-toggle="tab" data-bs-target="#izin" type="button" role="tab">
                <i class="bi bi-envelope-paper text-primary"></i> Izin & Sakit
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-bold" id="lembur-tab" data-bs-toggle="tab" data-bs-target="#lembur" type="button" role="tab">
                <i class="bi bi-clock-history text-warning"></i> Lembur
            </button>
        </li>
    </ul>

    <div class="card-body p-0">

        @if (session('success'))
        <div class="alert alert-success m-3 alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <div class="tab-content" id="pengajuanTabContent">

            <!-- ==================== TAB IZIN & SAKIT ==================== -->
            <div class="tab-pane fade show active" id="izin" role="tabpanel">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Dosen</th>
                                <th>Jenis</th>
                                <th>Tanggal</th>
                                <th>Alasan</th>
                                <th>Dokumen</th>
                                <th>Status</th>
                                <th class="text-center pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tabel-data-izin">
                            @forelse ($leaves as $leave)
                            <tr>
                                <td class="ps-4 fw-bold text-secondary">{{ $leave->user->name }}</td>
                                <td>
                                    <span class="badge bg-{{ $leave->type == 'Sakit' ? 'danger' : ($leave->type == 'Cuti' ? 'success' : 'warning') }}">
                                        {{ $leave->type }}
                                    </span>
                                </td>
                                <td>
                                    <small>
                                        {{ \Carbon\Carbon::parse($leave->start_date)->translatedFormat('d M Y') }} - <br>
                                        {{ \Carbon\Carbon::parse($leave->end_date)->translatedFormat('d M Y') }}
                                    </small>
                                </td>
                                <td><small>{{ Str::limit($leave->reason, 30) }}</small></td>
                                <td>
                                    @if($leave->attachment)
                                    <a href="{{ asset('storage/' . $leave->attachment) }}" target="_blank" class="btn btn-sm btn-outline-info">
                                        <i class="bi bi-file-earmark-text"></i> Lihat
                                    </a>
                                    @else
                                    <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($leave->status == 'pending')
                                    <span class="badge bg-secondary">Menunggu</span>
                                    @elseif($leave->status == 'approved')
                                    <span class="badge bg-success">Disetujui</span>
                                    @else
                                    <span class="badge bg-danger">Ditolak</span>
                                    @endif
                                </td>
                                <td class="text-center pe-4">
                                    <div class="d-flex justify-content-center gap-2 align-items-center">
                                        @if($leave->status == 'pending')
                                        <form action="{{ route('admin.izin.approve', $leave->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="status" value="approved">
                                            <button type="submit" class="btn btn-sm btn-success" title="Setujui" onclick="return confirm('Setujui pengajuan ini dan masukkan ke data absensi?')">
                                                <i class="bi bi-check-lg"></i>
                                            </button>
                                        </form>

                                        <form action="{{ route('admin.izin.approve', $leave->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="status" value="rejected">
                                            <button type="submit" class="btn btn-sm btn-danger" title="Tolak" onclick="return confirm('Tolak pengajuan ini?')">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                        </form>
                                        @else
                                        <i class="bi bi-lock text-muted me-1" title="Sudah diproses"></i>
                                        @endif

                                        <form action="{{ route('admin.izin.destroy', $leave->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus Data" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini beserta dokumen unggahannya?')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">Belum ada data pengajuan izin.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ==================== TAB LEMBUR ==================== -->
            <div class="tab-pane fade" id="lembur" role="tabpanel">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Dosen</th>
                                <th>Tanggal</th>
                                <th>Jam Lembur</th>
                                <th>Keterangan</th>
                                <th>Durasi</th>
                                <th>Status</th>
                                <th class="text-center pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tabel-data-lembur">
                            @forelse ($overtimes as $lembur)
                            <tr>
                                <td class="ps-4 fw-bold text-secondary">{{ $lembur->user->name }}</td>
                                <td><small class="fw-bold">{{ \Carbon\Carbon::parse($lembur->tanggal)->translatedFormat('d M Y') }}</small></td>
                                <td>
                                    <small class="text-primary fw-bold">
                                        {{ \Carbon\Carbon::parse($lembur->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($lembur->jam_selesai)->format('H:i') }}
                                    </small>
                                </td>
                                <td><small>{{ Str::limit($lembur->keterangan, 40) }}</small></td>
                                <td><small>{{ $lembur->durasi_menit }} menit</small></td>
                                <td>
                                    @if($lembur->status == 'pending')
                                    <span class="badge bg-secondary">Menunggu</span>
                                    @elseif($lembur->status == 'approved')
                                    <span class="badge bg-success">Disetujui</span>
                                    @else
                                    <span class="badge bg-danger">Ditolak</span>
                                    @endif
                                </td>
                                <td class="text-center pe-4">
                                    <div class="d-flex justify-content-center gap-2 align-items-center">
                                        @if($lembur->status == 'pending')
                                        <form action="{{ route('admin.lembur.approve', $lembur->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="status" value="approved">
                                            <button type="submit" class="btn btn-sm btn-success" title="Setujui" onclick="return confirm('Setujui pengajuan lembur ini?')">
                                                <i class="bi bi-check-lg"></i>
                                            </button>
                                        </form>

                                        <form action="{{ route('admin.lembur.approve', $lembur->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="status" value="rejected">
                                            <button type="submit" class="btn btn-sm btn-danger" title="Tolak" onclick="return confirm('Tolak pengajuan lembur ini?')">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                        </form>
                                        @else
                                        <i class="bi bi-lock text-muted me-1" title="Sudah diproses"></i>
                                        @endif

                                        <form action="{{ route('admin.lembur.destroy', $lembur->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus Data" onclick="return confirm('Apakah Anda yakin ingin menghapus data lembur ini?')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">Belum ada data pengajuan lembur.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<script type="module">
    // Ambil Token CSRF untuk form aksi
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    window.Echo.channel('admin-notification')
        // 1. LISTENER IZIN BARU
        .listen('.izin.baru', (data) => {
            const tbody = document.getElementById('tabel-data-izin');

            if (tbody) {
                const barisKosong = tbody.querySelector('td[colspan="7"]');
                if (barisKosong) barisKosong.closest('tr').remove();

                let warnaBadge = 'warning';
                if (data.izin.type === 'Sakit') warnaBadge = 'danger';
                else if (data.izin.type === 'Cuti') warnaBadge = 'success';

                let alasan = data.izin.reason;
                if (alasan.length > 30) alasan = alasan.substring(0, 30) + '...';

                let dokumenHtml = '<span class="text-muted small">-</span>';
                if (data.izin.attachment) {
                    dokumenHtml = `<a href="/storage/${data.izin.attachment}" target="_blank" class="btn btn-sm btn-outline-info"><i class="bi bi-file-earmark-text"></i> Lihat</a>`;
                }

                const barisBaru = `
                    <tr class="table-info">
                        <td class="ps-4 fw-bold text-secondary">${data.izin.user.name}</td>
                        <td><span class="badge bg-${warnaBadge}">${data.izin.type}</span></td>
                        <td>
                            <small>
                                ${data.izin.start_date} - <br>
                                ${data.izin.end_date}
                            </small>
                        </td>
                        <td><small>${alasan}</small></td>
                        <td>${dokumenHtml}</td>
                        <td><span class="badge bg-secondary">Menunggu</span></td>
                        <td class="text-center pe-4">
                            <div class="d-flex justify-content-center gap-2 align-items-center">
                                <form action="/admin/izin/${data.izin.id}/status" method="POST">
                                    <input type="hidden" name="_token" value="${csrfToken}">
                                    <input type="hidden" name="status" value="approved">
                                    <button type="submit" class="btn btn-sm btn-success" title="Setujui" onclick="return confirm('Setujui pengajuan ini dan masukkan ke data absensi?')">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                </form>
                                <form action="/admin/izin/${data.izin.id}/status" method="POST">
                                    <input type="hidden" name="_token" value="${csrfToken}">
                                    <input type="hidden" name="status" value="rejected">
                                    <button type="submit" class="btn btn-sm btn-danger" title="Tolak" onclick="return confirm('Tolak pengajuan ini?')">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </form>
                                <form action="/admin/izin/${data.izin.id}" method="POST">
                                    <input type="hidden" name="_token" value="${csrfToken}">
                                    <input type="hidden" name="_method" value="DELETE">
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus Data" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini beserta dokumen unggahannya?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                `;
                tbody.insertAdjacentHTML('afterbegin', barisBaru);
            }
        })
        // 2. LISTENER LEMBUR BARU
        .listen('.lembur.baru', (data) => {
            const tbody = document.getElementById('tabel-data-lembur');

            if (tbody) {
                const barisKosong = tbody.querySelector('td[colspan="7"]');
                if (barisKosong) barisKosong.closest('tr').remove();

                let keterangan = data.lembur.keterangan;
                if (keterangan.length > 40) keterangan = keterangan.substring(0, 40) + '...';

                const barisBaru = `
                    <tr class="table-warning">
                        <td class="ps-4 fw-bold text-secondary">${data.lembur.user.name}</td>
                        <td><small class="fw-bold">${data.lembur.tanggal}</small></td>
                        <td>
                            <small class="text-primary fw-bold">
                                ${data.lembur.jam_mulai.substring(0, 5)} - ${data.lembur.jam_selesai.substring(0, 5)}
                            </small>
                        </td>
                        <td><small>${keterangan}</small></td>
                        <td><small>${data.lembur.durasi_menit} menit</small></td>
                        <td><span class="badge bg-secondary">Menunggu</span></td>
                        <td class="text-center pe-4">
                            <div class="d-flex justify-content-center gap-2 align-items-center">
                                <form action="/admin/lembur/${data.lembur.id}/status" method="POST">
                                    <input type="hidden" name="_token" value="${csrfToken}">
                                    <input type="hidden" name="status" value="approved">
                                    <button type="submit" class="btn btn-sm btn-success" title="Setujui" onclick="return confirm('Setujui pengajuan lembur ini?')">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                </form>
                                <form action="/admin/lembur/${data.lembur.id}/status" method="POST">
                                    <input type="hidden" name="_token" value="${csrfToken}">
                                    <input type="hidden" name="status" value="rejected">
                                    <button type="submit" class="btn btn-sm btn-danger" title="Tolak" onclick="return confirm('Tolak pengajuan lembur ini?')">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </form>
                                <form action="/admin/lembur/${data.lembur.id}" method="POST">
                                    <input type="hidden" name="_token" value="${csrfToken}">
                                    <input type="hidden" name="_method" value="DELETE">
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus Data" onclick="return confirm('Apakah Anda yakin ingin menghapus data lembur ini?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                `;
                tbody.insertAdjacentHTML('afterbegin', barisBaru);
            }
        });
</script>
@endsection