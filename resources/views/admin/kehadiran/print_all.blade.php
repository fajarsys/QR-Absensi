<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Kehadiran Keseluruhan</title>
    <link rel="stylesheet" href="{{asset ('css/print.css')}}">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
        .text-left {
            text-align: left;
        }
    </style>
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
                    <th rowspan="2" style="width: 5%;">No</th>
                    <th rowspan="2">Nama Karyawan</th>
                    <th colspan="2">Kehadiran (Hadir)</th>
                    <th colspan="3">Tidak Hadir / Izin</th>
                    <th rowspan="2">Total Kehadiran</th>
                    <th rowspan="2">Total Lembur</th>
                </tr>
                <tr>
                    <th>Tepat Waktu</th>
                    <th>Terlambat</th>
                    <th>Sakit</th>
                    <th>Izin</th>
                    <th>Cuti</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rekap as $index => $data)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td class="text-left"><strong>{{ $data->nama }}</strong></td>
                    <td>{{ $data->tepat_waktu }}</td>
                    <td>{{ $data->telat }}</td>
                    <td>{{ $data->sakit }}</td>
                    <td>{{ $data->izin }}</td>
                    <td>{{ $data->cuti }}</td>
                    <td><strong>{{ $data->total_hadir }} Hari</strong></td>
                    <td><strong>{{ $data->total_lembur }} Menit</strong></td>
                </tr>
                @empty
                <tr>
                    <td colspan="8">Tidak ada data kehadiran pada periode ini.</td>
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