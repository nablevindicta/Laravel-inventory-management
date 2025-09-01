@extends('layouts.master', ['title' => $type === 'in' ? 'Barang Masuk' : 'Barang Keluar'])

@section('content')
    <x-container>
        <div class="col-12">

            <form action="{{ route('admin.transaction.product') }}" method="GET" class="mb-4">
                <input type="hidden" name="type" value="{{ $type }}"> <!-- Pertahankan type saat filter -->
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
                        <!-- Export PDF sesuai type -->
                        <a href="{{ route('admin.transaction.pdf', ['type' => $type] + request()->query()) }}" class="btn btn-primary w-100 mb-2">Export PDF</a>
                        <a href="{{ route('admin.transaction.product') }}?type={{ $type }}" class="btn btn-secondary w-100">Reset</a>
                    </div>
                </div>
            </form>

            @php
                $title = $type === 'in' ? 'DAFTAR BARANG MASUK' : 'DAFTAR BARANG KELUAR';
                $totalLabel = $type === 'in' ? 'Total Barang Masuk' : 'Total Barang Keluar';
                $icon = $type === 'in' ? 'shopping-cart-plus' : 'shopping-cart-minus';
            @endphp

            <x-card :title="$title" class="card-body p-0">
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

                        <!-- Baris Total (dinamis) -->
                        <tr>
                            <td colspan="5" class="font-weight-bold text-uppercase">
                                {{ $totalLabel }}
                            </td>
                            <td class="font-weight-bold text-right">
                                <span class="text-{{ $type === 'in' ? 'success' : 'danger' }}">
                                    {{ $grandQuantity }} Barang
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </x-table>
            </x-card>

            <!-- Pagination -->
            <div class="d-flex justify-content-end">
                {{ $transactions->appends(['type' => $type, 'start_date' => $startDate, 'end_date' => $endDate])->links() }}
            </div>
        </div>
    </x-container>

    <!-- Script untuk konversi waktu ke lokal pengguna -->
    @push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const userTimeZone = Intl.DateTimeFormat().resolvedOptions().timeZone;
            const formatOptions = {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                hour12: false,
                timeZone: userTimeZone
            };

            document.querySelectorAll('td[data-timestamp]').forEach(function (td) {
                const isoTime = td.getAttribute('data-timestamp');
                const date = new Date(isoTime);

                if (isNaN(date.getTime())) {
                    console.error('Invalid date:', isoTime);
                    return;
                }

                td.textContent = new Intl.DateTimeFormat('id-ID', formatOptions).format(date);
            });
        });
    </script>
    @endpush
@endsection