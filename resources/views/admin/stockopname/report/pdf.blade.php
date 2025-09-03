<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        body { font-family: 'Arial', sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
            color: white;
        }
        .bg-success { background-color: #28a745; }
        .bg-danger { background-color: #dc3545; }
        .bg-secondary { background-color: #6c757d; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Tanggal Opname</th>
                <th>Nama Barang</th>
                <th>Stok Sistem</th>
                <th>Stok Fisik</th>
                <th>Selisih</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($logs as $log)
            <tr>
                <td>{{ $log->created_at->format('d F Y H:i') }}</td>
                <td>{{ optional($log->product)->name }}</td>
                <td>{{ $log->stock_sistem }}</td>
                <td>{{ $log->stock_fisik }}</td>
                <td>{{ $log->selisih }}</td>
                <td>
                    @if ($log->keterangan === 'Kelebihan')
                        <span class="badge bg-success">{{ $log->keterangan }}</span>
                    @elseif ($log->keterangan === 'Kekurangan')
                        <span class="badge bg-danger">{{ $log->keterangan }}</span>
                    @else
                        <span class="badge bg-secondary">{{ $log->keterangan }}</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>