<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Expires" content="0">
    <title>Admin Panel - Absensi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}?v={{ filemtime(public_path('css/admin.css')) }}">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>

    <nav id="sidebar" class="p-3">
        <h5 class="text-center fw-bold mb-4 mt-3" style="color: #0d6efd;">
            <i class="bi bi-rocket-takeoff-fill me-2"></i>Absensi App
        </h5>
        <hr>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2 me-2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a href="{{route('admin.users.adminIndex')}}" class="nav-link {{ request()->routeIs('admin.users.adminIndex') ? 'active' : '' }}">
                    <i class="bi bi-people-fill me-2"></i> Data Karyawan
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('admin.schedule') }}" class="nav-link {{ request()->routeIs('admin.schedule') ? 'active' : '' }}">
                    <i class="bi bi-calendar-minus-fill"></i> Jadwal Karyawan
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.office') }}" class="nav-link {{ request()->routeIs('admin.office*') ? 'active' : '' }}">
                    <i class="bi bi-geo-alt-fill me-2"></i> Lokasi Kantor
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.holidays.index') }}" class="nav-link {{ request()->routeIs('admin.holidays*') ? 'active' : '' }}">
                    <i class="bi bi-calendar-x-fill me-2"></i> Tanggal Merah
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route ('admin.attendance.adminIndex')}}" class="nav-link nav-link {{ request()->routeIs('admin.attendance.adminIndex') ? 'active' : '' }} ">
                    <i class="bi bi-clipboard2-fill"></i> Data Kehadiran
                </a>
            </li>
            <li class="nav-item">
                <a href="{{route ('admin.izin.adminIndex') }}" class="nav-link nav-link {{ request()->routeIs('admin.izin.adminIndex') ? 'active' : '' }}">
                    <i class="bi bi-union"></i> Kelola Pengajuan
                </a>
            </li>
        </ul>

        <div class="mt-auto pt-5">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-danger w-100">
                    <i class="bi bi-box-arrow-left"></i> Logout
                </button>
            </form>
        </div>
    </nav>

    <div id="content">
        <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm px-4">
            <div class="container-fluid">
                <span class="navbar-text">
                    Selamat Datang, <b>{{ Auth::user()->name }}</b>
                </span>
            </div>
        </nav>

        <div class="container-fluid p-4">
            @yield('content')
        </div>
    </div>
    <div id="notif-container" style="position: fixed; top: 20px; right: 20px; z-index: 9999; display: flex; flex-direction: column; gap: 10px;"></div>


    <script type="module">
        let isAudioAllowed = false;
        const audioFile = new Audio('/assets/sounds/notification.mp3');

        // 1. Fungsi pembuka gembok audio (dieksekusi senyap di latar belakang)
        const unlockAudio = () => {
            if (!isAudioAllowed) {
                audioFile.volume = 0; // Bisukan saat inisialisasi
                audioFile.play().then(() => {
                    audioFile.pause();
                    audioFile.currentTime = 0;
                    audioFile.volume = 1; // Kembalikan volume normal
                    isAudioAllowed = true; // Audio berhasil diizinkan

                    // Hapus event listener agar fungsi ini tidak dieksekusi lagi pada klik berikutnya
                    document.removeEventListener('click', unlockAudio);
                    document.removeEventListener('keydown', unlockAudio);
                }).catch(e => console.warn("Menunggu interaksi user...", e));
            }
        };

        // 2. Pasang radar interaksi di seluruh elemen halaman
        document.addEventListener('click', unlockAudio);
        document.addEventListener('keydown', unlockAudio);

        // 3. Listener Reverb
        window.Echo.channel('admin-notification')
            .listen('.izin.baru', (data) => {
                const newNotif = document.createElement('div');
                newNotif.className = 'alert alert-info alert-dismissible fade show shadow-sm border-start border-4 border-info mb-0';
                newNotif.style = 'min-width: 300px;';

                newNotif.innerHTML = `
                    <div class="d-flex align-items-center">
                        <i class="bi bi-info-circle-fill me-3 fs-4"></i>
                        <div>
                            <strong class="d-block mb-1">Izin Baru Masuk!</strong>
                            <div class="small">${data.izin.user.name} - ${data.izin.reason}</div>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                `;

                document.getElementById('notif-container').appendChild(newNotif);

                // 4. Putar Suara
                if (isAudioAllowed) {
                    audioFile.cloneNode().play().catch(e => console.warn("Audio diblokir:", e));
                }
            })
            // 5. Listener Lembur Reverb
            .listen('.lembur.baru', (data) => {
                const newNotif = document.createElement('div');
                newNotif.className = 'alert alert-warning alert-dismissible fade show shadow-sm border-start border-4 border-warning mb-0';
                newNotif.style = 'min-width: 300px;';

                newNotif.innerHTML = `
                    <div class="d-flex align-items-center">
                        <i class="bi bi-clock-history me-3 fs-4"></i>
                        <div>
                            <strong class="d-block mb-1">Pengajuan Lembur Baru!</strong>
                            <div class="small">${data.lembur.user.name} - ${data.lembur.keterangan}</div>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                `;

                document.getElementById('notif-container').appendChild(newNotif);

                // Putar Suara
                if (isAudioAllowed) {
                    audioFile.cloneNode().play().catch(e => console.warn("Audio diblokir:", e));
                }
            });
    </script>
</body>

</html>
