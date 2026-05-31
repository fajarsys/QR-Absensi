<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>Absensi Karyawan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    
    <style>
        body {
            background-color: #f0f2f5;
            padding-bottom: 70px; /* Supaya konten tidak tertutup Bottom Nav */
        }
        /* Style untuk Bottom Navigation Bar ala Aplikasi HP */
        .bottom-nav {
            position: fixed;
            bottom: 0;
            width: 100%;
            background: white;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-around;
            padding: 10px 0;
            z-index: 1000;
        }
        .nav-item-mobile {
            text-align: center;
            color: #6c757d;
            text-decoration: none;
            font-size: 0.8rem;
        }
        .nav-item-mobile i {
            font-size: 1.5rem;
            display: block;
            margin-bottom: 2px;
        }
        .nav-item-mobile.active {
            color: #0d6efd;
            font-weight: bold;
        }
        /* Tombol Scan Besar di Tengah */
        .scan-btn-container {
            position: relative;
            top: -25px; 
        }
        .scan-btn {
            width: 60px;
            height: 60px;
            background-color: #0d6efd;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            box-shadow: 0 4px 10px rgba(13, 110, 253, 0.4);
            border: 4px solid #f0f2f5;
        }
    </style>
</head>
<body>

    <div class="bg-primary text-white p-3 shadow-sm rounded-bottom-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <small class="d-block opacity-75">Selamat Datang Kembali</small>
                <h5 class="m-0 fw-bold">{{ Auth::user()->name ?? 'Karyawan' }}</h5>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-sm btn-light text-primary">
                    <i class="bi bi-box-arrow-right"></i>
                </button>
            </form>
        </div>
    </div>

    <div class="container mt-3">
        @yield('content')
    </div>

    <div class="bottom-nav">
        <a href="{{ route('dashboard') }}" class="nav-item-mobile {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-house-door"></i>
            Home
        </a>

        <div class="scan-btn-container">
            <a href="{{ route('scan') }}" class="scan-btn"> <i class="bi bi-qr-code-scan" style="font-size: 1.5rem;"></i>
            </a>
        </div>

        <a href="{{ route('riwayat-kehadiran') }}" class="nav-item-mobile"> <i class="bi bi-clock-history"></i>
            Riwayat
        </a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>