@extends('layouts.user')

@section('content')
<div class="container mt-5 text-center">
    <h2><i class="bi bi-camera"></i> Scan Absensi</h2>
    <p class="text-muted">Pastikan izinkan akses Kamera dan Lokasi (GPS)</p>

    @if(session('error'))
        <div class="alert alert-danger mt-3 text-center fw-bold">
            <i class="bi bi-x-circle-fill"></i> {{ session('error') }}
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success mt-3 text-center fw-bold">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger mt-3 text-center fw-bold">
            <i class="bi bi-exclamation-triangle-fill"></i> Gagal! Pastikan GPS sudah menemukan lokasi (warna hijau) sebelum mulai scan QR.
        </div>
    @endif

    <div class="row justify-content-center mt-4">
        <div class="col-md-6">
            <div id="reader" width="100%"></div>
        </div>
    </div>

    <div class="alert alert-info mt-3" id="location-info">
        Mencari titik koordinat GPS Anda... <i class="bi bi-hourglass-split"></i>
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
                document.getElementById('location-info').innerHTML = "✅ Lokasi Ditemukan: " + position.coords.latitude + ", " + position.coords.longitude;
                document.getElementById('location-info').className = "alert alert-success mt-3";
            }, 
            function(error) {
                // Jika user menolak izin GPS atau gagal
                document.getElementById('location-info').innerHTML = "❌ Gagal akses GPS. Pastikan izin lokasi (Location) nyala di browser!";
                document.getElementById('location-info').className = "alert alert-danger mt-3";
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