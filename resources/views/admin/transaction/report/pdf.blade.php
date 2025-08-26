<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>{{ $title }}</h1>
    </div>

    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Nama Barang</th>
                <th>Kategori Barang</th>
                <th>Kuantitas</th>
                <th>Satuan Barang</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transactions as $transaction)
                @foreach ($transaction->details as $details)
                <tr>
                    <td>{{ $transaction->created_at->format('d-m-Y H:i') }}</td>
                    <td>{{ $details->product->name }}</td>
                    <td>{{ $details->product->category->name }}</td>
                    <td>{{ $details->quantity }}</td>
                    <td>{{ $details->product->unit }}</td>
                </tr>
                @endforeach
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="text-right"><strong>Total Kuantitas:</strong></td>
                <td colspan="2"><strong>{{ $grandQuantity }} Barang</strong></td>
            </tr>
        </tfoot>
    </table>

</body>
</html>