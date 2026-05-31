<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Kehadiran Keseluruhan</title>
    <link rel="stylesheet" href="{{asset ('css/print.css')}}">
</head>
<body>
    <div class="container-cetak" style="padding: 20px;">
        <button onclick="window.print()" class="btn-print" style="margin-bottom: 20px; padding: 8px 15px; cursor: pointer;">Cetak Dokumen</button>

        <div class="kop-surat">
            <h3 style="margin: 0;">REKAPITULASI KEHADIRAN KARYAWAN KESELURUHAN</h3>
            <h4 style="margin: 5px 0;">STMIK MERCUSUAR</h4>
            <p style="margin: 0; font-size: 14px;">Periode: <strong>{{ \Carbon\Carbon::create()->month((int)$month)->translatedFormat('F') }} {{ $year }}</strong></p>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th>Nama Karyawan</th>
                    <th>Tanggal</th>
                    <th>Jam Masuk</th>
                    <th>Jam Pulang</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($attendances as $index => $absen)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td class="text-left"><strong>{{ $absen->user->name }}</strong></td>
                    <td>{{ \Carbon\Carbon::parse($absen->date)->translatedFormat('d M Y') }}</td>
                    <td>{{ $absen->clock_in ?? '-' }}</td>
                    <td>{{ $absen->clock_out ?? '-' }}</td>
                    <td>{{ $absen->status }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">Tidak ada data kehadiran pada periode ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div style="float: right; text-align: center; margin-top: 40px; width: 250px;">
            <p>Bekasi, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
            <p style="margin-bottom: 70px;">Mengetahui,</p>
            <p style="margin: 0; font-weight: bold;">( Admin / HRD )</p>
        </div>
    </div>

    <script>
        window.onload = function() { window.print(); }
    </script>
</body>
</html>