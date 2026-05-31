@extends('layouts.user')

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm border-0" style="border-radius: 15px;">
        <div class="card-header bg-white mt-2 border-0">
            <h5 class="fw-bold"><i class="bi bi-pencil-square"></i> Edit Profil</h5>
        </div>
        <div class="card-body p-4">
            
            <form action="{{ route('profile.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT') <div class="mb-3">
                    <label class="form-label text-muted fw-bold">Nama Lengkap</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label text-muted fw-bold">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                </div>

                <hr class="my-4">

                <div class="mb-3">
                    <label class="form-label text-muted fw-bold">Password Baru (Opsional)</label>
                    <input type="password" name="password" class="form-control" placeholder="Isi jika ingin ganti password">
                    <small class="text-secondary">Biarkan kosong jika tidak ingin mengubah password.</small>
                </div>

                <div class="d-grid gap-2 mt-4">
                    <button type="submit" class="btn btn-primary shadow-sm" style="border-radius: 10px;">
                        <i class="bi bi-save"></i> Simpan Perubahan
                    </button>
                    <a href="{{ route('profile.index') }}" class="btn btn-light shadow-sm" style="border-radius: 10px;">
                        Batal
                    </a>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection