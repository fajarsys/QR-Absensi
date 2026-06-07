@extends('layouts.admin')

@section('content')
<div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center" style="border-radius: 15px 15px 0 0;">
        <h6 class="fw-bold text-primary mb-0"><i class="bi bi-people"></i> Manajemen Karyawan</h6>
        <a href="{{ route('admin.users.adminCreate') }}" class="btn btn-sm btn-primary fw-bold" style="border-radius: 8px;">
            <i class="bi bi-person-plus"></i> Tambah Karyawan
        </a>
    </div>

    <div class="card-body p-0">
        @if (session('success'))
            <div class="alert alert-success m-3 alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger m-3 alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">No</th>
                        <th>Nama Lengkap</th>
                        <th>Email</th>
                        <th>Jabatan</th>
                        <th>Role</th>
                        <th class="text-center pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $index => $user)
                    <tr>
                        <td class="ps-4 text-secondary">{{ $index + 1 }}</td>
                        <td class="fw-bold text-dark">{{ $user->name }}</td>
                        <td class="text-secondary">{{ $user->email }}</td>
                        <td>{{ $user->jabatan }}</td>
                        <td>
                            <span class="badge bg-{{ $user->role == 'admin' ? 'danger' : 'success' }} text-uppercase">
                                {{ $user->role }}
                            </span>
                        </td>
                        <td class="text-center pe-4">
                            <div class="d-flex justify-content-center gap-2">
                                <a href="{{ route('admin.users.adminEdit', $user->id) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                   <i class="bi bi-pencil-square"></i>
                                </a>

                                @if ($user->id !== \Illuminate\Support\Facades\Auth::id())
                                <form action="{{ route('admin.users.adminDestroy', $user->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data karyawan ini? Semua data absensi terkait juga akan ikut terhapus.')">
                                    @csrf
                                     @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                </form>
                                @else
                                <button type="button" class="btn btn-sm btn-secondary" title="Tidak bisa menghapus akun sendiri" disabled>
                                    <i class="bi bi-trash"></i>
                                </button>
                                @endif

                                @if ($user->role !== 'admin')
                                <form action="{{ route('admin.users.resetDevice', $user->id) }}" method="POST" onsubmit="return confirm('Reset pengikatan perangkat untuk karyawan ini? Karyawan akan diminta login ulang di HP mereka.')">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-info" title="Reset Perangkat">
                                        <i class="bi bi-phone-vibrate"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">Belum ada data karyawan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection