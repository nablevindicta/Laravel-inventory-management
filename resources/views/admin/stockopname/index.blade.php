@extends('layouts.master', ['title' => 'Log Stok Opname'])

@section('content')
    <x-container>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <a href="{{ route('admin.stockopname.create') }}" class="btn btn-primary">Mulai Stok Opname</a>
                <a href="{{ route('admin.stockopname.pdf', ['month' => $selectedMonth, 'year' => $selectedYear]) }}" class="btn btn-success">Export PDF</a>
                </div>
        </div>

        <x-card title="Filter Data" class="card-body mb-3">
            <form action="{{ route('admin.stockopname.index') }}" method="GET">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="month" class="form-label">Bulan</label>
                        <select name="month" id="month" class="form-select">
                            @foreach (range(1, 12) as $month)
                                <option value="{{ $month }}" {{ $selectedMonth == $month ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($month)->monthName }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="year" class="form-label">Tahun</label>
                        <select name="year" id="year" class="form-select">
                            @foreach (range(now()->year, 2020) as $year)
                                <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>
                                    {{ $year }}
                                </option>
                            @endforeach
                        </select>
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