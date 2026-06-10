<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Kehadiran - {{ $user->name }}</title>
    <link rel="stylesheet" href="{{asset ('css/print.css')}}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
   
</head>
<body>
    <div class="container-cetak">
        <button onclick="window.print()" class="btn btn-primary mb-3 btn-print">Cetak Dokumen</button>

        <div class="kop-surat text-center">
            <h4 class="mb-0 fw-bold">REKAPITULASI KEHADIRAN KARYAWAN</h4>
            <h5 class="mb-0">STMIK MERCUSUAR</h5>
        </div>

        <div class="row mb-3" style="font-size: 15px;">
            <div class="col-6">
                <table>
                    <tr style="border: none;"><td style="border: none; text-align: left; padding: 2px; width: 120px;">Nama</td><td style="border: none; text-align: left; padding: 2px;">: <strong>{{ $user->name }}</strong></td></tr>
                    <tr style="border: none;"><td style="border: none; text-align: left; padding: 2px;">Jabatan</td><td style="border: none; text-align: left; padding: 2px;">: {{ $user->jabatan }}</td></tr>
                </table>
            </div>
            <div class="col-6 text-end">
                <p class="mb-0 mt-2">Periode: <strong>{{ \Carbon\Carbon::create()->month((int)$month)->translatedFormat('F') }} {{ $year }}</strong></p>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th>Tanggal</th>
                    <th>Jam Masuk</th>
                    <th>Jam Pulang</th>
                    <th>Status</th>
                    <th>Lembur</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($attendances as $index => $absen)
                @php
                    $lemburHariIni = $overtimes->get($absen->date);
                    $durasiLembur = $lemburHariIni ? $lemburHariIni->durasi_menit . ' menit' : '-';
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($absen->date)->translatedFormat('l, d M Y') }}</td>
                    <td>{{ $absen->clock_in ?? '-' }}</td>
                    <td>{{ $absen->clock_out ?? '-' }}</td>
                    <td>{{ $absen->status }}</td>
                    <td>{{ $durasiLembur }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">Tidak ada data kehadiran pada periode ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="row mt-5">
            <div class="col-8"></div>
            <div class="col-4 text-center">
                <p>Bekasi, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
                <p class="mb-5">Mengetahui,</p>
                <br><br>
                <p class="fw-bold mb-0">( Admin / HRD )</p>
            </div>
        </div>
    </div>

    <script>
        // Otomatis memunculkan dialog print saat halaman dibuka
        window.onload = function() { window.print(); }
    </script>
</body>
</html>