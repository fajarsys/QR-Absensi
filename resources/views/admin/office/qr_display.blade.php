<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mesin Absensi - STMIK Mercusuar</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            overflow: hidden;
            user-select: none; /* Cegah text selection */
        }
        .clock-container {
            font-size: 5rem;
            font-weight: bold;
            color: #2c3e50;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 1rem;
            font-family: 'Courier New', Courier, monospace;
        }
        .date-container {
            font-size: 1.5rem;
            color: #7f8c8d;
            margin-bottom: 3rem;
            font-weight: 500;
        }
        .qr-card {
            background: white;
            padding: 2.5rem;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            text-align: center;
        }
        .qr-wrapper {
            width: 300px;
            height: 300px;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0 auto 1.5rem;
            border: 2px dashed #dee2e6;
            border-radius: 15px;
        }
        /* Memperbesar SVG QR Code agar mengisi wrapper */
        .qr-wrapper svg {
            width: 100%;
            height: 100%;
        }
        .brand-title {
            color: #0d6efd;
            font-weight: 800;
            letter-spacing: 1px;
            margin-top: 1rem;
        }
    </style>
</head>
<body>

    <div class="text-center">
        <div class="clock-container" id="digital-clock">00:00:00</div>
        <div class="date-container" id="digital-date">Memuat Tanggal...</div>
    </div>

    <div class="qr-card">
        <h4 class="mb-4 text-dark fw-bold"><i class="bi bi-qr-code-scan"></i> Arahkan Kamera HP ke Sini</h4>
        
        <div class="qr-wrapper" id="qr-container">
            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>

        <div class="progress mt-3 mx-auto" style="width: 80%; height: 6px; border-radius: 10px;">
            <div id="qr-progress" class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" style="width: 100%;"></div>
        </div>
        
        <div class="mt-4">
            <h5 class="brand-title">STMIK MERCUSUAR</h5>
            <p class="text-muted small mb-0"><i class="bi bi-shield-lock text-success"></i> Secured Dynamic QR System</p>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // --- LOGIKA JAM DIGITAL ---
            const clockEl = document.getElementById('digital-clock');
            const dateEl = document.getElementById('digital-date');

            function updateClock() {
                const now = new Date();
                const hours = String(now.getHours()).padStart(2, '0');
                const minutes = String(now.getMinutes()).padStart(2, '0');
                const seconds = String(now.getSeconds()).padStart(2, '0');
                
                clockEl.textContent = `${hours}:${minutes}:${seconds}`;

                const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
                dateEl.textContent = now.toLocaleDateString('id-ID', options);
            }
            setInterval(updateClock, 1000);
            updateClock();


            // --- LOGIKA DYNAMIC QR CODE ---
            const qrContainer = document.getElementById('qr-container');
            const progressBar = document.getElementById('qr-progress');
            const refreshInterval = 15000; // 15 detik
            let timeLeft = refreshInterval;

            function fetchQrCode() {
                fetch("{{ route('admin.office.qr-render') }}")
                    .then(response => response.text())
                    .then(svg => {
                        qrContainer.innerHTML = svg;
                        timeLeft = refreshInterval; // Reset timer
                    })
                    .catch(err => console.error("Gagal memuat QR Code:", err));
            }

            // Panggilan pertama
            fetchQrCode();

            // Animasi Progress bar yang mulus (update tiap 50ms)
            setInterval(() => {
                timeLeft -= 50;
                if (timeLeft <= 0) {
                    fetchQrCode();
                } else {
                    const percentage = Math.max(0, (timeLeft / refreshInterval) * 100);
                    progressBar.style.width = percentage + '%';
                }
            }, 50);
        });

        // Fitur klik ganda layar untuk masuk ke mode fullscreen (opsional namun berguna)
        document.body.addEventListener('dblclick', function() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(err => {
                    console.log(`Error attempting to enable fullscreen: ${err.message}`);
                });
            } else {
                document.exitFullscreen();
            }
        });
    </script>
</body>
</html>
