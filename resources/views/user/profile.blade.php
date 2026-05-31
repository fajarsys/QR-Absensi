@extends('layouts.user') @section('content')
<div class="container mt-4">
    <div class="card shadow-sm border-0" style="border-radius: 15px;">
        <div class="card-body text-center p-4">
            
            <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=0D6EFD&color=fff&size=120" 
                 class="rounded-circle mb-3 shadow" 
                 alt="Foto Profil {{ $user->name }}">
            
            <h4 class="fw-bold mb-1">{{ $user->name }}</h4>
            <p class="text-muted mb-4">{{ $user->email }}</p>

            <hr class="mb-4">

            <div class="text-start">
                <div class="mb-3">
                    <small class="text-muted d-block fw-bold">ID Karyawan</small>
                    <span class="fs-6">EMP-00{{ $user->id }}</span>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block fw-bold">Tanggal Bergabung</small>
                    <span class="fs-6">{{ $user->created_at->format('d F Y') }}</span>
                </div>
             </div>
            <a href="{{ route('profile.edit') }}" class="btn btn-light w-100 shadow-sm" style="border-radius: 10px;">
                    Edit Profile
             </a>
        </div>
    </div>

    <div class="mt-4 d-grid gap-2">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-danger w-100 shadow-sm" style="border-radius: 10px;">
                <i class="bi bi-box-arrow-right"></i> Keluar (Logout)
            </button>
        </form>
    </div>
</div>
@endsection
