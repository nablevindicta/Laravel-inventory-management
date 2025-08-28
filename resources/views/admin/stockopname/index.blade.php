@extends('layouts.master', ['title' => 'Log Stok Opname'])

@section('content')
    <x-container>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="{{ route('admin.stockopname.create') }}" class="btn btn-primary">Mulai Stok Opname</a>
            <a href="{{ route('admin.stockopname.pdf', request()->query()) }}" class="btn btn-success">Export PDF</a>
        </div>
        
        <x-card title="" class="card-body">
            <form action="{{ route('admin.stockopname.index') }}" method="GET" class="mb-4">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="start_date" class="form-label">Tanggal Mulai</label>
                        <input type="date" name="start_date" id="start_date" class="form-control" value="{{ old('start_date', $startDate) }}">
                    </div>
                    <div class="col-md-4">
                        <label for="end_date" class="form-label">Tanggal Selesai</label>
                        <input type="date" name="end_date" id="end_date" class="form-control" value="{{ old('end_date', $endDate) }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('admin.stockopname.index') }}" class="btn btn-secondary w-100">Reset</a>
                    </div>
                </div>
            </form>
        </x-card>

        <x-card title="LOG STOK OPNAME" class="card-body p-0">
            <x-table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tanggal Opname</th>
                        <th>Nama Barang</th>
                        <th>Stok Sistem</th>
                        <th>Stok Fisik</th>
                        <th>Selisih</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($logs as $i => $log)
                    <tr>
                        <td>{{ $logs->firstItem() + $i }}</td>
                        <td>{{ $log->created_at->format('d-m-Y H:i') }}</td>
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
            </x-table>
        </x-card>
        <div class="d-flex justify-content-end mt-3">{{ $logs->links() }}</div>
    </x-container>
@endsection