@extends('layouts.master', ['title' => 'Barang Keluar'])

@section('content')
    <x-container>
        <div class="col-12">

            <form action="{{ route('admin.transaction.product') }}" method="GET" class="mb-4">
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
                        <a href="{{ route('admin.transaction.pdf', ['type' => 'out'] + request()->query()) }}" class="btn btn-primary mb-3">Export PDF</a>
                        <a href="{{ route('admin.transaction.product') }}" class="btn btn-secondary w-100">Reset</a>
                    </div>
                </div>
            </form>

            <x-card title="DAFTAR BARANG KELUAR" class="card-body p-0">
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
                                <!-- Kolom Tanggal: Gunakan data-timestamp untuk konversi lokal -->
                                <td data-timestamp="{{ $transaction->created_at->toISOString() }}">
                                    {{ $transaction->created_at->format('d-m-Y H:i') }}
                                </td>

                                <td>
                                    @foreach ($transaction->details as $details)
                                        <div class="mb-2">
                                            <span class="avatar rounded avatar-md" style="background-image: url({{ $details->product->image }})"></span>
                                        </div>
                                    @endforeach
                                </td>
                                <td>
                                    @foreach ($transaction->details as $details)
                                        <div>{{ $details->product->name }}</div>
                                    @endforeach
                                </td>
                                <td>
                                    @foreach ($transaction->details as $details)
                                        <div>{{ $details->product->category->name }}</div>
                                    @endforeach
                                </td>
                                <td>
                                    @foreach ($transaction->details as $details)
                                        <div>{{ $details->quantity }}</div>
                                    @endforeach
                                </td>
                                <td>
                                    @foreach ($transaction->details as $details)
                                        <div>{{ $details->product->unit }}</div>
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

                        <!-- Baris Total -->
                        <tr>
                            <td colspan="5" class="font-weight-bold text-uppercase">
                                Total Barang Keluar
                            </td>
                            <td class="font-weight-bold text-danger text-right">
                                {{ $grandQuantity }} Barang
                            </td>
                        </tr>
                    </tbody>
                </x-table> 
            </x-card>

            <!-- Pagination -->
            <div class="d-flex justify-content-end">
                {{ $transactions->links() }}
            </div>
        </div>
    </x-container>

    <!-- Script untuk konversi waktu ke lokal pengguna -->
    @push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Ambil semua <td> yang memiliki data-timestamp
            document.querySelectorAll('td[data-timestamp]').forEach(function (td) {
                const isoTime = td.getAttribute('data-timestamp');
                const date = new Date(isoTime);

                // Format tanggal sesuai lokal pengguna (Indonesia)
                const options = {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                };

                // Ubah teks menjadi waktu lokal
                td.textContent = date.toLocaleString('id-ID', options);
            });
        });
    </script>
    @endpush
@endsection