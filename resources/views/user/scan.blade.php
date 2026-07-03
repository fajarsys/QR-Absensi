@extends('layouts.user')

@section('content')
<div class="container mt-3 mb-5">

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4 text-center">
            <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px;">
                <i class="bi bi-qr-code-scan text-primary fs-2"></i>
            </div>
            <h4 class="fw-bold text-dark mb-1">Scan Absensi</h4>
            <p class="text-muted small mb-0">Pastikan izinkan akses Kamera dan Lokasi (GPS)</p>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm rounded-4 text-center fw-bold" role="alert">
            <i class="bi bi-x-circle-fill me-1"></i> {{ session('error') }}
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-4 text-center fw-bold" role="alert">
            <i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger border-0 shadow-sm rounded-4 text-center fw-bold" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-1"></i> Gagal! Pastikan GPS sudah menemukan lokasi (warna hijau) sebelum mulai scan QR.
        </div>
    @endif

    <!-- Area Kamera Scanner -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
        <div class="card-body p-3">
            <div id="reader" style="width: 100%; border-radius: 12px; overflow: hidden;"></div>
        </div>
    </div>

    <!-- Info GPS -->
    <div class="card border-0 shadow-sm rounded-4 mb-3">
        <div class="card-body py-3 px-4">
            <div class="d-flex align-items-center" id="location-info-wrapper">
                <div class="bg-info bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width: 40px; height: 40px;" id="gps-icon-circle">
                    <i class="bi bi-geo-alt-fill text-info" id="gps-icon"></i>
                </div>
                <div>
                    <small class="fw-bold text-dark d-block">Status GPS</small>
                    <small class="text-muted" id="location-info">Mencari titik koordinat GPS Anda...</small>
                </div>
            </div>
        </div>
    </div>

    <form id="form-absen" action="{{ route('scan.store') }}" method="POST">
        @csrf
        <input type="hidden" name="qr_code" id="qr_code">
        <input type="hidden" name="latitude" id="latitude">
        <input type="hidden" name="longitude" id="longitude">
    </form>
</div>

<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

<script>
    // 1. FITUR GPS: Tarik Latitude & Longitude
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                // Jika sukses dapat GPS, masukkan ke form tersembunyi
                document.getElementById('latitude').value = position.coords.latitude;
                document.getElementById('longitude').value = position.coords.longitude;
                document.getElementById('location-info').textContent = "Lokasi Ditemukan: " + position.coords.latitude.toFixed(6) + ", " + position.coords.longitude.toFixed(6);
                document.getElementById('gps-icon-circle').className = "bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0";
                document.getElementById('gps-icon-circle').style.cssText = "width: 40px; height: 40px;";
                document.getElementById('gps-icon').className = "bi bi-geo-alt-fill text-success";
            },
            function(error) {
                // Jika user menolak izin GPS atau gagal
                document.getElementById('location-info').textContent = "Gagal akses GPS. Pastikan izin lokasi (Location) nyala di browser!";
                document.getElementById('gps-icon-circle').className = "bg-danger bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0";
                document.getElementById('gps-icon-circle').style.cssText = "width: 40px; height: 40px;";
                document.getElementById('gps-icon').className = "bi bi-geo-alt-fill text-danger";
            }
        );
    } else {
        alert("Browser Anda tidak mendukung fitur GPS.");
    }

    // 2. FITUR KAMERA: Nyalakan Scanner QR
    function onScanSuccess(decodedText, decodedResult) {
        // Hentikan kamera setelah berhasil membaca QR 1 kali
        html5QrcodeScanner.clear();

        // Masukkan hasil teks QR ke dalam form tersembunyi
        document.getElementById('qr_code').value = decodedText;

        alert("✅ QR Berhasil discan!\nData QR: " + decodedText + "\nMemproses absensi...");

        document.getElementById('form-absen').action = "{{ route('scan.store') }}";
        document.getElementById('form-absen').submit();
    }

    // Settingan Kamera (fps = frame per second, qrbox = ukuran kotak scan)
    var html5QrcodeScanner = new Html5QrcodeScanner("reader", { fps: 10, qrbox: 250 });
    html5QrcodeScanner.render(onScanSuccess);
</script>
@endsection