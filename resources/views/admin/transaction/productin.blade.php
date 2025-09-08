{{-- @extends('layouts.master', ['title' => 'Barang Masuk'])

@section('content')
    <x-container>
        <div class="col-12">
            <form action="{{ route('admin.transaction.productin') }}" method="GET" class="mb-4">
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
                        <a href="{{ route('admin.transaction.productin') }}" class="btn btn-secondary w-100">Reset</a> 
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('admin.transaction.pdf', ['type' => 'in'] + request()->query()) }}" class="btn btn-success w-75">Export PDF</a>
                    </div>
                </div>
            </form>

            <x-card title="DAFTAR BARANG MASUK" class="card-body p-0">
                <x-table>
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Foto</th>
                            <th>Nama Barang</th>
                            <th>Kategori Barang</th>
                            <th>Kuantitas</th>
                            <th>Satuan Barang</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($transactions as $transaction)
                            <tr>
                                <td>{{ $transaction->created_at->format('d-m-Y H:i') }}</td> 
                                <td>
                                    @foreach ($transaction->details as $details)
                                        <div class="mb-2">
                                            <span class="avatar rounded avatar-md" style="background-image: url({{ $details->product->image }})"></span>
                                        </div>
                                    @endforeach
                                </td>
                                <td>
                                    @foreach ($transaction->details as $details)
                                        {{ $details->product->name }}
                                    @endforeach
                                </td>
                                <td>
                                    @foreach ($transaction->details as $details)
                                        {{ $details->product->category->name }}
                                    @endforeach
                                </td>
                                <td>
                                    @foreach ($transaction->details as $details)
                                        {{ $details->quantity }}
                                    @endforeach
                                </td>
                                <td>
                                    @foreach ($transaction->details as $details)
                                        {{ $details->product->unit }}
                                    @endforeach
                                </td>
                                <td>
                                    <form action="{{ route('admin.transaction.destroy', $transaction->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus transaksi ini? Stok akan dikembalikan.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="5" class="font-weight-bold text-uppercase">
                                Total Barang Masuk
                            </td>
                            <td class="font-weight-bold text-success text-right">
                                {{ $grandQuantity }} Barang
                            </td>
                            <td></td> </tr>
                    </tbody>
                </x-table>
            </x-card>
            <div class="d-flex justify-content-end">{{ $transactions->links() }}</div>
        </div>
    </x-container>
@endsection --}}