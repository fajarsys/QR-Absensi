@extends('layouts.admin')

@section('content')
<div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center" style="border-radius: 15px 15px 0 0;">
        <h6 class="fw-bold text-primary mb-0"><i class="bi bi-envelope-check"></i> Kelola Pengajuan Izin & Sakit</h6>
    </div>

    <div class="card-body p-0">
        
        @if (session('success'))
            <div class="alert alert-success m-3 alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Karyawan</th>
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
</div>
<script type="module">
    window.Echo.channel('admin-notification')
        .listen('.izin.baru', (data) => {
            const tbody = document.getElementById('tabel-data-izin');
            
            if (tbody) {
                // Hapus pesan "Belum ada data" jika tabel sebelumnya kosong
                const barisKosong = tbody.querySelector('td[colspan="7"]');
                if (barisKosong) barisKosong.closest('tr').remove();

                // Logika penentuan warna badge
                let warnaBadge = 'warning';
                if (data.izin.type === 'Sakit') warnaBadge = 'danger';
                else if (data.izin.type === 'Cuti') warnaBadge = 'success';

                // Pemotongan teks alasan (maks 30 karakter)
                let alasan = data.izin.reason;
                if (alasan.length > 30) alasan = alasan.substring(0, 30) + '...';

                // Cek ketersediaan lampiran dokumen
                let dokumenHtml = '<span class="text-muted small">-</span>';
                if (data.izin.attachment) {
                    dokumenHtml = `<a href="/storage/${data.izin.attachment}" target="_blank" class="btn btn-sm btn-outline-info"><i class="bi bi-file-earmark-text"></i> Lihat</a>`;
                }

                // Ambil Token CSRF untuk form aksi
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                // Template baris HTML baru (Diberi class 'table-info' agar tersorot saat baru masuk)
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
                                <form action="/admin/izin/${data.izin.id}/approve" method="POST">
                                    <input type="hidden" name="_token" value="${csrfToken}">
                                    <input type="hidden" name="status" value="approved">
                                    <button type="submit" class="btn btn-sm btn-success" title="Setujui" onclick="return confirm('Setujui pengajuan ini dan masukkan ke data absensi?')">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                </form>
                                <form action="/admin/izin/${data.izin.id}/approve" method="POST">
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

                // Sisipkan baris baru di posisi teratas tabel
                tbody.insertAdjacentHTML('afterbegin', barisBaru);
            }
        });
</script>
@endsection
