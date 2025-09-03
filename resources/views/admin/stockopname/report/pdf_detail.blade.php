<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Log - {{ $session->title }}</title>
    <style>
        body {
            font-family: 'sans-serif';
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        h1 {
            font-size: 18px;
            margin: 0;
        }
        h2 {
            font-size: 16px;
            margin: 5px 0;
        }
        .info {
            margin-bottom: 20px;
        }
        .info p {
            margin: 2px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Laporan {{ $session->title }}</h1>
    </div>


    <table>
        <thead>
            <tr>
                <th class="text-center">No</th>
                <th class="text-center">Kode</th>
                <th class="text-center">Nama Barang</th>
                <th class="text-center">Stok Sistem</th>
                <th class="text-center">Stok Fisik</th>
                <th class="text-center">Selisih</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($session->logs as $log)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td class="text-center">{{ optional($log->product)->code }}</td>
                    <td class="text-center">{{ optional($log->product)->name ?? 'Produk Dihapus' }}</td>
                    <td class="text-center">{{ $log->stock_sistem }}</td>
                    <td class="text-center">{{ $log->stock_fisik }}</td>
                    <td class="text-center">{{ $log->selisih }}</td>
                    <td>{{ $log->keterangan }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>